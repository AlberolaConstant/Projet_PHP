<?php
// require ("modele.class.php");

class requete2{
    protected $c;
    protected $res;
    protected $gab;
    protected $tbs;

    function __construct($param_c, $param_res, $param_tbs){
        $this->c = $param_c;
        $this->res = $param_res;
        // $this->gab = $param_gab;
        $this->tbs = $param_tbs;
    }
}

class collection extends requete2
{

    public function executer(){
        $res = $this->c->prepare("SELECT nom,collection.idrelation FROM objet INNER JOIN collection ON collection.idobjet = objet.idobjet WHERE iduser = ?"); // requete qui nous permet d'avoir le nom des objets qu'un utilisateur possède
        $res->execute([$_SESSION["id"]]);
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