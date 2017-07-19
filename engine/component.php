<?

	class Component {
	
		var $db;
	
		function __construct($db) {
			$this->db = $db;
			$this->tpl = new Template;
			if(method_exists($this,'init')) {
				$this->init();
			}
		}
		
		function render() {
			return $this->tpl->render();
		}

	}