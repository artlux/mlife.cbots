<?
namespace Mlife\Cbots;

class Button{
	
	public $button = array();
	
	function __construct($params=array()){
		$this->setData($params);
	}
	
	/*
	array(
		'hide' => true, false - параметр скрытия кнопки после нажатия
		'title_button' => короткий текст на кнопке
		'title_comand' => длинный текст на кнопке
		'payload' => команда переданная по нажатию на кнопку
	)
	*/
	public function setData($params=array()){
		if(empty($params)) return;
		
		if($params['title_button'] && !$params['title_comand']) $params['title_comand'] = $params['title_button'];
		
		$this->button = $params;
		
	}
	
	public function formatButton($type){
		if($type == 'alisa'){
			$but = new \stdClass();	
			$but->hide = $this->button['hide'];
			$but->title = $this->button['title_button'];
			if($this->button['link']){
				$but->url = $this->button['link'];
			}else{
				$but->payload = $this->button['payload'];
			}
			return $but;
		}elseif($type == 'telegramm'){
			if($this->button['link']){
				return false;
			}
			$but = $this->button['title_button'];
			return $but;
		}elseif($type == 'viber'){
			if($this->button['link']){
				return false;
			}
			$but = array(
				"Columns"=> 3,
				"Rows"=> 1,
				"BgColor"=> "#0bb0eb",
				"Text"=> '<font color="#ffffff">'.$this->button['title_button'].'</font>',
				"TextVAlign"=> "middle",
				"TextHAlign"=> "center",
				"ActionBody"=>$this->button['payload']
			);
			return $but;
		}elseif($type === false){
			return $this->button;
		}
	}
	
}