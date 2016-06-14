
<?php

function random_string($length) {
    $key = '';
    $keys = array_merge(range(0, 9), range('a', 'z'));

    for ($i = 0; $i < $length; $i++) {
        $key .= $keys[array_rand($keys)];
    }

    return $key;
}

$name=random_string(10) . ".png";
echo $name . "\r\n";

$source = $_GET['link'];
echo $source . "\r\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $source);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSLVERSION,3);
$data = curl_exec ($ch);
$error = curl_error($ch); 
curl_close ($ch);

$destination = $name;
$file = fopen($destination, "w+");
fputs($file, $data);
fclose($file);
echo "Salva file" . "\r\n";

$bot_url    = "https://api.telegram.org/bot" . getenv('token') . "/";
$url        = $bot_url . "sendPhoto?chat_id=" . $_GET['chatid'];

$post_fields = array('chat_id'   => $chat_id,
    'photo'     => new CURLFile(realpath($name))
);


$ch = curl_init(); 
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Content-Type:multipart/form-data"
));
curl_setopt($ch, CURLOPT_URL, $url); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields); 
$output = curl_exec($ch);
echo "Invia" . "\r\n";
unlink($name);
echo "Elimina" . "\r\n";
