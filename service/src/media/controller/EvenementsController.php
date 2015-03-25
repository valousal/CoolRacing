<?php
namespace media\controller ;
use \media\modele\Event ;
use \media\modele\Organisateurs ;
use \media\modele\Participants ;
use \media\modele\Participe ;
use \media\modele\Statut ;
use \media\modele\Categorie ;
use \media\modele\Classement ;

class EvenementsController extends AbstractController{
	public function __construct(){

	}



	//recupère tous les évènements
	public function getAll(){
		$app = \Slim\Slim::getInstance();

		// Get request object
		$req = $app->request;
		//Get root URI
		$rootUri = $req->getUrl();
		$rootUri .= $req->getRootUri();

		if(isset($_GET['price'])){
			$p = $_GET['price'];
		}else{
			$p = 'allPrice';
		}
		if(isset($_GET['cat'])){
			$c = $_GET['cat'];
		}else{
			$c = 'allCategories';
		}
		if(isset($_GET['tags'])){
			$t = $_GET['tags'];
		}else{
			$t = 'noTags';
		}
		if(isset($_GET['statut'])){
			$s = $_GET['statut'];
		}else{
			$s = 'noStatut';
		}

		$idcategorie = null;
		if($c != 'allCategories'){
			$categorie_search = Categorie::where('nom', '=', $c)->first();
			if($categorie_search != null){
				$idcategorie = $categorie_search->id;
			}
		}

		$idstatut = null;
		if($s != 'noStatut'){
			$statut_search = Statut::where('label', '=', $s)->first();
			if($statut_search != null){
				$idstatut = $statut_search->id;
			}
		}

		$evenements = Event::select('id','titre','description', 'lieu', 'prix','idOrganisateur', 'idCategorie','idStatut', 'dateEvenement', 'dateOuverture', 'dateCloture')
		->where(function($query) use($p,$c,$t,$s,$idcategorie,$idstatut) {
			if($t!='noTags'){
				$query->where('titre', 'LIKE', "%$t%");
			}
			if($c!='allCategories'){
		        $query->where('idCategorie', '=', "$idcategorie");
		    }
		    if($s!='noStatut'){
		        $query->where('idStatut', '=', "$idstatut");
		    }
			if($p!='allPrice'){
		        $query->where('prix', '<', "$p");
		    }})
		->orderBy('id', 'DESC')
		->get();

		
		if( ! empty($evenements) && count($evenements) != 0){
			$all = array();
			foreach ($evenements as $key => $evenement){
	            $value['evenement'] = $evenement; //->toJson() pas sur de le mettre .. 
				$value['links'] = array(array('rel' => "self",
										'href' => "$rootUri/api/evenements/".$evenement->id),
										array('rel' => "organisateur",
										'href' => "$rootUri/api/organisateurs/".$evenement->idOrganisateur),
										);
				$event[]=$value;
			}
			//var_dump($evenements);
			$nbOrganisateurs = $evenements->count();
			$all['evenements'] = $event;
			$all['links'] = array(array('rel' => 'prev',
									'href' => "$rootUri/api/evenements/?limit=10&offset=150"),
									array('rel' => 'next',
									'href' => "$rootUri/api/evenements/?limit=10&offset=0"),
									array('rel' => 'first',
									'href' => "$rootUri/api/evenements/?limit=10&offset=150"),
									array('rel' => 'last',
									'href' => "$rootUri/api/evenements/?limit=10&offset=$nbOrganisateurs"),
									);



			$app->response->setStatus(200) ;
			$app->response->headers->set('Content-type','application/json') ;
			echo json_encode($all);
		}else{
			/* Invalid */
		    $app->response->headers->set('Content-type','application/json') ;
	        $app->halt(500, json_encode(array("erreur_message"=>'Aucun evènement')));
		}
	}



