<?php
// Replace with your actual token
$token = '8036575496:AAFtaYbG65gKDAFPV7BBcDNs9vmeFrB4nk0'; 

echo "Testing connection to Telegram...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot{$token}/getMe");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Trying insecure mode

$result = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ FAILED: " . $error . "\n";
} else {
    echo "✅ SUCCESS! Telegram replied: " . $result . "\n";
}