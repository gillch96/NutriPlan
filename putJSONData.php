<?php
// saveContacts.php
//This script updates the database with changes to your saved data, such as saving a new recipe

$BIN_ID  = '69867455d0ea881f40a671c3';
$API_KEY = '$2a$10$CU4Q9gHz0jVdNEB4KLzoS.rtWPjDgU8WJMcexyvlHviXT9pJTuu2O';

// 1) Read JSON body from the request
$incomingJson = file_get_contents('php://input');

if (!$incomingJson) {
	http_response_code(400);
	header('Content-Type: application/json');
	echo json_encode([
		'error' => true,
		'message' => 'No JSON body received'
	]);
	exit;
}

// Optional: validate JSON
$decoded = json_decode($incomingJson, true);
if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
	http_response_code(400);
	header('Content-Type: application/json');
	echo json_encode([
		'error' => true,
		'message' => 'Invalid JSON received'
	]);
	exit;
}

// 2) PUT the entire contacts array into JSONBin
$url = "https://api.jsonbin.io/v3/b/$BIN_ID";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
curl_setopt($ch, CURLOPT_POSTFIELDS, $incomingJson);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
	"X-Master-Key: $API_KEY",
	"Content-Type: application/json",
	"Content-Length: " . strlen($incomingJson)
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// SSL fix (Could not get the code to work otherwise)
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// 3) Return a simple status
header('Content-Type: application/json');
http_response_code($httpCode);

if ($httpCode >= 200 && $httpCode < 300 && $response !== false) {
	echo json_encode([
		'ok' => true,
		'message' => 'Contacts saved successfully'
	]);
} else {
	echo json_encode([
		'error' => true,
		'message' => 'Failed to save contacts to JSONBin',
		'status' => $httpCode
	]);
}
