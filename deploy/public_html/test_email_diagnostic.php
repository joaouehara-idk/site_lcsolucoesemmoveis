<?php
/**
 * DIAGNOSTIC SCRIPT - Upload to server, test, then DELETE
 * Tests all 3 email methods: Brevo API, SMTP, PHP mail()
 */

$to = 'joaomigueluehara@gmail.com';
$subject = 'Teste de Email - LC Solucoes';
$siteName = 'LC Solucoes em Moveis';
$token = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

$plainText = "Olá, seu código de verificação é: {$token}";
$html = "<html><body><h2>Seu código: {$token}</h2></body></html>";

$results = [];

// ═══ TEST 1: Brevo API ═══
echo "<h3>1. Testando Brevo API (cURL)...</h3>";
$apiKey = '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';
$payload = json_encode([
    'sender' => ['name' => $siteName, 'email' => 'lcmovel.planejadocg@gmail.com'],
    'to' => [['email' => $to]],
    'subject' => $subject,
    'textContent' => $plainText,
    'htmlContent' => $html,
]);

$ch = curl_init('https://api.brevo.com/v3/smtp/email');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_HTTPHEADER => [
        'accept: application/json',
        'content-type: application/json',
        'api-key: ' . $apiKey,
    ],
    CURLOPT_POSTFIELDS => $payload,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    echo "<p style='color:red'>ERRO cURL: " . htmlspecialchars($curlError) . "</p>";
} else {
    echo "<p>HTTP Code: <strong>{$httpCode}</strong></p>";
    echo "<p>Response: <pre>" . htmlspecialchars($response) . "</pre></p>";
}

if ($httpCode >= 200 && $httpCode < 300) {
    echo "<p style='color:green'>✅ Brevo API FUNCIONA!</p>";
    $results['brevo_api'] = true;
} else {
    echo "<p style='color:red'>❌ Brevo API FALHOU</p>";
    $results['brevo_api'] = false;
}

echo "<hr>";

// ═══ TEST 2: SMTP Brevo ═══
echo "<h3>2. Testando SMTP Brevo...</h3>";
$host = 'smtp-relay.brevo.com';
$port = 587;
$username = 'adbb77001@smtp-brevo.com';
$password = 'xsmtpsib-f74c4aa4be270726aa5ca0198ccfe0cad56eb2d1d24d40874a37729f143b785b-NKRdBRJIpRqHku17';

$boundary = md5(uniqid(time()));
$headers  = "From: =?UTF-8?B?" . base64_encode($siteName) . "?= <lcmovel.planejadocg@gmail.com>\r\n";
$headers .= "To: <{$to}>\r\n";
$headers .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";
$headers .= "Date: " . date('r') . "\r\n";

$body  = "--{$boundary}\r\n";
$body .= "Content-Type: text/plain; charset=UTF-8\r\n";
$body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
$body .= $plainText . "\r\n\r\n";
$body .= "--{$boundary}\r\n";
$body .= "Content-Type: text/html; charset=UTF-8\r\n";
$body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
$body .= $html . "\r\n\r\n";
$body .= "--{$boundary}--\r\n";

$fullMessage = $headers . "\r\n" . $body;

