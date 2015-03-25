<?php

namespace media\view;

class ViewFAQ extends ViewMain {
	//protected $evenement;

	public function __construct() { 
		parent::__construct();

		$this->layout = 'faq.html.twig'; 
	}
} 