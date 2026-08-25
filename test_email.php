<?php
/**
 * TESTE DE ENVIO DE EMAIL - DELETE DEPOIS DO TESTE
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Teste de Envio de Email</h2>";

// Test 1: Check if PHP mail() is available
echo "<h3>1. Verificando mail():</h3>";
echo "mail() function exists: " . (function_exists('mail') ? 'SIM' : 'NAO') . "<br>";

// Test 2: Check if cURL is available
echo "<h3>2. Verificando cURL:</h3>";
echo "cURL extension: " . (extension_loaded('curl') ? 'SIM' : 'NAO') . "<br>";

// Test 3: Try sending via Brevo API
echo "<h3>3. Testando Brevo API:</h3>";
$apiKey = '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';
$to = 'joaomigueluehara@gmail.com';
$fromEmail = 'lcmovel.planejadocg@gmail.com';
$fromName = 'LC Soluções em Móveis';
$subject = 'TESTE - Código de Verificação';
$token = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
$html = "<h1>Seu código: {$token}</h1>";
$plainText = "Seu código de verificação é: {$token}";

$payload = json_encode([
    'sender' => ['name' => $fromName, 'email' => $fromEmail],
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

echo "HTTP Code: {$httpCode}<br>";
echo "cURL Error: " . ($curlError ?: 'Nenhum') . "<br>";
echo "Response: " . htmlspecialchars(substr($response, 0, 500)) . "<br>";

if ($httpCode >= 200 && $httpCode < 300) {
    echo "<b style='color:green'>BREVO: EMAIL ENVIADO COM SUCESSO!</b><br>";
} else {
    echo "<b style='color:red'>BREVO: FALHOU</b><br>";
    
    // Test 4: Try PHP mail() as fallback
    echo "<h3>4. Testando mail() fallback:</h3>";
    $boundary = md5(uniqid(time()));
    $headers  = "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <{$fromEmail}>\r\n";
    $headers .= "Reply-To: {$fromEmail}\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";
    
    $body  = "--{$boundary}\r\n";
    $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $body .= $plainText . "\r\n\r\n";
    $body .= "--{$boundary}\r\n";
    $body .= "Content-Type: text/html; charset=UTF-8\r\n";
    $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $body .= $html . "\r\n\r\n";
    $body .= "--{$boundary}--\r\n";
    
    $result = @mail($to, $subject, $body, $headers);
    echo "mail() result: " . ($result ? 'ENVIADO' : 'FALHOU') . "<br>";
    
    if ($result) {
        echo "<b style='color:green'>PHP MAIL: EMAIL ENVIADO! Verifique sua caixa de entrada e spam.</b><br>";
    } else {
        echo "<b style='color:red'>PHP MAIL: TAMBÉM FALHOU</b><br>";
    }
}

echo "<br><b>Código gerado para teste: {$token}</b>";
echo "<br><hr><p style='color:red'>DELETE ESTE ARQUIVO DEPOIS DO TESTE!</p>";
echo "<br><a href='/test_email.php'>Clique para re-testar</a>";
