<?php

namespace media\view;

class ViewIndex extends ViewMain {

	public function __construct($evenements, $links,$eventVTT,$eventMarathon) { 
		parent::__construct();

		$this->layout = 'accueil.html.twig'; 
		$this->arrayVar['evenements'] = $evenements;
		$this->arrayVar['eventVTT'] = $eventVTT;
		$this->arrayVar['eventMarathon'] = $eventMarathon;
		//$this->arrayVar['evenementsCat1'] = $evenementsCat1;
		$this->arrayVar['links'] = $links;
		// $this->arrayVar['organisateur'] = $infoOrganisateur;
	}
} 