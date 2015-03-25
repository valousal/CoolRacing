<?php

namespace media\view;

class ViewRegistrationOK extends ViewMain {

	public function __construct($client_access_token) { 
		parent::__construct();

		$this->arrayVar['client_access_token'] = $client_access_token;
		$this->layout = 'registrationOK.html.twig'; 
	}
} 