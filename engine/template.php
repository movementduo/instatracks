<?php

	class Template {

		var $data = array();
		var $path = null;
		var $ext = '.php';
		var $loaded = false;
		var $debug = false;
	
		function __construct($path = null) {
			$this->init(array('path' => $path));
		}
		
		function init($data) {
			$path = is_set($data['path'], '');

			$this->url = '/' . trim($content->url, ' /');
			
			$path = $path ? $path : TEMPLATES;
			$this->path = $path;
			
			$this->set('_date', date('d.m.Y'));
			$this->set('_time', date('H:i:s'));
			$this->set('_url', $this->url, true);
			$this->set('_get', isset($_GET) ? $_GET : array(), true);
			$this->set('_post', isset($_POST) ? $_POST : array(), true);
			
			$this->loaded = true;
		}
			
		function setPath($path) {
			$this->path = TEMPLATES . $path;
		}

		function setTemplate($template)	{
			$this->template = $template;
		}
		
		function set($var, $val = null, $persistent = true) {
			if (is_array($var)) {
				$this->data = array_merge($this->data, $var);
			} else {
				$this->data[$var] = $val;
			}
			if ($persistent) {
				$this->persistent[] = $var;
			}
		}
		
		function get($var, $default = null) {
			return isset($this->data[$var]) ? $this->data[$var] : $default;
		}
		
		function render($template = FALSE, $data = array()) {
			if ($template) {
				$this->template = $template;
			}
			if ($this->debug || isset($_GET['_debug_tpl'])) {
				die('<pre>'.var_export($this->template,true));
			}
			if (!isset($this->template) || empty($this->template)) {
				exit('Please specify a template');
			}
			ob_start();
			if (!empty($data)) {
				$this->data += $data;
			}
			extract($this->data);
			if (!include($this->path.'/'.$this->template.$this->ext)) {
				exit('Could not find the template: "'.$this->template.'"');
			}
			$this->reset_data();
			return ob_get_clean();
		}
	
		function reset_data() {
			$data = $this->data;
			foreach ($data as $key => $value) {
				if (!in_array($key, $this->persistent))
					unset($data[$key]);
			}
			$this->data = $data;
		}	
	}