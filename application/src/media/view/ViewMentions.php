<?php

namespace media\view;

class ViewMentions extends ViewMain {
	//protected $evenement;

	public function __construct() { 
		parent::__construct();

		$this->layout = 'mentions.html.twig'; 
	}
} 