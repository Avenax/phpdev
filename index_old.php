<?php

require_once('./config.php');
require_once('./mysql.class.php');

set_time_limit(0);

// create new object
$db = new mysql(DB_HOST, '', DB_USER, DB_PASSWORD, DB_NAME); // connecting to database
// encoding
header('Content-Type: text/html; charset=utf-8');

class SmsSender
{
    // table name
    const TABLE = 'sms_list_tz';

    /**
     * new array, split string, sql query insert
     * @param $text
     */
    static function createNew($text)
    {
        $insert = array('TEXT' => self::clearText($text));
        SQLInsert(self::TABLE, $insert);
    }

    /**
     * split string
     * replace \r\n to null
     * @param $text
     * @return mixed|string
     */
    static function clearText($text)
    {
        $d = explode('©', $text);
        $text = $d[0];

        $text = str_replace(array("\r\n"), "", $text);
        $text = trim($text);

        return $text;
    }

    /**
     * select 1 random line
     * etc ..
     * @return bool
     */
    static function notify()
    {
        // такую выборку нельзя делать (!)
        // что будет, если в бд будет 100500 записей, где IS_SENDED = false
        $sql = 'SELECT * FROM `' . self::TABLE . '` WHERE IS_SENDED = 0 ORDER BY RAND()';
        // mysql query fetch, return array sql query
        $exists = SQLSelectOne($sql);

        // is not return sql query->fetch
        // return false
        if (!$exists) {
            self::sendEmail('SMS NOT FOUND');
            return false;
        }
        // else good,  send sms && email
        self::sendSms($exists['TEXT']);
        self::sendEmail($exists['TEXT']);

        // edit status (IS_SENDED)
        $exists['IS_SENDED'] = 1;
        // update status (IS_SENDED)
        SQLUpdate(self::TABLE, $exists);

        return true;
    }

    /**
     * return info
     * @param $text
     * @return bool
     */
    static function sendEmail($text)
    {
        echo 'Email sended: ' . $text . '<br>';
        return true;
    }

    /**
     * return info
     * @param $text
     * @return bool
     */
    public static function sendSms($text)
    {
        echo 'Sms sended: ' . $text . '<br>';
        return true;
    }

    /**
     * from => to
     * @param $str
     * @return string
     */
    public static function translitIt($str)
    {
        $tr = array(
            "А" => "A", "Б" => "B", "В" => "V", "Г" => "G",
            "Д" => "D", "Е" => "E", "Ж" => "J", "З" => "Z", "И" => "I",
            "Й" => "Y", "К" => "K", "Л" => "L", "М" => "M", "Н" => "N",
            "О" => "O", "П" => "P", "Р" => "R", "С" => "S", "Т" => "T",
            "У" => "U", "Ф" => "F", "Х" => "H", "Ц" => "TS", "Ч" => "CH",
            "Ш" => "SH", "Щ" => "SCH", "Ъ" => "", "Ы" => "YI", "Ь" => "",
            "Э" => "E", "Ю" => "YU", "Я" => "YA", "а" => "a", "б" => "b",
            "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ж" => "j",
            "з" => "z", "и" => "i", "й" => "y", "к" => "k", "л" => "l",
            "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r",
            "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h",
            "ц" => "ts", "ч" => "ch", "ш" => "sh", "щ" => "sch", "ъ" => "y",
            "ы" => "yi", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ya"
        );
        return strtr($str, $tr);
    }
}

?>

<html>
<head>
    <title>Тестовое задание</title>
</head>
<body>
<?
// Форма не когда не будет отображена, т.к. $_REQUEST['addsms'] мы получаем от формы, которая скрыта за этим условием
if ($_REQUEST['addsms']) {
    if ($_REQUEST['save']) {
        // new message
        SmsSender::createNew($_REQUEST['TEXT']);
    }

    // html form
    echo '<form method="post"><textarea name="TEXT" rows="5" cols="40"></textarea><br><input type="hidden" name="save" value="1"><input type="submit" name="addsms" value="Отправить"></form>';
} else {
    // эта часть когда, будет постоянно выполнятся
    // потому что условие выше, некогда не будет выполнено
    $file = './last.txt';

    // found file?
    if (is_file($file)) {
        // load file
        $date = file_get_contents($file);

        // unix time
        $date = strtotime($date);

        // от текущего времени отнимаем время которое взяли из фала, и если оно меньше, то прекращаем работу скрипта
        if (time() - $date < 3600 * 24 * DAYS_COUNT) exit;
        if (date('G') < 9 || date('G') > 12) exit; // send sms from 9 to 12
        if (in_array(date('N'), array(6, 7))) exit; // send sms from monday to friday

        // del file
        unlink($file);
    }

    $need = rand(0, 1);
    // $need = 1
    if ($need) {
        // select random, etc ..
        if (SmsSender::notify()) {
            // write to file
            file_put_contents($file, date('Y-m-d H:i:s'));
        }
    }
}
?>
</body>
</html>
