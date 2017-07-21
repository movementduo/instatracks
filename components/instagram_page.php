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
?>

        <h1><?php echo $data->user->username ?></h1>
        <ul>
        <?php
          // display all user likes
          foreach ($result->data as $media) {
            $content = "<li>";

            // output media
            if ($media->type === 'video') {
              // video
              $poster = $media->images->low_resolution->url;
              $source = $media->videos->standard_resolution->url;
              $content .= "<video class=\"media video-js vjs-default-skin\" width=\"250\" height=\"250\" poster=\"{$poster}\"
                           data-setup='{\"controls\":true, \"preload\": \"auto\"}'>
                             <source src=\"{$source}\" type=\"video/mp4\" />
                           </video>";
            } else {
              // image
die('<pre>'.var_export($media,true));
              $image = $media->images->low_resolution->url;
              $content .= "<img class=\"media\" src=\"{$image}\"/>";
            }

            // create meta section
            $avatar = $media->user->profile_picture;
            $username = $media->user->username;
            $comment = $media->caption->text;
            $content .= "<div class=\"content\">
                           <div class=\"avatar\" style=\"background-image: url({$avatar})\"></div>
                           <p>{$username}</p>
                           <div class=\"comment\">{$comment}</div>
                         </div>";

            // output media
            echo $content . "</li>";
          }
        ?>
        </ul>

<?php

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