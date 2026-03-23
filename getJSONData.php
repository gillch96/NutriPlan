<?php
// getJSONData.php

// Your secret values — only visible on the server
$BIN_ID = '69867455d0ea881f40a671c3';
$API_KEY = '$2a$10$CU4Q9gHz0jVdNEB4KLzoS.rtWPjDgU8WJMcexyvlHviXT9pJTuu2O';

// JSONBin URL (latest version of the bin)
$url = "https://api.jsonbin.io/v3/b/$BIN_ID/latest";

// Initialize cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
	"X-Master-Key: $API_KEY",
	"Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// SSL fix (Could not get the code to work otherwise)
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

// Execute request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Forward status & data back to the browser
http_response_code($httpCode);
header('Content-Type: application/json');

if ($httpCode >= 200 && $httpCode < 300 && $response !== false) {
	// Decode JSONBin’s response
	$decoded = json_decode($response, true);

	// If it has a "record" field, that’s your contacts array
	if (isset($decoded['record'])) {
		echo json_encode($decoded['record']);   // just the array
	} else {
		// Fallback: maybe the bin already stores a plain array
		echo $response;
	}
} else {
	echo json_encode([
		'error' => true,
		'message' => 'Failed to fetch JSON from JSONBin',
		'status' => $httpCode
	]);
}
