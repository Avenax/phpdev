<?php
session_start();
require_once('./components/Send.php');
if (filter_has_var(INPUT_POST, 'recordToDelete')) {
    Send::recordToDelete(intval($_POST['recordToDelete']));
} else if (filter_has_var(INPUT_POST, 'text')) {
    Send::contentText($_POST['text']);
    /**
     * Или как нужно было
     * SmsSender::createNew($_REQUEST['text']);
    }
     */
} else if (filter_has_var(INPUT_POST, 'show')) {

    $_SESSION['show'] = intval($_POST['show']);
    header('Location: /');
    exit();
}
Send::getMessage();