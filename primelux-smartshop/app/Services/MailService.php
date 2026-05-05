<?php
declare(strict_types=1);

/*
 * Envío de emails via SMTP sobre SSL (puerto 465).
 * SSL directo en lugar de STARTTLS — más fiable en IONOS shared hosting.
 * Sin dependencias externas.
 */

class MailService
{
    private const TIMEOUT = 15;

    public function send(string $to, string $toName, string $subject, string $htmlBody, string $textBody = ''): bool
    {
        $socket = null;
        try {
            $socket = fsockopen('ssl://' . MAIL_SMTP_HOST, 465, $errno, $errstr, self::TIMEOUT);
            if (!$socket) throw new \RuntimeException("Conexión SMTP fallida: {$errstr} ({$errno})");
            stream_set_timeout($socket, self::TIMEOUT);
            $this->expect($socket, 220);
            $this->send_cmd($socket, 'EHLO ' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
            $this->expect($socket, 250);
            $this->send_cmd($socket, 'AUTH LOGIN');
            $this->expect($socket, 334);
            $this->send_cmd($socket, base64_encode(MAIL_SMTP_USER));
            $this->expect($socket, 334);
            $this->send_cmd($socket, base64_encode(MAIL_SMTP_PASS));
            $this->expect($socket, 235);
            $this->send_cmd($socket, 'MAIL FROM:<' . MAIL_SMTP_USER . '>');
            $this->expect($socket, 250);
            $this->send_cmd($socket, 'RCPT TO:<' . $to . '>');
            $this->expect($socket, 250);
            $this->send_cmd($socket, 'DATA');
            $this->expect($socket, 354);
            fwrite($socket, $this->buildMessage(MAIL_NOREPLY_ADDRESS, MAIL_NOREPLY_NAME, $to, $toName, $subject, $htmlBody, $textBody));
            $this->expect($socket, 250);
            $this->send_cmd($socket, 'QUIT');
            return true;
        } catch (\RuntimeException $e) {
            error_log('[MailService] ' . $e->getMessage());
            return false;
        } finally {
            if ($socket) fclose($socket);
        }
    }

    private function send_cmd(mixed $socket, string $cmd): void { fwrite($socket, $cmd . "\r\n"); }

    private function expect(mixed $socket, int $expectedCode): string
    {
        $response = '';
        while ($line = fgets($socket, 1024)) {
            $response .= $line;
            if (strlen($line) >= 4 && $line[3] === ' ') break;
        }
        $actualCode = (int) substr($response, 0, 3);
        if ($actualCode !== $expectedCode) throw new \RuntimeException("SMTP esperaba {$expectedCode}, recibió {$actualCode}: " . trim($response));
        return $response;
    }

    private function buildMessage(string $fromAddress, string $fromName, string $to, string $toName, string $subject, string $htmlBody, string $textBody): string
    {
        $boundary = '----=_Part_' . md5(uniqid('', true));
        if (empty($textBody)) $textBody = strip_tags(str_replace(['<br>', '<br/>', '</p>'], "\n", $htmlBody));
        $msg  = 'Date: ' . date('r') . "\r\n";
        $msg .= 'From: =?UTF-8?B?' . base64_encode($fromName) . '?= <' . $fromAddress . ">\r\n";
        $msg .= 'Reply-To: <' . $fromAddress . ">\r\n";
        $msg .= 'To: =?UTF-8?B?' . base64_encode($toName) . '?= <' . $to . ">\r\n";
        $msg .= 'Subject: =?UTF-8?B?' . base64_encode($subject) . "?=\r\n";
        $msg .= "MIME-Version: 1.0\r\nContent-Type: multipart/alternative; boundary=\"{$boundary}\"\r\nX-Mailer: PrimeLux-SMTP\r\n\r\n";
        $msg .= "--{$boundary}\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n" . chunk_split(base64_encode($textBody)) . "\r\n";
        $msg .= "--{$boundary}\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n" . chunk_split(base64_encode($htmlBody)) . "\r\n";
        $msg .= "--{$boundary}--\r\n\r\n.\r\n";
        return $msg;
    }
}
