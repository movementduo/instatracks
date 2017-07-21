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

		function redirect($url) {
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: {$url}");
			exit;
		}
	}