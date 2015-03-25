<?php

namespace media\view;

class ViewSearch extends ViewMain {
	//protected $evenement;

	public function __construct($links,$bool,$event) { 
		parent::__construct();

		$this->layout = 'recherche.html.twig'; 
		
		if ($bool == 'WithoutCategorie'){
			$this->arrayVar['events'] = $event;
			$this->arrayVar['links'] = $links;
		}

		if ($bool == 'WithCategorie'){
			$this->arrayVar['events'] = $evenements;
			$this->arrayVar['links'] = $links;
		}
	}
} 