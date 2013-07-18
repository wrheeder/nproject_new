<?php

class Model_Owner extends Model_Table {

    public $table = 'owner';

    function init() {
        parent::init();
        $this->addField('Owner')->mandatory('Owner is Required');
    }

    function getOwnerID($owner) {
        $this->tryLoadBy('Owner', $owner);
        if ($this->loaded()) {
            return $this->id;
        } else {
            $this->set('Owner', $owner);
            $this->save();
            return $this->id;
        }
    }

}