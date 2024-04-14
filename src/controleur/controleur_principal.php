<?php

require("tbs_class.php");
require("connect.inc.php");
require("../modele/modele_collection.php");

$tbs_main = new clsTinyButStrong;
$tbs_main->LoadTemplate("../vue/principal.tpl.html");

$tbs_collection = new clsTinyButStrong;
$tbs_collection->LoadTemplate("../vue/collection.tpl.html");

$tbs_chercher = new clsTinyButStrong;
$tbs_chercher->LoadTemplate("../vue/chercher.tpl.html");

$tbs_collection_public = new clsTinyButStrong;
$tbs_collection_public->LoadTemplate("../vue/collection_publique.tpl.html");

$tbs_expedition = new clsTinyButStrong;
$tbs_expedition->LoadTemplate("../vue/expedition.tpl.html");

$cible = $_SERVER["PHP_SELF"];


session_start(); // redemarrage de la session pour recuperer le login stocké

if ( isset($_SESSION["login"]) ){ // verification que l'utiliateur est bien passé par une authentification

    $pseudo = $_SESSION["login"];
    $id = $_SESSION["id"];

    if (isset($_GET["statut"]) ){
        $statut = $_GET["statut"];
    }else{
        $statut = "";
    }

    try {

        $c = new PDO("mysql:host=$host;dbname=$dbname", $login, $password);
        
    
    } catch(PDOException $erreur){
        $etatConnexion = $erreur->getMessage();
    }

    $collection = new collection($c,$tbs_collection);

    switch ($statut){

        case "deconnection":
            session_destroy(); // on detruit les informations stocké dans la session
            header("Location: controleur_connection.php"); // on redirige l'utilisateur vers la page de connection
            break;

        case "collection": // afficher la collection de l'utilisateur

            $cible = $_SERVER["PHP_SELF"] . "?statut=collection";

            if ( isset($_GET["supr"]) ){

                $res = $c->prepare("DELETE FROM collection WHERE (idrelation = ? and iduser = ?)");
                $res->execute([$_GET["supr"],$_SESSION["id"]]);

            }

            $collection->executer();
            $collection->afficher();

            break;

        case "expedition":

            $cible = $_SERVER["PHP_SELF"] . "?statut=expedition";

            if ( !(isset($_GET["action"])) ){
               $action = "";
            }else{
                $action = $_GET["action"];
            }

            $message = "";

            switch ($action){


                case "tirage":

                    $nom_objet = $collection->tirage();
                    $message = "Vous avez obtenu :" . $nom_objet;
                    $cible = $_SERVER["PHP_SELF"] . "?statut=expedition&action=recolte";

                    $tbs_expedition->Show();

                    break;


                case "recolte":

                    $collection->executer();
                    $collection->afficher();

                    break;


                default:

                    $message = "Appuyez pour démarer l'expedition";
                    $cible = $_SERVER["PHP_SELF"] . "?statut=expedition&action=tirage"; 
                    $tbs_expedition->Show();

            }   

            break;


        case "chercher":

            if ( isset($_GET["voir"]) ){

                $cible = $_SERVER["PHP_SELF"] . "?statut=chercher&voir=" . $_GET["voir"];

                $res = $c->prepare("SELECT nom,collection.idrelation FROM objet INNER JOIN collection ON collection.idobjet = objet.idobjet WHERE iduser = ?"); // requete qui nous permet d'avoir le nom des objets qu'un utilisateur possède
                $res->execute([$_GET["voir"]]);

                $idObjetList = array();
                $objetList = array();

                foreach($res as $ligne) { 

                    array_push($objetList,$ligne["nom"]); // on récupère le resultat de la requete pour le stocker dans objetList
                    array_push($idObjetList,$ligne["idrelation"]); 

                }

                $id = $_GET["voir"]; // peut etre a supprimé si ca sert a rien 

                if (isset ($_GET["supr"]) ){
                    $res = $c->prepare("DELETE FROM collection WHERE (idrelation = ? and iduser = ?)");
                    $res->execute([$_GET["supr"],$_GET["voir"]]);
                }

                $res = $c->prepare("SELECT nom,collection.idrelation FROM objet INNER JOIN collection ON collection.idobjet = objet.idobjet WHERE iduser = ?"); // requete qui nous permet d'avoir le nom des objets qu'un utilisateur possède
                $res->execute([$_GET["voir"]]);

                $idObjetList = array();
                $objetList = array();

                foreach($res as $ligne) { 

                    array_push($objetList,$ligne["nom"]); // on récupère le resultat de la requete pour le stocker dans objetList
                    array_push($idObjetList,$ligne["idrelation"]); 

                }     

                if ( $_SESSION["droit"] == 0 )
                {

                    $tbs_collection->MergeBlock("idobjet",$idObjetList);
                    $tbs_collection->MergeBlock("objet",$objetList);
                    $tbs_collection->Show();

                }else{

                    $tbs_collection_public->MergeBlock("objet",$objetList);
                    $tbs_collection_public->Show();

                }
                
                
            }else{

                if (isset($_POST["chercher"])){
                    $recherche = $_POST["chercher"];
                }else{
                    $recherche = "";
                }
                
                $res = $c->prepare("SELECT iduser,login FROM user WHERE login LIKE '%' ? '%' ");
                $res->execute([$recherche]);

                $idList = array();
                $userList = array();

                foreach($res as $ligne) { 

                    array_push($idList,$ligne["iduser"]); // on récupère le resultat de la requete pour le stocker dans objetList
                    array_push($userList,$ligne["login"]); 

                }

                $tbs_chercher->MergeBlock("id",$idList);
                $tbs_chercher->MergeBlock("user",$userList);
                $tbs_chercher->Show();
                
            }

            break;

        default:
            
    }

    $tbs_main->Show();  

}else{
    $pseudo = "Erreur";
    echo 'Erreur lors du chargement de la page, verifiez que vous êtes bien connecté.';
}


?>