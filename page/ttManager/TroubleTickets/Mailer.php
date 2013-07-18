<?php

class Page_ttManager_TroubleTickets_Mailer extends Page {

    function init() {
        parent::init();
        $mail = $this->add('TMail');
        //$mail->loadTemplate('test');

        //$mail->addTransport('echo');
        $patterns = array();
        $repl = array();
        $patterns[0] = '/"/';
        $patterns[1] = '/on,/';
        $patterns[2] = '/on/';
        $patterns[3] = '/,]/';
        $patterns[4] = '/\]/';
        $patterns[5] = '/\[/';
        $repl[0] = '';
        $repl[1] = '';
        $repl[2] = '';
        $repl[3] = '';
        $repl[4] = '';
        $repl[5] = '';

        $f = $this->add('Form');
        $sel = '';
        $subj = '';
        if ($_GET['checked']) {
            $sel = preg_replace($patterns, $repl, $_GET['checked']);
            $subj = 'Site :'. $_GET['id'] .'_%subco% - TTs :'. $sel ;
            
        }
        $this->api->stickyGet('id');
        $this->api->stickyGet('checked');
        $f->addField('line', 'sel_tts')->set($sel)->js(true)->attr('readonly', true);


        $crud = $f->add('Grid');
        $sel = array(explode(',', $sel));
        $tt = $this->api->db->dsql()
                ->table('troubletickets')
                ->field('zamowienie')
                ->where('id in', $sel[0])
                ->do_getOne();
        $sub = $this->api->db->dsql()
                ->table('po_subco')
                ->field('subco_id')
                ->where('po', $tt)
                ->do_getOne();
        $sub_name = $this->api->db->dsql()
                ->table('subco')
                ->field('subco')
                ->where('id', $sub)
                ->do_getOne();
        $subj=str_replace("%subco%", $sub_name, $subj);
        $model = $f->add('Model_SubcoDL')->addCondition('subco_id', $sub);
        $crud->setModel($model);

        $f_checked = $f->addField('line', 'checked_mails');

        $f_checked->js(true)->closest('.atk-form-row-line ')->hide();
        $crud->addSelectable($f_checked);
        $add_mails=$f->addField('line','add_mails')->setFieldHint('Additional Mails not on Distribution List(Comma Seperated)');;
        $f->addSubmit();

        $this->api->stickyGet('selected');
        
        if ($f->isSubmitted()) {
            $dl = preg_replace($patterns, $repl, $f->get('checked_mails'));
            $mailer_list = $this->api->db->dsql()->table('subco_dl')->field('email')->where('id in', $dl)->do_getAll();

            $dl = array();
//            $to='willem.rheeder@baysidehw.com';
//            $mail->send($to);
            $headers = array('id', 'Zamowienie', 'NrNetWorkS', 'NazwaStacji', 'Opis', 'Uwagi', 'Kategoria', 'Subco', 'ZgloszeniePL', 'DataWyslaniaDoSUB', 'DataZgloszeniaUsunieciaPL', 'DataZamknieciaUsterek', 'OsobaKontaktowa', 'author', 'region', 'Wlasciciel');
            $tt = $this->add('Model_ReportAll', $headers)->addCondition('id','in',$f->getElement('sel_tts')->get());
//            $this->api->stickyGet('sel_tts');

            $tts = explode(',', $f->getElement('sel_tts')->get());
            $tt_upd = $this->add('Model_TroubleTicket');
//            $tt_mail_array= array();
            foreach ($tts as $ticket) {
                $tt_upd->tryLoad($ticket); 
                if(!$tt_upd->get('data_wyslania')){
                    $tt_upd->set('data_wyslania', date("Y-m-d"));
//                    //add to be mailed
//                    $tt_mail_array[$tt_upd['subco_id']][]=$tt_upd['id'];
                    $tt_upd->save();
                }
                
            }
            $v = $this->add('View_listTTs');
            $v->setModel($tt);
            $o = $v->getHTML();
            $mail->setHTML($o);
            $dl[] = $this->api->auth->get('email');
            $dl[] = 'willem.rheeder@baysidehw.com';
            //$mail->AddAddress('willem.rheeder@baysidehw.com');
            foreach ($mailer_list as $contact) {
                $dl[] = $contact['email'];
                //$mail->AddAddress($contact['email']);
            }
            $js = array();
            $js[] = $this->js(true)->univ()->closeDialog();
            $js[] = $this->js(true)->closest('.reloadable')->trigger('myreload');
            $mail->set('subject',$subj);
            if($add_mails->get()!=null){
                $dl[]=$add_mails->get();
            }
            $mail->send(implode(',', $dl));
            $f->js(true,$js)->univ()->successMessage('TTs sent to selected Subcontractors')->execute();
        }
    }

}