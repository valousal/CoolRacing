<?php

namespace media\view;

class ViewContact extends ViewMain {

	public function __construct() { 
		parent::__construct();

		$this->layout = 'contact.html.twig'; 
	}
} 