	//get 1 evenement
	public function get($id){
		$evenement = Event::find($id);
		
		$app = \Slim\Slim::getInstance();
		// Get request object
		$req = $app->request;
		//Get root URI
		$rootUri = $req->getUrl();
		$rootUri .= $req->getRootUri();

		if( ! empty($evenement)){
			$evenementArray = array();
			$evenementArray['id'] = $evenement->id;
			$evenementArray['titre'] = $evenement->titre;
			$evenementArray['dateCreation'] = $evenement->dateCreation;
			$evenementArray['description'] = $evenement->description;
			$evenementArray['lieu'] = $evenement->lieu;
			$evenementArray['dateCloture'] = $evenement->dateCloture;
			$evenementArray['dateOuverture'] = $evenement->dateOuverture;
			$evenementArray['dateEvenement'] = $evenement->dateEvenement;
			$evenementArray['session'] = $evenement->session;
			$evenementArray['nombrePlace'] = $evenement->nombrePlace;
			$evenementArray['prix'] = $evenement->prix;
			$evenementArray['distance'] = $evenement->distance;
			$evenementArray['statut'] = $evenement->statut->label;
			$categorie = Categorie::where('id' , '=', $evenement->idCategorie)->first(); 
			$evenementArray['categorie'] = $categorie->nom;
			
			$links = array(array('rel' => "organisateur",
									'href' => "$rootUri/api/organisateurs/".$evenement->idOrganisateur),
							array('rel' => "participants",
									'href' => "$rootUri/api/evenements/".$evenement->id."/participants"),
							array('rel' => "categorie",
									'href' => "$rootUri/api/categorie/".$categorie->id),
							);
			

			$app->response->setStatus(200) ;
			$app->response->headers->set('Content-type','application/json') ;
			echo(json_encode(array("evenement" => $evenementArray, "links" => $links)));
		}else{
			/* Invalid */
		     $app->response->headers->set('Content-type','application/json') ;
	         $app->halt(404, json_encode(array("erreur_message"=>"evenement n'existe plus/pas")));
		}
	}



	//Ajouter un évènement
	public function addEvent(){
		$app = \Slim\Slim::getInstance();
		$req = $app->request->getBody();
		$data = (json_decode($req, true)); // decode les données envoyées sous forme de tableau associatif
		
		if (isset($data['titre'], $data['description'], $data['lieu'], $data['dateCloture'],$data['dateOuverture'], $data['dateEvenement'],$data['nombrePlace'],$data['prix'],$data['distance'])){ //si les variables existent
			$titre = filter_var($data['titre'], FILTER_SANITIZE_STRING);
			$description = filter_var($data['description'], FILTER_SANITIZE_STRING);
			$lieu = filter_var($data['lieu'], FILTER_SANITIZE_STRING);
			$dateCloture = filter_var($data['dateCloture'], FILTER_SANITIZE_STRING);
			$dateEvenement = filter_var($data['dateEvenement'], FILTER_SANITIZE_STRING);
			$dateOuverture = filter_var($data['dateOuverture'], FILTER_SANITIZE_STRING);
			$nombrePlace = filter_var($data['nombrePlace'], FILTER_SANITIZE_NUMBER_INT);
			$prix = filter_var($data['prix'], FILTER_SANITIZE_NUMBER_INT);
			$distance = filter_var($data['distance'], FILTER_SANITIZE_NUMBER_INT);
		}else{
			$app->response->headers->set('Content-type','application/json') ;
	        $app->halt(400, json_encode(array("erreur_message"=>"Requête incomplete, données obligatoires manquantes")));
		}

		$evenement = new Event;
		$evenement->titre = $titre;
		$evenement->description = $description;
		$evenement->lieu = $lieu;
		$evenement->dateCloture = $dateCloture;
		$evenement->dateEvenement = $dateEvenement;
		$evenement->dateOuverture = $dateOuverture;
		$evenement->nombrePlace = $nombrePlace;
		$evenement->prix = $prix;
		$evenement->distance = $distance;

		//Categorie de l'evenement
		$categorie = Categorie::where('nom' , '=', $data['categorie'])->first();
		$evenement->idCategorie = $categorie->id;

		//Organisateur de l'evenement
		$evenement->idOrganisateur = $data['id_organisateur'];

		//Statut de l'evenement
		$evenement->idStatut = 1;

		if(!empty($data['sessions'])){
			$evenement->sessions = filter_var($data['sessions'], FILTER_SANITIZE_STRING);
		}

		$evenement->dateCreation = date('Y-m-d');

		try {
		   $evenement->save();
		} catch (Exception $e) {
		    $app->response->headers->set('Content-type','application/json') ;
	        $app->halt(400, json_encode(array("erreur_message"=>"Mauvais formats des données envoyées")));
		}

		/***************************************************************************/
		/*TODO: licences, description licences et Adresse Cryptage Pas*******************/
		/***************************************************************************/
		$rootUri = $app->request->getUrl();
		$rootUri .= $app->request->getRootUri();

		$app->response->setStatus(201) ;
		$app->response->headers->set('Content-type','application/json') ;
		$res = array("evenement"=>array("titre"=>$evenement->titre, "description"=>$evenement->description, 
					"lieu"=>$evenement->lieu, "dateEvenement"=>$evenement->dateEvenement, 
					"dateOuverture"=>$evenement->dateOuverture, "dateCloture"=>$evenement->dateCloture, 
					"nombrePlace"=>$evenement->nombrePlace, "nombrePlace"=>$evenement->nombrePlace, "distance"=>$evenement->distance,"organisateur"=>$evenement->organisateur->nom_structure),
					 "links"=>array("href"=>"$rootUri/api/evenements/".$evenement->id));
		echo json_encode($res);
	}


