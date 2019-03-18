<?
namespace Mlife\Cbots\Contecst\Bot;

use Mlife\Cbots\Helper as Helper;
use Mlife\Cbots\Message as Mess;
use Mlife\Cbots\Button as Button;

class Globalc implements \Mlife\Cbots\Interfaces\Contecst{
	
	public static function send($text=''){
		
		$command = self::getCommandData($text);
		
		if($command) {
			self::log(array($command,$text),'Global command');
			
			$session = \Mlife\Cbots\Main::getInstance()->getSession();
			
			if($command == 'start') {
				
				$lastData = $session->getParam('LAST_MESS_DATA');
				if($lastData['text']){
					$mess = new Mess(
						'Продолжим диалог? '."\n".'Для выхода в главное меню отправьте команду: старт. '."\n".
						'Нужна помощь? Отправьте команду: помощь.',
						'',
						array(
							array('hide'=>true, 'title_button'=> 'помощь', 'payload'=>'помощь'),
							//array('hide'=>true, 'title_button'=> 'старт', 'payload'=>'старт'),
						)
					);
				}else{
					$mess = new Mess(
					'Привет я бот. '."\n"
					.'Нужна помощь? Отправьте команду: помощь.'
					.'Для выхода в главное меню можно отправьте команду: старт.'."\n",
						'',
						array(
							array('hide'=>true, 'title_button'=> 'помощь', 'payload'=>'помощь'),
							//array('hide'=>true, 'title_button'=> 'старт', 'payload'=>'старт'),
						)
					);
				}
				$session->setContecst('\Mlife\Cbots\Contecst\Bot\Start',true);
				
			}elseif($command == 'help'){
				
				$but = array(
							//array('hide'=>true, 'title_button'=> 'помощь', 'payload'=>'помощь'),
							array('hide'=>true, 'title_button'=> 'старт', 'payload'=>'старт'),
						);
				
				$mess = new Mess(
					'Напишите что нибудь боту.'."\n"
					.'Для выхода в главное меню можно воспользоваться командой: старт.'."\n",
						'',
					$but	
				);
				
			}elseif($command == 'settings'){
				$mess = new Mess(
					'Пока программист не сделал настроек для бота',
						'',
						'last'
				);
			}elseif($command == 'thanks'){
				$mess = array(
					new Mess(
						'Не за что, была рада помочь.',
						'',
						'last'
					),
					new Mess(
						'Обращайтесь еще, была рада помочь.',
						'',
						'last'
					),
					new Mess(
						'Хоть я и робот, но мне приятно ваше внимание.',
						'',
						'last'
					),
					new Mess(
						'Мне очень приятно. Я люблю помогать людям.',
						'',
						'last'
					),
				);
			}elseif($command == 'obida'){
				$mess = array(
					new Mess(
						'Давайте будем немного вежливее.',
						'',
						'last'
					),
					new Mess(
						'Вы точно говорили эту фразу мне?',
						'',
						'last'
					),
					new Mess(
						'Я ведь не зеркало и не всегда понимаю такие фразы.',
						'',
						'last'
					),
				);
			}elseif($command == 'remesage'){
				$lastData = $session->getParam('LAST_MESS_DATA');
				if($lastData['text']){
					$mess = new Mess(
						$lastData['text'], $lastData['tts'], $lastData['buttons']
					);
					if($lastData['type']=='BigImage'){
						$mess->setCustom(array(
							'type'=>$lastData['type'], 
							'image_id'=>$lastData['image_id'],
							'image_description'=>$lastData['image_description'],
							'image_title'=>$lastData['image_title']
						));
					}
					return $mess;
				}
			}
			
			if(is_array($mess)) {
				$rand_key = array_rand($mess);
				$mess = $mess[$rand_key];
			}
			
		}
		
		if($mess) \Mlife\Cbots\Main::getInstance()->setLastMessage($mess);
		
		if($mess) return $mess;
		
		return false;
		
	}
	
	public static function getCommandData($text=''){
		
		$text = Helper::toLowerText($text);
		$session = \Mlife\Cbots\Main::getInstance()->getSession();
		
		$command = '';
		if($text == '/help' || $text == 'help' || $text == 'помощь' || strpos($text,'что ты умееш')!==false){
			$session->setParam('LAST_COMMAND','',true);
			$command = 'help';
		}elseif(strpos($text,'повтор')!==false){
			$command = 'remesage';
		}elseif($text == '/settings' || $text == 'settings' || $text == 'настройки'){
			$session->setParam('LAST_COMMAND','',true);
			$command = 'settings';
		}elseif($text == '/start' || $text == 'start' || $text == 'старт'){
			$session->setParam('LAST_COMMAND','',true);
			$command = 'start';
		}elseif($text == 'спасибо' || $text == 'круто'){
			$command = 'thanks';
		}elseif(strpos($text,'дура')!==false || strpos($text,'тупая')!==false || strpos($text,'тупица')!==false){
			$command = 'obida';
		}
		
		return $command;
		
	}
	
	public static function log($data, $title = ''){
		return \Mlife\Cbots\Main::getInstance()->log($data, $title);
	}
	
}