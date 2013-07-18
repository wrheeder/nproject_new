<?php
class Page_siteManager_polist extends Page_ApplicationPage {

    function init() {
        parent::init();
        $m = $this->add('Model_poList');
        $m->addCondition('csc',$_GET['id']);
        $crud=$this->add('CRUD')->setModel($m);
    }
}
