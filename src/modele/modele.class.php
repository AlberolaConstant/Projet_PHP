<?php
class Requete {
    
    protected $c;
    protected $tbs;
    protected $res;

    function __construct($param_c, $param_tbs){
        $this->c = $param_c;
        $this->tbs = $param_tbs;
    }
}
?>