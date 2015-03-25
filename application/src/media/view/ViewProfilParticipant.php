<?php

namespace media\view;

class ViewProfilParticipant extends ViewMain {
	//protected $evenement;

	public function __construct($participant, $event) { 
		parent::__construct();

		$this->layout = 'profilParticipant.html.twig'; 
		$this->arrayVar['participant'] = $participant;
		$this->arrayVar['events'] = $event;
		
	}
} 