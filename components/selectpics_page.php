<?

	class Selectpics_Page extends Component {


		function init() {
			$this->tpl->setTemplate('selectpics');
			$this->tpl->set('title','Instatracks - Select Your Favourite Pictures');
			$this->tpl->set('link', '/loading');

			//THIS SH*T DONT MAKE SENSE
			$this->tpl->set('user', $this->db->executeSql("SELECT * FROM instances WHERE id = :x1",[$_SESSION['instanceId']])->fetchAssoc());

			$this->tpl->set('username','@movement_london'); // GET FROM DB
			$this->tpl->set('popular', $this->db->executeSql("SELECT * FROM instanceSlides WHERE instanceId = :x1 ORDER BY likes DESC LIMIT 6",[$_SESSION['instanceId']])->fetchAssoc());
			$this->tpl->set('recent', $this->db->executeSql("SELECT * FROM instanceSlides WHERE instanceId = :x1 ORDER BY instagramID DESC LIMIT 20",[$_SESSION['instanceId']])->fetchAssoc());
		}

	}