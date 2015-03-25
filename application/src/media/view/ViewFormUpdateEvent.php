<?php

namespace media\view;

class ViewFormUpdateEvent extends ViewMain {
	//protected $evenement;

	public function __construct($cats,$infoEvent) { 
		parent::__construct();

		$this->layout = 'formModifEvent.html.twig'; 
		$this->arrayVar['action'] = \Slim\Slim::getInstance()->urlFor('traitement_update_event', array('id' => $infoEvent['evenement']['id']));
		$this->arrayVar['cats'] = $cats;
		$this->arrayVar['infoEvent'] = $infoEvent['evenement'];
		//var_dump($infoEvent);
		/*echo $t;*/
	}
} 