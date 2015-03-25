<?php

namespace media\view;

class ViewError extends ViewMain {
	//protected $evenement;

	public function __construct($message) { 
		parent::__construct();

		$this->layout = 'error.html.twig'; 
		$this->arrayVar['message'] = $message;
	}
} 