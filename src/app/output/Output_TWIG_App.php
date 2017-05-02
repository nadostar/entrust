<?php
require_once __DIR__ . '/../../lib/Twig/Autoloader.php';
Twig_Autoloader::register();

require_once __DIR__ . '/../../fw/core/output/Output_HTML.php';
require_once __DIR__ . '/../view/ViewHelper_App_Twig_Extension.php';

class Output_TWIG_App extends Output_HTML {
	const TMPL_FILE_NAME_4_DEBUG = 'debug.html';
	
	protected function initialize() {
		parent::initialize();
		
		LogManager::debug("Output_TWIG_App ".BASE_DIR . Env::TEMPLATE_DIR . '/');
		
		$this -> Twig = new Twig_Environment(new Twig_Loader_Filesystem(BASE_DIR . Env::TEMPLATE_DIR . '/'), array('cache' => false, 'auto_reload' => TRUE, 'debug' => TRUE, ));
		
        $this -> Twig -> addExtension(new Twig_Extension_Escaper(TRUE));
        $this -> Twig -> addExtension(new Twig_Extension_Core(TRUE));
        $this -> Twig -> addExtension(new ViewHelper_App_Twig_Extension());
	}
	
	public function setTmpl($tmpl) {
		$this -> tmpl = $tmpl;
	}

	public function output() {
		try {
			//$this->tmpl =  self::TMPL_FILE_NAME_4_DEBUG;
			
			LogManager::debug($this -> Twig -> loadTemplate($this -> tmpl));
			echo $this -> Twig -> loadTemplate($this -> tmpl) -> render($this -> data);
		} catch (Exception $e) {
			throw new Exception_Output($e -> getMessage() . $e -> getTraceAsString());
		}
	}
}