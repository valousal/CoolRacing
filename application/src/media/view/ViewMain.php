<?php
namespace media\view;

abstract class ViewMain extends AbstractView{

	public function __construct(){ 
			$app = \Slim\Slim::getInstance();

			// Get request object
			$req = $app->request;
			//Get root URI
			$rootUri = $app->request->getUrl();
			$rootUri .= $app->request->getRootUri();
			$this->addVar("rootUri", $rootUri);

			$this->addVar("action_connect", "http://coolracing/service/oauth/form_signin?url_redirect=http://coolracing/application/oauth/jeton&id_client=coolracing");

			if (isset($_SESSION['is_logged'])){ //acessToken
				$this->addVar('session', $_SESSION['is_logged']);
			}

			if (isset($_SESSION['type']) && $_SESSION['type'] == 'Organisateur'){ //acessToken
				$this->addVar('type', 'true');
			}else if (isset($_SESSION['type']) && $_SESSION['type'] == 'Participant'){
				$this->addVar('type', 'false');
			}

			$disconnect = $app->urlFor('deconnection');
			$this->addVar("deconnection", $disconnect);
	}

}