	//Modifie un évènement
	public function putEvent($id){
		$app = \Slim\Slim::getInstance();
		$req = $app->request->getBody();
		$data = (json_decode($req, true)); // decode les données envoyées sous forme de tableau associatif
		
		if (isset($data['titre'], $data['description'], $data['lieu'], $data['dateCloture'],$data['dateOuverture'], $data['dateEvenement'],$data['nombrePlace'],$data['prix'],$data['distance'])){ //si les variables existent
			$titre = filter_var($data['titre'], FILTER_SANITIZE_STRING);
			$description = filter_var($data['description'], FILTER_SANITIZE_STRING);
			$lieu = filter_var($data['lieu'], FILTER_SANITIZE_STRING);
			$dateCloture = filter_var($data['dateCloture'], FILTER_SANITIZE_STRING);
			$dateEvenement = filter_var($data['dateEvenement'], FILTER_SANITIZE_STRING);
			$dateOuverture = filter_var($data['dateOuverture'], FILTER_SANITIZE_STRING);
			$nombrePlace = filter_var($data['nombrePlace'], FILTER_SANITIZE_NUMBER_INT);
			$prix = filter_var($data['prix'], FILTER_SANITIZE_NUMBER_INT);
			$distance = filter_var($data['distance'], FILTER_SANITIZE_NUMBER_INT);
			//$statut = filter_var($data['statut'], FILTER_SANITIZE_STRING);
		}else{
			$app->response->headers->set('Content-type','application/json') ;
	        $app->halt(400, json_encode(array("erreur_message"=>"Requête incomplete, données obligatoires manquantes")));
		}

		$evenement = Event::find($id);
		$evenement->titre = $titre;
		$evenement->description = $description;
		$evenement->lieu = $lieu;
		$evenement->dateCloture = $dateCloture;
		$evenement->dateEvenement = $dateEvenement;
		$evenement->dateOuverture = $dateOuverture;
		$evenement->nombrePlace = $nombrePlace;
		$evenement->prix = $prix;
		$evenement->distance = $distance;

		//Organisateur de l'evenement
		$evenement->idOrganisateur = $data['id_organisateur'];

		//Statut de l'evenement
		// $statut = Statut::where('label','=',$statut)->first();
		// $evenement->idStatut = $statut->id;
		$evenement->idStatut = 1;

		if(!empty($data['sessions'])){
			$evenement->sessions = filter_var($data['sessions'], FILTER_SANITIZE_STRING);
		}

		$evenement->dateCreation = date('Y-m-d');

		try {
		   $evenement->save();
		} catch (Exception $e) {
		    $app->response->headers->set('Content-type','application/json') ;
	        $app->halt(400, json_encode(array("erreur_message"=>"Mauvais formats des données envoyées")));
		}

		/***************************************************************************/
		/*TODO: licences, description licences et Adresse Cryptage Pas*******************/
		/***************************************************************************/
		$rootUri = $app->request->getUrl();
		$rootUri .= $app->request->getRootUri();

		$app->response->setStatus(201) ;
		$app->response->headers->set('Content-type','application/json') ;
		$res = array("evenement"=>array("titre"=>$evenement->titre, "description"=>$evenement->description, 
					"lieu"=>$evenement->lieu, "dateEvenement"=>$evenement->dateEvenement, 
					"dateOuverture"=>$evenement->dateOuverture, "dateCloture"=>$evenement->dateCloture, 
					"nombrePlace"=>$evenement->nombrePlace, "nombrePlace"=>$evenement->nombrePlace, "distance"=>$evenement->distance,"organisateur"=>$evenement->organisateur->nom_structure),
					 "links"=>array("href"=>"$rootUri/api/evenements/".$evenement->id));
		echo json_encode($res);
	}



