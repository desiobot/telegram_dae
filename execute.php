<?php

function sendImage($idChat, $linkImage) {
	$bot_url    = "https://api.telegram.org/bot" . getenv('token') . "/";
	$url        = $bot_url . "sendPhoto?chat_id=" . $chat_id ;
	
	$post_fields = array('chat_id'   => $chat_id,
	    'photo'     => new CURLFile($linkImage)
	);
	
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    "Content-Type:multipart/form-data"
	));
	curl_setopt($ch, CURLOPT_URL, $url); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields); 
	$output = curl_exec($ch);	
}




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
		$response = "Creato per il Comune di Desio";
	}
	elseif($text=="demo")
	{
		$response = "Messaggio DEMO";
		sendImage($chatId,"https://maps.googleapis.com/maps/api/staticmap?zoom=13&size=300x300&maptype=roadmap&markers=color:red%7Clabel:A%7C45.626687,9.205079&markers=color:red%7Clabel:B%7C45.618636,9.210455&markers=color:red%7Clabel:C%7C45.618451,9.193961&markers=color:green%7Clabel:%7C45.617213,9.207562")
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
	$url="http://www.webconsole.it/infocitta_api/dae?LAT=$latitude&LON=$longitude";
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
