<?php

namespace media\view;

class ViewFormCreateEvent extends ViewMain {
	//protected $evenement;

	public function __construct($cats) { 
		parent::__construct();

		$this->layout = 'formCreateEvent.html.twig'; 
		$this->arrayVar['action'] = \Slim\Slim::getInstance()->urlFor('traitement_create_event');
		$this->arrayVar['cats'] = $cats;
	}
} 