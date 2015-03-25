<?php

namespace media\view;
use \media\script\CRSFGuard ;

class ViewFormSignin extends ViewMain {

	public function __construct($url_redirect, $id_client, $nameClient) { 
		parent::__construct();

		$this->arrayVar['traitement'] = \Slim\Slim::getInstance()->urlFor('traitement_signin');
		$this->arrayVar['url_redirect'] = $url_redirect;
		$this->arrayVar['id_client'] = $id_client;
		$this->arrayVar['nameClient'] = $nameClient;
		$this->layout = 'form_signin.html.twig'; 
	}

	public function render() {
		$res = parent::render();
		$crsfGuard = new CRSFGuard();
		$res = $crsfGuard::csrfguard_replace_forms($res);

		return $res;
	}
} 