<?

	class V_Page extends Component {
		
		var $video;

		function init() {
			$this->tpl->setTemplate('v');
			$this->tpl->set('title','Instatracks Home');
			
			
			die('<pre>'.var_export($this->args,true));
			if(array_key_exists(1,$this->args)) {
				$this->video = $this->db->executeSql("SELECT * FROM instances WHERE instanceId = :x1 AND status = 'complete' LIMIT 1",array($this->args[1]))->fetchAssoc()[0];
			}
			die('<pre>'.var_export($this->video,true));
			
			if(!$this->video) {
				die('error');
			}

			$this->tpl->set('video',$this->video);

		}

	}