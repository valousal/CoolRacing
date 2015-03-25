<?php
namespace media\controller ;
use \media\modele\Participants ;
use \media\modele\Adresse ;
use \media\modele\Participe ;
use \media\modele\Event ;

class ParticipantController extends AbstractController{
	public function __construct(){

	}	


	//get 1 participant
	public function get($id){
		$participant = Participants::with('adresse')->find($id);
		
		$app = \Slim\Slim::getInstance();

		if(!empty($participant)){

			$participantArray = array();
			$participantArray['id'] = $participant->id;
			$participantArray['prenom'] = $participant->prenom;
			$participantArray['nom'] = $participant->nom;
			$participantArray['dateNaissance'] = $participant->dateNaissance;
			$participantArray['dateInscription'] = $participant->dateInscription;
			$participantArray['sexe'] = $participant->sexe;
			$participantArray['description'] = $participant->description;
			$participantArray['login'] = $participant->login;
			$participantArray['mailPub'] = $participant->mailPub;
			$participantArray['tel'] = $participant->tel;
			$participantArray['licence'] = $participant->licence;
			$participantArray['descriptionLicence'] = $participant->descriptionLicence;
			$participantArray['adresse'] = $participant->adresse;

			$app->response->setStatus(200) ;
			$app->response->headers->set('Content-type','application/json') ;

			echo(json_encode($participantArray));
		}else{
			$app->response->headers->set('Content-type','application/json') ;
	        $app->halt(404, json_encode(array("erreur_message"=>"participant non trouvée")));
		}
	}

	//recupère tous les participants
	public function getAll(){
		$app = \Slim\Slim::getInstance();

		// Get request object
		$req = $app->request;
		//Get root URI
		$rootUri = $req->getUrl();
		$rootUri .= $req->getRootUri();


			if(isset($_GET['nom'])){
				$participants = Participants::where('nom', 'like', '%'.$_GET['nom'].'%')->with("adresse")->get();
			}else{
				$participants = Participants::with("adresse")->get();
			}
			
			$all = array();
			$participantArray = array();

			foreach ($participants as $key => $participant){
				$participantArray['id'] = $participant->id;
				$participantArray['prenom'] = $participant->prenom;
				$participantArray['nom'] = $participant->nom;
				$participantArray['dateNaissance'] = $participant->dateNaissance;
				$participantArray['dateInscription'] = $participant->dateInscription;
				$participantArray['sexe'] = $participant->sexe;
				$participantArray['description'] = $participant->description;
				$participantArray['login'] = $participant->login;
				$participantArray['mailPub'] = $participant->mailPub;
				$participantArray['tel'] = $participant->tel;
				$participantArray['licence'] = $participant->licence;
				$participantArray['descriptionLicence'] = $participant->descriptionLicence;
				$participantArray['adresse'] = $participant->adresse;
	           
	            $value['participant'] = $participantArray; 
				$value['links'] = array('rel' => "self",
										'href' => "$rootUri/participants/".$participant->id);
				$all[]=$value;
			}

			$nbParticipants = $participants->count();

			//next et prev ????
			$all['links'] = array(array('rel' => 'prev',
									'href' => "$rootUri/participants/?limit=10&offset=150"),
									array('rel' => 'next',
									'href' => "$rootUri/participants/?limit=10&offset=0"),
									array('rel' => 'first',
									'href' => "$rootUri/participants/?limit=10&offset=150"),
									array('rel' => 'last',
									'href' => "$rootUri/participants/?limit=10&offset=$nbParticipants"),
									);

			if($nbParticipants != 0){
				$app->response->setStatus(200) ;
				$app->response->headers->set('Content-type','application/json') ;
				echo json_encode($all);
			}else{
				$app->response->headers->set('Content-type','application/json') ;
	        	$app->halt(404, json_encode(array("erreur_message"=>"Aucun participant")));
			}
	}

