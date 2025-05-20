<?php
/**
 * Устанавливаем уровень ошибок:
 */
// error_reporting(0);
// ini_set('display_errors', 'On');
// error_reporting(E_ALL | E_STRICT);
error_reporting(E_ERROR | E_WARNING | E_PARSE);

/**
 * Запускаем фреймворк:
 */
require_once '../app/bootstrap.php';

/**
 * Заглушка:
 */
//$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];

/**
 * Запускаем приложение:
 */
$app = Application::getInstance();
$app -> run();
