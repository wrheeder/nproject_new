<?php

class Model_Vendor extends Model_Table {

    public $entity_code = 'vendor';

    function init() {
        parent::init();
        $this->addField('Dostawca')->mandatory('Vendor is Required')->type('text');
    }

    function getVendorID($vendor) {
        $this->tryLoadBy('Dostawca', $vendor);
        if ($this->loaded()) {
            return $this->id;
        } else {
            $this->set('Dostawca', $vendor);
            $this->save();
            return $this->id;
        }
    }

}