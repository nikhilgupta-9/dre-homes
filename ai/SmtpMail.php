<?php
// ---------------------------------------------------------------
// Sends one plain-text email through an SMTP account (for example Gmail or the company's mail server).
//
// Used for the AI chat's lead emails when "smtp_host" is filled in the secrets file. Without it the
// chat uses PHP's mail(), like the website's enquiry forms. mail() only works on a server that has a
// mail system; a local Mac does not, so SMTP is the way to get the emails there.
//
// SmtpMail::send($settings, ['a@b.com'], 'Subject', "Body", 'reply@to.com') returns '' when the server
// accepted the email, or a short reason when it did not. The password never appears in a reason.
// ---------------------------------------------------------------
class SmtpMail
{
    const TIMEOUT_SECONDS = 15;

    public static function send(array $settings, array $to, $subject, $body, $reply_to = '')
    {
        $host = trim((string) ($settings['host'] ?? ''));
        $port = (int) ($settings['port'] ?? 587);
        $user = (string) ($settings['user'] ?? '');
        $pass = (string) ($settings['pass'] ?? '');
        $from = trim((string) ($settings['from'] ?? '')) ?: $user;
        if ($host === '' || !filter_var($from, FILTER_VALIDATE_EMAIL) || !$to) {
            return 'SMTP is not set up completely (host and sender address are needed).';
        }
        // Port 465 is encrypted from the first byte; other ports switch to encryption with STARTTLS.
        $address = ($port === 465 ? 'ssl://' : 'tcp://') . $host . ':' . $port;
        $errno = 0;
        $errstr = '';
        $socket = @stream_socket_client($address, $errno, $errstr, self::TIMEOUT_SECONDS);
        if (!$socket) {
            return "Could not reach the mail server $host:$port ($errstr).";
        }
        stream_set_timeout($socket, self::TIMEOUT_SECONDS);
        try {
            self::expect($socket, null, 220);
            $features = self::expect($socket, 'EHLO ' . self::helloName(), 250);
            if ($port !== 465) {
                if (stripos($features, 'STARTTLS') !== false) {
                    self::expect($socket, 'STARTTLS', 220);
                    if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT)) {
                        throw new RuntimeException('The secure connection to the mail server failed.');
                    }
                    $features = self::expect($socket, 'EHLO ' . self::helloName(), 250);
                } elseif (!in_array(strtolower($host), ['localhost', '127.0.0.1'], true)) {
                    // A password is never sent over an unencrypted connection to another machine.
                    throw new RuntimeException('The mail server does not offer a secure connection. Use port 465 or 587.');
                }
            }
            if ($user !== '') {
                self::expect($socket, 'AUTH LOGIN', 334);
                self::expect($socket, base64_encode($user), 334);
                self::expect($socket, base64_encode($pass), 235, 'The mail server did not accept the user name and password. For Gmail, use an App Password.');
            }
            self::expect($socket, 'MAIL FROM:<' . $from . '>', 250);
            foreach ($to as $recipient) {
                self::expect($socket, 'RCPT TO:<' . $recipient . '>', [250, 251]);
            }
            self::expect($socket, 'DATA', 354);
            $headers = [
                'Date: ' . date('r'),
                'From: DRE Homes Website <' . $from . '>',
                'To: ' . implode(', ', $to),
                'Subject: =?UTF-8?B?' . base64_encode($subject) . '?=',
                'Message-ID: <' . bin2hex(random_bytes(12)) . '@' . self::helloName() . '>',
                'MIME-Version: 1.0',
                'Content-Type: text/plain; charset=UTF-8',
                // Base64 keeps every line short and needs no escaping of lines that start with a dot.
                'Content-Transfer-Encoding: base64',
            ];
            if ($reply_to !== '' && filter_var($reply_to, FILTER_VALIDATE_EMAIL)) {
                $headers[] = 'Reply-To: ' . $reply_to;
            }
            $message = implode("\r\n", $headers) . "\r\n\r\n" . rtrim(chunk_split(base64_encode((string) $body), 76, "\r\n")) . "\r\n.";
            self::expect($socket, $message, 250);
            self::send_line($socket, 'QUIT');
            return '';
        } catch (RuntimeException $error) {
            return $error->getMessage();
        } finally {
            fclose($socket);
        }
    }

    private static function helloName()
    {
        $name = preg_replace('/[^A-Za-z0-9.-]/', '', (string) ($_SERVER['SERVER_NAME'] ?? gethostname()));
        return $name !== '' ? $name : 'localhost';
    }

    private static function send_line($socket, $line)
    {
        fwrite($socket, $line . "\r\n");
    }

    // Sends a line (unless $line is null) and reads the whole answer; throws when its code is not the expected one.
    private static function expect($socket, $line, $codes, $message = '')
    {
        if ($line !== null) {
            self::send_line($socket, $line);
        }
        $answer = '';
        while (($row = fgets($socket, 1024)) !== false) {
            $answer .= $row;
            // The last line of an answer has a space after the code ("250 OK"), earlier ones a dash ("250-SIZE").
            if (strlen($row) < 4 || $row[3] !== '-') {
                break;
            }
        }
        $code = (int) substr($answer, 0, 3);
        if (!in_array($code, (array) $codes, true)) {
            throw new RuntimeException($message !== '' ? $message : 'The mail server answered: ' . trim(substr($answer, 0, 200)));
        }
        return $answer;
    }
}
