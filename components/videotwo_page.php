<?

	class Videotwo_Page extends Component {

		function init() {
			$this->tpl->setTemplate('videotwo');
			$this->tpl->set('title','Instatracks Home');
			$this->tpl->set('who','jamie');
			$this->tpl->set('link1', 'index.php?request=/videoone');
			$this->tpl->set('link2', 'index.php?request=/selectpics');
		}

	}