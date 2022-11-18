<?php
    class tipo 
    {
        private $idt; private $nomet;

        function __construct($v_idt, $v_nomet)
        { 
            $this->idt=$v_idt; 
            $this->nomet=$v_nomet;
        }
        public function getIdT()
        {
            return $this->idt;
        }
        public function getNomeT()
        {
            return $this->nomet;
        }

        PUBLIC FUNCTION setIdT ($v_idt)
        {
            $this->idt=$v_idt;
        }
        PUBLIC FUNCTION setNomeT ($v_nomet)
        {
            $this->nomet=$v_nomet;
        }
    }
?>