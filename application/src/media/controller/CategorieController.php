<?php
namespace media\controller ;
use \media\view ;



/****************************** GUZZLE *******************************/
use GuzzleHttp\Client; //Guzzle init - Obligatoire
use GuzzleHttp\EntityBody; //Recuperation Body reponse 
use GuzzleHttp\Event\CompleteEvent; //Requete parrallèle (voir docs)

class CategorieController extends AbstractController{
	public function __construct(){

	}	


	//All EventBy Cat
	public function displayAllEventByCat($id){
		$client = new Client();
		$response = $client->get("http://coolracing/service/api/categories/".$id."/evenements"); 
		$body = $response->getBody();
		try{ //si la ressource existe bien
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
	}
}