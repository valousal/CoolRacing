<?php
namespace media\controller ;
use \media\view ;



/****************************** GUZZLE *******************************/
use GuzzleHttp\Client; //Guzzle init - Obligatoire
use GuzzleHttp\EntityBody; //Recuperation Body reponse 
use GuzzleHttp\Event\CompleteEvent; //Requete parrallèle (voir docs)


class MainController extends AbstractController{

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
			        'query'   => ['accessToken' => 'null', 'client_access_token' => '209592932550acf353b2528.70564490']
			        //'auth'    => ['username', 'password'],
			        //'proxy'   => 'tcp://localhost:80'
			    ]
			]);
		}
	}	

	public function displayIndex(){
		//EVENT BY CAT
		// $id = 1; // ID 1 == VTT 
		// $response2 = $this->client->get("categories/$id/evenements"); 
		// $body2 = $response2->getBody();
		// if($response2->getStatusCode() == "200"){
		// 	$body2 = json_decode($body2, true);
		// 	$evenementsCat1 = $body2['evenements'];
		// 	foreach($evenementsCat1 as $value2){
		// 		$all2['event'] = $value2['evenement'];
		// 		foreach ($value2['links'] as $key => $rel) {
		// 			if($rel['rel'] == 'organisateur'){
		// 				$organisateurLinkResponse = $this->client->get($rel['href']); 
		// 				$organisateurLink = json_decode($organisateurLinkResponse->getBody(),true);
		// 				$all2['linkOrg'] = $organisateurLink;
		// 			}
		// 		}
		// 		$evenementsCat1[] = $all2;
		// 	}
		// }


		//récupérer les évènements de cette categorie VTT
		$request = $this->client->createRequest('GET', "evenements");
		$query = $request->getQuery();
		$query['cat'] = 'VTT';
		$url = $request->getUrl(); 
		try{
			$response = $this->client->get($url);
			$body = $response->getBody();
			$body = json_decode($body, true);
			$events = $body['evenements'];
			$eventVTT = array();
			foreach ($events as $value){
				$eventVTT[] = $value['evenement'];
			}

		}catch (RequestException $e) {
		    $eventVTT = false;
		}

		//récupérer les évènements de cette categorie VTT
		$request = $this->client->createRequest('GET', "evenements");
		$query = $request->getQuery();
		$query['cat'] = 'Marathon';
		$url = $request->getUrl(); 
		try{
			$response = $this->client->get($url);
			$body = $response->getBody();
			$body = json_decode($body, true);
			$events = $body['evenements'];
			$eventMarathon = array();
			foreach ($events as $value){
				$eventMarathon[] = $value['evenement'];
			}

		}catch (RequestException $e) {
		    $eventMarathon = false;
		}

		//ALL EVENT
		$response = $this->client->get("evenements/"); 
		$body = $response->getBody();
		if ($response->getStatusCode() == "200"){ //si la ressource existe bien
			$body = json_decode($body, true);
			$evenements = $body['evenements'];
			$links = $body['links'];

			
			//Récupere les links des evenements (organisateur et self)
			// foreach($evenements as $value){
			// 	$all['event'] = $value['evenement'];
			// 	foreach ($value['links'] as $key => $rel) {
			// 		if($rel['rel'] == 'organisateur'){
			// 			$organisateurLinkResponse = $this->client->get($rel['href']); 
			// 			$organisateurLink = json_decode($organisateurLinkResponse->getBody(),true);
			// 			$all['linkOrg'] = $organisateurLink;
			// 		}
			// 	}
			// 	$evenements[] = $all;
			// }


			$v = new view\ViewIndex($evenements, $links,$eventVTT, $eventMarathon);
			$v->display();
		}else{
			//appeler une vue pour 404 not found
		}
	}


	public function displayError($message){
		$v = new view\ViewError($message);
		$v->display();
	}

	public function displayFaq(){
		$v = new view\ViewFaq();
		$v->display();
	}

	public function displayMentions(){
		$v = new view\ViewMentions();
		$v->display();
	}

	public function signupChoice(){
		$v = new view\ViewInscriptionChoice();
		$v->display();
	}

}