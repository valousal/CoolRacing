<?php

namespace media\view;

class ViewInscriptionChoice extends ViewMain {

	public function __construct() { 
		parent::__construct();

		$this->layout = 'formSignupChoice.html.twig'; 
	}
} 