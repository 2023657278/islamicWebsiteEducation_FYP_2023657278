<?php
$token = '8036575496:AAFtaYbG65gKDAFPV7BBcDNs9vmeFrB4nk0';

// Check bot info
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot{$token}/getMe");
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$data = json_decode($response, true);

echo "Bot Status:\n";
echo json_encode($data, JSON_PRETTY_PRINT);
?>
