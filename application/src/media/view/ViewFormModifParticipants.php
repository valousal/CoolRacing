<?php

namespace media\view;

class ViewFormModifParticipants extends ViewMain {
	//protected $evenement;

	public function __construct($infoPart) { 
		parent::__construct();

		$this->layout = 'FormModifParticipant.html.twig';
		$this->arrayVar['action'] = \Slim\Slim::getInstance()->urlFor('traitement_modif_participant'); 
		$this->arrayVar['infoPart'] = $infoPart;
		//var_dump($infoPart); echo$t;
	}
} 