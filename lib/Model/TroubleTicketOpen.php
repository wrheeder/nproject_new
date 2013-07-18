<?php

class Model_TroubleTicketOpen extends Model_TroubleTicket {

    function init() {
        parent::init();
        $this->addCondition('site_id', $_GET['id'])->dsql->where(array('DataUsuniecia is null')); //
        //$this->debug();
    }

}