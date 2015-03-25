<?php

namespace media\view;

class ViewDoc extends ViewMain {

	public function __construct() { 
		parent::__construct();

		$this->layout = 'doc.html.twig'; 
	}
} 