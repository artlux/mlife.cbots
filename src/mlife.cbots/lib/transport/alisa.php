<?
namespace Mlife\Cbots\Transport;

class Alisa implements \Mlife\Cbots\Interfaces\Transport{
	
	public $config = array();
	public $appsConfig = array();
	public $cacheData = array();
	
	function __construct($config = array()) {
		
		$this->config = $config;
		
		try{
			
			$json = file_get_contents('php://input');
			$jsonData = json_decode($json);
			
			//$this->log($jsonData, 'yandex start data'); reflection, log moving construct Mlife\Cbots\Main
			
			$this->cacheData = $jsonData;
			
		}catch(\Exception $ex){
			header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
			echo $ex->getMessage();
		}
		
	}
	
	public function getName(){
		return static::class;
	}
	
	public function log($data, $title = ''){
		return \Mlife\Cbots\Main::getInstance()->log($data, $title);
	}
	
	public function getSession(){
		return \Mlife\Cbots\Main::getInstance()->getSession();
	}
	
	public function getConfigSession(){
		
		if(!isset($this->cacheData->session->user_id)) return array();
		
		return array(
			'SESSION' => array(
				'ID'=>md5($this->cacheData->session->user_id),
			)
		);
	}
	
	public function getConfigUser(){
		
		if(!isset($this->cacheData->session->user_id)) return array();
		
		return array(
			'USER_KEY'=>'alisa_'.$this->cacheData->session->user_id
		);
	}
	
	public function request($data){
		
		if(empty($data)) $data = $this->cacheData;
		
		if($this->cacheData->request->command == 'ping' || $this->cacheData->request->original_utterance == 'ping') return $this->sendMessage(false);
		
		$session = $this->getSession();
		
		if($session === null) return $this->sendMessage(false);
		
		if($data->request->payload && !$data->request->command) {
			$data->request->command = $data->request->payload;
			$this->cacheData->request->command = $data->request->payload;
		}
		if($this->cacheData->session->new){
			$this->cacheData->request->command_start = $this->cacheData->request->command;
			$this->cacheData->request->command = '/start';
			$data->request->command = '/start';
		}
		
		if($data->request->command){
			
			$mess = trim($data->request->command);
			
			$contecstClass = $session->getContecst();
			
			$globalContecst = $session->getParam('CONTECST_GLOBAL');
			
			$resultGlobal = false;
			if(class_exists($globalContecst)){
				$resultGlobal = $globalContecst::send($mess);
			}
			
			if($resultGlobal !== false) {
				$result = $this->sendMessage($resultGlobal);
			}else{
				$messOb = $contecstClass::send($mess);
				$result = $this->sendMessage($messOb);
			}
			
			return $result;
		
		}
		
		return $this->sendMessage(false);
		
	}
	
	public function sendMessage($mess){
		if($_REQUEST['dbg']) {echo'<pre>';print_r($mess);echo'</pre>';}
		if($mess === false) {
			if($this->cacheData->request->command == 'ping'){
				$data = new \stdClass();
				$data->response = new \stdClass();
				$data->session = new \stdClass();
				$data->version = "1.0";
				$data->response->end_session = false;
				$data->session->session_id = $this->cacheData->session->session_id;
				$data->session->message_id = $this->cacheData->session->message_id;
				$data->session->user_id = $this->cacheData->session->user_id;
				$data->response->text = 'ok';
				$data->response->tts = 'ok';
				$r = json_encode($data);
				echo $r;
			}
			return false;
		}
		try{
			if(!$mess->getText()) {
				$this->log($mess, 'message is empty');
				return false;
			}
		}catch(\Exception $ex){
			$ar = Helper::getTextErrorDefault();
			$rand_key = array_rand($ar);
			$mess = new \Mlife\Cbots\Message(
				$ar[$rand_key],
				'',
				'last'
			);
		}
		
		$mess = $this->prepareData($mess);
		
		$data = new \stdClass();
		$data->response = new \stdClass();
		$data->session = new \stdClass();
		$data->version = "1.0";
		
		
		$data->response->end_session = false;
		//if($this->cacheData->request->command == 'ыыы'){
		//	$data->response->end_session = true;
		//}
		$data->session->session_id = $this->cacheData->session->session_id;
		$data->session->message_id = $this->cacheData->session->message_id;
		$data->session->user_id = $this->cacheData->session->user_id;
		
		$data->response->text = $mess->getText();
		$data->response->tts = $mess->getTts();
		
		if($mess->getCustom('type') == 'BigImage'){
			$desc = $mess->getCustom('image_description');
			if(!$desc) $desc = $mess->getText();
			$data->response->card = array(
				"image_id"=>$mess->getCustom('image_id'),
				"type"=> "BigImage",
				"title"=>$mess->getCustom('image_title'),
				"description"=>$desc
			);
		}
		
		$buttons = $mess->getButtons('alisa');
		
		if(!empty($buttons)) $data->response->buttons = $buttons;
		
		$r = json_encode($data);
		$this->log($data, 'data for alisa');
		echo $r;
		
		return true;
		
	}
	
	private function prepareData($data){
		
		$session = \Mlife\Cbots\Main::getInstance()->getSession();
		
		$sessionId = $session->getSessionId();
		
		$messTo = $session->getParam('LAST_MESS_DATA');
		$messTo['last_command'] = $session->getParam('LAST_COMMAND');
		$messTo['last_data'] = $session->getParam('LAST_DATA');
		
		$messFrom = array(
			'text'=>$this->cacheData->request->command,
			'payload'=>$this->cacheData->request->payload,
			'type'=>$this->cacheData->request->type,
			'user_id'=>$this->cacheData->session->user_id,
			'client_id'=>$this->cacheData->meta->client_id
		);
		
		/*
		//for bitrix cp1251 (not recomended, use utf-8)
		$messFrom = $GLOBALS["APPLICATION"]->ConvertCharsetArray($messFrom, "utf-8", SITE_CHARSET);
		$messTo = $GLOBALS["APPLICATION"]->ConvertCharsetArray($messTo, "utf-8", SITE_CHARSET);
		*/
		
		if($this->config['TIME_START']) {
			$time_end = microtime(true);
			$messTo['TIME_EXISTS'] = $time_end - $this->config['TIME_START'];
		}
		
		$contecstClass = $session->getContecst();
		
		if($sessionId && !empty($messTo) && !empty($messFrom) && $contecstClass){
			\Mlife\Cbots\DialogsTable::add(
				array(
					'SESSION_ID'=>$sessionId,
					'MESSAGE_FROM'=>$messFrom,
					'MESSAGE_TO'=>$messTo,
					'CONTECST'=>$contecstClass,
					'TRANSPORT'=>$this->getName(),
					'TIME_ADD'=>time()
				)
			);
		}
		
		return $data;
		
	}
	
	
}