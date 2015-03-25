<?php
session_start();
session_regenerate_id();
if(!isset($_SESSION['type'])){
	$_SESSION['type'] = 'Visiteur';
}

/****************************** REQUIRE *******************************/
require "vendor/autoload.php";
use \media\controller ;
use \media\view ;


/****************************** SLIM *******************************/ 
$app = new \Slim\Slim(); //Slim init


/****************************** GUZZLE *******************************/
use GuzzleHttp\Client; //Guzzle init - Obligatoire
use GuzzleHttp\EntityBody; //Recuperation Body reponse 
use GuzzleHttp\Event\CompleteEvent; //Requete parrallèle (voir docs)




$checkType = function ($type){ 

    return function() use ($type)
    { 
    	$app = \Slim\Slim::getInstance();
    	if ($_SESSION['type'] != $type) {
    		$message = "Vous n'êtes pas autorisé à acceder à cette page";
    		$c = new controller\MainController;
			$c->displayError($message);
			exit();
        }
    };

};


/*********************************************************************/
/****************************** ROUTING *******************************/
/*********************************************************************/
// Page d'accueil, index
$app->get('/', function() use ($app) {
	$c = new controller\MainController;
	$c->displayIndex();
})->name('accueil');


// Page d'accueil, index
$app->get('/error', function() use ($app) {
	$c = new controller\MainController;
	$c->displayError();
})->name('error');

// Inscription
$app->get('/inscription', $checkType('Visiteur'), function() use ($app) {
	$c = new controller\MainController;
	$c->signupChoice();
});



/***********************************************/
/*				Evenements 	   				*/
/**********************************************/
$app->group('/evenements', function () use ($app, $checkType) {
	$ec = new controller\EventController;

	//Affiche un evenement 
	$app->get('/:id',  function($id) use ($app,$ec) {
		$ec->displayEvent($id);
	})->name('event');

	//Affiche tous les evenement  -- orderBy ? limit ?
	$app->get('/',function() use ($app,$ec) {
		$ec->displayAllEvent();
	})->name('allEvent');

	//Inscrire participants à un evenement
	$app->get('/:id/subscribe', $checkType('Participant'), function($id) use ($app,$ec) {
		$ec->addFormParticipantEvent($id);
	});

	//Traitement Inscrire participants à un evenement
	$app->post('/:id/subscribe', function($id) use ($app,$ec) {
		$ec->addParticipantEvent($id);
	});

	//Create event
	$app->get('/create/event', $checkType('Organisateur'), function() use ($app,$ec) {
		$ec->addFormEvent();
	});

	//update event
	$app->get('/:id/update/', $checkType('Organisateur'), function($id) use ($app,$ec) {
		$ec->updateFormEvent($id);
	});

	//Traitement Create event
	$app->post('/create/event', function() use ($app,$ec) {
		$ec->addEvent();
	})->name('traitement_create_event');

	//Traitement update event
	$app->put('/:id/update', function($id) use ($app,$ec) {
		$ec->updateEvent($id);
	})->name('traitement_update_event');

	//Classement de l'event
	$app->get('/:id/classement', function($id) use ($app,$ec) {
		$ec->classementEvent($id);
	});



});




/***********************************************/
/*				Categories  	   				*/
/**********************************************/
$app->group('/categories', function () use ($app,$checkType) {
	$cc = new controller\CategorieController;

	//Affiche tous les evenement en fct d'une categorie 
	$app->get('/:id/evenements', function($id) use ($app,$cc) {
		$cc->displayAllEventByCat($id);
	});

});


/***********************************************/
/*				Other Pages    				*/
/**********************************************/

$app->get('/faq', function(){
	$c = new controller\MainController;
	$c->displayFaq();
})->name('F.A.Q');

$app->get('/mentions', function(){
	$c = new controller\MainController;
	$c->displayMentions();
})->name('mentions');


/***********************************************/
/*				Organisateur  	   				*/
/**********************************************/
$app->group('/organisateurs', function () use ($app,$checkType) {
	$oc = new controller\OrganisateurController;

	//Affiche son profil (private)
	$app->get('/profil/', $checkType('Organisateur'), function() use ($app,$oc) {
		$oc->profil();
	});

	//Affiche son profil (private)
	$app->get('/profil/evenements', $checkType('Organisateur'), function() use ($app,$oc) {
		$oc->profilEvenements();
	});

	//Formulaire pour s'enregistrer
	$app->get('/signup', $checkType('Visiteur'), function() use ($app,$oc) {
		$oc->signup();
	});

	//Traitement pour s'enregistrer
	$app->post('/signup', function() use ($app,$oc) {
		$oc->signupTraitement();
	})->name('traitement_signup_participant');

	//Affiche son profil (public)
	$app->get('/profil/:id',  function($id) use ($app,$oc) {
		$oc->profilPublic($id);
	});

});


/***********************************************/
/*				Participants  	   				*/
/**********************************************/
$app->group('/participants', function () use ($app,$checkType) {
	$pc = new controller\ParticipantController;

	//Formulaire pour s'enregistrer
	$app->get('/signup', $checkType('Visiteur'), function() use ($app,$pc) {
		$pc->signup();
	});

	//Traitement pour s'enregistrer
	$app->post('/signup',  function() use ($app,$pc) {
		$pc->signupTraitement();
	})->name('traitement_signup_organisateur');

	//Affiche son profil
	$app->get('/profil', $checkType('Participant'), function() use ($app,$pc) {
		$pc->profil();
	})->name('profil_participant');

	//Affiche ses épreuves
	$app->get('/profil/evenements', $checkType('Participant'), function() use ($app,$pc) {
		$pc->evenementPart();
	});


	//Formulaire pour s'update
	$app->get('/update', $checkType('Participant'), function() use ($app,$pc) {
		$pc->update();
	});

	//Traitement pour s'update
	$app->put('/update',  function() use ($app,$pc) {
		$pc->updateTraitement();
	})->name('traitement_modif_participant');
});	

/***********************************************/
/*				Connexion 	   				*/
/**********************************************/
$app->group('/oauth', function () use ($app,$checkType) {
	$oauthc = new controller\OauthController;

	$app->get('/jeton', $checkType('Visiteur'),function() use ($app,$oauthc) {
		$oauthc->jeton();
	});

	$app->get('/access_token', function() use ($app,$oauthc) {
		$oauthc->token();
	});

	$app->get('/deconnection', function() use ($app,$oauthc) {
		$oauthc->deconnection();
	})->name('deconnection');

});

$app->run();