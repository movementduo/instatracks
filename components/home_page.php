<?

	class Home_Page extends Component {

		function init() {
			$this->tpl->setTemplate('home');
			$this->tpl->set('title','Instatracks Home');
			$this->tpl->set('who','jamie');
			$this->tpl->set('link', 'index.php?request=/loading');
		}

	}