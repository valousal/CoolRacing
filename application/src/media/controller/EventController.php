<?php
namespace media\controller ;
use \media\view ;



/****************************** GUZZLE *******************************/
use GuzzleHttp\Client; //Guzzle init - Obligatoire
use GuzzleHttp\EntityBody; //Recuperation Body reponse 
use GuzzleHttp\Event\CompleteEvent; //Requete parrallèle (voir docs)
use GuzzleHttp\Exception\RequestException; //Exception GUzzle



class EventController extends AbstractController{
	
	private $client;

	public function __construct(){
		if (isset($_SESSION['accessToken'])){
			$this->client = new Client([
			    'base_url' => 'http://coolracing/service/api/',
			    'defaults' => [
			        //'headers' => ['Foo' => 'Bar'],
			        'query'   => ['accessToken' => $_SESSION['accessToken'], 'client_access_token' => '209592932550acf353b2528.70564490']
			        //'auth'    => ['username', 'password'],
			        //'proxy'   => 'tcp://localhost:80'
			    ]
			]);
		}else{
			$this->client = new Client([
			    'base_url' => 'http://coolracing/service/api/',
			    'defaults' => [
			        //'headers' => ['Foo' => 'Bar'],
			        'query'   => ['accessToken' => 'null','client_access_token' => '209592932550acf353b2528.70564490']
			        //'auth'    => ['username', 'password'],
			        //'proxy'   => 'tcp://localhost:80'
			    ]
			]);
		}
	}	


	//One Event
	public function displayEvent($id){
		try{ //si la ressource existe bien
			$response = $this->client->get("evenements/$id");
			$body = $response->getBody();

			$body = json_decode($body, true);
			//print_r($body);

			$evenement = $body['evenement'];
			$dateCloture = $body['evenement']['dateCloture'];
			$dateOuverture = $body['evenement']['dateOuverture'];
			$dateEvenement = $body['evenement']['dateEvenement'];
			$date = date('Y-m-d');
			if ($dateCloture > $date && $dateOuverture < $date){
				$dateInscription = true;
			}else{
				$dateInscription = false;
			}
			
			if($dateEvenement < $date){
				$dateEvenement = true;
			}else{
				$dateEvenement = false;
			}
			$links = $body['links'];
			foreach ($links as $key => $link) {
				if($link['rel'] == 'organisateur'){
					$organisateurLinkResponse = $this->client->get($link['href']); 
					$organisateurLink = json_decode($organisateurLinkResponse->getBody(),true);
					$infoOrganisateur = $organisateurLink['organisateur'];
				}

				/*if($link['rel'] == 'participants'){
					$participantLinkResponse = $this->client->get($link['href']);
					$participantLink = json_decode($participantLinkResponse->getBody(),true);
					$infoParticipant = $participantLink['participants'];
				}*/
			}

			if (isset($_SESSION['id']) && $_SESSION['type'] == 'Organisateur'){
				$idOrga = $_SESSION['id'];
			}else{
				$idOrga = null;
			}
			try{
				$modifier = false;
				$response = $this->client->get("organisateurs/$idOrga/evenements"); 
				$body = $response->getBody();
				$body = json_decode($body, true);
				$evenements = $body['evenements'];
				foreach ($evenements as $evenementt){
					if (in_array($id, $evenementt['evenement'])) {
						$modifier = true;
					}
				}
			}catch (RequestException $e){
				$modifier = false;
			}



			$v = new view\ViewEvent($evenement,$infoOrganisateur,$dateInscription, $dateEvenement, $modifier); //$infoParticipant
			$v->display();
		}catch (RequestException $e){
			//appeler une vue pour 404 not found
			$message = "Ressource n'existe pas";
			$v = new view\ViewError($message);
			$v->display();
		}
	}

	//All Event
	public function displayAllEvent(){
		$request = $this->client->createRequest('GET', 'evenements/');
		$query = $request->getQuery();
		if(isset($_GET['tags'])){
			$query['tags'] = $_GET['tags'];
		}
		if(isset($_GET['price'])){
			$query['price'] = $_GET['price'];
		}
		if(isset($_GET['cat'])){
			$query['cat'] = $_GET['cat'];
		}
		if(isset($_GET['statut'])){
			$query['statut'] = $_GET['statut'];
		}
		$url = $request->getUrl(); 
		try{ //si la ressource existe bien
			$response = $this->client->get($url);
			$body = $response->getBody();
			$body = json_decode($body, true);
			$evenements = $body['evenements'];
			$links = $body['links'];
			$bool = 'WithoutCategorie';

			
			//Récupere les links des evenements (organisateur et self)
			foreach($evenements as $value){
				$all['event'] = $value['evenement'];
				foreach ($value['links'] as $key => $rel) {
					if($rel['rel'] == 'organisateur'){
						$organisateurLinkResponse = $this->client->get($rel['href']); // ----------------------ecrase la valeur :(
						$organisateurLink = json_decode($organisateurLinkResponse->getBody(),true);
						$all['linkOrg'] = $organisateurLink;
					}
				}
				$event[] = $all;
			} 


			$v = new view\ViewSearch($links,$bool,$event);
			$v->display();
		}catch (RequestException $e) {
		    $message = 'Aucun evenements pour cette recherche';
		    $v = new view\ViewError($message);
			$v->display();
		}
	}


