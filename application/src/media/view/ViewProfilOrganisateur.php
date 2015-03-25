<?php

namespace media\view;

class ViewProfilOrganisateur extends ViewMain {
	//protected $evenement;

	public function __construct($organisateur, $event) { 
		parent::__construct();

		$this->layout = 'profilOrganisateur.html.twig'; 
		$this->arrayVar['organisateur'] = $organisateur;
		$this->arrayVar['events'] = $event;
	}
} 