<?

	use MetzWeb\Instagram\Instagram;

	class Home_Page extends Component {

		function init() {
			
			require_once('lib/instagram/vendor/autoload.php');

			// initialize class
			$instagram = new Instagram(array(
			  'apiKey'      => INSTAGRAM_KEY,
			  'apiSecret'   => INSTAGRAM_SECRET,
			  'apiCallback' => WEB_ROOT.'instagram'
			));

			// create login URL
			$loginUrl = $instagram->getLoginUrl();

			$this->tpl->setTemplate('home');
			$this->tpl->set('title','Instatracks Home');
			$this->tpl->set('link', $loginUrl);
		}

	}