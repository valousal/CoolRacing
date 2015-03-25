<?php

namespace media\view;

class ViewEvenementsParticipant extends ViewMain {
	//protected $evenement;

	public function __construct($evenements) { 
		parent::__construct();

		$this->layout = 'evenementsParticipants.html.twig'; 
		$this->arrayVar['events'] = $evenements;
		//var_dump($evenements);echo $t;

		
	}
} 