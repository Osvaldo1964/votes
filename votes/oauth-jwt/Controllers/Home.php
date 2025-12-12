<?php
    class Home extends Controllers{

        public function __construct()
        {
            parent::__construct();
        }

        public function home($params)
        {
            $data['page_tag'] = "Home";
            $data['page_title'] = "Página principal - Abel OSH";
            $data['page_name'] = "home";
            $this->views->getView($this,"home",$data);
        }


    }


?>