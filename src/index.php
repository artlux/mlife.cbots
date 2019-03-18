<?php
error_reporting(E_ALL & ~E_NOTICE);
header('Content-Type: text/html; charset=utf-8');
include(__DIR__ . '/mlife.cbots/include.php'); //����������� mlife.cbots
use \Mlife\Cbots\Main as App;

$time_start = microtime(true);

$app = App::getInstance(
	array(
		'transport'=>array(
			'CLASS'=>'\Mlife\Cbots\Transport\Alisa', //���������
			'CONFIG'=>array( //��� ��������� ��� ���������� (��������, ��� ��������� ��� ����� ���� botid � token)
				'TIME_START'=>$time_start //����� ������ unixtime
			)
		),
		'session'=>array(
			'CLASS'=>'\Mlife\Cbots\Usersession', //���� ��� ������ � ����������������� �������, �������� �� �����
			'SESSION_EXPIRED'=>24*60*60*28, //����� �������� ���������������� ������
			'SESSION_DIR'=>__DIR__ . '/sessions', //���������� �������� ������
			'CONTECST'=>'\Mlife\Cbots\Contecst\Bot\Start', //��������� ��������
			'CONTECST_GLOBAL'=>'\Mlife\Cbots\Contecst\Bot\Globalc', //���������� ��������
		),
		'log' => array(
			'CLASS' => '\Mlife\Cbots\Log', //����� ��� �����������
			'FILE_NAME' => 'log_txt.txt', //���� � ����� ����, �������� ������, ���� ����� ��������� ���
			'FILE_DIR' => __DIR__ //���������� ��� ������ ����� ���� (������ ���� ������� � ����� ����� �� ������)
		)
	)
);

$app->send();
$app->getSession()->save(); //��������� ������