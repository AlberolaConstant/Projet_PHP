<?php
// require ("modele.class.php");

class requete2{
    protected $c;
    protected $gab;
    protected $tbs;
    protected $res;

    function __construct($param_c, $param_tbs){
        $this->c = $param_c;
        $this->tbs = $param_tbs;
    }
}

class collection extends requete2
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
        // $id = $_SESSION["id"];
        $this->tbs->MergeBlock("idobjet",$idObjetList);
        $this->tbs->MergeBlock("objet",$objetList);
        $this->tbs->Show();
    }
}     
?>