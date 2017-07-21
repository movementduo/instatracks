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
				$_SESSION['oauthToken'] = $_REQUEST['code'];

				// receive OAuth token object
				$data = $instagram->getOAuthToken($_SESSION['oauthToken']);
				$username = $username = $data->user->username;

				// store user access token
				$instagram->setAccessToken($data);

				// now you have access to all authenticated user methods
				$result = $instagram->getUserMedia('self',100);

				$instance = $this->db->executeSql("INSERT INTO instances (sessionId) VALUES (:x1)",array(session_id()));
				$instanceId = $this->db->lastId();

				foreach ($result->data as $media) {
				if ($media->type == 'image') {

					$metadata = array(
						$media->likes->count,
						$width = $media->images->standard_resolution->width,
						$height = $media->images->standard_resolution->height,

					);
	
					$instance = $this->db->executeSql("INSERT INTO instanceSlides (instanceID,instagramID,cdnURL,metadata) VALUES (:x1)",array(
						$instanceId,
						$media->id,
						$media->images->standard_resolution->url,
						serialize($metadata);
					));

				}
				
				exit 'done';
          }

} else {

  // check whether an error occurred
  if (isset($_GET['error'])) {
    echo 'An error occurred: ' . $_GET['error_description'];
    exit;
  }

}
    exit;


		}
	
	}