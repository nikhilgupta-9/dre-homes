<?php
// ---------------------------------------------------------------
// Fingerprint sign-in (WebAuthn): the checks done on the server.
//
// The browser asks the device (Touch ID, Windows Hello ...) to make a key pair
// for this website. The private key never leaves the device and is only used
// after the fingerprint matched. This class
//   - reads the public key the device sends when a fingerprint is set up
//     (WebAuthn::readRegistration), and
//   - checks the signature the device sends at sign-in (WebAuthn::readSignIn).
//
// Each step throws a RuntimeException with a short reason when a check fails.
// ---------------------------------------------------------------
class WebAuthn
{
    const CHALLENGE_SECONDS = 300;

    // The website's name for the device: the host of the admin address.
    public static function siteId()
    {
        return strtolower((string) parse_url(BASE_URL, PHP_URL_HOST));
    }

    public static function encode($bytes)
    {
        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }

    public static function decode($text)
    {
        if (!is_string($text) || $text === '' || !preg_match('/^[A-Za-z0-9_-]+$/', $text)) {
            throw new RuntimeException('Unreadable data from the browser.');
        }
        $bytes = base64_decode(strtr($text, '-_', '+/'), true);
        if ($bytes === false) {
            throw new RuntimeException('Unreadable data from the browser.');
        }
        return $bytes;
    }

    // A new one-time value the device has to sign. Kept in the session until it is used.
    public static function newChallenge($purpose)
    {
        $challenge = self::encode(random_bytes(32));
        $_SESSION['webauthn_challenge'] = ['value' => $challenge, 'purpose' => $purpose, 'time' => time()];
        return $challenge;
    }

    // Returns the stored value once and forgets it, so an answer cannot be sent twice.
    private static function takeChallenge($purpose)
    {
        $stored = $_SESSION['webauthn_challenge'] ?? null;
        unset($_SESSION['webauthn_challenge']);
        if (!is_array($stored) || ($stored['purpose'] ?? '') !== $purpose || time() - (int) ($stored['time'] ?? 0) > self::CHALLENGE_SECONDS) {
            throw new RuntimeException('The request took too long. Please try again.');
        }
        return (string) $stored['value'];
    }

    // What the browser says it did: the kind of request, our one-time value and the website it ran on.
    private static function checkClientData($client_json, $type, $challenge)
    {
        $client = json_decode($client_json, true);
        if (!is_array($client) || ($client['type'] ?? '') !== $type) {
            throw new RuntimeException('Unexpected answer from the browser.');
        }
        if (!is_string($client['challenge'] ?? null) || !hash_equals($challenge, $client['challenge'])) {
            throw new RuntimeException('The request does not match. Please try again.');
        }
        $origin = parse_url((string) ($client['origin'] ?? ''));
        $host = strtolower((string) ($origin['host'] ?? ''));
        $site = self::siteId();
        $host_ok = $host === $site || substr($host, -strlen('.' . $site)) === '.' . $site;
        $scheme_ok = ($origin['scheme'] ?? '') === 'https' || $host === 'localhost';
        if (!$host_ok || !$scheme_ok) {
            throw new RuntimeException('The answer came from another website.');
        }
    }

    // The part the device signs: website, flags, counter and (when setting up) the new key.
    private static function readDeviceData($data, $with_key)
    {
        if (strlen($data) < 37) {
            throw new RuntimeException('Unreadable answer from the device.');
        }
        if (!hash_equals(hash('sha256', self::siteId(), true), substr($data, 0, 32))) {
            throw new RuntimeException('The device answered for another website.');
        }
        $flags = ord($data[32]);
        // Bit 1: someone was present. Bit 4: the device checked who (fingerprint, face or device PIN).
        if (($flags & 0x01) === 0 || ($flags & 0x04) === 0) {
            throw new RuntimeException('The device did not confirm the fingerprint.');
        }
        $result = ['sign_count' => unpack('N', substr($data, 33, 4))[1]];
        if (!$with_key) {
            return $result;
        }
        if (($flags & 0x40) === 0 || strlen($data) < 55) {
            throw new RuntimeException('The device sent no key.');
        }
        $id_length = unpack('n', substr($data, 53, 2))[1];
        if ($id_length < 1 || $id_length > 180 || strlen($data) < 55 + $id_length) {
            throw new RuntimeException('The device sent an unusable key.');
        }
        $result['credential_id'] = substr($data, 55, $id_length);
        $offset = 55 + $id_length;
        $result['public_key'] = self::keyToPem(self::readCbor($data, $offset));
        return $result;
    }

    // Setting up: returns ['credential_id' => base64url, 'public_key' => PEM, 'sign_count' => int].
    public static function readRegistration(array $answer)
    {
        $challenge = self::takeChallenge('register');
        self::checkClientData(self::decode($answer['clientDataJSON'] ?? null), 'webauthn.create', $challenge);
        $offset = 0;
        $attestation = self::readCbor(self::decode($answer['attestationObject'] ?? null), $offset);
        if (!is_array($attestation) || !is_string($attestation['authData'] ?? null)) {
            throw new RuntimeException('Unreadable answer from the device.');
        }
        $device = self::readDeviceData($attestation['authData'], true);
        $device['credential_id'] = self::encode($device['credential_id']);
        return $device;
    }

