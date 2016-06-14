<?php
$content = file_get_contents("php://input");
$update = json_decode($content, true);
if(!$update)
{
  exit;
}
$message = isset($update['message']) ? $update['message'] : "";
$messageId = isset($message['message_id']) ? $message['message_id'] : "";
$chatId = isset($message['chat']['id']) ? $message['chat']['id'] : "";
$firstname = isset($message['chat']['first_name']) ? $message['chat']['first_name'] : "";
$lastname = isset($message['chat']['last_name']) ? $message['chat']['last_name'] : "";
$username = isset($message['chat']['username']) ? $message['chat']['username'] : "";
$date = isset($message['date']) ? $message['date'] : "";
$latitude= isset($message['location']['latitude']) ? $message['location']['latitude'] : "";
$longitude= isset($message['location']['longitude']) ? $message['location']['longitude'] : "";
$response = '';
if(isset($message['text']))
{
//	$response = "Ho ricevuto il seguente messaggio di testo: " . $message['text'];
	$text = isset($message['text']) ? $message['text'] : "";
	$text = trim($text);
	$text = strtolower($text);
	if(strpos($text, "/start") === 0 || $text=="ciao")
	{
		$response = "Ciao $firstname, benvenuto nel servizio di interrogazione posizione DAE. Invia la tua posizione!";
	}
	elseif($text=="info")
	{
		$response = "Creato per il Comune di Desio\r\nVersione 1.0";
	}
	elseif($text=="chatid")
	{
		$response = $chatId;
	}	
	else
	{
		$response = "Comando non valido! Invia  la tua posizione per usufruire del servizio";

	}
}
elseif(isset($message['audio']))
{
	$response = "Ho ricevuto un messaggio audio che verrà ignorato";
}
elseif(isset($message['document']))
{
	$response = "Ho ricevuto un messaggio documento che verrà ignorato";
}
elseif(isset($message['photo']))
{
	$response = "Ho ricevuto un messaggio foto che verrà ignorato";
}
elseif(isset($message['sticker']))
{
	$response = "Ho ricevuto un messaggio sticker che verrà ignorato";
}
elseif(isset($message['video']))
{
	$response = "Ho ricevuto un messaggio video che verrà ignorato";
}
elseif(isset($message['voice']))
{
	$response = "Ho ricevuto un messaggio vocale che verrà ignorato";
}
elseif(isset($message['contact']))
{
	$response = "Ho ricevuto un messaggio contatto che verrà ignorato";
}
elseif(isset($message['location']))
{
//	$response = "Ho ricevuto un messaggio location $latitude $longitude";
//	$url="http://www.webconsole.it/infocitta_api/dae?LAT=45.619502&LON=9.197949";
	$url="http://www.webconsole.it/infocitta_api/dae?LAT=$latitude&LON=$longitude&CHATID=$chatId";
	$response = file_get_contents($url);	
}
elseif(isset($message['venue']))
{
	$response = "Ho ricevuto un messaggio venue che verrà ignorato";
}
else
{
	$response = "Ho ricevuto un messaggio ?";
}
header("Content-Type: application/json");
$parameters = array('chat_id' => $chatId, "text" => $response);
$parameters["method"] = "sendMessage";
echo json_encode($parameters);
