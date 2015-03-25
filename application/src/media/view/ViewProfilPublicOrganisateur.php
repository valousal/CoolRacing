<?php

namespace media\view;

class ViewProfilPublicOrganisateur extends ViewMain {
	//protected $evenement;

	public function __construct($organisateur,$event,$event2) { 
		parent::__construct();

		$this->layout = 'organisateur.html.twig'; 
		$this->arrayVar['events'] = $event;
		$this->arrayVar['events2'] = $event2;
		$this->arrayVar['organisateur'] = $organisateur;
	}
} 