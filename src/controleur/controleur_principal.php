<?php

require("tbs_class.php");
require("connect.inc.php");
require("../modele/modele_collection.php");
require("../modele/modele_user.php");

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

    $collection = new Collection($c,$tbs_collection);
    $recherche = new User($c,$tbs_chercher);

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

            $collection->executer($_SESSION["id"]);
            $collection->afficher($tbs_collection);

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

                    $collection->executer($_SESSION["id"]);
                    $collection->afficher($tbs_collection);

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
                
                if (isset ($_GET["supr"]) ){
                    $res = $c->prepare("DELETE FROM collection WHERE (idrelation = ? and iduser = ?)");
                    $res->execute([$_GET["supr"],$_GET["voir"]]);
                }

                $collection->executer($_GET["voir"]);

                if ( $_SESSION["droit"] == 0 )
                {
                    $collection->afficher($tbs_collection);

                }else{
                    $collection->afficher($tbs_collection_public);
                }
                
                
            }else{

                if (isset($_POST["chercher"])){
                    $val = $_POST["chercher"];
                }else{
                    $val = "";
                }
                
                $recherche->executer($val);
                $recherche->afficher();
                
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