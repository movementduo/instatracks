<?

	use MetzWeb\Instagram\Instagram;

	class Videotwo_Page extends Component {

		function init() {

			require_once('lib/instagram/vendor/autoload.php');

			// initialize class
			$instagram = new Instagram(array(
			  'apiKey'      => INSTAGRAM_KEY,
			  'apiSecret'   => INSTAGRAM_SECRET,
			  'apiCallback' => WEB_ROOT.'instagram/popular'
			));

			// create login URL
			$loginUrl = $instagram->getLoginUrl();

			$this->tpl->setTemplate('videotwo');
			$this->tpl->set('title','Instatracks Home');
			$this->tpl->set('link1', '/videoone');
			$this->tpl->set('link2', '/selectpics');
			$this->tpl->set('popular_link', $loginUrl);
		}

	}