<?php

namespace media\view;

class ViewEventsOrganisateur extends ViewMain {
	//protected $evenement;

	public function __construct($event) { 
		parent::__construct();

		$this->layout = 'evenementsOrganisateur.html.twig';
		$this->arrayVar['events'] = $event;
		// var_dump($event); echo$t;
	}
} 