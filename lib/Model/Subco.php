<?php

class Model_Subco extends Model_Table {

    public $entity_code = 'subco';

    function init() {
        parent::init();
        $this->addField('subco')->mandatory('Subco is Required');
//        if($this->api->auth->get('subco_id')){
//            $this->addCondition('subco_id',$this->api->auth->get('subco_id'));
//        }
        $this->dsql()->order('subco');
    }

    function getSubcoID($subco) {
        if ($subco != '' && $subco != null && $subco != 'Subcon') {
            $this->tryLoadBy('subco', $subco);
            if ($this->loaded()) {
                return $this->id;
            } else {
                $this->set('subco', $subco);
                $this->save();
                return $this->id;
            }
        } else {
            return null;
        }
    }

}