<?php

class Model_TroubleTicketClosed extends Model_TroubleTicket {

    function init() {
        parent::init();
        $this->addCondition('site_id', $_GET['id'])->dsql->where(array('DataUsuniecia is not null')); //
        //$this->debug();
    }

}