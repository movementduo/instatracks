<?

	class Selectpics_Page extends Component {


		function init() {
			$this->tpl->setTemplate('selectpics');
			$this->tpl->set('title','Instatracks - Select Your Favourite Pictures');
			$this->tpl->set('link', '/loading');

			$this->tpl->set('popular', $this->db->executeSql("SELECT * FROM instanceSlides WHERE instanceId = :x1 ORDER BY likes DESC LIMIT 8",[$_SESSION['instanceId']])->fetchAssoc());
			$this->tpl->set('recent', $this->db->executeSql("SELECT * FROM instanceSlides WHERE instanceId = :x1 ORDER BY instagramID DESC LIMIT 8",[$_SESSION['instanceId']])->fetchAssoc());
		}

	}