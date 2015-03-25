<?php

namespace media\view;

class ViewEvent extends ViewMain {
	//protected $evenement;

	public function __construct($evenement,$infoOrganisateur,$dateInscription, $dateEvenement, $modifier) { //infoParticipant
		parent::__construct();

		$this->layout = 'evenement.html.twig'; 
		$this->arrayVar['event'] = $evenement;
		$this->arrayVar['organisateur'] = $infoOrganisateur;
		//$this->arrayVar['participants'] = $infoParticipant;

		$this->arrayVar['dateInscription'] = $dateInscription;
		$this->arrayVar['dateEvenement'] = $dateEvenement;
		$this->arrayVar['modifier'] = $modifier;

		
	}
} 