<?php
/**
 * Reads projects from the GenieMap partner API (https://api.geniemap.net/docs).
 *
 * Read-only: this class only ever sends GET requests. The key stays on the server,
 * in the secrets file, and is never sent to the browser.
 */
class GenieMap
{
	const BASE = 'https://api.geniemap.net/v1';
	const LIST_TTL = 21600;     // the project list is kept for 6 hours; "Read again" on the page refreshes it sooner
	const PAGE = 500;

	private $token;
	public $error = '';

	public function __construct($token)
	{
		$this->token = trim((string) $token);
	}

	public function isConfigured()
	{
		return $this->token !== '';
	}

	/** One GET request. Returns the decoded answer, or null with $this->error set. */
	public function get($path, array $query = [], $timeout = 40)
	{
		$this->error = '';
		$curl = curl_init(self::BASE . $path . ($query ? '?' . http_build_query($query) : ''));
		curl_setopt_array($curl, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPGET => true,
			CURLOPT_TIMEOUT => $timeout,
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $this->token, 'Accept: application/json'],
		]);
		$body = curl_exec($curl);
		$status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$problem = curl_error($curl);
		curl_close($curl);
		if ($body === false) {
			$this->error = 'GenieMap could not be reached (' . $problem . ').';
			return null;
		}
		if ($status === 401 || $status === 403) {
			$this->error = 'GenieMap refused the API key. Check the key in the secrets file.';
			return null;
		}
		if ($status !== 200) {
			$this->error = 'GenieMap answered with an error (' . $status . ').';
			return null;
		}
		$data = json_decode($body, true);
		if (!is_array($data)) {
			$this->error = 'GenieMap sent an answer that could not be read.';
			return null;
		}
		return $data;
	}

	/**
	 * The folder the project list and the small pictures are kept in: ai/storage/geniemap, which
	 * the web server never serves. Returns '' when no folder can be written to; the page then
	 * says so, because without it every visit has to read the whole list from GenieMap again.
	 */
	public static function storageDir()
	{
		static $dir = null;
		if ($dir === null) {
			$dir = '';
			foreach ([dirname(__DIR__, 2) . '/ai/storage/geniemap', sys_get_temp_dir() . '/dre_geniemap'] as $candidate) {
				if ((is_dir($candidate) || @mkdir($candidate, 0755, true)) && is_writable($candidate)) {
					$dir = $candidate;
					break;
				}
			}
		}
		return $dir;
	}

	private function cacheFile()
	{
		$dir = self::storageDir();
		return $dir === '' ? '' : $dir . '/projects_' . substr(hash('sha256', $this->token), 0, 12) . '.json';
	}

	/** When the kept list was fetched (0 = there is none). */
	public function listAge()
	{
		$file = $this->cacheFile();
		return $file !== '' && is_file($file) ? (int) filemtime($file) : 0;
	}

	/**
	 * Every project GenieMap lists, in a short form. Fetched page by page, then kept for 6 hours
	 * because the full answer is several megabytes.
	 */
	public function projects($refresh = false)
	{
		$file = $this->cacheFile();
		$kept = $file !== '' && is_file($file) ? json_decode((string) file_get_contents($file), true) : null;
		if (!$refresh && is_array($kept) && time() - filemtime($file) < self::LIST_TTL) {
			return $kept;
		}
		$all = [];
		// Four pages are asked for at the same time, which makes the full read several times quicker.
		for ($offset = 0, $more = true; $more && $offset < 40000; $offset += self::PAGE * 4) {
			$multi = curl_multi_init();
			$handles = [];
			for ($i = 0; $i < 4; $i++) {
				$handles[$i] = curl_init(self::BASE . '/projects?' . http_build_query(['limit' => self::PAGE, 'offset' => $offset + $i * self::PAGE]));
				curl_setopt_array($handles[$i], [
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_HTTPGET => true,
					CURLOPT_TIMEOUT => 90,
					CURLOPT_CONNECTTIMEOUT => 10,
					CURLOPT_ENCODING => '',
					CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $this->token, 'Accept: application/json'],
				]);
				curl_multi_add_handle($multi, $handles[$i]);
			}
			do {
				$state = curl_multi_exec($multi, $running);
				if ($running) {
					curl_multi_select($multi, 1.0);
				}
			} while ($running && $state === CURLM_OK);
			foreach ($handles as $handle) {
				$status = (int) curl_getinfo($handle, CURLINFO_HTTP_CODE);
				$page = $status === 200 ? json_decode((string) curl_multi_getcontent($handle), true) : null;
				curl_multi_remove_handle($multi, $handle);
				curl_close($handle);
				if (!is_array($page)) {
					curl_multi_close($multi);
					$this->error = $status === 401 || $status === 403 ? 'GenieMap refused the API key. Check the key in the secrets file.' : 'GenieMap could not be read (' . $status . ').';
					// An older list is better than none.
					return is_array($kept) ? $kept : null;
				}
				foreach ($page as $project) {
					$all[] = [
						'id' => (int) ($project['id'] ?? 0),
						'name' => (string) ($project['name'] ?? ''),
						'developer' => (string) ($project['developer']['name'] ?? ''),
						'district' => (string) ($project['district']['name'] ?? ''),
						'emirate' => (string) ($project['district']['emirate']['name'] ?? ''),
						'status' => (string) ($project['status'] ?? ''),
						'handover' => (string) ($project['handover'] ?? ''),
						'price_from' => (float) ($project['unitPriceRange']['from'] ?? 0),
						'image' => (string) ($project['images'][0] ?? ''),
						'images' => count($project['images'] ?? []),
						'layouts' => array_values(array_filter(array_map(function ($layout) { return (string) ($layout['name'] ?? ''); }, $project['unitLayouts'] ?? []))),
						'types' => array_values(array_filter(array_map(function ($type) { return (string) ($type['name'] ?? ''); }, $project['unitTypes'] ?? []))),
					];
				}
				if (count($page) < self::PAGE) {
					$more = false;
				}
			}
			curl_multi_close($multi);
		}
		if ($file !== '') {
			@file_put_contents($file, json_encode($all), LOCK_EX);
		}
		return $all;
	}

	/** Everything GenieMap knows about one project, or null. */
	public function project($id)
	{
		return $this->get('/projects/' . (int) $id);
	}

	/** True when this PHP can make the small pictures for the list (it has to read WebP files). */
	public static function canMakeThumbs()
	{
		return self::storageDir() !== '' && function_exists('imagecreatefromwebp') && function_exists('imagejpeg');
	}

	/**
	 * A small copy (480 pixels wide) of a project's first picture, made once and kept.
	 * Returns the path of the file, or '' when it cannot be made.
	 */
	public function thumb($project_id)
	{
		$project_id = (int) $project_id;
		if (!self::canMakeThumbs() || $project_id <= 0) {
			return '';
		}
		$file = self::storageDir() . '/thumbs/' . $project_id . '.jpg';
		if (is_file($file)) {
			return filesize($file) > 0 ? $file : '';
		}
		$url = '';
		foreach ($this->projects() ?: [] as $project) {
			if ($project['id'] === $project_id) {
				$url = $project['image'];
				break;
			}
		}
		$host = strtolower((string) parse_url($url, PHP_URL_HOST));
		if ($url === '' || parse_url($url, PHP_URL_SCHEME) !== 'https' || ($host !== 'geniemap.net' && substr($host, -13) !== '.geniemap.net')) {
			return '';
		}
		if (!is_dir(dirname($file))) {
			@mkdir(dirname($file), 0755, true);
		}
		$curl = curl_init($url);
		curl_setopt_array($curl, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 25, CURLOPT_CONNECTTIMEOUT => 8]);
		$body = curl_exec($curl);
		$status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		$image = $body !== false && $status === 200 && strlen($body) < 15 * 1024 * 1024 ? @imagecreatefromstring($body) : false;
		if (!$image) {
			// An empty file remembers the failure, so a broken picture is not fetched again on every visit.
			@file_put_contents($file, '');
			return '';
		}
		if (imagesx($image) > 480) {
			// imagecopyresampled works on every PHP build; imagescale() fails on some of them.
			$height = max(1, (int) round(imagesy($image) * 480 / imagesx($image)));
			$small = imagecreatetruecolor(480, $height);
			if (imagecopyresampled($small, $image, 0, 0, 0, 0, 480, $height, imagesx($image), imagesy($image))) {
				imagedestroy($image);
				$image = $small;
			} else {
				imagedestroy($small);
			}
		}
		imageinterlace($image, true);
		$ok = @imagejpeg($image, $file . '.tmp', 78) && @rename($file . '.tmp', $file);
		imagedestroy($image);
		return $ok ? $file : '';
	}

	/** GenieMap writes names as: DEVELOPER "Project name" NEW LAUNCH. Returns the project's own name. */
	public static function cleanName($name)
	{
		$name = trim((string) $name);
		// The quoted part is the name; words after it ("NEW LAUNCH") are GenieMap's own labels.
		if (preg_match('/["“]([^"”]+)["”]/u', $name, $match) && trim($match[1]) !== '') {
			return trim($match[1]);
		}
		return $name;
	}

	/**
	 * Downloads one picture from GenieMap's file server into $folder under a new random name.
	 * Returns the file name, or '' when the address is not GenieMap's or the file is not a picture.
	 */
	public static function downloadImage($url, $folder, $prefix)
	{
		return self::download($url, $folder, $prefix, ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'], 12 * 1024 * 1024);
	}

	/** The same for a PDF (a brochure, a floor plan), up to 40 MB. */
	public static function downloadPdf($url, $folder, $prefix)
	{
		return self::download($url, $folder, $prefix, ['application/pdf' => 'pdf'], 40 * 1024 * 1024);
	}

	/** True when the address is a file on GenieMap's own servers. */
	public static function isOwnFile($url)
	{
		$host = strtolower((string) parse_url((string) $url, PHP_URL_HOST));
		return parse_url((string) $url, PHP_URL_SCHEME) === 'https' && ($host === 'geniemap.net' || substr($host, -13) === '.geniemap.net');
	}

	private static function download($url, $folder, $prefix, array $types, $max_bytes)
	{
		if (!self::isOwnFile($url)) {
			return '';
		}
		$curl = curl_init($url);
		curl_setopt_array($curl, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 120, CURLOPT_CONNECTTIMEOUT => 10, CURLOPT_FOLLOWLOCATION => false]);
		$body = curl_exec($curl);
		$status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		if ($body === false || $status !== 200 || strlen($body) < 500 || strlen($body) > $max_bytes) {
			return '';
		}
		$real = (new finfo(FILEINFO_MIME_TYPE))->buffer($body);
		if (!isset($types[$real])) {
			return '';
		}
		if (!is_dir($folder)) {
			mkdir($folder, 0755, true);
		}
		$name = $prefix . '_' . bin2hex(random_bytes(6)) . '.' . $types[$real];
		return file_put_contents($folder . $name, $body) !== false ? $name : '';
	}
}
