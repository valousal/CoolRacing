<?php

namespace media\view;

class ViewFormSignupParticipants extends ViewMain {
	//protected $evenement;

	public function __construct($message = null) { 
		parent::__construct();

		$this->layout = 'FormSignupParticipants.html.twig';
		$this->arrayVar['action'] = \Slim\Slim::getInstance()->urlFor('traitement_signup_organisateur'); 
		$this->arrayVar['message'] = $message; 
		
	}
} 