	//insère un partciipant dans la BDD  //TODO verifier que y'a pas deux login pareil dans la m^mee table
	public function post(){
		$app = \Slim\Slim::getInstance();
		$req = $app->request->getBody();
		$data = (json_decode($req, true)); // decode les données envoyées sous forme de tableau associatif

		if (isset($data['prenom'], $data['nom'], $data['sexe'], $data['login'], $data['password'], $data['mailPrivate'], $data['dateNaissance'])){ //si les variables existent
			$prenom = filter_var($data['prenom'], FILTER_SANITIZE_STRING);
			$nom = filter_var($data['nom'], FILTER_SANITIZE_STRING);
			$login = filter_var($data['login'], FILTER_SANITIZE_STRING);
			$sexe = filter_var($data['sexe'], FILTER_SANITIZE_STRING);
			$password = filter_var($data['password'], FILTER_SANITIZE_STRING);
			$mailPrivate = filter_var($data['mailPrivate'], FILTER_SANITIZE_EMAIL);
			$dateNaissance = filter_var($data['dateNaissance'], FILTER_SANITIZE_STRING);
		}else{
			$app->response->headers->set('Content-type','application/json') ;
	        $app->halt(400, json_encode(array("erreur_message"=>"Mauvais format data")));
		}

		$participant = new Participants();
		$participant->prenom = $prenom;
		$participant->nom = $nom;
		$participant->sexe = $sexe;
		$participant->login = $login;
		$participant->password = password_hash($password, PASSWORD_BCRYPT, array("cost" => 10));
		$participant->mailPrivate = $mailPrivate;
		$participant->dateNaissance = $dateNaissance;

		$adresse = new Adresse;

		if(!empty($data['tel'])){
			$participant->tel = filter_var($data['tel'], FILTER_SANITIZE_NUMBER_INT);
		}
		if(!empty($data['mailPub'])){
			$participant->mailPub = filter_var($data['mailPub'], FILTER_SANITIZE_EMAIL);
		}
		if(!empty($data['adresse']['num'])){
			$adresse->num = filter_var($data['adresse']['num'], FILTER_SANITIZE_STRING);
		}
		if(!empty($data['adresse']['type'])){
			$adresse->type = filter_var($data['adresse']['type'], FILTER_SANITIZE_STRING);
		}
		if(!empty($data['adresse']['rue'])){
			$adresse->rue = filter_var($data['adresse']['rue'], FILTER_SANITIZE_STRING);
		}
		if(!empty($data['adresse']['CP'])){
			$adresse->CP = filter_var($data['adresse']['CP'], FILTER_SANITIZE_STRING);
		}
		if(!empty($data['adresse']['ville'])){
			$adresse->ville = filter_var($data['adresse']['ville'], FILTER_SANITIZE_STRING);
		}

		//$adresse['num']='13';
		$adresse->save();
		$participant->dateInscription = date('Y-m-d');

		//$participant->idAdress = 1; //provisoire

		try {
			$participant->adresse()->associate($adresse);
		  	//$participant->save(get_object_vars($adresse));
		  	$participant->save();
			//$participant->save($adresse);
			//$participant->save($adresse['id']);

		} catch (Exception $e) {
		    $app->response->headers->set('Content-type','application/json') ;
	        $app->halt(400, json_encode(array("erreur_message"=>"Mauvais formats des donées envoyées")));
		}

		/***************************************************************************/
		/*TODO: licences, description licences et Adresse Cryptage Pas*******************/
		/***************************************************************************/
		$rootUri = $app->request->getUrl();
		$rootUri .= $app->request->getRootUri();

		$app->response->setStatus(201) ;
		$app->response->headers->set('Content-type','application/json') ;
		echo json_encode(array('participant_ajouté'=>$participant, 'href'=>"$rootUri/api/participants/".$participant->id)); //renvoit le mdp, pas cool
	}