	//Supprime un evenement
	public function deleteEvent($id){
		$evenement = Event::find($id);
		
		$app = \Slim\Slim::getInstance();
		try{
			$evenement->delete(); //rajouter une confirmation?
		}catch(Exception $e){
		    $app->response->headers->set('Content-type','application/json') ;
	        $app->halt(400, json_encode(array("erreur_message"=>"Impossible de supprimer la ressource")));
		}
		$app->response->setStatus(200) ;
		$app->response->headers->set('Content-type','application/json') ;
		echo json_encode(array("delete"=>"ok"));
	}



	//Recupere les participants d'un evenement
	public function getParticipantEvent($id){
		$app = \Slim\Slim::getInstance();
		$rootUri = $app->request->getUrl();
		$rootUri .= $app->request->getRootUri();

		$participants = Event::find($id)->participants()->select('id','nom','prenom','dateNaissance','dateInscription',
			'sexe','description','mailPub','tel')->get();
		// echo $participants;
		$all = array();
		if( ! empty($participants)){
			foreach ($participants as $key => $participant){
	            $value['participant'] = $participant; //->toJson() pas sur de le mettre .. 
				$value['links'] = array('rel' => "self",
										'href' => "$rootUri/api/participants/".$participant->id);
				$part[]=$value;
			}

			$nbParticipants = $participants->count();
			$all['participants'] = $part;
			$all['links'] = array(array('rel' => 'prev',
									'href' => "$rootUri/api/evenements/?limit=10&offset=150"),
									array('rel' => 'next',
									'href' => "$rootUri/api/evenements/?limit=10&offset=0"),
									array('rel' => 'first',
									'href' => "$rootUri/api/evenements/?limit=10&offset=150"),
									array('rel' => 'last',
									'href' => "$rootUri/api/evenements/?limit=10&offset=$nbParticipants"),
									array('rel' => 'self',
									'href' => "$rootUri/api/evenements/".$participant->pivot->idEvent),
									);



			$app->response->setStatus(200) ;
			$app->response->headers->set('Content-type','application/json') ;
			echo json_encode($all);
		}else{
			/* Invalid */
		    $app->response->headers->set('Content-type','application/json') ;
	        $app->halt(500, json_encode(array("erreur_message"=>'Aucun participant pour cet evenement')));
		}
	}



	//Recuperer le classement d'un evenement
	public function getClassementEvent($id){
		$app = \Slim\Slim::getInstance();
		$rootUri = $app->request->getUrl();
		$rootUri .= $app->request->getRootUri();


		$classement = Event::find($id)->classement()->orderBy('tempsTotal')->get();
		$event = Event::find($id);
		//var_dump($classement);
		if(!empty($classement) && isset($classement) && count($classement) > 0){
			foreach ($classement as $class) {
				$value['id'] = $class->id;
				$value['nom'] = $class->nom;
				$value['prenom'] = $class->prenom;
				$value['sexe'] = $class->sexe;
				$value['dateNaissance'] = $class->dateNaissance;
				$value['licence'] = $class->licence;
				$value['position'] = $class->pivot->positionFinale;
				$value['tempsTotal'] = $class->pivot->tempsTotal;
				$value['tempsIntermediaire'] = $class->pivot->tempsIntermediaire;
				$value['statut'] = $class->pivot->statut;
				$value['links'] = array('rel' => "self",
										'href' => "$rootUri/api/participants/".$class->id);

				$all[] = $value;
			}


			$all['evenement'] = array('dateEvenement' => $event->dateEvenement);

			$app->response->setStatus(200) ;
			$app->response->headers->set('Content-type','application/json') ;
			echo json_encode($all);
		}else{
			/* Invalid */
		    $app->response->headers->set('Content-type','application/json') ;
	        $app->halt(500, json_encode(array("erreur_message"=>'Aucun classement pour cet evenement')));
		}
	}



