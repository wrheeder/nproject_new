<?php
class Model_poList extends Model_CustModel {

    public $entity_code = 'polist';

    function init() {
        parent::init();
        $this->addField('po');//->mandatory('');
        $this->addField('csc');//->mandatory('');
    }
}