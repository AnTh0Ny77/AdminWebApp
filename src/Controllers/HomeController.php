<?php

namespace Src\Controllers;
require  '././vendor/autoload.php';

use Src\Controllers\BaseController;
use PDO;
use PDOException;

class HomeController extends BaseController
{
 
    public static function path(){
        return '';
    }

    public static function index()
    {
		$path  = "game";
		$myfile = fopen("instructions.txt", "a");
		
		$alert = false;
		$success = false ;
        self::init();
		self::stopRepost('game');
		$export = self::exportPresence();
		if (array_key_exists('postdata', $_SESSION)) {
			if (!empty($_SESSION['postdata']['game'])){
				if (!json_decode($_SESSION['postdata']['game'])) {
					unset($_SESSION['postdata']);
					self::alertMaker('game' , 'Le tableau json n est pas conforme ( attention aux espaces blanc en debut et fin de tableau )');
				}
				$game_array = json_decode($_SESSION['postdata']['game']);
				if (!empty($game_array)) {
					foreach ($game_array as $game) {
						
						$game = (array) $game;
							$pdo = new PDO('mysql:dbname=meb;host=localhost' , 'root' , '', array(1002 => 'SET NAMES utf8mb4'));
							$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

							$duplicate = self::returnRelation('games' , 'id' , $game['ID']  , $pdo);
							if(!empty($duplicate)){
								unset($_SESSION['postdata']);
								self::alertMaker('game' , 'le Jeux existe déja');
							}
							if (self::checkGame($game)) {
								unset($_SESSION['postdata']);
								self::alertMaker('game' ,self::checkGame($game));
							}
							self::insertGame($game , $pdo);
							$dump = new \Ifsnop\Mysqldump\Mysqldump('mysql:dbname=meb;host=localhost', 'root','' , 
							['no-create-info' => true ,
							  'exclude-tables' => [ 'ranks' , 'type_slide' , 'type_poi'] , 
							 
							]);
							$dump->setTransformTableRowHook(function ($tableName, array $row) {
								if ($tableName === 'client_games' or $tableName === 'user' ) {
									$row['id'] = null;
								}
								return $row;
							});
							$dump->start('export.sql');	
							self::exportTxt($myfile ,self::returnInstructionGame($game));
							$success = ' le Jeux a été inséré dans la base de donnée de test avec succès';
							$export = self::exportPresence();	
						
					}
				}else{
					
					$alert = 'Aucune donnés de jeux présentes !';
				}
			}
			
			unset($_SESSION['postdata']);
		}

		if (!empty($_GET['delete'])  ) {
			if ($_GET['delete'] = 'delete') {
				if (file_exists('export.sql')) {
					$pdo = new PDO('mysql:dbname=meb;host=localhost' , 'root' , '', array(1002 => 'SET NAMES utf8mb4'));
					$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					unlink('export.sql');
					unlink('instructions.txt');
					self::empty('games' , $pdo);
					self::empty('quest' , $pdo);
					self::empty('poi' , $pdo);
					self::empty('slide' , $pdo);
					self::empty('user' , $pdo);
					self::empty('client_game' , $pdo);
					$success = 'les données ont étés supprimées avec succès';
					$export = false;
				}
			}
			
		}
		if (isset($_SESSION['alert'])) {
			$alert = $_SESSION['alert'];
		}
		unset($_SESSION['alert']);
		fclose($myfile);
        return self::$twig->render(
            'home.html.twig',[ 
				'alert' => $alert,
				'path'  => $path , 
				'export' => $export , 
				'success' => $success
            ]
        );
    }

