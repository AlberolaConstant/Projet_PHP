<?php // td5modele.class.php    
class Requete {        
    protected $pdo;   // Identifiant de connexion        
    protected $tbs;   // Moteur de template           
    protected $req;   // Requête SQL                
    protected $gab;   // Nom de gabarit                  
    protected $data;  // Résultat de requête        
    function __construct($param_pdo, $param_tbs, $param_req, $param_gab) 
    {            
        $this->pdo = $param_pdo;            
        $this->tbs = $param_tbs;            
        $this->req = $param_req;            
        $this->gab = $param_gab;        
    }        
    public function executer() {            
        $res = $this->pdo->prepare($this->req);            
        $res->execute();            
        $this->data = $res->fetchAll();        
    }    
}

class RQ1 extends Requete {            
    public function afficher() {                // Préparation des données                
        $i = 0;                
        $listeCode = array();                
        $listeLibe = array();                  
        $listeCoef = array();                
        foreach($this->data as $ligne) {                    
            $listeCode[$i++] = $ligne["codemat"];                    
            $listeLibe[$i++] = $ligne["libelle"];                    
            $listeCoef[$i++] = $ligne["coef"];                
        }                               
        // Affichage du gabarit                
        $this->tbs->LoadTemplate($this->gab);                
        $this->tbs->MergeBlock("codemat", $listeCode);                
        $this->tbs->MergeBlock("libelle", $listeLibe);                
        $this->tbs->MergeBlock("coef",    $listeCoef);                
        $this->tbs->Show();                 
    }     
}     
class RQ2 extends Requete {            
    public function afficher() {                // Préparation des données                
        $i = 0;                
        $listeNom = array();               
        $listePre = array();                  
        $listeNot = array();                
        $listeLib = array();                
        foreach($this->data as $ligne) {                    
            $listeNom[$i++] = $ligne["nom"];                    
            $listePre[$i++] = $ligne["prenom"];                    
            $listeNot[$i++] = $ligne["note"];                    
            $listeLib[$i++] = $ligne["libelle"];                
        }                               // Affichage du gabarit                
        $this->tbs->LoadTemplate($this->gab);                
        $this->tbs->MergeBlock("nom", $listeNom);                
        $this->tbs->MergeBlock("prenom", $listePre);                
        $this->tbs->MergeBlock("note", $listeNot);                
        $this->tbs->MergeBlock("libelle", $listeLib);                
        $this->tbs->Show();                 
    }     
} 
        ?>