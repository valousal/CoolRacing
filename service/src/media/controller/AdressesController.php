<?php
namespace media\controller ;
use \media\modele\Adresse ;

class AdressesController extends AbstractController{
	public function __construct(){

	}	

	public function getAll(){
		$app = \Slim\Slim::getInstance();
		// Get request object
		$req = $app->request;
		//Get root URI
		$rootUri = $req->getUrl();
		$rootUri .= $req->getRootUri();

		$adresses = Adresse::get();

		$res = array();
		foreach ($adresses as $adresse){
			$res[] = $adresse;
		}

		$nbAdresses = $adresses->count();

		$links = array(array('rel' => 'prev',
								'href' => "$rootUri/api/adresses/?limit=10&offset=150"),
								array('rel' => 'next',
								'href' => "$rootUri/api/adresses/?limit=10&offset=0"),
								array('rel' => 'first',
								'href' => "$rootUri/api/adresses/?limit=10&offset=150"),
								array('rel' => 'last',
								'href' => "$rootUri/api/adresses/?limit=10&offset=$nbAdresses"),
								);



		$app->response->setStatus(200) ;
		$app->response->headers->set('Content-type','application/json') ;
		echo json_encode(array('adresses'=>$res, "links"=>$links));
	}

	public function put($id){
		$app = \Slim\Slim::getInstance();
		$req = $app->request->getBody();
		$data = (json_decode($req, true)); // decode les données envoyées sous forme de tableau associatif

		$adresse = Adresse::find($id);

		if(!empty($adresse)){

			if(!empty($data['num'])){
				$adresse->num = filter_var($data['num'], FILTER_SANITIZE_STRING);
			}
			if(!empty($data['type'])){
				$adresse->type = filter_var($data['type'], FILTER_SANITIZE_STRING);
			}
			if(!empty($data['rue'])){
				$adresse->rue = filter_var($data['rue'], FILTER_SANITIZE_STRING);
			}
			if(!empty($data['CP'])){
				$adresse->CP = filter_var($data['CP'], FILTER_SANITIZE_STRING);
			}
			if(!empty($data['ville'])){
				$adresse->ville = filter_var($data['ville'], FILTER_SANITIZE_STRING);
			}

			try {
			   $adresse->save();
			} catch (Exception $e) {
			    $app->response->headers->set('Content-type','application/json') ;
		        $app->halt(400, json_encode(array("erreur_message"=>"Mauvais formats des donées envoyées")));
			}

			$rootUri = $app->request->getUrl();
			$rootUri .= $app->request->getRootUri();

			$app->response->setStatus(200) ;
			$app->response->headers->set('Content-type','application/json') ;
			echo $adresse->toJson();
		}else{
			$app->response->headers->set('Content-type','application/json') ;
	        $app->halt(404, json_encode(array("erreur_message"=>"adresse non trouvée")));
		}
	}

	public function delete($id){
		$adresse = Adresse::find($id);
		
		$app = \Slim\Slim::getInstance();
		try{
			$adresse->delete(); //rajouter une confirmation?
		}catch(Exception $e){
		    $app->response->headers->set('Content-type','application/json') ;
	        $app->halt(400, json_encode(array("erreur_message"=>"Impossible de supprimer la ressource")));
		}
		$app->response->setStatus(200) ;
		$app->response->headers->set('Content-type','application/json') ;
		echo json_encode(array("delete"=>"ok"));
	}
}