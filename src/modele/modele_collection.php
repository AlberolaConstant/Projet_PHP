<?php
require ("modele.class.php");

class collection extends Requete
{

    public function executer(){
        $this->res = $this->c->prepare("SELECT nom,collection.idrelation FROM objet INNER JOIN collection ON collection.idobjet = objet.idobjet WHERE iduser = ?"); // requete qui nous permet d'avoir le nom des objets qu'un utilisateur possède
        $this->res->execute([$_SESSION["id"]]);
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
}     
?>