    public static function quest(){
		$path = 'quest'; 
		$myfile = fopen("instructions.txt", "a");
		$alert = false ;
		$success = false;
		self::stopRepost('quetes');
		$export = self::exportPresence();
		if (array_key_exists('postdata', $_SESSION)) {
			if (!empty($_SESSION['postdata']['quest'])){
				if (!json_decode($_SESSION['postdata']['quest'])) {
					unset($_SESSION['postdata']);
					self::alertMaker('quetes' , 'Le tableau json n est pas conforme ( attention aux espaces blanc en debut et fin de tableau )');
				}
				$quest_array = json_decode($_SESSION['postdata']['quest']);
				if (!empty($quest_array)) {
					$pdo = new PDO('mysql:dbname=meb;host=localhost' , 'root' , '', array(1002 => 'SET NAMES utf8mb4'));
					$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					// controle de  lintégrité pour la DB :
					foreach ($quest_array as $quest) {
						$quest = (array) $quest;
						$duplicate = self::returnRelation('quest' , 'id' , $quest['ID']  , $pdo);
							if(!empty($duplicate)){
								unset($_SESSION['postdata']);
								self::alertMaker('quetes' , 'la quete existe déja');
							}
							if (self::checkQuest($quest)) {
								unset($_SESSION['postdata']);
								self::alertMaker('quetes' ,self::checkQuest($quest));
							}
						$gameRelation = self::returnRelation('games' , 'id' , $quest['Game']  , $pdo);
							if (empty($gameRelation )) {
								unset($_SESSION['postdata']);
								self::alertMaker('quetes' , ' la quetes est reliée a un jeux non existant dans la base de donnée de test');
							}
					}
					// tout est ok donc insertion :
					foreach ($quest_array as $quest) {
						self::insertQuest((array)$quest , $pdo);
					}
					$dump = new \Ifsnop\Mysqldump\Mysqldump('mysql:dbname=meb;host=localhost', 'root','' , 
							['no-create-info' => true ,
							  'exclude-tables' => [ 'ranks' , 'type_slide' , 'type_poi']
							]);
							$dump->setTransformTableRowHook(function ($tableName, array $row) {
								if ($tableName === 'client_games' or $tableName === 'user' ) {
									$row['id'] = null;
								}
								return $row;
							});
					$dump->start('export.sql');	
					$success = 'la/les quetes ont étés insérées avec success dans la base de données de test ';
				}
				unset($_SESSION['postdata']);
			}
		}
        if (isset($_SESSION['alert'])) {
			$alert = $_SESSION['alert'];
		}
		unset($_SESSION['alert']);
        self::init();
		fclose($myfile);
        return self::$twig->render(
            'home.html.twig',[    
				'path' => $path , 
				'export' => $export, 
				'alert' => $alert, 
				'success' => $success
            ]
        );
    }

	public static function poi(){
		$path = 'poi'; 
		$alert = false ;
		$myfile = fopen("instructions.txt", "a");
		$success = false ;
		self::init();
		self::stopRepost('poi');
		$export = self::exportPresence();
		if (array_key_exists('postdata', $_SESSION)) {
			if (!empty($_SESSION['postdata']['poi'])){
				if (!json_decode($_SESSION['postdata']['poi'])) {
					unset($_SESSION['postdata']);
					self::alertMaker('poi' , 'Le tableau json n est pas conforme ( attention aux espaces blanc en debut et fin de tableau )');
				}
				$poi_array = json_decode($_SESSION['postdata']['poi']);
				if (!empty($poi_array)) {
					$pdo = new PDO('mysql:dbname=meb;host=localhost' , 'root' , '', array(1002 => 'SET NAMES utf8mb4'));
					$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					// controle de  lintégrité pour la DB :
					foreach ($poi_array as $poi) {
						$poi = (array) $poi;
						if (self:: checkPOI($poi)) {
							unset($_SESSION['postdata']);
							self::alertMaker('poi' ,self:: checkPOI($poi));
						}
						$relation = self::returnRelation('quest' , 'id' , $poi['Quest'] , $pdo);
						if (empty($relation)) {
							unset($_SESSION['postdata']);
							self::alertMaker('poi' ,'Le poi est reliée à une quete qui n existe pas dans la base de donnée de test');
						}
						$duplicate = self::returnRelation('poi' , 'id' , $poi['ID'] , $pdo);
						if (!empty($duplicate)) {
							unset($_SESSION['postdata']);
							self::alertMaker('poi' ,'Le poi existe déja dans la base de donnée de test');
						}
					}
					// tout est ok donc insertion :
					foreach ($poi_array as $poi) {
						self::insertPoi((array)$poi , $pdo);
						self::exportTxt($myfile ,self::returnInstructionPoi( (array)$poi));
					}
					$dump = new \Ifsnop\Mysqldump\Mysqldump('mysql:dbname=meb;host=localhost', 'root','' , 
							['no-create-info' => true ,
							  'exclude-tables' => [ 'ranks' , 'type_slide' , 'type_poi'] 
							]);
							$dump->setTransformTableRowHook(function ($tableName, array $row) {
								if ($tableName === 'client_games' or $tableName === 'user' ) {
									$row['id'] = null;
								}
								return $row;
							});
					$dump->start('export.sql');
					$success = ' le/les POI ont étés insérés avec succes dans la base de donnée de test';	
				}
			}
		}
		unset($_SESSION['postdata']);
		if (isset($_SESSION['alert'])) {
			$alert = $_SESSION['alert'];
		}
		unset($_SESSION['alert']);
		fclose($myfile);
		return self::$twig->render(
            'home.html.twig',[    
				'alert' => $alert,
				'path' => $path , 
				'export' => $export , 
				'success' => $success
            ]
        );
	}

