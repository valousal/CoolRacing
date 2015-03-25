<?php

namespace media\view;

abstract class View{
	protected $layout =null ;
	protected $obj ;
	protected $arrayVar ;

	public function __construct($o=null) { $this->obj = $o ; }

	public function addVar($var, $val) { $this->arrayVar[$var]=$val; }

	public function render() {
		$loader = new \Twig_Loader_Filesystem('src/media/view/templates');
		$twig = new \Twig_Environment( $loader , array('debug' => true));
		
		$tmpl = $twig->loadTemplate($this->layout);
		return $tmpl->render($this->arrayVar); 
	}
		

	public function display() { 
		echo $this->render() ; 
	}
}