	//Recuperer l'organisateur d'un evenement
	public function getOrgaEvent($id){
		$app = \Slim\Slim::getInstance();
		$rootUri = $app->request->getUrl();
		$rootUri .= $app->request->getRootUri();

		$organisateur = Event::find($id)->organisateur;
		

		if( ! empty($organisateur)){
			$organisateurArray = array();
			$organisateurArray['id'] = $organisateur->id;
			$organisateurArray['nom_structure'] = $organisateur->nom_structure;
			$organisateurArray['forme_juridique'] = $organisateur->forme_juridique;
			$organisateurArray['nomResponsable'] = $organisateur->nomResponsable;
			$organisateurArray['prenomResponsable'] = $organisateur->prenomResponsable;
			$organisateurArray['dateInscription'] = $organisateur->dateInscription;
			$organisateurArray['login'] = $organisateur->login;
			$organisateurArray['mailPub'] = $organisateur->mailPub;
			$organisateurArray['tel'] = $organisateur->tel;
			$organisateurArray['mailResponsable'] = $organisateur->mailResponsable;
			$organisateurArray['adresse'] = $organisateur->adresse;
			
			$links = array('rel' => "evenements",
									'href' => "$rootUri/api/organisateurs/".$organisateur->id."/evenements");
			

			$app->response->setStatus(200) ;
			$app->response->headers->set('Content-type','application/json') ;
			echo(json_encode(array("organisateur" => $organisateurArray, "links" => $links)));
		}else{
			/* Invalid */
		     $app->response->headers->set('Content-type','application/json') ;
	         $app->halt(500, json_encode(array("erreur_message"=>"Organisateur n'existe plus/pas")));
		}
	}



	//Recuperer le classement d'un participant à un evenement donnée
	public function getClassementPartEvent($id_e,$id_p){
		$app = \Slim\Slim::getInstance();
		$rootUri = $app->request->getUrl();
		$rootUri .= $app->request->getRootUri();

		$classement = Event::find($id_e);
		if ($classement != null){
			$classement = $classement->classement;
			foreach ($classement as $key => $value) {
				$pivot[] = $value->pivot;
				foreach ($pivot as $key => $value2) {
					if($value2['idParticipants'] == $id_p){
					$classementPerso['idParticipants'] = $value2['idParticipants'];
					$classementPerso['position'] = $value2['positionFinale'];
					$classementPerso['tempsTotal'] = $value2['tempsTotal'];
					$classementPerso['tempsIntermediaire'] = $value2['tempsIntermediaire'];
					$classementPerso['statut'] = $value2['statut'];
					}
				}
			}
		}else{
			/* Invalid */
		    $app->response->headers->set('Content-type','application/json') ;
	        $app->halt(500, json_encode(array("erreur_message"=>"Cet evenement n'existe pas")));
		}

		if(!isset($classementPerso)){
			/* Invalid */
		    $app->response->headers->set('Content-type','application/json') ;
	        $app->halt(500, json_encode(array("erreur_message"=>"Ce participant ne figure pas dans le classement")));
		}

		$app->response->setStatus(200) ;
		$app->response->headers->set('Content-type','application/json') ;
		$all['participant'] = $classementPerso;
		$all['links'] = array('rel' => "self",
										'href' => "$rootUri/api/participants/".$classementPerso['idParticipants']);

		echo json_encode($all);
	}



