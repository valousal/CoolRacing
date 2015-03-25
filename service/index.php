<?php
session_start();
session_regenerate_id();

require "vendor/autoload.php";
use \media\controller ;
use \media\modele\DataBaseConnect ;
use \media\modele\Organisateurs ;
use \media\modele\Participants ;
use \media\modele\Event ;
use \media\modele\Participe ;
use \media\modele\OauthToken ;
use \media\modele\OauthClient ;
use \media\modele\OauthCode ;

$app = new \Slim\Slim(); //Slim init

DataBaseConnect::setConfig("config/config.ini"); 


/*********************************************************************/
/******************************API REST*******************************/
/*********************************************************************/
$checkToken = function (){ //token envoyé en entête de header

    return function()
    { //Récupérer le token dans le header avec Bearer et non dans le get. Plus secure (du coup le faire passer dans le header lors de l'appel client)
        $app = \Slim\Slim::getInstance();

        if(!isset($_GET['accessToken']) || empty($_GET['accessToken'])){
            $app->response->headers->set('Content-type','application/json') ;
            $app->halt(500, json_encode(array("erreur_message"=>'Access Token is missing')));
            }else{
                $tokenAccess = $_GET['accessToken'];
                $valid_tokenAccess = OauthToken::find($tokenAccess);
                if (count($valid_tokenAccess) != 1 || $valid_tokenAccess == null){
                    $app->response->headers->set('Content-type','application/json') ;
           			$app->halt(500, json_encode(array("erreur_message"=>'invalide Access Token')));
                }
            }
    };

};

//Token API client
$checkClientToken = function (){ //token envoyé en entête de header

    return function()
    { //Récupérer le token dans le header avec Bearer et non dans le get. Plus secure (du coup le faire passer dans le header lors de l'appel client)
        $app = \Slim\Slim::getInstance();

        if(!isset($_GET['client_access_token']) || empty($_GET['client_access_token'])){
            $app->response->headers->set('Content-type','application/json') ;
            $app->halt(500, json_encode(array("erreur_message"=>'Client Access Token is missing')));
            }else{
                $tokenAccess = $_GET['client_access_token'];
                $valid_tokenAccess = OauthClient::where('client_access_token', '=', $tokenAccess)->first();
                if (count($valid_tokenAccess) != 1 || $valid_tokenAccess == null){
                    $app->response->headers->set('Content-type','application/json') ;
           			$app->halt(500, json_encode(array("erreur_message"=>'invalide Client Access Token')));
                }
            }
    };

};

//Verifie si l'utilisateur a les bon droits
$haveScope = function ($scope){ //TODO : passer un array pour si il y 'a plusieurs scopes?
    return function() use($scope)
    {
        $app = \Slim\Slim::getInstance();
        if(!isset($_GET['accessToken']) || empty($_GET['accessToken'])){
            $app->response->headers->set('Content-type','application/json') ;
            $app->halt(500, json_encode(array("erreur_message"=>'Access Token is missing')));
        }else{
            $tokenAccess = $_GET['accessToken'];
            $valid_tokenAccess = OauthToken::find($tokenAccess);
            if (count($valid_tokenAccess) != 1 || $valid_tokenAccess == null){
	             $app->response->headers->set('Content-type','application/json') ;
	   			 $app->halt(500, json_encode(array("erreur_message"=>'invalide Access Token')));
            }
            if ($valid_tokenAccess->scope != $scope){
            	 $app->response->headers->set('Content-type','application/json') ;
       			 $app->halt(500, json_encode(array("erreur_message"=>"Invalid Scope")));
            }
        }
    };

};

