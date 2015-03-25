<?php

namespace media\view;
use \media\script\CRSFGuard ;

class ViewSignInClient extends ViewMain {

	public function __construct() { 
		parent::__construct();

		$this->arrayVar['traitement'] = \Slim\Slim::getInstance()->urlFor('signinClient');
		$this->layout = 'signinclient.html.twig'; 
	}
	public function render() {
		$res = parent::render();
		$crsfGuard = new CRSFGuard();
		$res = $crsfGuard::csrfguard_replace_forms($res);

		return $res;
	}
} 