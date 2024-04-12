<?php

// require("user.php");
require("connect.inc.php");
require("tbs_class.php");

$tbs = new clsTinyButStrong;
$cible = $_SERVER["PHP_SELF"];
$tbs->LoadTemplate("../vue/connexion.tpl.html");

if (isset($_POST["login"])){   

    try {

        $c = new PDO("mysql:host=$host;dbname=$dbname", $login, $password);
        
    
    } catch(PDOException $erreur){
        $etatConnexion = $erreur->getMessage();
    }

    $req_login = $_POST["login"];
    $req_password = $_POST["password"];

    $res = $c->prepare("SELECT iduser,login,password,droit FROM user;"); // recuperation des utilisateurs avec leurs mdp 
    $res->execute();

    $listeLog = array();
    $listePas = array();

    foreach($res as $ligne) { // on verifie pour chaque utilisateur si le mdp et le login entré correspond

        if ( ($ligne["login"] == $req_login) && ($ligne["password"] == $req_password)){
            
            session_start(); // on demmarre une session pour stocker le login et le password
            $_SESSION["login"] = $req_login;
            $_SESSION["password"] = $req_password;
            $_SESSION["id"] = $ligne["iduser"];
            $_SESSION["droit"] = $ligne["droit"];

            header("Location: /~ebaroudi/projet/src/controleur/controleur_principal.php"); // on redirige l'utilisateur sur la page principale
            exit();

            break;

        }else{
            $message = "Mot de passe ou login incorrect";
        }
        
        
    }

    if ( ($_POST["login"] == "") && ($_POST["password"] == "") )
    {
        $message = "Veuillez saisir un login et un password";
    }

}else{

    $message = "Veuillez saisir un login et un password";

}

$tbs->Show();




?>