	//modifie un participant donné
	public function put($id){
		$app = \Slim\Slim::getInstance();
		$req = $app->request->getBody();
		$data = (json_decode($req, true)); // decode les données envoyées sous forme de tableau associatif

		/*if (isset($data['prenom'], $data['nom'], $data['sexe'], $data['login'], $data['password'], $data['mailPrivate'], $data['dateNaissance'])){ //si les variables existent
			$prenom = filter_var($data['prenom'], FILTER_SANITIZE_STRING);
			$nom = filter_var($data['sexe'], FILTER_SANITIZE_STRING);
			$login = filter_var($data['login'], FILTER_SANITIZE_STRING);
			$sexe = filter_var($data['sexe'], FILTER_SANITIZE_STRING);
			$password = filter_var($data['password'], FILTER_SANITIZE_STRING);
			$mailPrivate = filter_var($data['mailPrivate'], FILTER_SANITIZE_EMAIL);
			$dateNaissance = filter_var($data['dateNaissance'], FILTER_SANITIZE_STRING);
		}else{
			$app->response->headers->set('Content-type','application/json') ;
	        $app->halt(400, json_encode(array("erreur_message"=>"Requête incomplete, données obligatoires manquantes")));
		}*/

		$participant = Participants::with('adresse')->find($id);

		if(!empty($participant)){

			if(!empty($data['tel'])){
				$participant->tel = filter_var($data['tel'], FILTER_SANITIZE_NUMBER_INT);
			}
			if(!empty($data['mailPub'])){
				$participant->mailPub = filter_var($data['mailPub'], FILTER_SANITIZE_EMAIL);
			}
			if(!empty($data['description'])){
				$participant->description = filter_var($data['description'], FILTER_SANITIZE_STRING);
			}
			if(!empty($data['prenom'])){
				$participant->prenom = filter_var($data['prenom'], FILTER_SANITIZE_STRING);
			}
			if(!empty($data['nom'])){
				$participant->nom = filter_var($data['nom'], FILTER_SANITIZE_STRING);
			}
			if(!empty($data['sexe'])){
				$participant->sexe = filter_var($data['sexe'], FILTER_SANITIZE_STRING);
			}
			if(!empty($data['password'])){
				$password = filter_var($data['password'], FILTER_SANITIZE_STRING);
				$password = password_hash($password, PASSWORD_BCRYPT, array("cost" => 10));
				$participant->password = $password;
			}
			if(!empty($data['mailPrivate'])){
				$participant->mailPrivate = filter_var($data['mailPrivate'], FILTER_SANITIZE_EMAIL);
			}
			if(!empty($data['dateNaissance'])){
				$participant->dateNaissance = filter_var($data['dateNaissance'], FILTER_SANITIZE_STRING);
			}
			if(!empty($data['idAdresse']) && $data['idAdresse']>0){
				$participant->idAdresse = filter_var($data['idAdresse'], FILTER_SANITIZE_NUMBER_INT);
			}
			/*if(!empty($data['adresse']['type'])){
				$participant->adresse->type = filter_var($data['adresse']['type'], FILTER_SANITIZE_STRING);
			}
			if(!empty($data['adresse']['rue'])){
				$participant->adresse->rue = filter_var($data['adresse']['rue'], FILTER_SANITIZE_STRING);
			}
			if(!empty($data['adresse']['CP'])){
				$participant->adresse->CP = filter_var($data['adresse']['CP'], FILTER_SANITIZE_STRING);
			}
			if(!empty($data['adresse']['ville'])){
				$participant->adresse->ville = filter_var($data['adresse']['ville'], FILTER_SANITIZE_STRING);
			}*/

			try {
			   $participant->save();
			} catch (Exception $e) {
			    $app->response->headers->set('Content-type','application/json') ;
		        $app->halt(400, json_encode(array("erreur_message"=>"Mauvais formats des donées envoyées")));
			}

			/***************************************************************************/
			/*TODO: licences, description licences et Adresse *******************/
			/***************************************************************************/
			$rootUri = $app->request->getUrl();
			$rootUri .= $app->request->getRootUri();

			$app->response->setStatus(200) ;
			$app->response->headers->set('Content-type','application/json') ;
			echo json_encode(array('participant_modifié'=>$participant->toJson(), 'href'=>"$rootUri/api/participants/".$participant->id)); //renvoit le mdp, pas cool
		}else{
			$app->response->headers->set('Content-type','application/json') ;
	        $app->halt(404, json_encode(array("erreur_message"=>"participant non trouvée")));
		}
	}

	//supprime un participant
	public function delete($id){
		$participant = Participants::find($id);
		
		$app = \Slim\Slim::getInstance();
		try{
			$participant->delete(); //rajouter une confirmation?
		}catch(Exception $e){
		    $app->response->headers->set('Content-type','application/json') ;
	        $app->halt(400, json_encode(array("erreur_message"=>"Impossible de supprimer la ressource")));
		}
		$app->response->setStatus(200) ;
		$app->response->headers->set('Content-type','application/json') ;
		echo json_encode(array("delete"=>"ok"));
	}
	//recupère les evenement d'un participant
	public function getEvent($id){
		$app = \Slim\Slim::getInstance();
		// Get request object
		$req = $app->request;
		//Get root URI
		$rootUri = $req->getUrl();
		$rootUri .= $req->getRootUri();

		$participant = Participants::find($id);

		if(!empty($participant)){
			$participations = Participe::where("idParticipants", "=", $participant->id)->get();

			$res = array();
			if (!empty($participations)){
				foreach ($participations as $participe){
					$classementPerso = '';
					$event = Event::find($participe->idEvent);
					$classement = $event->classement;
					if((!empty($classement)) && isset($classement)){
						foreach ($classement as $key => $value) {
							$pivot[] = $value->pivot;
							if(!empty($pivot)){
								foreach ($pivot as $key => $value2) {
									if($value2['idParticipants'] == $participant->id && !empty($pivot)){
										$classementPerso['idParticipants'] = $value2['idParticipants'];
										$classementPerso['position'] = $value2['positionFinale'];
										$classementPerso['tempsTotal'] = $value2['tempsTotal'];
										$classementPerso['tempsIntermediaire'] = $value2['tempsIntermediaire'];
										$classementPerso['statut'] = $value2['statut'];
									}else{
										$classementPerso = 'Pas encore de classement';
									}
								}
							}else{
								$classementPerso = 'Pas encore de classement';
							}
						}
					}else{
						$classementPerso = 'Pas encore de classement';
					}
					$res[] = array("evenement"=>$event, "classement" => $classementPerso, "href"=> "$rootUri/api/evenements/$event->id");
				}
			}else{
				$app->response->headers->set('Content-type','application/json') ;
	        	$app->halt(404, json_encode(array("erreur_message"=>"Ce participant n'a participé a aucun evenement")));
			}
			$app->response->setStatus(200) ;
			$app->response->headers->set('Content-type','application/json') ;
			echo json_encode($res);
		}else{
			$app->response->headers->set('Content-type','application/json') ;
	        $app->halt(404, json_encode(array("erreur_message"=>"participant non trouvée")));
		}
	}

}