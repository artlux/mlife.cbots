<?
namespace Mlife\Cbots\Interfaces;

interface Transport{
	
	public function getName(); //название транспорта
	public function getConfigSession(); //конфигурация сессии
	public function getConfigUser(); //конфигурация диалога по умолчанию
	//public function prepare_request($data); private подготовка данных
	public function request($data); //обработка данных
	
	//public function prepareData($data); private подготовка сообщения
	public function sendMessage($mess); //отправка сообщения
	
	public function log($data, $title); //логирование instance Mlife\Cbots\Main
	public function getSession(); //сессия пользователя instance Mlife\Cbots\Main
	
}