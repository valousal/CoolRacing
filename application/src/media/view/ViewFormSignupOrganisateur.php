<?php

namespace media\view;

class ViewFormSignupOrganisateur extends ViewMain {
	//protected $evenement;

	public function __construct($message = null) { 
		parent::__construct();

		$this->layout = 'formSignupOrganisateur.html.twig'; 
		$this->arrayVar['action'] = \Slim\Slim::getInstance()->urlFor('traitement_signup_participant');
		$this->arrayVar['message'] = $message;
	}
} 