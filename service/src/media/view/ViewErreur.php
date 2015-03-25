<?php

namespace media\view;

class ViewErreur extends ViewMain {

	public function __construct($message='erreur') { 
		parent::__construct();

		$this->layout = 'erreur.html.twig'; 
		$this->arrayVar['message'] =$message;
	}
} 