<?php
class Model_DefGroup extends Model_Table{
    public $entity_code = 'defgroup';
    function init(){
        parent::init();
        $this->addField('kod')->mandatory('Kode is Required');
        $this->addField('opis')->mandatory('Description is Required')->type('text');
    }
    function getDefGroupID($defGroup,$desc){
        $this->tryLoadBy('kod',$defGroup);
        if($this->loaded()){
            return $this->id;
        }
        else{
            $this->set(array('kod'=>$defGroup,'opis'=>$desc));
            $this->save();
            return $this->id;
        }
    }
}