	//inscrire un participant à un evenement
	public function postParticipantEvent($id_e){
		$app = \Slim\Slim::getInstance();
		$rootUri = $app->request->getUrl();
		$rootUri .= $app->request->getRootUri();

		// $affectedRows = User::update(array('status' => 2));

		
		$req = $app->request->getBody();
		$data = (json_decode($req, true));

		$event = Event::find($id_e);
		//$participant = Participants::find($id_p);

		if ($event != null){
			$inscription = new Participe;
			$inscription->idParticipants = $data['id_participant']; 
			$inscription->idEvent = $id_e;

			//Générer aléatoirement le dossard Verifier si il existe deja ou non
			$dossard = $inscription->dossard = rand (0, $event->nombrePlace);

			if(!empty($data['sessionDepart'])){ //Générer aléatoirement directement dans l'API?
				$inscription->sessionDepart = filter_var($data['sessionDepart'], FILTER_SANITIZE_STRING);
			}
			if(!empty($data['sasDepart'])){ //Générer aléatoirement directement dans l'API?
				$inscription->sasDepart = filter_var($data['sasDepart'], FILTER_SANITIZE_STRING);
			}
			if(!empty($data['certificatMedical'])){ ///img? comment le gérer?
				$inscription->certificatMedical = filter_var($data['certificatMedical'], FILTER_SANITIZE_STRING);
			}
			if(!empty($data['club'])){ //lien avec licence dans la table participants?
				$inscription->club = filter_var($data['club'], FILTER_SANITIZE_STRING);
			}

			try {
		   		$inscription->save();
		   		while ($inscription->dossard == $dossard){
		   			$inscription->dossard = rand (0, $event->nombrePlace);
		   		}
			} catch (Exception $e) {
			    $app->response->headers->set('Content-type','application/json') ;	
		        $app->halt(500, json_encode(array("erreur_message"=>"Erreur inscription")));
			}
			$app->response->setStatus(201) ;
			$app->response->headers->set('Content-type','application/json') ;
			echo $inscription->toJson(); //Rajouter un retour vers l'event, le participant ou Participe?

		}else{
			$app->response->headers->set('Content-type','application/json') ;
	        $app->halt(500, json_encode(array("erreur_message"=>"Evenement ou Participant manquant")));
		}


	}



	/**********************************************************************/
	/***********************TODO********************************************/
	/************************************************************************/


	//changer le statut d'un evenement
	public function putStatutEvent($id){ //equivalent a PUT event
		$app = \Slim\Slim::getInstance();
		$rootUri = $app->request->getUrl();
		$rootUri .= $app->request->getRootUri();


	}



	//Générer/mettre à jour le classement d'un évènement
	public function postClassementEvent($idEvent){ //TODO : le mettre dans la DOC
		$app = \Slim\Slim::getInstance();
		$rootUri = $app->request->getUrl();
		$rootUri .= $app->request->getRootUri();

		$req = $app->request->getBody();
		$data = (json_decode($req, true));

		$evenement = Event::find($idEvent);
		if (!empty($evenement)){
			if (isset($data['dossard'], $data['positionFinale'], $data['statut'], $data['tempsTotal'])){ //si les variables existent
				$dossard = filter_var($data['dossard'], FILTER_SANITIZE_STRING);
				$positionFinale = filter_var($data['positionFinale'], FILTER_SANITIZE_NUMBER_INT);
				$statut = filter_var($data['statut'], FILTER_SANITIZE_STRING);
				$tempsTotal = filter_var($data['tempsTotal'], FILTER_SANITIZE_STRING);

				$array = $evenement->participants;
				$i=0;
				$total = count($array);
				while(($i < $total) && ($array[$i]->pivot->dossard == $dossard)){
					$participant = $array[$i];
					$i++;
				}
					if (!empty($participant)){
						$classement = new Classement;
						$classement->idParticipants = $participant['id'];
						$classement->idEvent = $idEvent;
						$classement->statut = $statut;
						$classement->positionFinale = $positionFinale;
						$classement->tempsTotal = $tempsTotal;
						//$classement->tempsIntermediaire = $tempsIntermediaire;

							try {
							  	$classement->save();
							  	$app->response->setStatus(200) ;
								$app->response->headers->set('Content-type','application/json') ;
								echo json_encode($classement);
							} catch (Exception $e) {
							    $app->response->headers->set('Content-type','application/json') ;
								$app->halt(500, json_encode(array("erreur_message"=>"Erreur dans la sauvegarde des données")));
							}
					}else{
						$app->response->headers->set('Content-type','application/json') ;
						$app->halt(500, json_encode(array("erreur_message"=>"Pas de participant associé au dossard/event")));
					}
			}else{
				$app->response->headers->set('Content-type','application/json') ;
		        $app->halt(400, json_encode(array("erreur_message"=>"Mauvais format data")));
			}
		}else{
			$app->response->headers->set('Content-type','application/json') ;
	        $app->halt(500, json_encode(array("erreur_message"=>"Evenement inexistant")));
		}
	}
}	