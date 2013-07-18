<?php

class Model_Site extends Model_Table {

    public $table = 'site';
    public $id_field = 'csc';
    public $records_updated = 0;
    public $records_added = 0;
    public $upload_log = '';

    function init() {
        parent::init();
        set_time_limit(0);
        $po_array = array();
        if ($this->api->auth->get('subco_id') != null) {
            $po_subco = $this->api->db->dsql()->table('po_subco')->field('po')->where('subco_id', $this->api->auth->get('subco_id'));
            $po_subco->do_getAll();
            //$po_subco->debug();
            
            foreach ($po_subco as $po) {
                $po_array[] = $po['po'];
            }
            $tts = $this->api->db->dsql()->table('troubletickets')->field('site_id')->where('zamowienie in', implode(',', $po_array))->group('site_id')->do_getAll();
            $site_array = array();

            foreach ($tts as $tt) {
                $site_array[] = $tt['site_id'];
            }
            $this->addCondition('csc', 'in', implode(',', $site_array));
        }
        $this->getField('csc')->Caption('NrNetWorkS')->editable(true)->visible(true)->mandatory(true);
        $this->addField('NrPTC');
        $this->addField('NazwaStacji');
        $this->addField('NazwaPTC');
        $this->addField('NazwaPTK');
        $this->addField('DataZgloszenia')->type('datetime')->Caption('DataZgloszeniaUsunieciaPL');
        $this->hasOne('Owner', 'Owner_id', 'Owner')->mandatory('Owner is Required');
        $this->hasMany('TroubleTicket');
        $this->addExpression('TT_list')->set($this->refSQL('TroubleTicket')->group_concat('id'))->type('text');
        $this->addExpression('PO_list')->set($this->refSQL('TroubleTicket')->group_concat('Zamowienie', true))->type('text');
        $this->addHook('beforeSave', $this);
    }

    function beforeSave($m) {

        if ($m->loaded()) {
            //$this->upload_log.='Updating</br>';
            $this->records_updated++;
        } else {
            //$this->upload_log.='Adding</br>';
            $this->records_added++;
        }
    }

    function getLog() {
        $this->upload_log = 'Updated ' . $this->records_updated . ' sites</br>Created ' . $this->records_added . ' sites</br>';
        return $this->upload_log;
    }
    function getSiteID($csc,$site_out){
        $this->tryLoadBy('csc',$csc);
        if($this->loaded()){
            //die(var_dump($site_out));
            $this->set($site_out);
            $this->save();
            return $this->id;
        }else
        {
            //die(var_dump($site_out));
            $this->set($site_out);
            $this->save();
            return $this->id;
        }
    }
}