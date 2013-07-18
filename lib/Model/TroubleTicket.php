<?php

class Model_TroubleTicket extends Model_CustModel {

    public $table = 'troubletickets';
    public $records_updated = 0;
    public $records_added = 0;
    public $upload_log = '';

    function init() {
        parent::init();
        set_time_limit(0);
        $this->addField('Uwagi')->type('text');
        $this->addField('Kategoria');
        $this->addField('Zamowienie');
        $this->addField('DataWpisu')->type('date')->Caption('ZgloszeniePL');
        $this->addField('DataUsuniecia')->type('date')->Caption('DataZamknieciaUsterek');
        $this->addField('OsobaKontaktowa')->type('text');
        $this->addField('data_wyslania')->type('date')->Caption('DataWyslaniaDoSUB');
        $this->addField('DataUsunieciaPrzezSub')->type('date')->Caption('DataUsunieciaPrzezSub')->editable(false);
        $this->addField('Status')->type('boolean')->caption('Status');
        $this->addField('liczbaodrzucenodbiorupousterk');
        $this->hasOne('Site', 'site_id', false);
        $this->hasOne('DefGroup', 'defgroup_id', 'kod')->Caption('Kod');
        $this->hasOne('Author', 'author_id', 'Author')->mandatory('Author Required');
//        $this->hasOne('Vendor', 'vendor_id', 'Dostawca')->mandatory('Vendor Required');
//        $this->hasOne('Region', 'region_id', 'Region')->mandatory('Region Required');
        $this->hasOne('TTOwner', 'ttowner_id', 'ttowner')->defaultValue(2);
        $this->addHook('beforeSave', $this);
    }

    function beforeSave($m) {
        if ($m->loaded()) {
            $this->records_updated++;
            if($m->get('Status')==1){
               $m->set('DataUsunieciaPrzezSub',date("Y-m-d H:i:s"));                
            }else
            {
               $m->set('DataUsunieciaPrzezSub',null);   
            }
        } else {
            $this['ttowner_id']=2;
            $this->records_added++;
        }
    }

    function getLog() {
        $this->upload_log = 'Updated ' . $this->records_updated . ' TroubleTickets</br>Created ' . $this->records_added . ' TroubleTickets</br>';
        return $this->upload_log;
    }

}