<?php
$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!$update) {
  exit;
}

function response_by_handler($key, $body, $request_data) {
	switch ($key) {
		case "text":
			$text = trim($body);
			$text = strtolower($text);
			if (strpos($text, "/start") === 0 || $text == "ciao") {
				return "Ciao {$request_data["chat"]["first_name"]}, benvenuto nel servizio di interrogazione posizione DAE. Invia la tua posizione!";
			} elseif ($text == "info") {
				return "Creato per il Comune di Desio";
			} else {
				return "Comando non valido! Invia  la tua posizione per usufruire del servizio";
			}
			break;
		case "location":
				$latitude = $body["latitude"] || "";
				$longitude = $body["longitude"] || "";
				$url = "http://www.webconsole.it/infocitta_api/dae?LAT=$latitude&LON=$longitude";
				return file_get_contents($url);
			break;
		default:
			return "Ho ricevuto un messaggio $key che verrà ignorato.";
			break;
	}
}

function build_response($message, request_data) {
	static $allowed_keys = ["text", "audio", "document", "photo", "sticker", "video", "voice", "contact", "location", "venue"];

	$filtered_message = array_filter($message, function($value, $key) use ($allowed_keys) {
		return in_array($key, $allowed_keys);
	}, ARRAY_FILTER_USE_BOTH);

	$keys = array_keys($filtered_message);
	if (count($keys) == 0) {
		return "Ho ricevuto un messaggio ?";
	} else {
		$key = $keys[0];
		$body = $filtered_message[$key];
		return response_by_handler($key, $body, $request_data);
	}
}

function build_request_data($message) {
  return [
    "message_id" => isset($message["message_id"]) ? $message["message_id"] : "",
    "chat" => [
      "id" => isset($message["chat"]["id"]) ? $message["chat"]["id"] : "",
      "first_name" => isset($message["chat"]["first_name"]) ? $message["chat"]["first_name"] : "",
      "last_name" => isset($message["chat"]["last_name"]) ? $message["chat"]["last_name"] : "",
      "username" => isset($message["chat"]["username"]) ? $message["chat"]["username"] : "",
    ],
    "date" => isset($message["date"]) ? $message["date"] : "",
  ];
}

$request_data = build_request_data($message);
$response = build_response($message, $request_data);
header("Content-Type: application/json");
$parameters = [
  "chat_id" => $request_data["chat"]["id"],
  "text" => $response,
  "method" => "sendMessage"
]
echo json_encode($parameters);
