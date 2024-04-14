<?php
require ("modele.class.php");

class carte{
    private $pdo;
    private $qmat;

    function __construct($param_pdo, $param_tbs) 
    {
        $this->pdo = $param_pdo;
        $this->qmat = new RQ1($this->pdo, $param_tbs, "SELECT * FROM CARTE", "td67tab.tpl.html");         
    }

    public function ajouterCarte(){
        
    }

}
?>