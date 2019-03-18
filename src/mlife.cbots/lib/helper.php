<?
namespace Mlife\Cbots;

class Helper {
	
	public static function toLowerText($text){
		$text = mb_strtolower($text);
		return $text;
	}
	
	public static function getTextErrorDefault(){
		
		$text = array(
			'Не могу определить команду. Возникли сложности? Можно отправить команду: старт или помощь.',
			'Не понимаю Вас. Возникли сложности? Можно отправить команду: старт или помощь.',
			'Не могу обработать запрос. Есть сложности? Можно отправить команду: старт или помощь.',
			'Не могу ответить на запрос. Возникли сложности? Можно отправить команду: старт или помощь.',
			'Попробуйте перефразировать запрос. Есть сложности? Можно отправить команду: старт или помощь.'
		);
		
		return $text;
	}
	
	public static function num_decline( $number, $titles=false, $param2 = '', $param3 = '' ){
		
		if( is_string($titles) )
			$titles = preg_split('~,\s*~', $titles );

		if( count($titles) < 3 )
			$titles = array( func_get_arg(1), func_get_arg(2), func_get_arg(3) );

		$cases = array(2, 0, 1, 1, 1, 2);

		$intnum = abs( intval( strip_tags( $number ) ) );

		return $number .' '. $titles[ ($intnum % 100 > 4 && $intnum % 100 < 20) ? 2 : $cases[min($intnum % 10, 5)] ];
	}
	
}