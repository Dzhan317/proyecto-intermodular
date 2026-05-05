<?php
declare(strict_types=1);

/*
 * Envío de emails via SMTP sobre SSL (puerto 465).
 * Se usa SSL directo en lugar de STARTTLS para mayor compatibilidad
 * con IONOS shared hosting. Sin dependencias externas.
 */

class MailService
{
    // Tiempo máximo de espera por respuesta del servidor
    private const TIMEOUT = 15;

    public function send(
        string $to,
        string $toName,
        string $subject,
        string $htmlBody,
        string $textBody = ''
    ): bool {
        $socket = null;

        try {
            // Puerto 465 con SSL directo — más fiable en IONOS que STARTTLS/587
            $socket = fsockopen(
                'ssl://' . MAIL_SMTP_HOST,
                465,
                $errno,
                $errstr,
                self::TIMEOUT
            );

            if (!$socket) {
                throw new \RuntimeException("No se pudo conectar al servidor SMTP: {$errstr} ({$errno})");
            }

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

            fwrite($socket, $this->buildMessage(
                MAIL_NOREPLY_ADDRESS,
                MAIL_NOREPLY_NAME,
                $to, $toName,
                $subject, $htmlBody, $textBody
            ));

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

    private function send_cmd(mixed $socket, string $cmd): void
    {
        fwrite($socket, $cmd . "\r\n");
    }

    private function expect(mixed $socket, int $expectedCode): string
    {
        $response = '';
        while ($line = fgets($socket, 1024)) {
            $response .= $line;
            // Línea final del bloque: código + espacio (ej: "250 OK")
            if (strlen($line) >= 4 && $line[3] === ' ') break;
        }

        $actualCode = (int) substr($response, 0, 3);
        if ($actualCode !== $expectedCode) {
            throw new \RuntimeException(
                "SMTP esperaba {$expectedCode}, recibió {$actualCode}: " . trim($response)
            );
        }

        return $response;
    }

    private function buildMessage(
        string $fromAddress,
        string $fromName,
        string $to,
        string $toName,
        string $subject,
        string $htmlBody,
        string $textBody
    ): string {
        $boundary = '----=_Part_' . md5(uniqid('', true));
        if (empty($textBody)) {
            $textBody = strip_tags(str_replace(['<br>', '<br/>', '</p>'], "\n", $htmlBody));
        }

        $msg  = 'Date: ' . date('r') . "\r\n";
        $msg .= 'From: =?UTF-8?B?' . base64_encode($fromName) . '?= <' . $fromAddress . ">\r\n";
        $msg .= 'Reply-To: <' . $fromAddress . ">\r\n";
        $msg .= 'To: =?UTF-8?B?' . base64_encode($toName) . '?= <' . $to . ">\r\n";
        $msg .= 'Subject: =?UTF-8?B?' . base64_encode($subject) . "?=\r\n";
        $msg .= "MIME-Version: 1.0\r\n";
        $msg .= 'Content-Type: multipart/alternative; boundary="' . $boundary . '"' . "\r\n";
        $msg .= "X-Mailer: PrimeLux-SMTP\r\n";
        $msg .= "\r\n";

        // Parte texto plano
        $msg .= '--' . $boundary . "\r\n";
        $msg .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $msg .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $msg .= chunk_split(base64_encode($textBody)) . "\r\n";

        // Parte HTML
        $msg .= '--' . $boundary . "\r\n";
        $msg .= "Content-Type: text/html; charset=UTF-8\r\n";
        $msg .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $msg .= chunk_split(base64_encode($htmlBody)) . "\r\n";

        $msg .= '--' . $boundary . "--\r\n";
        $msg .= "\r\n.\r\n";

        return $msg;
    }
}
