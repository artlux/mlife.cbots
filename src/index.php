<?php
error_reporting(E_ALL & ~E_NOTICE);
header('Content-Type: text/html; charset=utf-8');
include(__DIR__ . '/mlife.cbots/include.php'); //подключение mlife.cbots
use \Mlife\Cbots\Main as App;

$time_start = microtime(true);

$app = App::getInstance(
	array(
		'transport'=>array(
			'CLASS'=>'\Mlife\Cbots\Transport\Alisa', //транспорт
			'CONFIG'=>array( //доп парвметры для транспорта (например, для телеграмм тут может быть botid и token)
				'TIME_START'=>$time_start //время старта unixtime
			)
		),
		'session'=>array(
			'CLASS'=>'\Mlife\Cbots\Usersession', //клас для работы с пользовательскими данными, хранение на диске
			'SESSION_EXPIRED'=>24*60*60*28, //время хранения пользовательских данных
			'SESSION_DIR'=>__DIR__ . '/sessions', //директория хранения сессий
			'CONTECST'=>'\Mlife\Cbots\Contecst\Bot\Start', //стартовый контекст
			'CONTECST_GLOBAL'=>'\Mlife\Cbots\Contecst\Bot\Globalc', //глобальный контекст
		),
		'log' => array(
			'CLASS' => '\Mlife\Cbots\Log', //класс для логирования
			'FILE_NAME' => 'log_txt.txt', //путь к файлу лога, оставить пустым, если нужно отключить лог
			'FILE_DIR' => __DIR__ //директория для записи файла лога (должна быть создана и иметь права на запись)
		)
	)
);

$app->send();
$app->getSession()->save(); //сохраняем сессию