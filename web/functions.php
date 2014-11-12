<?php

$config['smtp_username'] = 'noreply@paperbod.com';  //Смените на имя своего почтового ящика.
$config['smtp_port']     = '25'; // Порт работы. Не меняйте, если не уверены.
$config['smtp_host']     = 'smtp.yandex.ru';  //сервер для отправки почты
$config['smtp_password'] = '**!**';  //Измените пароль
$config['smtp_debug']   = true;  //Если Вы хотите видеть сообщения ошибок, укажите true вместо false
$config['smtp_charset']  = 'UTF-8';  //кодировка сообщений. (или UTF-8, итд)
$config['smtp_from']     = 'PaperboD*'; //Ваше имя - или имя Вашего сайта. Будет показывать при прочтении в поле "От кого"

function smtpmail($mail_to, $subject, $message, $headers='') {
    global $config;
    $SEND = "Date: ".date("D, d M Y H:i:s") . " UT\r\n";
    $SEND .=    'Subject: =?'.$config['smtp_charset'].'?B?'.base64_encode($subject)."=?=\r\n";
    if ($headers) $SEND .= $headers."\r\n\r\n";
    else
    {
            $SEND .= "Reply-To: ".$config['smtp_username']."\r\n";
            $SEND .= "MIME-Version: 1.0\r\n";
            $SEND .= "Content-Type: text/plain; charset=\"".$config['smtp_charset']."\"\r\n";
            $SEND .= "Content-Transfer-Encoding: 8bit\r\n";
            $SEND .= "From: \"".$config['smtp_from']."\" <".$config['smtp_username'].">\r\n";
            $SEND .= "To: $mail_to <$mail_to>\r\n";
            $SEND .= "X-Priority: 3\r\n\r\n";
    }
    $SEND .=  $message."\r\n";
     if( !$socket = fsockopen($config['smtp_host'], $config['smtp_port'], $errno, $errstr, 30) ) {
        if ($config['smtp_debug']) {
            throw new Error("smtp",$errno."&lt;br&gt;".$errstr);
        }
        return false;
     }
 
    if (!server_parse($socket, "220", __LINE__)) return false;
 
    fputs($socket, "HELO " . $config['smtp_host'] . "\r\n");
    if (!server_parse($socket, "250", __LINE__)) {
        if ($config['smtp_debug']) {
            throw new Error("smtp",'Не могу отправить HELO!');
        }
        fclose($socket);
        return false;
    }
    fputs($socket, "AUTH LOGIN\r\n");
    if (!server_parse($socket, "334", __LINE__)) {
        if ($config['smtp_debug']) {
            throw new Error("smtp",'Не могу найти ответ на запрос авторизаци.');
        }
        fclose($socket);
        return false;
    }
    fputs($socket, base64_encode($config['smtp_username']) . "\r\n");
    if (!server_parse($socket, "334", __LINE__)) {
        if ($config['smtp_debug']) {
            throw new Error("smtp",'Логин авторизации не был принят сервером!');
        }
        fclose($socket);
        return false;
    }
    fputs($socket, base64_encode($config['smtp_password']) . "\r\n");
    if (!server_parse($socket, "235", __LINE__)) {
        if ($config['smtp_debug']) {
            throw new Error("smtp",'Пароль не был принят сервером как верный! Ошибка авторизации!');
        }
        fclose($socket);
        return false;
    }
    fputs($socket, "MAIL FROM: <".$config['smtp_username'].">\r\n");
    if (!server_parse($socket, "250", __LINE__)) {
        if ($config['smtp_debug']) {
            throw new Error("smtp",'Не могу отправить комманду MAIL FROM: ');
        }
        fclose($socket);
        return false;
    }
    fputs($socket, "RCPT TO: <" . $mail_to . ">\r\n");
 
    if (!server_parse($socket, "250", __LINE__)) {
        if ($config['smtp_debug']) {
            throw new Error("smtp",'Не могу отправить комманду RCPT TO: ');
        }
        fclose($socket);
        return false;
    }
    fputs($socket, "DATA\r\n");
 
    if (!server_parse($socket, "354", __LINE__)) {
        if ($config['smtp_debug']) {
            throw new Error("smtp",'Не могу отправить комманду DATA');
        }
        fclose($socket);
        return false;
    }
    fputs($socket, $SEND."\r\n.\r\n");
 
    if (!server_parse($socket, "250", __LINE__)) {
        if ($config['smtp_debug']) {
            throw new Error("smtp",'Не смог отправить тело письма. Письмо не было отправленно!');
        }
        fclose($socket);
        return false;
    }
    fputs($socket, "QUIT\r\n");
    fclose($socket);
    return TRUE;
}
 
