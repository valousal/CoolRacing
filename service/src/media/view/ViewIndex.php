<?php

namespace media\view;

class ViewIndex extends ViewMain {

	public function __construct() { 
		parent::__construct();

		$this->layout = 'index.html.twig'; 
	}
} 