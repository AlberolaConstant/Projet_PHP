<?php
require ("modele.class.php");

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


?>