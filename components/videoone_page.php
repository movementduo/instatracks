<?

	class Videoone_Page extends Component {

		function init() {
			$this->tpl->setTemplate('videoone');
			$this->tpl->set('title','Instatracks Home');
			$this->tpl->set('who','jamie');
			$this->tpl->set('link', 'index.php?request=/videotwo');
		}

	}