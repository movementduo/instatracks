<?

	class Loading_Page extends Component {

		function init() {
			$this->tpl->setTemplate('loading');
			$this->tpl->set('title','Instatracks Home');
			$this->tpl->set('who','jamie');
			$this->tpl->set('link', 'index.php?request=/videoone');
		}

	}