    // Signing in: checks the signature against the stored key and returns the device's new counter.
    public static function readSignIn(array $answer, $public_key_pem, $stored_count)
    {
        $challenge = self::takeChallenge('sign-in');
        $client_json = self::decode($answer['clientDataJSON'] ?? null);
        self::checkClientData($client_json, 'webauthn.get', $challenge);
        $device_data = self::decode($answer['authenticatorData'] ?? null);
        $device = self::readDeviceData($device_data, false);
        $signature = self::decode($answer['signature'] ?? null);
        $key = openssl_pkey_get_public($public_key_pem);
        if (!$key || openssl_verify($device_data . hash('sha256', $client_json, true), $signature, $key, OPENSSL_ALGO_SHA256) !== 1) {
            throw new RuntimeException('The fingerprint check did not match this account.');
        }
        // A counter that goes backwards means the key was copied. Devices that do not count always send 0.
        if (($device['sign_count'] !== 0 || (int) $stored_count !== 0) && $device['sign_count'] <= (int) $stored_count) {
            throw new RuntimeException('This device key is no longer trusted. Set the fingerprint up again.');
        }
        return $device['sign_count'];
    }

    // Reads one value of the CBOR format the device uses and moves $offset past it.
    private static function readCbor($data, &$offset)
    {
        if ($offset >= strlen($data)) {
            throw new RuntimeException('Unreadable answer from the device.');
        }
        $first = ord($data[$offset++]);
        $kind = $first >> 5;
        $info = $first & 0x1f;
        if ($info < 24) {
            $number = $info;
        } elseif ($info >= 24 && $info <= 27) {
            $size = 1 << ($info - 24);
            if ($offset + $size > strlen($data)) {
                throw new RuntimeException('Unreadable answer from the device.');
            }
            $number = 0;
            for ($i = 0; $i < $size; $i++) {
                $number = ($number << 8) | ord($data[$offset + $i]);
            }
            $offset += $size;
        } else {
            throw new RuntimeException('Unreadable answer from the device.');
        }
        switch ($kind) {
            case 0:
                return $number;
            case 1:
                return -1 - $number;
            case 2:
            case 3:
                if ($number < 0 || $offset + $number > strlen($data)) {
                    throw new RuntimeException('Unreadable answer from the device.');
                }
                $value = substr($data, $offset, $number);
                $offset += $number;
                return $value;
            case 4:
                $list = [];
                for ($i = 0; $i < $number; $i++) {
                    $list[] = self::readCbor($data, $offset);
                }
                return $list;
            case 5:
                $map = [];
                for ($i = 0; $i < $number; $i++) {
                    $name = self::readCbor($data, $offset);
                    $map[is_int($name) || is_string($name) ? $name : ''] = self::readCbor($data, $offset);
                }
                return $map;
            default:
                // Tags, true/false/null and floats are not used in the parts read here.
                return $kind === 6 ? self::readCbor($data, $offset) : null;
        }
    }

    // Turns the device's key (COSE format) into the PEM text openssl reads.
    private static function keyToPem($key)
    {
        if (!is_array($key)) {
            throw new RuntimeException('The device sent an unusable key.');
        }
        $type = $key[1] ?? null;
        $algorithm = $key[3] ?? null;
        if ($type === 2 && $algorithm === -7 && ($key[-1] ?? null) === 1 && strlen((string) ($key[-2] ?? '')) === 32 && strlen((string) ($key[-3] ?? '')) === 32) {
            // Elliptic curve P-256 (Touch ID, most devices).
            $der = hex2bin('3059301306072a8648ce3d020106082a8648ce3d030107034200') . "\x04" . $key[-2] . $key[-3];
        } elseif ($type === 3 && $algorithm === -257 && strlen((string) ($key[-1] ?? '')) >= 256 && strlen((string) ($key[-2] ?? '')) >= 1) {
            // RSA (some Windows Hello devices).
            $numbers = self::der("\x02", self::positive($key[-1])) . self::der("\x02", self::positive($key[-2]));
            $der = self::der("\x30", hex2bin('300d06092a864886f70d0101010500') . self::der("\x03", "\x00" . self::der("\x30", $numbers)));
        } else {
            throw new RuntimeException('This device uses a kind of key that is not supported.');
        }
        return "-----BEGIN PUBLIC KEY-----\n" . chunk_split(base64_encode($der), 64, "\n") . "-----END PUBLIC KEY-----\n";
    }

    private static function positive($bytes)
    {
        $bytes = ltrim($bytes, "\x00");
        return (ord($bytes[0] ?? "\x00") & 0x80) ? "\x00" . $bytes : ($bytes === '' ? "\x00" : $bytes);
    }

    private static function der($tag, $content)
    {
        $length = strlen($content);
        if ($length < 128) {
            return $tag . chr($length) . $content;
        }
        $bytes = ltrim(pack('N', $length), "\x00");
        return $tag . chr(0x80 | strlen($bytes)) . $bytes . $content;
    }
}
