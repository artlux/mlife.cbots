<?
namespace Mlife\Cbots;

class Usersession {
	
	protected $filePath = null;
	
	protected $params = null;
	protected $sessionId = null;
	protected $sessionLocked = false;
	
	function __construct($sessionId, $defaultParams = array()) {
		
		if($defaultParams['SESSION_DIR']) $fileDir = $defaultParams['SESSION_DIR'];
		
		if(!isset($defaultParams['CONTECST'])){
			$defaultParams['CONTECST'] = '\Mlife\Cbots\Contecst\Start';
		}
		
		$this->sessionId = $sessionId;
		
		$cacheId = $this->sessionId;
		
		if (!$fileDir) return false;
		
		$expired = $defaultParams['SESSION_EXPIRED'];
		
		$fileName = $fileDir.'/'.$cacheId.'.session';
		$this->filePath = $fileName;
		if (file_exists($fileName)){
			
			$this->params = unserialize(file_get_contents($fileName));
			
			if($this->params['SESSION_EXPIRED']) $expired = $this->params['SESSION_EXPIRED'];
			
			if($expired){
				
				$timeFile = filemtime($fileName) + $expired;
				if($timeFile < time()){
					$this->params = $defaultParams;
					if($defaultParams['CONTECST']) $this->setContecst($defaultParams['CONTECST']);
					$this->save();
				}
				
			}else{
				$this->params = unserialize(file_get_contents($fileName));
			}
			
		}else{
			$this->params = $defaultParams;
			if($defaultParams['CONTECST']) $this->setContecst($defaultParams['CONTECST']);
		}
		
	}
	
	public function getSessionId(){
		return $this->sessionId;
	}
	
	public function setContecst($contecst, $replace=false){
		
		/*
		//bitrix event
		$event = new \Bitrix\Main\Event("mlife.cbots", "OnSetContecst",array('CONTECST'=>$contecst));
		$event->send();
		if ($event->getResults()){
			foreach($event->getResults() as $evenResult){
				if($evenResult->getResultType() == \Bitrix\Main\EventResult::SUCCESS){
					$params = $evenResult->getParameters();
					if($params['CONTECST']) $contecst = $params['CONTECST'];
				}
			}
		}
		*/
		$this->params['CONTECST'] = $contecst;
		if($replace) $this->save();
	}
	public function getContecst(){
		if(is_array($this->params['CONTECST'])){
			return $this->params['CONTECST'][0].$this->params['CONTECST'][1];
		}else{
			return $this->params['CONTECST'];
		}
	}
	
	public function setParam($name, $value='', $replace=false){
		$this->params[$name] = $value;
		if($replace) $this->save();
	}
	
	public function getParam($name){
		if(isset($this->params[$name]))
			return $this->params[$name];
		return false;
	}
	
	public function save(){
		if (!$this->filePath)
			return false;
		
		if($this->sessionLocked) return false;
		
		$config = serialize($this->params);
		file_put_contents($this->filePath, $config);
		@chmod($this->filePath, 0744);
	}
	
	public function lock(){
		$this->sessionLocked = true;
	}
	
	public function unlock(){
		$this->sessionLocked = false;
	}
	
	public function delete(){
		if (!$this->filePath)
			return false;
		
		if(file_exists($this->filePath)){
			@unlink($this->filePath);
		}
		
		$this->params = null;
		$this->sessionId = null;
	}
	
}