	//Inscrire participant à un evenement
	public function addFormParticipantEvent($id){
		$response = $this->client->get("evenements/$id");
		$body = $response->getBody();
		$body = json_decode($body, true);
		$dateOuverture = $body['evenement']['dateOuverture'];
		$dateCloture = $body['evenement']['dateCloture'];
		$date = date('Y-m-d');
		if($dateOuverture < $date && $dateCloture > $date){
			$v = new view\ViewFormInscriptionParticipant();
			$v->display();
		}else{
			$message = 'Les inscriptions ne sont encore pas ouvertes';
		    $v = new view\ViewError($message);
			$v->display();
		}
	}

	//Traitement Inscrire participant à un evenement
	public function addParticipantEvent($id){
		$app = \Slim\Slim::getInstance();
		if ((!empty($_POST['club']))){ //&& (!empty($_POST['session'])) && (!empty( $_POST['sas']))
			try{
				$response = $this->client->post("evenements/$id/participants/", [
					'json' => [
						'club' => $_POST['club'],
						//'sessionDepart' => $_POST['session'],
						//'sasDepart' => $_POST['sas'],
						'id_participant' => $_SESSION['id']
					]
				]);
			$app->response->redirect($app->urlFor('accueil'));
			} catch (RequestException $e) {
			    echo $e->getRequest() . "\n";
			     if ($e->hasResponse()) {
        			echo $e->getResponse();
    			}
			}
		
		}else{
			echo 'Champs incomplets';
		}
	}


	//Créer un evenement
	public function addFormEvent(){
		$response = $this->client->get("categories/"); 
		$body = $response->getBody();
		if ($response->getStatusCode() == "200"){ //si la ressource existe bien
			$body = json_decode($body, true);
			$categories = $body['categories'];

			foreach ($categories as $key => $value) {
				$cats[] = $value['nom'];
			}

			$v = new view\ViewFormCreateEvent($cats);
			$v->display();
		}else{
			echo 'statuts code != 200';
		}
	}

	//Traitement Créer un evenement
	public function addEvent(){
		$app = \Slim\Slim::getInstance();
		if ((!empty($_POST['titre'])) && (!empty($_POST['description'])) && (!empty( $_POST['lieu']))&& (!empty( $_POST['dateCloture']))&& (!empty( $_POST['dateOuverture']))&& (!empty( $_POST['dateEvenement']))&& (!empty($_POST['nombrePlace']))&& (!empty($_POST['prix']))&& (!empty($_POST['distance']))){ //si les variables existent
			try{
				$response = $this->client->post("evenements/", [
					'json' => [
						'titre' => $_POST['titre'],
						'description' => $_POST['description'],
						'lieu' => $_POST['lieu'],
						'dateCloture' => $_POST['dateCloture'],
						'dateOuverture' => $_POST['dateOuverture'],
						'dateEvenement' => $_POST['dateEvenement'],
						'prix' => $_POST['prix'],
						'distance' => $_POST['distance'],
						'nombrePlace' => $_POST['nombrePlace'],
						'categorie' => $_POST['categorie'],
						'id_organisateur' => $_SESSION['id']
					]
				]);
				
				//$response = $response->getBody();
				//echo $response;
				//echo json_decode($response, true);	
				
				//$app->response->redirect($app->urlFor('event', array('id' => $id_annonce)));
				$app->response->redirect($app->urlFor('allEvent'));
			} catch (RequestException $e) {
			    echo $e->getRequest() . "\n";
			}
		
		}else{
			echo 'Champs incomplets';
		}
	}



