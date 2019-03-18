<?
namespace Mlife\Cbots\Contecst\Bot;

use Mlife\Cbots\Helper as Helper;
use Mlife\Cbots\Message as Mess;
use Mlife\Cbots\Button as Button;

class Start implements \Mlife\Cbots\Interfaces\Contecst{
	
	public static function send($text=''){
		
		$session = \Mlife\Cbots\Main::getInstance()->getSession();
		$command = self::getCommandData($text);
		
		if($command == 'start'){
			$globalContecst = $session->getParam('CONTECST_GLOBAL');
			$mess = $globalContecst::send($command);
		}
		
		//вывод сообщения об ошибке распознавания команды
		if(!is_object($mess)){
			
			//массив сообщений об ошибке 
			$errText = Helper::getTextErrorDefault(); 
			
			//выбора случайного сообщения об ошибке
			$rand_key = array_rand($errText);
			
			$mess = new Mess(
				$errText[$rand_key],
				'', //tts необязательно, возьмет из text
				'last' //last - кнопки из пред. сообщения
			);
			
		}
		
		//запись последнего сообщения
		if($mess) \Mlife\Cbots\Main::getInstance()->setLastMessage($mess);
		
		return $mess;
		
	}
	
	public static function getCommandData($text=''){
		
		$session = \Mlife\Cbots\Main::getInstance()->getSession();
		$lastCommand = $session->getParam('LAST_COMMAND');
		$lastData = $session->getParam('LAST_DATA');
		
		$text = Helper::toLowerText($text);
		
		$command = '';
		
		if($text == 'выход в меню' || $text == 'выход'){
			$session->setParam('LAST_COMMAND','',true);
			$command = 'start';
		}
		
		return $command;
		
	}
	
}