	public static function slides(){
		$path = 'slide'; 
		$alert = false ;
		$success = false ;
		$myfile = fopen("instructions.txt", "a");
		self::init();
		self::stopRepost('slide');
		$export = self::exportPresence();
		if (array_key_exists('postdata', $_SESSION)) {
			if (!empty($_SESSION['postdata']['slide'])){
				if (!json_decode($_SESSION['postdata']['slide'])) {
					unset($_SESSION['postdata']);
					self::alertMaker('slide' , 'Le tableau json n est pas conforme ( attention aux espaces blanc en debut et fin de tableau )');
				}

				$slide_array = json_decode($_SESSION['postdata']['slide']);
				if (!empty($slide_array)) {
					$pdo = new PDO('mysql:dbname=meb;host=localhost' , 'root' , '', array(1002 => 'SET NAMES utf8mb4'));
					$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					// controle de  lintégrité pour la DB :
					foreach ($slide_array as $slide) {
						$slide = (array) $slide;
						if (self:: checkSlide($slide)) {
							unset($_SESSION['postdata']);
							self::alertMaker('slide' ,self:: checkSlide($slide));
						}
						$relation = self::returnRelation('poi' , 'id' , $slide['POI'] , $pdo);
						if (empty($relation)) {
							unset($_SESSION['postdata']);
							self::alertMaker('slide' ,'Le slide '.  $slide['Name'] .'  est rataché a une quete qui n existe pas dans la base de donnée de test');
						}
						$duplicate = self::returnRelation('slide' , 'id' , $slide['ID'] , $pdo);
						if (!empty($duplicate)) {
							unset($_SESSION['postdata']);
							self::alertMaker('slide' ,'Le slide '.  $slide['Name'] .'  existe déja dans la base de donnée de test');
						}
					}
					// tout est ok donc insertion :
					foreach ($slide_array as $slide) {
						$slide = (array) $slide;
						self::insertSlide($slide , $pdo);
						self::exportTxt( $myfile, self::returnInstructionSlide( $slide));
					}
					$dump = new \Ifsnop\Mysqldump\Mysqldump('mysql:dbname=meb;host=localhost', 'root','' , 
							['no-create-info' => true ,
							  'exclude-tables' => [ 'ranks' , 'type_slide' , 'type_poi'] 
							]);
							$dump->setTransformTableRowHook(function ($tableName, array $row) {
								if ($tableName === 'client_games' or $tableName === 'user' ) {
									$row['id'] = null;
								}
								return $row;
							});
					$dump->start('export.sql');
					$success = ' le/les Slides ont étés insérés avec succes dans la base de donnée de test';
				}
			}
		}

		unset($_SESSION['postdata']);
		if (isset($_SESSION['alert'])) {
			$alert = $_SESSION['alert'];
		}
		unset($_SESSION['alert']);
		fclose($myfile);
		return self::$twig->render(
            'home.html.twig',[    
				'alert' => $alert,
				'path' => $path , 
				'export' => $export , 
				'success' => $success
            ]
        );
	}

