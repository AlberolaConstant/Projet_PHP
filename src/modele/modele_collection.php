<?php
require ("modele.class.php");

class collection extends Requete
{
    public function executer($id){
        $this->res = $this->c->prepare("SELECT nom,collection.idrelation FROM objet INNER JOIN collection ON collection.idobjet = objet.idobjet WHERE iduser = ?"); // requete qui nous permet d'avoir le nom des objets qu'un utilisateur possède
        $this->res->execute([$id]);
    }
    
    public function afficher(){

        $objetList = array();
        $idObjetList = array();

        foreach($this->res as $ligne) { 

            array_push($objetList,$ligne["nom"]); // on récupère le resultat de la requete pour le stocker dans objetList
            array_push($idObjetList,$ligne["idrelation"]);

        }

        $this->tbs->MergeBlock("idobjet",$idObjetList);
        $this->tbs->MergeBlock("objet",$objetList);
        $this->tbs->Show();
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