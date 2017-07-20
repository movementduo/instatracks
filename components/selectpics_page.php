<?

	class Selectpics_Page extends Component {


		function init() {
			$this->tpl->setTemplate('selectpics');
			$this->tpl->set('title','Instatracks Home');
			$this->tpl->set('who','jamie');
			$this->tpl->set('link', 'index.php?request=/loading');
			// $this->db->set('what', 'what');
			$images = 'get images over here? read some db? then foreach loop for display them?';
			$this->tpl->set('images', $images);
		}

	}