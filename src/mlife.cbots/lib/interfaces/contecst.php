<?
namespace Mlife\Cbots\Interfaces;

interface Contecst{
	
	public static function send($text); //возврат ответа от бота в текущем контексте
	public static function getCommandData($text); //определение команды по тексту
	
}