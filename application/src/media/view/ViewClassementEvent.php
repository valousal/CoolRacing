<?php

namespace media\view;

class ViewclassementEvent extends ViewMain {
	//protected $evenement;

	public function __construct($classement) { 
		parent::__construct();

		$this->layout = 'classementEvenement.html.twig'; 
		$this->arrayVar['classement'] = $classement;
		// $this->arrayVar['organisateur'] = $infoOrganisateur;
		// $this->arrayVar['participants'] = $infoParticipant;
	}
} 