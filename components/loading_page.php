<?

	class Loading_Page extends Component {

		function init() {
			$this->tpl->setTemplate('loading');
			$this->tpl->set('title','Instatracks Home');
			$this->tpl->set('link', '/videoone');
		}

	}