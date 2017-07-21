<?

	class Home_Page extends Component {

		function init() {
			$this->tpl->setTemplate('home');
			$this->tpl->set('title','Instatracks Home');
			$this->tpl->set('link', '/loading');
		}

	}