<?php

class Model_ReportAll extends Model_Table {

    //  public $table = 'troubletickets';
    public $table = 'site';
    public $id_field = 'csc';

    function init() {
        parent::init();
//        $this->addField('Uwagi')->type('Wrap');
//        $this->addField('Kategoria');
//        $this->addField('DataWpisu')->type('DateTime')->Caption('ZgloszeniePL');
//        $this->addField('DataUsuniecia')->type('DateTime')->Caption('DataZamknieciaUsterek');
//        $this->addField('OsobaKontaktowa')->type('Wrap');
//        $this->addField('data_wyslania')->type('DateTime')->Caption('DataWyslaniaDoSUB');
//
//        $def_group = $this->join('defgroup');
//        $def_group->addField('kod');
//        $def_group->addField('opis')->type('text');
//        $subpo = $this->join('po_subco.po', 'Zamowienie','left');
//        $this->addField('Zamowienie')
//        ->visible(true)
//        ->editable(true)
//        ->caption('Zamowienie')->mandatory('PO Required');
//        $subco = $subpo->join('subco.id', 'subco_id', 'left');
//        $subco->addField('subco')->readonly();
//        //$subpo->addField('data_wyslania')->type('datetime')->Caption('DataWyslaniaDoSUB');
//        $this->hasOne('Author', 'author_id', 'Author');
//        $this->hasOne('Vendor', 'vendor_id', 'Dostawca');
//        $this->hasOne('Region', 'region_id', 'Region');
//        $this->hasOne('TTOwner', 'ttowner_id', 'ttowner')->Caption('Wlasciciel');
//        $this->addField('DataUsunieciaPrzezSub')->type('Date')->Caption('DataUsunieciaPrzezSub');
//        $site = $this->join('site.csc','site_id','right');
//        
//        $site->hasOne('Owner', 'Owner_id', 'Owner');
        $this->getField('csc')->Caption('NrNetWorkS')->editable(true)->visible(true);
        $this->addField('NrPTC');
        $this->addField('NazwaStacji');
        //$this->addField('TypUrzadzenia');
        $this->addField('DataZgloszenia')->Caption('DataZgloszeniaUsunieciaPL');
        $this->hasOne('Owner', 'Owner_id', 'Owner');
        $tt = $this->join('troubletickets.site_id', 'csc');
        $tt->addField('id')->visible(true);
        $tt->addField('Uwagi')->type('wrap');
        $tt->addField('Kategoria');
        $tt->addField('DataWpisu')->type('datetime')->Caption('ZgloszeniePL');
        $tt->addField('DataUsuniecia')->type('datetime')->Caption('DataZamknieciaUsterek');
        $tt->addField('OsobaKontaktowa')->type('wrap');
        $tt->addField('data_wyslania')->type('datetime')->Caption('DataWyslaniaDoSUB');
        $def_g = $tt->join('defgroup', 'defgroup_id', 'left');
        $def_g->addField('opis');
        $def_g->addField('kod');
        $tt->hasOne('Author', 'author_id', 'Author');
        $tt->hasOne('Vendor', 'vendor_id', 'Dostawca');
        $tt->hasOne('Region', 'region_id', 'Region');
        $tt->hasOne('TTOwner', 'ttowner_id', 'ttowner')->Caption('Wlasciciel');
        $tt->addField('DataUsunieciaPrzezSub')->type('datetime')->Caption('DataUsunieciaPrzezSub');
        $tt->addField('liczbaodrzucenodbiorupousterk');
        $subpo=$tt->join('po_subco.po', 'Zamowienie', 'left');
        $tt->addField('Zamowienie')
                ->visible(true)
                ->editable(true)
                ->caption('Zamowienie')->mandatory('PO Required');
        $subco = $subpo->join('subco.id', 'subco_id', 'left');
        $subco->addField('subco')->readonly();
    }

}

?>