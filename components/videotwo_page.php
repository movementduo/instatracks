<?

	class Videotwo_Page extends Component {

		function init() {
			$this->tpl->setTemplate('videotwo');
			$this->tpl->set('title','Instatracks Home');
			$this->tpl->set('link1', '/videoone');
			$this->tpl->set('link2', '/selectpics');
		}

	}