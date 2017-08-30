<?

	use MetzWeb\Instagram\Instagram;

	class Videotwo_Page extends Component {

		function init() {

			require_once('lib/instagram/vendor/autoload.php');

			// initialize class
			$popular = new Instagram(array(
			  'apiKey'      => INSTAGRAM_KEY,
			  'apiSecret'   => INSTAGRAM_SECRET,
			  'apiCallback' => WEB_ROOT.'instagram/popular'
			));

			$manual = new Instagram(array(
			  'apiKey'      => INSTAGRAM_KEY,
			  'apiSecret'   => INSTAGRAM_SECRET,
			  'apiCallback' => WEB_ROOT.'instagram/manual'
			));

			$random = new Instagram(array(
			  'apiKey'      => INSTAGRAM_KEY,
			  'apiSecret'   => INSTAGRAM_SECRET,
			  'apiCallback' => WEB_ROOT.'instagram'
			));

			// create login URL

			$this->tpl->setTemplate('videotwo');
			$this->tpl->set('title','Instatracks Home');
			$this->tpl->set('link1', '/videoone');
			$this->tpl->set('link2', '/selectpics');	
			$this->tpl->set('popular_link', $popular->getLoginUrl());
			$this->tpl->set('manual_link', $manual->getLoginUrl());
			$this->tpl->set('random_link', $random->getLoginUrl());
		}

	}