	public static function clientgames(){
		$path = 'clientgame'; 
		$alert = false ;
		$success = false ;
		$myfile = fopen("instructions.txt", "a");
		self::init();
		self::stopRepost('clientgame');
		$export = self::exportPresence();
		if (array_key_exists('postdata', $_SESSION)) {
			if (!empty($_SESSION['postdata']['clientgame'])){
				if (!json_decode($_SESSION['postdata']['clientgame'])) {
					unset($_SESSION['postdata']);
					self::alertMaker('clientgame' , 'Le tableau json n est pas conforme ( attention aux espaces blanc en debut et fin de tableau )');
				}
			}

			$clientgames = json_decode($_SESSION['postdata']['clientgame']);
				if (!empty($clientgames)) {
					$pdo = new PDO('mysql:dbname=meb;host=localhost' , 'root' , '', array(1002 => 'SET NAMES utf8mb4'));
					$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					// controle de  lintégrité pour la DB :
					foreach ($clientgames as $game) {
						$game = (array) $game;
						$relation = self::returnRelation('games' , 'id' , $game['NameJeux'] , $pdo);
						if (empty($relation)) {
							unset($_SESSION['postdata']);
							self::alertMaker('clientgame' ,'Le liens est rattaché à un jeux qui n existe pas ');
						}
						$relation = self::returnRelation('user' , 'id' , $game['NameUser'] , $pdo);
						if (empty($relation)) {
							unset($_SESSION['postdata']);
							self::alertMaker('clientgame' ,'Le liens est rattaché à un utilisateur qui n existe pas ');
						}
					}
					//tous est ok on insert : 
					foreach ($clientgames as $game) {
						$game = (array) $game;
						self::insertClientGames($game , $pdo);	
					}
					$dump = new \Ifsnop\Mysqldump\Mysqldump('mysql:dbname=meb;host=localhost', 'root','' , 
							['no-create-info' => true ,
							  'exclude-tables' => [ 'ranks' , 'type_slide' , 'type_poi'] 
							]);
							$dump->setTransformTableRowHook(function ($tableName, array $row) {
								if ($tableName === 'client_games' or $tableName === 'user' ) {
									$row['id'] = null;
								}
								return $row;
							});
					$dump->start('export.sql');
					$success = ' le/les relations client/jeux ont étés insérées avec succès';
				}

		}
		unset($_SESSION['postdata']);
		if (isset($_SESSION['alert'])) {
			$alert = $_SESSION['alert'];
		}
		unset($_SESSION['alert']);
		fclose($myfile);
		return self::$twig->render(
            'home.html.twig',[    
				'alert' => $alert,
				'path' => $path , 
				'export' => $export , 
				'success' => $success
            ]
        );
	}

    public static function error404()
    {
        self::init();
        return '404';
    }

