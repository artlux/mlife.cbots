<?
namespace Mlife\Cbots;

class Message {
	
	protected $data = array();
	
	function __construct($text='', $tts='', $buttons=array()){
		
		$this->data = array(
			'text'=>$text,
			'tts'=>($tts ? $tts : $text),
			'buttons'=>array()
		);
		
		if(is_string($buttons) && ($buttons == 'last')) {
			$session = \Mlife\Cbots\Main::getInstance()->getSession();
			$but = $session->getParam('LAST_MESS_DATA');
			if($but['buttons']) {
				$buttons = $but['buttons'];
			}else{
				$buttons = array();
			}
		}
		
		if(!empty($buttons)){
			foreach($buttons as $button){
				$this->setButton($button);
			}
		}
		
	}
	
	public function setText($text=''){
		$this->data['text'] = $text;
	}
	
	public function getText(){
		return $this->data['text'];
	}
	
	public function setTts($tts=''){
		$this->data['tts'] = $tts;
	}
	
	public function getTts(){
		return $this->data['tts'];
	}
	
	public function setButton($button){
		$this->data['buttons'][] = new \Mlife\Cbots\Button($button);
	}
	
	public function setButtons($buttons=array()){
		foreach($buttons as $button){
			$this->setButton($button);
		}
	}
	
	public function getButtons($type=false){
			//print_r($this->data);
		if($type == 'alisa' || $type === false){
			$retArray = array();
			foreach($this->data['buttons'] as $button){
				if($bTemp = $button->formatButton($type)){
					$retArray[] = $bTemp;
				}
			}
			return $retArray;
		}elseif($type == 'viber'){
			$retArray = array(
				"Type"=>"keyboard",
				"Buttons"=>array()
			);
			foreach($this->data['buttons'] as $button){
				if($bTemp = $button->formatButton($type)){
					$retArray['Buttons'][] = $bTemp;
				}
			}
			return $retArray;
		}elseif($type == 'telegramm'){
			$retArray = array(
				"one_time_keyboard" => true,
				"resize_keyboard" => true,
				'keyboard' => array(),
			);
			foreach($this->data['buttons'] as $button){
				if($bTemp = $button->formatButton($type)){
					$retArray['keyboard'][] = $bTemp;
				}
			}
			if(!empty($retArray['keyboard'])){
				$buttons_temp = array();
				$cn = 0;
				foreach($retArray['keyboard'] as $k=>$button){
					$buttons_temp[$cn][] = $button;
					if(fmod($k,2)==1) $cn++;
				}
				$retArray['keyboard'] = $buttons_temp;
			}
			return $retArray;
		}
		return array();
	}
	
	public function setCustom($custom=array()){
		foreach($custom as $k=>$v){
			$this->data[$k] = $v;
		}
	}
	
	public function getCustom($paramName){
		if(isset($this->data[$paramName])){
			return $this->data[$paramName];
		}
		return false;
	}
	
	public function deleteButtons(){
		$this->data['buttons'] = array();
	}
}