function server_parse($socket, $response, $line = __LINE__) {
    global $config;
    while (@substr($server_response, 3, 1) != ' ') {
        if (!($server_response = fgets($socket, 256))) {
            if ($config['smtp_debug']) {
                throw new Error("smtp","Проблемы с отправкой почты! $response $line");
            }
            return false;
        }
    }
    if (!(substr($server_response, 0, 3) == $response)) {
        if ($config['smtp_debug']) {
            throw new Error("smtp","Проблемы с отправкой почты! $response $line");
        }
        return false;
    }
    return true;
}

// Отправка почты
function sendEMail($from,$to,$subject,$body) {
    $message = '<html><head><meta charset="utf-8"><title>'.$subject.'</title></head><body><p>'.$body.'</p></body></html>';

    /* Для отправки HTML-почты вы можете установить шапку Content-type. */
    $headers= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=utf-8\r\n";
    $headers .= "From: ".SITE_NAME." <".$from.">\r\n";

    //mail($to,$subject,$message,$headers);
    smtpmail($to,$subject,$message,$headers);
}



// Преобразуем JSON данные в массив Array
function json2array($json, $jsd=1) {
    if ($jsd == 1) {
        if (function_exists('json_decode')) {
            return json_decode($json);
        }
    } else {
        if (get_magic_quotes_gpc ()) {
            $json = stripslashes($json);
        }
        if( (substr($json, 0, 1)=='"') and (substr($json, -1)=='"') ) { $json = substr($json, 1, -1); } // Если есть <"> в начала и в конце, то очищаем
        $json = substr($json, 1, -1);

        $json = str_replace(array(":", "{", "[", "}", "]"), array("=>", "array(", "array(", ")", ")"), $json);

        @eval("\$json_array = array({$json});");

        return $json_array;
    }
}

// Преобразуем массив Array в JSON данные
function array2json($arr, $jse=1) {

    if ($jse == 1) {
        if (function_exists('json_encode')) {
            return json_encode($arr); //Lastest versions of PHP already has this functionality.
        }
    } else {
        $parts = array();
        $is_list = false;

        if (!is_array($arr))
            return;
        if (count($arr) < 1)
            return '{}';

        //Выясняем, данный  массив это числовой массив?!
        $keys = array_keys($arr);
        $max_length = count($arr) - 1;
        if (($keys[0] == 0) and ($keys[$max_length] == $max_length)) {//See if the first key is 0 and last key is length - 1
            $is_list = true;
            for ($i = 0; $i < count($keys); $i++) { //See if each key correspondes to its position
                if ($i != $keys[$i]) { //A key fails at position check.
                    $is_list = false; //It is an associative array.
                    break;
                }
            }
        }
        foreach ($arr as $key => $value) {
            if (is_array($value)) { //Custom handling for arrays
                if ($is_list)
                    $parts[] = array2json($value, $jse); /* :РЕКУРСИЯ: */
                else
                    $parts[] = '"' . $key . '":' . array2json($value, $jse); /* :РЕКУРСИЯ: */
            } else {
                $str = '';
                if (!$is_list) {
                    $str = '"' . $key . '":';
                }

                //Custom handling for multiple data types
                if (is_numeric($value)) {
                    $str .= $value; //Numbers
                } elseif ($value === false) {
                    $str .= 'false'; //The booleans
                } elseif ($value === true) {
                    $str .= 'true';
                } else {
                    $str .= '"' . addslashes($value) . '"'; //All other things
                    // Есть ли более типов данных мы должны быть в поиске? (объект?)
                }

                $parts[] = $str;
            }
        }
        $json = implode(',', $parts);

        if ($is_list) {
            return '[' . $json . ']'; //Вернуть как числовой  JSON
        }
        return '{' . $json . '}'; //Вернуть как ассоциативный JSON
    }
}

/*//array to string
function array2stringAL($array=array()) {

    $length = 0;
    foreach($array as $key => $value) {
        $keystring .= "$key ";
        $valuestring .= "$value ";
        $length++;
    }
    return array($length, $keystring, $valuestring);
}*/

//sting to array
function string2arrayAL($valuestring="") {
    $newarray=array();
    $array = explode(":", $valuestring);
    foreach($array as $key) {
        $arrayData = explode("=", $key);
        //$newarray[] = array($arrayData[0] => $arrayData[1]);
        $newarray[$arrayData[0]] =  $arrayData[1];
    }
    return $newarray;
}

// Преобразование номера в код
function n2c64($number) {
    $mb64c=base64_encode($number);
    $mb64c=str_replace("=","",$mb64c);

    $mb64c=base64_encode($mb64c);
    $mb64c=str_replace("=","",$mb64c);

    return $mb64c;
}
// Преобразование кода в номер
function c2n64($code) {
    $mb64c=base64_decode($code);
    $mb64c=base64_decode($mb64c);
    return $mb64c;
}

function GUID()
{
    if (function_exists('com_create_guid') === true) {
        return trim(com_create_guid(), '{}');
    }
    return sprintf('%04X%04X%04X%04X%04X%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}

?>