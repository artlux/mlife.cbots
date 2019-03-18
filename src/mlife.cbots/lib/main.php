<?
namespace Mlife\Cbots;

class Main{
	
	private static $_instance = null;
	
	private $data = array();
	private $user = null;
	private $transport = null;
	private $loger = null;
	
	private function __construct($params) {
			
		$className = $params['transport']['CLASS'];
		$this->transport = new $className($params['transport']['CONFIG']);;
		
		$className = $params['log']['CLASS'];
		$this->loger = new $className($params['log']);
		
		if($this->transport->cacheData->request->command != 'ping') $this->log($this->transport->cacheData, 'start data'); //стук бот платформы
		
		if(!$params['data']) $params['data'] = array();
		$this->data = array_merge($params['data'],$this->transport->getConfigSession(),$this->transport->getConfigUser());
		
		if($this->data['SESSION']['ID']){
			$className = $params['session']['CLASS'];
			$this->user = new $className($this->data['SESSION']['ID'], $params['session']);
		}
		
	}
	
	public static function getInstance($params=array()) {
		if(is_null(self::$_instance)){
			self::$_instance = new self($params);
		}
		return self::$_instance;
	}
	
	public function getTransport(){
		return $this->transport;
	}
	
	public function send($data=array()){
		return $this->getTransport()->request($data);
	}
	
	public function getTransportName(){
		return $this->getTransport()->getName();
	}
	
	public function log($data, $title = ''){
		return $this->loger->add($data, $title);
	}
	
	public function getSession(){
		return $this->user;
	}
	
	public function setLastMessage($mess){
		
		$session = $this->getSession();
		$session->setParam('LAST_MESS_DATA',array('text'=>$mess->getText(),'tts'=>$mess->GetTts(), 'buttons'=>$mess->getButtons(),'image_id'=>$mess->getCustom('image_id'),'image_description'=>$mess->getCustom('image_description'),'image_title'=>$mess->getCustom('image_title'),'type'=>$mess->getCustom('type')),true);
		
	}
	
}