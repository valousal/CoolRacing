<?php
namespace media\controller ;
use \media\view ;



/****************************** GUZZLE *******************************/
use GuzzleHttp\Client; //Guzzle init - Obligatoire
use GuzzleHttp\EntityBody; //Recuperation Body reponse 
use GuzzleHttp\Event\CompleteEvent; //Requete parrallèle (voir docs)
use GuzzleHttp\Exception\RequestException; //Exception GUzzle

class OrganisateurController extends AbstractController{
	private $client;

	public function __construct(){
		if (isset($_SESSION['accessToken'])){
			$this->client = new Client([
			    'base_url' => 'http://coolracing/service/api/',
			    'defaults' => [
			        //'headers' => ['Foo' => 'Bar'],
			        'query'   => ['accessToken' => $_SESSION['accessToken'],'client_access_token' => '209592932550acf353b2528.70564490']
			        //'auth'    => ['username', 'password'],
			        //'proxy'   => 'tcp://localhost:80'
			    ]
			]);
		}else{
			$this->client = new Client([
			    'base_url' => 'http://coolracing/service/api/',
			    'defaults' => [
			        //'headers' => ['Foo' => 'Bar'],
			        'query'   => ['accessToken' => null,'client_access_token' => '209592932550acf353b2528.70564490']
			        //'auth'    => ['username', 'password'],
			        //'proxy'   => 'tcp://localhost:80'
			    ]
			]);
		}
	}


	public function profil(){
		//TODO : veriffication si le mec est log
		//TODO  : verifications si le mec à les droits pour accéder à ce profil (en gros si c'est un particpan auth et pas un orga ou un visiteur)


		$response = $this->client->get("organisateurs/".$_SESSION['id'].""); 
		$body = $response->getBody();
		if ($response->getStatusCode() == "200"){ //si la ressource existe bien
			$body = json_decode($body, true);
			$organisateur = $body['organisateur'];
			$links = $body['links'];

			//récupérer les évènements liés au organisateur
			$response = $this->client->get($links['href']); 
			if ($response->getStatusCode() == "200"){
				$body = $response->getBody();
				$body = json_decode($body, true);
				$events = $body['evenements'];

				$event = array();
				foreach ($events as $value){
					$event[] = $value['evenement'];

					/*foreach ($value['links'] as $key => $rel) {
						if($rel['rel'] == 'self'){
							$evenementLinkResponse = $client->get($rel['href']); 
							$evenementLink = json_decode($evenementLinkResponse->getBody(),true);
							$all['linkEvent'] = $organisateurLink;
						}
					}*/
				}


			}else{
				$message = "aucun evenement";
				$v = new view\ViewError($message);
				$v->display();
			}

			$v = new view\ViewProfilOrganisateur($organisateur,$event);
			$v->display();
		}else{
			$message = "Ressource n'existe pas";
			$v = new view\ViewError($message);
			$v->display();
		}
	}


	public function signup(){
		$v = new view\ViewFormSignupOrganisateur();
		$v->display();
	}


	public function signupTraitement(){
		$app = \Slim\Slim::getInstance();
		if ((!empty($_POST['nom_structure'])) && (!empty($_POST['forme_juridique'])) && (!empty( $_POST['mailPrivate']))&& (!empty( $_POST['login']))&& (!empty( $_POST['password']))&& (!empty( $_POST['nomResponsable']))&& (!empty($_POST['prenomResponsable']))&& (!empty($_POST['mailResponsable']))){ //si les variables existent
			try{
				$response = $this->client->post("organisateurs/", [
					'json' => [
						'nom_structure' => $_POST['nom_structure'],
						'forme_juridique' => $_POST['forme_juridique'],
						'mailPrivate' => $_POST['mailPrivate'],
						'login' => $_POST['login'],
						'password' => $_POST['password'],
						'nomResponsable' => $_POST['nomResponsable'],
						'prenomResponsable' => $_POST['prenomResponsable'],
						'mailResponsable' => $_POST['mailResponsable'],
						'tel' => $_POST['tel'],
						'mailPub' => $_POST['mailPub'],
						
						'adresse' => [
							'num' => $_POST['num'],
							'type' => $_POST['type'],
							'CP' => $_POST['CP'],
							'ville' => $_POST['ville'],
							'rue' => $_POST['rue'],
						]
					]
				]);
			$app->response->redirect($app->urlFor('accueil'));
			} catch (RequestException $e) {
			    echo $e->getRequest() . "\n";
			}
		
		}else{
			$message = "Champs incomplet(s) et/ou invalide(s)";
			$v = new view\ViewFormSignupOrganisateur($message);
			$v->display();
		}

	}


	public function profilPublic($id){

		$response = $this->client->get("organisateurs/".$id.""); 
		$body = $response->getBody();
		if ($response->getStatusCode() == "200"){ //si la ressource existe bien
			$body = json_decode($body, true);
			$organisateur = $body['organisateur'];
			$links = $body['links'];

			//récupérer les évènements liés au organisateur A VENIR
			$request = $this->client->createRequest('GET', "organisateurs/".$organisateur['id']."/evenements");
			$query = $request->getQuery();
			$query['statut'] = 'venir';
			$url = $request->getUrl(); 
			try{
				$response = $this->client->get($url);
				$body = $response->getBody();
				$body = json_decode($body, true);
				$events = $body['evenements'];
				$event = array();
				foreach ($events as $value){
					$event[] = $value['evenement'];
				}

			}catch (RequestException $e) {
			    $event = false;
			}

			//récupérer les évènements liés au organisateur TERMINE
			$request2 = $this->client->createRequest('GET', "organisateurs/".$organisateur['id']."/evenements");
			$query2 = $request->getQuery();
			$query2['statut'] = 'termine';
			$url2 = $request->getUrl();
			try{
				$response2 = $this->client->get($url2);
				$body2 = $response2->getBody();
				$body2 = json_decode($body2, true);
				$events2 = $body2['evenements'];
				$event2 = array();
				foreach ($events2 as $value2){
					$event2[] = $value2['evenement'];
				}
			}catch (RequestException $e) {
			    $event2 = false;
			}

		$v = new view\ViewProfilPublicOrganisateur($organisateur,$event,$event2);
		$v->display();
		}else{
			//appeler une vue pour 404 not found
			//ERREUR
		}
	}


	public function profilEvenements(){
		$response = $this->client->get("organisateurs/".$_SESSION['id']."/evenements"); 
		try{
			$body = $response->getBody();
			$body = json_decode($body, true);
			$events = $body['evenements'];

			$event = array();
			foreach ($events as $value){
				$event[] = $value['evenement'];
			}
			
			$v = new view\ViewEventsOrganisateur($event);
			$v->display();
		}catch (RequestException $e) {
		    $message = "Aucun evenements";
			$v = new view\ViewError($message);
			$v->display();
		}
	}

	
}