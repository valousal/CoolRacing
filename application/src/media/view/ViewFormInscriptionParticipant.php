<?php

namespace media\view;

class ViewFormInscriptionParticipant extends ViewMain {
	//protected $evenement;

	public function __construct() { 
		parent::__construct();

		$this->layout = 'formInscriptionParticipant.html.twig'; 
		
	}
} 