	//Classement de l'évenement
	public function classementEvent($id){
		try{
			$response = $this->client->get("evenements/$id/classement"); 
			$body = $response->getBody();
			$classement = json_decode($body, true);
			/*$dateEvenement = $classement['evenement']['dateEvenement'];
			$date = date('Y-m-d');
			if($dateEvenement < $date){
				$dateEvenement = true;
			}else{
				$dateEvenement = false;
			}*/

			$v = new view\ViewClassementEvent($classement);
			$v->display();
		}catch (RequestException $e) {
			$message = 'Aucun classement pour cet evenement';
		    $v = new view\ViewError($message);
			$v->display();
		}
	}



	//update un evenement
	public function updateFormEvent($id){

		//test si l'organisateur est bien le créateur de l'évènement
		$idOrga = $_SESSION['id'];
		try{
			$response = $this->client->get("organisateurs/$idOrga/evenements"); 
			$body = $response->getBody();
			$body = json_decode($body, true);
			$evenements = $body['evenements'];
			$modifier = false;
			foreach ($evenements as $evenement){			
				if (in_array($id, $evenement['evenement'])) { //Si l'organisateur est bien le créateur de l'évènement
				   $modifier = true;
				}
			}
			if ($modifier == false) {
				$v = new view\ViewError("Vous n'êtes pas authorisé à modifier cette évènement");
				$v->display();
				exit();
			}else {
				 try{ //si la ressource existe bien
					$response = $this->client->get("categories/"); 
					$body = $response->getBody();
					$body = json_decode($body, true);
					$categories = $body['categories'];

					$response2 = $this->client->get("evenements/$id"); 
					$body2 = $response2->getBody();
					$infoEvent = json_decode($body2, true);

					foreach ($categories as $key => $value) {
						$cats[] = $value['nom'];
					}

					$v = new view\ViewFormUpdateEvent($cats, $infoEvent);
					$v->display();
				}catch (RequestException $e) {
					$message = "Problèmes, veuillez nous contacter";
				    $v = new view\ViewError($message);
					$v->display();
					exit();
				}
			}
		}catch (RequestException $e) {
			$message = "Problèmes, veuillez nous contacter";
		    $v = new view\ViewError($message);
			$v->display();
			exit();
		}
	}

	//Traitement Créer un evenement
	public function updateEvent($id){
		$app = \Slim\Slim::getInstance();
		if ((!empty($_POST['titre'])) && (!empty($_POST['description'])) && (!empty( $_POST['lieu']))&& (!empty( $_POST['dateCloture']))&& (!empty( $_POST['dateOuverture']))&& (!empty( $_POST['dateEvenement']))&& (!empty($_POST['nombrePlace']))&& (!empty($_POST['prix']))&& (!empty($_POST['distance']))){ //si les variables existent
			try{
				$response = $this->client->put("evenements/$id", [
					'json' => [
						'titre' => $_POST['titre'],
						'description' => $_POST['description'],
						'lieu' => $_POST['lieu'],
						'dateCloture' => $_POST['dateCloture'],
						'dateOuverture' => $_POST['dateOuverture'],
						'dateEvenement' => $_POST['dateEvenement'],
						'prix' => $_POST['prix'],
						'distance' => $_POST['distance'],
						'nombrePlace' => $_POST['nombrePlace'],
						'categorie' => $_POST['categorie'],
						'id_organisateur' => $_SESSION['id']
					]
				]);
				
				//$response = $response->getBody();
				//echo $response;
				//echo json_decode($response, true);	
				
				$app->response->redirect($app->urlFor('event', array('id' => $id)));
				//$app->response->redirect($app->urlFor('allEvent'));
			} catch (RequestException $e) {
			   /*echo $e->getRequest() . "\n";
			     if ($e->hasResponse()) {
        			echo $e->getResponse();
    			}*/
    			$message = "Problème sur votre modification, veuillez nous contacter";
			    $v = new view\ViewError($message);
				$v->display();
			}
		
		}else{
			echo 'Champs incomplets';
		}
	}


	//All EventBy Cat
	/*public function displayAllEventByCat($id){
		try{ //si la ressource existe bien
			$response = $this->client->get("categories/".$id."/evenements"); 
			$body = $response->getBody();
			$body = json_decode($body, true);
			$evenements = $body['evenements'];
			$links = $body['links'];
			$bool = 'WithCategorie';

			//Récupere les links des evenements (organisateur et self)
			
			//PAS DE LIEN VERS ORGANISATEUR DANS LE JSON RECU...... ALORS LUCAS !!!

			$v = new view\ViewEvents($evenements,$links,$bool);
			$v->display();
		}catch (RequestException $e){
			$message = "ERROR";
			$v = new view\ViewError($message);
			$v->display();
		}
	}*/
}