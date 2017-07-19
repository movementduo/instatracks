<?php

	class Ajax_Page extends Component {
	
		function init() {
			if(array_key_exists('action',$_REQUEST)) {
				switch($_REQUEST['action']) {
					case 'create':
						return $this->create();
					case 'status':
						return $this->status();
						break;
				}
			} else {
				die('No access!');
			}
		}

		function create() {
			$instance = $this->db->executeSql("INSERT INTO instances (sessionId) VALUES (:x1)",array('chipshop'));
			$instanceId = $this->db->lastId();
			echo 'Instance ID: '.$instanceId;

			// shell_exec('php '.APP_ROOT.'app.php '.$instanceId);

			exit;

		}
		
		function status() {

			echo $this->db->executeSql("SELECT NOW() AS time")->fetchAssoc()[0]['time'];
//			echo $this->db->executeSql("SELECT * FROM instances WHERE ")->fetchAssoc()[0]['time'];
			exit;
		}
	
	}