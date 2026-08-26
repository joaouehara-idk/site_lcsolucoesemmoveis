<?php

namespace App\Services;

use App\Core\Logger;

class EmailService {
    public function send(string $to, string $subject, string $htmlBody, string $plainText = ''): bool {
        $fromEmail = $this->getBrevoSenderEmail();
        $fromName  = $this->getBrevoSenderName();

        $sent = $this->sendViaBrevoApi($to, $fromEmail, $fromName, $subject, $plainText, $htmlBody);
        if ($sent) {
            Logger::info("Email enviado via Brevo API para: {$to}");
            return true;
        }

        Logger::error("Brevo API falhou para: {$to}, tentando SMTP (relay Brevo)...");
        $sent = $this->sendViaSmtp($to, $fromEmail, $fromName, $subject, $plainText, $htmlBody);
        if ($sent) {
            Logger::info("Email enviado via SMTP (Brevo relay) para: {$to}");
            return true;
        }

        Logger::error("Brevo API + SMTP falharam para: {$to}, tentando mail()...");
        $fallbackEmail = defined('CONTACT_EMAIL') ? CONTACT_EMAIL : 'lcmovel.planejadocg@gmail.com';
        $sent = $this->sendViaPhpMail($to, $fallbackEmail, SITE_NAME, $subject, $plainText, $htmlBody);
        if ($sent) {
            Logger::info("Email enviado via mail() para: {$to}");
            return true;
        }

        Logger::error("Todos os métodos de envio falharam para: {$to}");
        return false;
    }

    private function getBrevoSenderEmail(): string {
        $email = $_SERVER['BREVO_SENDER_EMAIL'] ?? '';
        return $email !== '' ? $email : 'suporte@lcsolucoesemmoveis.com.br';
    }

    private function getBrevoSenderName(): string {
        $name = $_SERVER['BREVO_SENDER_NAME'] ?? '';
        return $name !== '' ? $name : SITE_NAME;
    }

    private function sendViaPhpMail(string $to, string $fromEmail, string $fromName, string $subject, string $plainText, string $htmlBody): bool {
        if (!ini_get('sendmail_from') && $fromEmail) {
            ini_set('sendmail_from', $fromEmail);
        }

        $boundary = md5(uniqid(time()));
        $headers  = "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <{$fromEmail}>\r\n";
        $headers .= "Reply-To: {$fromEmail}\r\n";
        $headers .= "Return-Path: {$fromEmail}\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "X-Mailer: LC-Solucoes-Mailer/1.0\r\n";
        $headers .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";

        $body  = "--{$boundary}\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";
        $body .= quoted_printable_encode($plainText) . "\r\n\r\n";
        $body .= "--{$boundary}\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";
        $body .= quoted_printable_encode($htmlBody) . "\r\n\r\n";
        $body .= "--{$boundary}--\r\n";

        $result = @mail($to, $subject, $body, $headers);
        if (!$result) {
            Logger::error("mail() retornou false para: {$to}");
        }
        return $result;
    }

    private function sendViaBrevoApi(string $to, string $fromEmail, string $fromName, string $subject, string $plainText, string $htmlBody): bool {
        $apiKey = $_SERVER['BREVO_API_KEY'] ?? '';
        if ($apiKey === '') {
            Logger::error("Brevo API: BREVO_API_KEY não configurada");
            return false;
        }

        $payload = json_encode([
            'sender' => ['name' => $fromName, 'email' => $fromEmail],
            'to' => [['email' => $to]],
            'subject' => $subject,
            'textContent' => $plainText,
            'htmlContent' => $htmlBody,
        ]);

        $maxAttempts = 3;
        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $ch = curl_init('https://api.brevo.com/v3/smtp/email');
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 20,
                CURLOPT_HTTPHEADER => [
                    'accept: application/json',
                    'content-type: application/json',
                    'api-key: ' . $apiKey,
                ],
                CURLOPT_POSTFIELDS => $payload,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if (!$error && $httpCode >= 200 && $httpCode < 300) {
                return true;
            }

            Logger::error("Brevo API erro na tentativa {$attempt}/{$maxAttempts}: " . ($error !== '' ? $error : "HTTP {$httpCode}: {$response}"));
            if ($attempt < $maxAttempts) {
                usleep($attempt * 500000);
            }
        }

