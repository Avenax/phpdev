<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
session_start();

require_once('./config.php');
require_once('./mysql.class.php');
set_time_limit(0);
header('Content-Type: text/html; charset=utf-8');

class SmsSender {
    const TABLE = 'sms_list_tz';

    static function createNew($text) {
        $insert = array('TEXT' => self::clearText($text));
        SQLInsert(self::TABLE, $insert);
    }

    static function clearText($text) {
        $d = explode('©', $text);
        $text = $d[0];

        $text = str_replace(array("\r\n"), "", $text);
        $text = trim($text);

        return $text;
    }

    static function notify() {
        $sql = 'SELECT * FROM `' . self::TABLE . '` WHERE IS_SENDED = 0 ORDER BY RAND()';
        $exists = SQLSelectOne($sql);

        if (!$exists) {
            self::sendEmail('SMS NOT FOUND');
            return false;
        }
        self::sendSms($exists['TEXT']);
        self::sendEmail($exists['TEXT']);

        $exists['IS_SENDED'] = 1;
        SQLUpdate(self::TABLE, $exists);

        return true;
    }

    static function sendEmail($text) {
        echo 'Email sended: ' . $text . '<br>';
        return true;
    }

    public static function sendSms($text) {
        echo 'Sms sended: ' . $text . '<br>';
        return true;
    }

    public static function translitIt($str) {
        $tr = array(
            "А" => "A",
            "Б" => "B",
            "В" => "V",
            "Г" => "G",
            "Д" => "D",
            "Е" => "E",
            "Ж" => "J",
            "З" => "Z",
            "И" => "I",
            "Й" => "Y",
            "К" => "K",
            "Л" => "L",
            "М" => "M",
            "Н" => "N",
            "О" => "O",
            "П" => "P",
            "Р" => "R",
            "С" => "S",
            "Т" => "T",
            "У" => "U",
            "Ф" => "F",
            "Х" => "H",
            "Ц" => "TS",
            "Ч" => "CH",
            "Ш" => "SH",
            "Щ" => "SCH",
            "Ъ" => "",
            "Ы" => "YI",
            "Ь" => "",
            "Э" => "E",
            "Ю" => "YU",
            "Я" => "YA",
            "а" => "a",
            "б" => "b",
            "в" => "v",
            "г" => "g",
            "д" => "d",
            "е" => "e",
            "ж" => "j",
            "з" => "z",
            "и" => "i",
            "й" => "y",
            "к" => "k",
            "л" => "l",
            "м" => "m",
            "н" => "n",
            "о" => "o",
            "п" => "p",
            "р" => "r",
            "с" => "s",
            "т" => "t",
            "у" => "u",
            "ф" => "f",
            "х" => "h",
            "ц" => "ts",
            "ч" => "ch",
            "ш" => "sh",
            "щ" => "sch",
            "ъ" => "y",
            "ы" => "yi",
            "ь" => "",
            "э" => "e",
            "ю" => "yu",
            "я" => "ya"
        );
        return strtr($str, $tr);
    }
}

?>

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; Charset=UTF-8">
    <link href="/style/style.css" rel="stylesheet" type="text/css">
    <title>Тестовое задание</title>
    <script src="/js/jquery.min.js"></script>
    <script src="/js/ajax.js"></script>
</head>
<body>
<div class="loading-div"><img src="loader.gif" width="32"></div>
<div id="content"></div>

<form id="myForm">
    <label>
        <textarea rows="5" cols="40" id="text"></textarea>
    </label>
    <br/>
    <input type="submit" value="Отправить">
</form>


<form method="post" action="run.php">
    <label>
        Вывод записей: <br/>
        <input name="show" type="number" value="<?= isset($_SESSION['show']) ? intval($_SESSION['show']) : 0; ?>">
    </label>
    <br/>
    <input type="submit" value="Сохранить">
</form>


</body>
</html>
