<?php

	class Ajax_Page extends Component {

		var $instanceId = false;
	
		function init() {
		
			if(array_key_exists('instanceId',$_SESSION)) {
				$this->instanceId = $_SESSION['instanceId'];
			}
		
			if(array_key_exists('action',$_REQUEST)) {
				switch($_REQUEST['action']) {
					case 'status':
						return $this->status();
						break;
					case 'select':
						return $this->select();
						break;
				}
			} else {
				die('No access!');
			}
		}

		function status() {
			if($this->instanceId) {
				$instanceQ = $this->db->executeSql("SELECT * FROM instances WHERE id = :x1 LIMIT 1",[$this->instanceId]);
				if($instanceQ->rowCount()) {
					$instance = $instanceQ->fetchAssoc()[0];
/*
pending: you're in the queue

active: return creationState

rejected: error handle

completed: send object w/ share url

*/

//						if status == active ... 

//
					header('Content-type: text/json');

					echo json_encode(
						[
							'state' => $instance['creationState'],
							'share' => $instance['shareUrl'],
						]
					);
					exit;
				}
			}
		}


		function select() {
		
			if(array_key_exists('instanceId',$_SESSION)) {
				$this->instanceId = $_SESSION['instanceId'];
			}

			if(array_key_exists('url',$_GET)) {
			
				foreach($_GET['url'] as $slideId) {
					$this->db->executeSql("UPDATE instanceSlides SET status = 'accepted' WHERE instanceId = :x1 AND id = :x2",[$slideId,$this->instanceId]);
				}
				shell_exec('echo "/usr/bin/php '.APP_ROOT.'app.php '.$this->instanceId.'" | at now');
				echo 'true';
				exit;
			}
			echo 'false';
			exit;
		}
	
	}