        return false;
    }

    private function sendViaSmtp(string $to, string $fromEmail, string $fromName, string $subject, string $plainText, string $htmlBody): bool {
        $smtpUser = $_SERVER['BREVO_SMTP_USER'] ?? '';
        $smtpKey  = $_SERVER['BREVO_SMTP_PASS'] ?? '';
        if ($smtpUser === '' || $smtpKey === '') {
            Logger::error("SMTP: credenciais Brevo não configuradas (BREVO_SMTP_USER/BREVO_SMTP_PASS)");
            return false;
        }

        $socket = @fsockopen('smtp-relay.brevo.com', 587, $errno, $errstr, 10);
        if (!$socket) {
            Logger::error("SMTP connect failed: {$errstr} ({$errno})");
            return false;
        }

        stream_set_timeout($socket, 15);
        $smtpRead = function() use ($socket) {
            $data = '';
            while (!feof($socket)) {
                $line = fgets($socket, 515);
                $data .= $line;
                if (strlen($line) >= 4 && substr($line, 3, 1) === ' ') break;
            }
            return $data;
        };
        $smtpCmd = function($cmd) use ($socket, $smtpRead) {
            fwrite($socket, $cmd . "\r\n");
            return $smtpRead();
        };
        $expect = function($resp, string $code): bool {
            return strpos($resp, $code) !== false;
        };

        if (!$expect($smtpRead(), '220')) {
            Logger::error("SMTP: greeting inválido");
            fclose($socket);
            return false;
        }
        if (!$expect($smtpCmd('EHLO localhost'), '250')) {
            Logger::error("SMTP: EHLO falhou");
            fclose($socket);
            return false;
        }
        if (!$expect($smtpCmd('STARTTLS'), '220')) {
            Logger::error("SMTP: STARTTLS não suportado");
            fclose($socket);
            return false;
        }

        $crypto = @stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        if (!$crypto) {
            Logger::error("SMTP TLS handshake failed");
            fclose($socket);
            return false;
        }

        if (!$expect($smtpCmd('EHLO localhost'), '250')) {
            Logger::error("SMTP: EHLO pós-TLS falhou");
            fclose($socket);
            return false;
        }
        if (!$expect($smtpCmd('AUTH LOGIN'), '334')) {
            Logger::error("SMTP: AUTH LOGIN recusado");
            fclose($socket);
            return false;
        }
        if (!$expect($smtpCmd(base64_encode($smtpUser)), '334')) {
            Logger::error("SMTP: usuário rejeitado");
            fclose($socket);
            return false;
        }
        $authResp = $smtpCmd(base64_encode($smtpKey));
        if (!$expect($authResp, '235')) {
            Logger::error("SMTP auth failed: {$authResp}");
            fclose($socket);
            return false;
        }

        if (!$expect($smtpCmd("MAIL FROM:<{$fromEmail}>"), '250')) {
            Logger::error("SMTP: MAIL FROM rejeitado ({$fromEmail})");
            fclose($socket);
            return false;
        }
        if (!$expect($smtpCmd("RCPT TO:<{$to}>"), '250')) {
            Logger::error("SMTP: RCPT TO rejeitado ({$to})");
            fclose($socket);
            return false;
        }
        if (!$expect($smtpCmd('DATA'), '354')) {
            Logger::error("SMTP: DATA recusado");
            fclose($socket);
            return false;
        }

        $boundary = md5(uniqid(time()));
        $encodableSubject = '=?' . 'UTF-8' . '?B?' . base64_encode($subject) . '?=';
        $headers  = "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <{$fromEmail}>\r\n";
        $headers .= "Reply-To: {$fromEmail}\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "X-Mailer: LC-Solucoes-Mailer/1.0\r\n";
        $headers .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";

        $body  = "--{$boundary}\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";
        $body .= quoted_printable_encode($plainText) . "\r\n\r\n";
        $body .= "--{$boundary}\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";
        $body .= quoted_printable_encode($htmlBody) . "\r\n\r\n";
        $body .= "--{$boundary}--\r\n";

        $fullEmail = "Subject: {$encodableSubject}\r\n" . $headers . "\r\n" . $body . ".";

        fwrite($socket, $fullEmail . "\r\n");
        $resp = $smtpRead();
        fclose($socket);

        if (strpos($resp, '250') !== false) {
            return true;
        }
        Logger::error("SMTP DATA response: {$resp}");
        return false;
    }
}
