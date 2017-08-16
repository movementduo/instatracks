<?php
	
	use MetzWeb\Instagram\Instagram;

	class Instagram_Page extends Component {

		function init() {
			
			require_once('lib/instagram/vendor/autoload.php');

			// initialize class
			$instagram = new Instagram(array(
			  'apiKey'      => INSTAGRAM_KEY,
			  'apiSecret'   => INSTAGRAM_SECRET,
			  'apiCallback' => WEB_ROOT.'instagram'
			));

			if(array_key_exists('code',$_REQUEST)) {

				$this->checkSessionID();

				$_SESSION['oauthToken'] = $_REQUEST['code'];

				// receive OAuth token object
				$data = $instagram->getOAuthToken($_SESSION['oauthToken']);
				$username = $username = $data->user->username;

				// store user access token
				$instagram->setAccessToken($data);

				// now you have access to all authenticated user methods
				$result = $instagram->getUserMedia('self',100);
				
				$mode = 'popular';
				// $mode = array_key_exists(1,$this->args)?$this->args[1]:'random';
				
				if(!in_array($mode,['random','popular','manual'])) {
					$this->is404();
				}

				$instance = $this->db->executeSql("INSERT INTO instances (sessionId, oauthToken, lang, sessionMode, status, stampCreate) VALUES (:x1, :x2, :x3, :x4, 'pending', NOW())",array(session_id(),$_SESSION['oauthToken'],APP_LANGUAGE,$mode));
				$instanceId = $this->db->lastId();
				
				$_SESSION['instanceId'] = $instanceId;

				foreach ($result->data as $media) {
					if ($media->type == 'image') {
						$instance = $this->db->executeSql("INSERT INTO instanceSlides (instanceID, instagramID, cdnURL, likes, width, height) VALUES (:x1, :x2, :x3, :x4, :x5, :x6)",array(
							$instanceId,
							$media->id,
							$media->images->standard_resolution->url,
							$media->likes->count,
							$width = $media->images->standard_resolution->width,
							$height = $media->images->standard_resolution->height,
						));

					}
				
				}
				
				if($this->db->executeSql("SELECT COUNT(*) AS total FROM instanceSlides WHERE instanceId = :x1",array($instanceId))->fetchAssoc()['total'] < 4) {
					die('not enough images');
				}

				if(in_array($mode,['popular','random'])) {
					shell_exec('echo "/usr/bin/php '.APP_ROOT.'app.php '.$instanceId.'" | at now');
					$this->redirect('/loading');
				} else {
					$this->redirect('/select');
				}
				
				

			} else {


				if (isset($_GET['error'])) {
					echo 'An error occurred: ' . $_GET['error_description'];
				}

			}
		    exit;
		}
		
		function checkSessionID() {
			$sessionQ = $this->db->executeSql("SELECT * FROM instances WHERE sessionId = :x1",array(session_id()));
			if($sessionQ->rowCount()) {
				session_regenerate_id();
				$this->checkSessionID();
			}
		}
	
	}