$smtpOk = false;
$errno = 0;
$errstr = '';
$socket = @stream_socket_client("tcp://{$host}:{$port}", $errno, $errstr, 10);
if (!$socket) {
    echo "<p style='color:red'>ERRO SMTP connect: {$errstr}</p>";
} else {
    // Read banner
    $resp = @fgets($socket);
    echo "<p>Banner: " . htmlspecialchars(trim($resp)) . "</p>";
    
    // EHLO
    fwrite($socket, "EHLO lcsolucoesemmoveis.com.br\r\n");
    stream_set_timeout($socket, 5);
    $resp = '';
    while (($line = @fgets($socket)) !== false && strlen($line) >= 4 && $line[3] === ' ') { $resp .= $line; }
    $resp .= $line;
    echo "<p>EHLO: " . htmlspecialchars(trim($resp)) . "</p>";
    
    // STARTTLS
    fwrite($socket, "STARTTLS\r\n");
    $resp = @fgets($socket);
    echo "<p>STARTTLS: " . htmlspecialchars(trim($resp)) . "</p>";
    
    if (strpos($resp, '220') === 0) {
        stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT);
        
        // EHLO again
        fwrite($socket, "EHLO lcsolucoesemmoveis.com.br\r\n");
        stream_set_timeout($socket, 5);
        $resp = '';
        while (($line = @fgets($socket)) !== false && strlen($line) >= 4 && $line[3] === ' ') { $resp .= $line; }
        $resp .= $line;
        
        // AUTH LOGIN
        fwrite($socket, "AUTH LOGIN\r\n");
        $resp = @fgets($socket);
        echo "<p>AUTH: " . htmlspecialchars(trim($resp)) . "</p>";
        
        if (strpos($resp, '334') === 0) {
            // Username
            fwrite($socket, base64_encode($username) . "\r\n");
            $resp = @fgets($socket);
            echo "<p>USER: " . htmlspecialchars(trim($resp)) . "</p>";
            
            if (strpos($resp, '334') === 0) {
                // Password
                fwrite($socket, base64_encode($password) . "\r\n");
                $resp = @fgets($socket);
                echo "<p>PASS: " . htmlspecialchars(trim($resp)) . "</p>";
                
                if (strpos($resp, '235') === 0) {
                    echo "<p style='color:green'>✅ SMTP Brevo FUNCIONA!</p>";
                    $smtpOk = true;
                    $results['brevo_smtp'] = true;
                    
                    // Send email
                    fwrite($socket, "MAIL FROM:<lcmovel.planejadocg@gmail.com>\r\n");
                    @fgets($socket);
                    fwrite($socket, "RCPT TO:<{$to}>\r\n");
                    @fgets($socket);
                    fwrite($socket, "DATA\r\n");
                    @fgets($socket);
                    fwrite($socket, $fullMessage . "\r\n.\r\n");
                    @fgets($socket);
                    fwrite($socket, "QUIT\r\n");
                } else {
                    echo "<p style='color:red'>❌ SMTP auth password failed</p>";
                    $results['brevo_smtp'] = false;
                }
            } else {
                echo "<p style='color:red'>❌ SMTP auth username failed</p>";
                $results['brevo_smtp'] = false;
            }
        } else {
            echo "<p style='color:red'>❌ SMTP AUTH LOGIN failed</p>";
            $results['brevo_smtp'] = false;
        }
    } else {
        echo "<p style='color:red'>❌ STARTTLS failed</p>";
        $results['brevo_smtp'] = false;
    }
    fclose($socket);
}

if (!$smtpOk && !isset($results['brevo_smtp'])) {
    echo "<p style='color:red'>❌ SMTP Brevo FALHOU</p>";
    $results['brevo_smtp'] = false;
}

echo "<hr>";

// ═══ TEST 3: PHP mail() ═══
echo "<h3>3. Testando PHP mail()...</h3>";
$boundary2 = md5(uniqid(time()));
$headers2  = "From: =?UTF-8?B?" . base64_encode($siteName) . "?= <lcmovel.planejadocg@gmail.com>\r\n";
$headers2 .= "Reply-To: lcmovel.planejadocg@gmail.com\r\n";
$headers2 .= "MIME-Version: 1.0\r\n";
$headers2 .= "Content-Type: text/html; charset=UTF-8\r\n";

$body2 = "<h2>Teste LC Solucoes</h2><p>Seu código: <strong>{$token}</strong></p>";

$mailResult = @mail($to, $subject, $body2, $headers2);
if ($mailResult) {
    echo "<p style='color:green'>✅ PHP mail() retornou true!</p>";
    $results['php_mail'] = true;
} else {
    echo "<p style='color:red'>❌ PHP mail() retornou false</p>";
    $results['php_mail'] = false;
}

echo "<hr>";

// ═══ TEST 4: PHP info ═══
echo "<h3>4. Info do PHP</h3>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>cURL: " . (function_exists('curl_init') ? '✅ Disponível' : '❌ Não disponível') . "</p>";
echo "<p>allow_url_fopen: " . ini_get('allow_url_fopen') . "</p>";
echo "<p>sendmail_path: " . ini_get('sendmail_path') . "</p>";
echo "<p>mail function: " . (function_exists('mail') ? '✅ Existe' : '❌ Não existe') . "</p>";

// ═══ SUMMARY ═══
echo "<hr>";
echo "<h2>RESUMO</h2>";
foreach ($results as $method => $ok) {
    $status = $ok ? '✅ FUNCIONA' : '❌ FALHOU';
    echo "<p><strong>{$method}</strong>: {$status}</p>";
}

echo "<p style='color:orange;font-weight:bold;'>⚠️ DELETE ESTE ARQUIPO APÓS O TESTE!</p>";
