<?

	class V_Page extends Component {
		
		var $video;

		function init() {
			$this->tpl->setTemplate('v');
			$this->tpl->set('title','Instatracks Home');

			if(array_key_exists(1,$this->args)) {
				$videoQ = $this->db->executeSql("SELECT * FROM instances WHERE instanceId = :x1 AND status = 'complete' LIMIT 1",array($this->args[1]));
				$this->video = $videoQ->fetchAssoc()[0];
			}

			//$images = $this->db->executeSql("SELECT cdnURL FROM instanceSlides WHERE instanceId = 195);
			//$this->images = $images->fetchAssoc()[0];

			//$this->tpl->set('images',$this->images);
			
			if(!$this->video) {
				die('error');
			}

			$this->tpl->set('video',$this->video);
			$this->tpl->set('link','/videotwo');

			session_destroy();
		}

	}
