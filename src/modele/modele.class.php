<?php
class Requete {
    
    protected $c;
    protected $tbs;

    function __construct($param_c, $param_tbs){
        $this->c = $param_c;
        $this->tbs = $param_tbs;
    }
}

class User extends Requete{

    public function executer($recherche){
        $this->res = $this->c->prepare("SELECT iduser,login FROM user WHERE login LIKE '%' ? '%' ");
        $this->res->execute([$recherche]);
    }

    public function afficher(){
        $idList = array();
        $userList = array();

        foreach($this->res as $ligne) { 

            array_push($idList,$ligne["iduser"]); // on récupère le resultat de la requete pour le stocker dans objetList
            array_push($userList,$ligne["login"]); 

        }

        $this->tbs->MergeBlock("id",$idList);
        $this->tbs->MergeBlock("user",$userList);
        $this->tbs->Show();
    }
}

class Collection extends Requete
{
    private $nomuser;

    public function executer($id){
        $this->res = $this->c->prepare("SELECT nom,collection.idrelation FROM objet INNER JOIN collection ON collection.idobjet = objet.idobjet WHERE iduser = ?"); // requete qui nous permet d'avoir le nom des objets qu'un utilisateur possède
        $this->res->execute([$id]);

        $this->nomuser = $this->c->prepare("SELECT login FROM user WHERE iduser = ?"); // recuperation du nom de la collection que l'on regarde
        $this->nomuser->execute([$id]);
    }
    
    public function afficher($tbs){

        $objetList = array();
        $idObjetList = array();

        foreach($this->res as $ligne) { 

            array_push($objetList,$ligne["nom"]); // on récupère le resultat de la requete pour le stocker dans objetList
            array_push($idObjetList,$ligne["idrelation"]);

        }

        foreach ($this->nomuser as $ligne){
            $nom = $ligne["login"];
        }

        $GLOBALS["nom"] = $nom;
        $tbs->MergeBlock("idobjet",$idObjetList);
        $tbs->MergeBlock("objet",$objetList);
        $tbs->Show();
    }

    public function tirage(){
        $id = rand(1,2); // génération du numéro aléatoire 
        $res = $this->c->prepare("INSERT INTO collection (idobjet, iduser) VALUES (?,?);"); 
        $res->execute([$_SESSION["id"],$id]);  // on insère un objet tiré aléatoirement dans la collection de l'utilisateur

        $this->res = $this->c->prepare("SELECT nom FROM objet where idobjet = ?"); 
        $this->res->execute([$id]);  // on insère un objet tiré aléatoirement dans la collection de l'utilisateur

        foreach($this->res as $ligne) { 

            $nom_objet = $ligne["nom"];

        }
        return $nom_objet;
    }
}     
?>