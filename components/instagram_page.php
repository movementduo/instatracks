<?php
	
	use MetzWeb\Instagram\Instagram;

	class Instagram_Page extends Component {

		function init() {
			
			require_once('instagram/vendor/autoload.php');

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

				$instance = $this->db->executeSql("INSERT INTO instances (sessionId, oauthToken, status, stampCreate) VALUES (:x1, :x2, 'pending', NOW())",array(session_id(),$_SESSION['oauthToken']));
				$instanceId = $this->db->lastId();
				
				$_SESSION['instanceId'] = $instanceId;

				foreach ($result->data as $media) {
					if ($media->type == 'image') {

						$metadata = array(
							$media->likes->count,
							$width = $media->images->standard_resolution->width,
							$height = $media->images->standard_resolution->height,

						);
	
						$instance = $this->db->executeSql("INSERT INTO instanceSlides (instanceID, instagramID, cdnURL, metadata) VALUES (:x1, :x2, :x3, :x4)",array(
							$instanceId,
							$media->id,
							$media->images->standard_resolution->url,
							serialize($metadata)
						));

					}
				
				}
				
				shell_exec('echo "/usr/bin/php '.APP_ROOT.'app.php '.$instanceId.'" | at now');

				$this->redirect('/loading');

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