    public static function returnIdGame($nameGame , $pdo){
        $request = $pdo->query('SELECT id from games WHERE name = "'.$nameGame.'" LIMIT 1');
        $result = $request->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

	public static function returnRelation($table , $fields ,  $name , $pdo){
		$request = $pdo->query('SELECT id from '.$table.' WHERE '.$fields.' = "'.$name.'"');
        $result = $request->fetch(PDO::FETCH_ASSOC);
        return $result;
	}

	public static function empty($table  , $pdo){
		$request = $pdo->prepare('SET foreign_key_checks = 0; 
		DELETE FROM '.$table.'  WHERE 1 = 1 ');
        $result = $request->execute();
        return $result;
	}

	public static function exportPresence(){
		if (file_exists('export.sql')) {
			return true;
		}
		return false;
	}

	public static function exportTxt(  $myfile, $txt){	
			fwrite($myfile, $txt);	
	}
	

    public static function insertQuest( array $quest ,  $pdo){
        $request = $pdo->prepare('INSERT INTO quest ( id ,  game_id , name , color , response_quest)
        VALUES ( :id , :game_id , :name , :color , :response_quest)');
		$request->bindValue(":id" , $quest['ID']);
        $request->bindValue(":game_id" , $quest['Game']);
        $request->bindValue(":name" , $quest['Name']);
        $request->bindValue(":color" , $quest['Color']);
        $request->bindValue(":response_quest" , $quest['Reponse']);
		$request->execute();
		return $pdo->lastInsertID();
    }

	public static  function insertGame(array $game , $pdo){
		$request = $pdo->prepare('INSERT INTO games (id ,  name , destination , cover_path , rules)
        VALUES (:id ,:name , :destination , :cover_path , :rules)');
		$request->bindValue(":id" , $game['ID']);
		$request->bindValue(":name" , $game['Name']);
		$request->bindValue(":destination" , $game['Destination']);
		$request->bindValue(":cover_path" , '/images/games/' . $game['Cover']);
		$request->bindValue(":rules" , $game['Regles']);
		$request->execute();
		return $pdo->lastInsertID();
	}

	public static function insertClientGames(array $game , $pdo){
		$request = $pdo->prepare('INSERT INTO client_games (  user_id , game_id , cost )
        VALUES ( :user_id , :game_id , :cost)');
		$request->bindValue(":user_id" , $game['NameUser']);
		$request->bindValue(":game_id" , $game['NameJeux']);
		$request->bindValue(":cost" , $game['Cost']);
		$request->execute();
		return $pdo->lastInsertID();

	}

	public function dumpSql(array $game){
		$dump = ' /* INSERTION DU JEUX :  '.$game['Name']. ' */ ';
		$dump .= 'INSERT INTO games ( name , destination , cover_path , rules)
        VALUES (:'.$game['Name'].' , :'.$game['Destination'].' , :/images/games/'.$game['Cover'].' , :'.$game['Regles'].')';
		return $dump;
	}

	public static  function insertPoi(array $poi , $pdo){
		
		$type = 1 ;
		switch ($poi['Type']) {
			case 'FLS':
				$type = 1 ;
				break;
			case 'CUL':
				$type = 2 ;
				break;
			case 'OBS':
				$type = 3 ;
				break;
			case 'ENG':
				$type = 4 ;
				break;
			case 'ORI':
				$type = 5 ;
				break;
			case 'SPO':
				$type = 6 ;
				break;
			case 'DUR':
				$type = 7 ;
				break;
		}
		$output_gps = explode(';' , $poi['GPS']);
		$gps = '{ "lat" : '.$output_gps[0].' , "lng" : '.$output_gps[1].' }';

		$cover = null;
		if (!empty($poi['Clue'])) {
			$cover = '/images/clues/' . $poi['Clue'] ;
		}
		
		$request = $pdo->prepare('INSERT INTO poi ( id , quest_id , name , latlng , clue , image_clue , step ,type_poi_id ,radius)
        VALUES ( :id , :quest_id , :name , :latlng , :clue , :image_clue , :step , :type_poi_id , :radius )');
		$request->bindValue(":id" , $poi['ID']);
		$request->bindValue(":quest_id" , $poi['Quest']);
		$request->bindValue(":name" , $poi['Name']);
		$request->bindValue(":latlng" , $gps);
		$request->bindValue(":clue" , $poi['ClueText']);
		$request->bindValue(":image_clue" , $cover);
		$request->bindValue(":step" ,  $poi['Step']);
		$request->bindValue(":type_poi_id" ,  $type);
		$request->bindValue(":radius" ,  $poi['Radius']);

		$request->execute();
		return $pdo->lastInsertID();
	}

	public static function insertSlide(array $slide , $pdo){
		$type = 1;
		switch ($slide['Type']) {
			case 'INF':
				$type = 1;
				break;
			case 'QCM':
				$type = 2;
				break;
			case 'QUO':
				$type = 4;
				break;
			case 'ORI':
				$type = 3;
				break;
			case 'QCP':
				$type = 5;
				break;
		}
		$cover = null;
		if (!empty($slide['Cover'])) {
			$cover = '/images/slides/' . $slide['Cover'] ;
		}
		$request = $pdo->prepare('INSERT INTO slide ( id , poi_id , type_slide_id ,  name , text , text_success , text_fail , time ,step  , response , penality ,cover_path , solution)
        VALUES ( :id , :poi_id , :type_slide_id ,  :name , :text , :text_success , :text_fail , :time , :step  , :response , :penality , :cover_path , :solution )');
		$request->bindValue(":id" , $slide['ID']);
		$request->bindValue(":poi_id" , $slide['POI']);
		$request->bindValue(":type_slide_id" ,$type);
		$request->bindValue(":name" ,$slide['Name']);
		$request->bindValue(":text" , $slide['Text']);
		$request->bindValue(":text_success" ,  $slide['TextSucess']);
		$request->bindValue(":text_fail" ,  $slide['TextFail']);
		$request->bindValue(":time" ,  null);
		$request->bindValue(":step" ,  $slide['Step']);
		$request->bindValue(":response" ,  $slide['Reponse']);
		$request->bindValue(":penality" ,  0);
		$request->bindValue(":cover_path" ,  $cover);
		$request->bindValue(":solution" ,  $slide['Solution']);
		$request->execute();
		return $pdo->lastInsertID();
	}

	
	public static function updateQuest(array $quest,$pdo){

		$request = $pdo->prepare('UPDATE quest SET game_id = ? , color = ? , response_quest = ?
		WHERE name = ?  ');
		$update = $pdo->execute($quest);
	}

	public static function checkGame( array $game_array){
		if (strlen($game_array['Name']) < 3) {
			return 'le nom du jeux semble comporter un problème';
		}

		if (strlen($game_array['Regles']) < 15) {
			return 'les règles du jeux semble etre trop courte';
		}

		return false ;
	}

	public static function checkQuest(array $quest){
		if (strlen($quest['Name']) < 3 or  strlen($quest['Name']) > 50) {
			return 'le nom de la quete '.$quest['Name'].' semble comporter un problème';
		};
		return false ;
	}

	public static function checkPOI(array $poi){
		if (strlen($poi['Name']) < 3 or  strlen($poi['Name']) > 80) {
			return 'le nom du poi  '.$poi['Name'].' semble comporter un problème';
		};
		if (strlen($poi['GPS']) < 3 or  strlen($poi['Name']) > 180) {
			return 'le GPS du POI '.$poi['Name'].' semble comporter un problème';
		};
		if (strlen($poi['ID']) < 3 or  strlen($poi['ID']) > 20) {
			return 'l ID du POI  '.$poi['Name'].' semble comporter un problème';
		};
		if (!strpos( $poi['GPS'] , ';' )) {
			return 'lz GPS du POI  '.$poi['Name'].' semble comporter un problème';
		};
		
		return false ;
	}

	public static function checkSlide($slide){
		if (strlen($slide['Name']) < 3 or  strlen($slide['Name']) > 250) {
			return 'le nom du slide  '.$slide['Name'].' semble comporter un problème';
		};
		if (empty($slide['Step'])) {
			return 'le step du slide  '.$slide['Name'].' semble comporter un problème';
		};
		if (strlen($slide['Type']) != 3 ) {
			return 'le Type du slide  '.$slide['Name'].' semble comporter un problème';
		};
		return false ;
	}

	public static function returnInstructionGame(array $game_array){
		$txt = '';
		if (!empty($game_array['Cover'])) {
			$txt = ' 
			Enregistrer l image : ' . $game_array['Cover'] . 
			' dans la destination suivante /var/www/explorelab.app/images/games/  
			directement sur le server de production 
			
			';
		} 

		return $txt;
	}

	
	public static function returnInstructionPoi(array $poi){
		$txt = '';
		
		if (!empty($poi['Clue'])) {
			$txt .= '
			Enregistrer l image : ' . $poi['Clue'] . 
			' dans la destination suivante /var/www/explorelab.app/images/clues/ 
			 
			';
		}
		return $txt;
	}

	public static function returnInstructionSlide(array $slide){
		$txt = '';

		if (!empty($slide['Cover'])) {
			$txt .= ' 
			Enregistrer l image : ' . $slide['Cover'] . 
			' dans la destination suivante /var/www/explorelab.app/images/slides/  
			
			';
		} 

		if ($slide['Type'] == 'QCP') {
			$txt .=  '
			dans la destination suivante /var/www/explorelab.app/images/qcmp/  
			Créer un dossier nommé : '.$slide['ID'] .' et y insérer les photos de réponses au qcmp 
			
			';
		}

		return $txt;
	}


	public static function stopRepost($path){

		if (!isset($_SESSION)){
			session_start();
		}
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$_SESSION['postdata'] = $_POST;
			unset($_POST);
			header("Location: ".$path);
			exit;
		}
	}

	public static function alertMaker($path , $text){
		if (!isset($_SESSION)){
			session_start();
		}
		$_SESSION['alert'] = $text;
		header("Location: ".$path);
		exit;
	}
}
