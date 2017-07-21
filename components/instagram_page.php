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
echo $username.'<br /><br />';
		  // store user access token
		  $instagram->setAccessToken($data);

		  // now you have access to all authenticated user methods
		  $result = $instagram->getUserMedia();

	die('<pre>'.var_export($result,true));
} else {

  // check whether an error occurred
  if (isset($_GET['error'])) {
    echo 'An error occurred: ' . $_GET['error_description'];
    exit;
  }

}


			}		
		}
	
	}