$app->group('/api', $checkClientToken(), function () use ($app, $checkToken, $haveScope) {
		
	/***********************************************/
	/*				Participants 	   			*/
	/**********************************************/
	$app->group('/participants', function () use ($app, $checkToken, $haveScope) {
		$pc = new controller\ParticipantController;
		
		//Affiche données d'un participant
		$app->get('/:id', $checkToken(), $haveScope('Participant'), function($id) use ($app, $pc) {
			$pc->get($id);
		});
		
		//Récupère tous les partcipants
		$app->get('/', function() use ($app, $pc) {
			$pc->getAll();
		});
		
		//insère un nouveau participant
		$app->post('/', function() use($app, $pc){
			$pc->post();
		});

		//supprime un participant
		$app->delete('/:id', $checkToken(), $haveScope('Participant'),function($id) use ($app, $pc){
			$pc->delete($id);
		});

		//modifie un participant
		$app->put('/:id', $checkToken(), $haveScope('Participant'),function($id) use ($app, $pc){
			$pc->put($id);
		});

		//récupérer l'historique des évènements d'un participant
		$app->get('/:id/evenements', $checkToken(), $haveScope('Participant'), function($id) use ($app, $pc) {
			$pc->getEvent($id);
		});
	});





	/***********************************************/
	/*				Organisateurs 					*/
	/**********************************************/
	$app->group('/organisateurs', function () use ($app, $checkToken, $haveScope) {
		$oc = new controller\OrganisateurController;
		//Affiche données d'un organisateur
		$app->get('/:id', function($id) use ($app, $oc) { //ROUTE PRIVEE AND ROUTE PUBLIC
			$oc->get($id);
		});

		//Récupère tous les organisateurs
		$app->get('/', function() use ($app, $oc) {
			$oc->getAll();
		});

		//Récupère les évènements d'un organisateurs
		$app->get('/:id/evenements', function($id) use ($app, $oc) {
			$oc->getEventsOrg($id);
		});

		//Ajoute un organisateur
		$app->post('/', function() use ($app, $oc) {
			$oc->addOrg();
		});

		//Modifie un organisateur
		$app->put('/:id', $checkToken(), $haveScope('Organisateur'), function($id) use ($app, $oc) {
			$oc->putOrg($id);
		});

		//Supprime un organisateur
		$app->delete('/:id', $checkToken(), $haveScope('Organisateur'), function($id) use ($app, $oc) {
			$oc->deleteOrg($id);
		});
	});






	/***********************************************/
	/*				Evenements 					*/
	/**********************************************/
	$app->group('/evenements', function () use ($app, $checkToken, $haveScope) {
		$ec = new controller\EvenementsController;
		//Affiche données d'un evenements
		$app->get('/:id', function($id) use ($app, $ec) {
			$ec->get($id);
		});

		//Affiche tous les evenements
		$app->get('/', function() use ($app, $ec) {
			$ec->getAll();
		});

		//Ajoute un evenement
		$app->post('/',  $checkToken(), $haveScope('Organisateur'), function() use ($app, $ec) {
			$ec->addEvent();
		});

		//Modifie un evenement
		$app->put('/:id',  $checkToken(), $haveScope('Organisateur'), function($id) use ($app, $ec) {
			$ec->putEvent($id);
		});

		//Supprime un evenement
		$app->delete('/:id',  $checkToken(), $haveScope('Organisateur'), function($id) use ($app, $ec) {
			$ec->deleteEvent($id);
		});

		//Liste des participants d'un evenement
		$app->get('/:id/participants', function($id) use ($app, $ec) {
			$ec->getParticipantEvent($id);
		});

		//Classement d'un evenement
		$app->get('/:id/classement', function($id) use ($app, $ec) {
			$ec->getClassementEvent($id);
		});

		//Recuperer l'organisateur d'un evenement
		$app->get('/:id/organisateurs', function($id) use ($app, $ec) {
			$ec->getOrgaEvent($id);
		});

		//Recuperer le classement d'un participant à un evenement donnée
		$app->get('/:id_e/participants/:id_p/classement', function($id_e, $id_p) use ($app, $ec) {
			$ec->getClassementPartEvent($id_e,$id_p);
		});

		//inscrire un participant à un evenement
		$app->post('/:id_e/participants/', $checkToken(), $haveScope('Participant'), function($id_e) use ($app, $ec) { 
			$ec->postParticipantEvent($id_e);
		});


		/**********************************************************************/
	/***********************TODO********************************************/
	/************************************************************************/


		//changer le statut d'un evenement
		$app->put('/:id', $checkToken(), $haveScope('Organisateur'), function($id) use ($app, $ec) {
			$ec->putStatutEvent($id);
		});

		//Générer/mettre à jour le classement d'un évènement
		/*$app->put('/:id/classement/', $checkToken(), $haveScope('Organisateur'),   function($id) use ($app, $ec) {
			$ec->putClassementEvent($id);
		});*/
		$app->post('/:id/classement', function($idEvent) use ($app, $ec) {
			$ec->postClassementEvent($idEvent);
		});

		
	});

	/***********************************************/
	/*				Adresses					*/
	/**********************************************/
	$app->group('/adresses', function () use ($app) {
		$ac = new controller\AdressesController;

		//Affiche toutes les adresses
		$app->get('/', function() use ($app, $ac) {
			$ac->getAll();
		});

		//Modifie une adresse
		$app->put('/:id', function($id) use ($app, $ac) {
			$ac->put($id);
		});

		//Supprime une adresse
		$app->delete('/:id', function($id) use ($app, $ac) {
			$ac->delete($id);
		});
	});

	/***********************************************/
	/*				Categories				      */
	/**********************************************/
	$app->group('/categories', function () use ($app, $checkToken) {
		$cc = new controller\CategorieController;

		$app->get('/', function() use ($app, $cc) {
			$cc->getAll();
		});

		$app->get('/:id', function($id) use ($app, $cc) {
			$cc->getById($id);
		});

		$app->put('/:id', $checkToken(), function($id) use ($app, $cc) {
			$cc->put($id);
		});

		$app->post('/', $checkToken(), function() use ($app, $cc) {
			$cc->post();
		});

		$app->delete('/:id', $checkToken(), function($id) use ($app, $cc) {
			$cc->delete($id);
		});

		$app->get('/:id/evenements', function($id) use ($app, $cc) {
			$cc->getEvent($id);
		});

	});
});

  /**********************************************/
 /*				Oauth					       */
