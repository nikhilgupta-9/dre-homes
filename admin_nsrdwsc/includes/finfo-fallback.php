<?php
// ---------------------------------------------------------------
// Stand-in for PHP's "fileinfo" extension.
//
// The upload checks of the admin ask "what kind of file is this really?" with
//   $finfo = new finfo(FILEINFO_MIME_TYPE);  $finfo->file($path);
// On a server where the fileinfo extension is switched off that class does not
// exist and every upload ends in "Class finfo not found". This file is loaded by
// db/functions.php and only does something on such a server: it recognises the
// kinds of file the admin accepts by their first bytes, which is also how
// fileinfo tells them apart. Anything else is reported as unknown, so it is refused.
// ---------------------------------------------------------------
if (!defined('FILEINFO_MIME_TYPE')) {
	define('FILEINFO_MIME_TYPE', 16);
}

if (!class_exists('finfo', false)) {
	class finfo
	{
		public function __construct($flags = 0, $magic_database = null)
		{
		}

		// The kind of a file on disk, for example "image/jpeg".
		public function file($filename, $flags = 0, $context = null)
		{
			$handle = is_string($filename) && is_file($filename) ? @fopen($filename, 'rb') : false;
			if (!$handle) {
				return false;
			}
			$start = (string) fread($handle, 64);
			fclose($handle);
			return self::kind($start);
		}

		// The kind of content that is already in memory.
		public function buffer($string, $flags = 0, $context = null)
		{
			return self::kind(substr((string) $string, 0, 64));
		}

		private static function kind($start)
		{
			if (strncmp($start, "\xFF\xD8\xFF", 3) === 0) {
				return 'image/jpeg';
			}
			if (strncmp($start, "\x89PNG\r\n\x1A\n", 8) === 0) {
				return 'image/png';
			}
			if (strncmp($start, 'GIF87a', 6) === 0 || strncmp($start, 'GIF89a', 6) === 0) {
				return 'image/gif';
			}
			if (strncmp($start, 'RIFF', 4) === 0 && substr($start, 8, 4) === 'WEBP') {
				return 'image/webp';
			}
			if (strncmp($start, '%PDF-', 5) === 0) {
				return 'application/pdf';
			}
			if (strncmp($start, "\x00\x00\x01\x00", 4) === 0) {
				return 'image/vnd.microsoft.icon';
			}
			if (strncmp($start, "\x1A\x45\xDF\xA3", 4) === 0) {
				return 'video/webm';
			}
			if (substr($start, 4, 4) === 'ftyp') {
				// MP4 and QuickTime share this layout: the brand that follows tells them apart.
				return substr($start, 8, 2) === 'qt' ? 'video/quicktime' : 'video/mp4';
			}
			return 'application/octet-stream';
		}
	}
}