/**********************************************/
$app->group('/oauth', function () use ($app, $checkToken) {
	$ac = new controller\OauthController;

/*	$app->get('/', function() use ($app, $ac) { //a delete
		//$headers = $app->request->headers;
		//echo $headers['Bearer'];
		var_dump($app->request->headers->get("Authorization"));
    	var_dump($_SERVER["HTTP_AUTHORIZATION"]);
		//print_r($headers);
	});*/

	$app->get('/form_signin', function() use ($app, $ac) {
		$ac->form_signin();
	});

	$app->post('/signin', function() use ($app, $ac) {
		$ac->traitement_signin();
	})->name("traitement_signin");

	$app->get('/exangeTokenAccess', function() use ($app, $ac) {
		$ac->exangeTokenAccess();
	})->name("exangeTokenAccess");

	$app->get('/disconnect', $checkToken(), function() use ($app, $ac) {
		$ac->disconnect();
	})->name("disconnect");

	$app->get('/infos_users', $checkToken(), function() use ($app, $ac) {
		$ac->infos();
	})->name("infos_users");

	$app->post('/signinClient', function() use ($app, $ac) {
		$ac->signinClient();
	})->name("signinClient");
});


  /**********************************************/
 /*				Documentation			       */
/**********************************************/
$app->group('/doc', function () use ($app) {
	$dc = new controller\DocController;
	$app->get('/', function() use ($app, $dc) {
		$dc->displayIndex();
	});

	$app->get('/doc', function() use ($app, $dc) {
		$dc->displayDoc();
	});

	$app->get('/contact', function() use ($app, $dc) {
		$dc->displayContact();
	});

	$app->get('/signin', function() use ($app, $dc) { //TODO : mieux faire pour séparer gros client, de petit dev...
		$dc->signin();
	});
});

$app->run();
