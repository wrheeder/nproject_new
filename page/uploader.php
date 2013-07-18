<?php

class page_uploader extends Page_ApplicationPage {

    function init() {
        if (!$this->api->auth->isUploadManager()) {
            $this->api->redirect('index');
        }
        parent::init();

        //$mail->addTransport('echo');
        set_time_limit(0);
        //die(ini_get('max_execution_time'));
        $v = $this->add('View_Columns');
        $g = $v->addColumn(6);
        $g->add('H3')->set('Upload N!Project xls');

        $f = $g->add('Form');

        $upl1 = $f->addField('upload', 'MyFile')->validateNotNull();
        $upl1->template->set('after_field', 'Max size: 64Mb');

        $fs = $upl1->setModel('filestore/MyFile');
        //$fs->debug();
        $fs->setVolume('TroubleTickets');
        $process = $f->addSubmit('Process file');
       // $f->js('submit', "document.body.style.cursor = 'wait'");
        $info1 = $f->add('View_Info');
        $info1->set('Summary of upload :');

        ///////////////Replace this with static field list 

        $upl_flds_site = array('NrNetWorkS', 'NrPTC', 'NazwaStacji', 'NazwaPTC', 'NazwaPTK', 'NE_Urzadzenie', 'TypUrzadzenia', 'Owner'); // $this->add('Model_UploadFields')->addCondition('model', 'site');
        $upl_flds_tts = array('Uwagi', 'Kategoria', 'Zamowienie', 'DataWpisu', 'DataUsuniecia','liczbaodrzucenodbiorupousterk', 'Site_ID', 'DefGroup_id', 'Author_id', 'Region_id', 'Vendor_id', 'DataZgloszenia', 'OsobaKontaktowa', 'data_wyslania'); //$this->add('Model_UploadFields')->addCondition('model', 'troubleticket');
        ////////////////////////////////////////////////////

        $g2 = $v->addColumn(6);
        $g2->add('H3')->set('Upload Subcontractor Mapping xls');
        $f2 = $g2->add('Form');
        $upl2 = $f2->addField('upload', 'MyFile')->validateNotNull();
        $f2->addSubmit('Process file');
        $fs1 = $upl2->setModel('filestore/MyFile');
        $fs1->setVolume('IPM');

        //$upl->js('change',$process->js(true)->removeAttr('disabled')->attr('aria-disabled','false')->removeClass('ui-button-disabled ui-state-disabled'));
        if ($f->isSubmitted()) {
            $fs->tryLoad($upl1->form->data);
            $furl = './' . $fs['dirname'] . '/' . $fs['filename'];
//            $fh = fopen($furl, "rb");
//            $data = fread($fh, filesize($furl));
//
//            fclose($fh);
            $data = file_get_contents($furl);

            $site = $this->add('Model_Site');
            $owner = $this->add('Model_Owner');
            $tt = $this->add('Model_TroubleTicket');
            $defg = $this->add('Model_DefGroup');
            $auth = $this->add('Model_Author');
            $vendor = $this->add('Model_Vendor');
            $region = $this->add('Model_Region');

            if (file_exists($furl)) {
                $data = preg_replace('/&[^; ]{0,6}.?/e', "((substr('\\0',-1) == ';') ? '\\0' : '&amp;'.substr('\\0',1))", $data);
                $data = preg_replace('/\001/e', " ", $data);
                $xml = new SimpleXMLElement($data);
                $cols_cnt = 28; // $xml->Worksheet->Table->attributes();
                $row_cnt = $xml->Worksheet->Table->Row->count();


                //echo $cols_cnt.'....'.$row_cnt.'</br>';
                //die(var_dump($xml->Worksheet->Table->Column->count()));
                $k = 0;

                $headers = array();
                $data_out = array();
                $site_out = array();
                $site_line = array();
                $tt_out = array();
                $tt_line = array();
                $sub_mail_list = array();
                for ($j = 0; $j < $row_cnt; $j++) {
                    $row_out = array();
                    if ($j > 0)
                        $row_out = $headers;
                    for ($i = 0; $i < $cols_cnt; $i++) {
                        if ($j == 0) {
                            $data_out[0][$i] = (String) $xml->Worksheet->Table->Row[$j]->Cell[$i]->Data;
                            $headers[(String) $xml->Worksheet->Table->Row[$j]->Cell[$i]->Data] = null;
                        } else {
                            $row_out[$data_out[0][$i]] = (String) $xml->Worksheet->Table->Row[$j]->Cell[$i]->Data;
                        }

                        $k++;
                    }
                    if ($j != 0) {
                        $site_line['csc'] = null;
                        $site_line['NrNetWorkS'] = null;
                        $site_line['NrPTC'] = null;
                        $site_line['NazwaStacji'] = null;
                        $site_line['NazwaPTC'] = null;
                        $site_line['NazwaPTK'] = null;
                        $site_line['NE_Urzadzenie'] = null;
                        $site_line['TypUrzadzenia'] = null;
                        $site_line['Owner'] = null;
                        if ($row_out['NrNetWorkS'] != null && $row_out['NrNetWorkS'] != '') {
                            $site_line['csc'] = $row_out['NrNetWorkS'];
                            $site_line['NrNetWorkS'] = $row_out['NrNetWorkS'];
                            $site_line['NrPTC'] = $row_out['NrPTC'];
                            $site_line['NazwaStacji'] = $row_out['NazwaStacji'];
                            $site_line['NazwaPTC'] = $row_out['NazwaPTC'];
                            $site_line['NazwaPTK'] = $row_out['NazwaPTK'];
                            $site_line['NE_Urzadzenie'] = $row_out['NE_Urzadzenie'];
                            $site_line['TypUrzadzenia'] = $row_out['TypUrzadzenia'];
                            $site_line['Owner'] = $row_out['Owner'];
                            $site_line['Owner_id'] = $owner->getOwnerID($row_out['Owner']);
                            //$site_id = $site->getSiteID($site_line['csc'], $site_line);
                        }
                        $site_out[$row_out['NrNetWorkS']] = $site_line;

                        $tt_line['id'] = null;
                        $tt_line['site_id'] = null;
                        $tt_line['Uwagi'] = null;
                        $tt_line['Kategoria'] = null;
                        $tt_line['Zamowienie'] = null;
                        $tt_line['DataWpisu'] = null;
                        $tt_line['DataUsuniecia'] = null;
                        $tt_line['Site_ID'] = null;
                        $tt_line['DefGroup_id'] = null;
                        $tt_line['Author_id'] = null;
                        $tt_line['Region_id'] = null;
                        $tt_line['Vendor_id'] = null;
                        $tt_line['DataZgloszenia'] = null;
                        $tt_line['OsobaKontaktowa'] = null;
                        $tt_line['data_wyslania'] = null;
                        $tt_line['liczbaodrzucenodbiorupousterk'] = null;
                        if ($row_out['ID'] != null && $row_out['ID'] != '' && $row_out['NrNetWorkS'] != null && $row_out['NrNetWorkS'] != '') {
                            $tt_line['id'] = $row_out['ID'];
                            $tt_line['site_id'] = $row_out['NrNetWorkS'];
                            $tt_line['Uwagi'] = $row_out['Uwagi'];
                            $tt_line['Kategoria'] = $row_out['Kategoria'];
                            $tt_line['Zamowienie'] = $row_out['Zamowienie'];
                            $tt_line['DataWpisu'] = $row_out['DataWpisu'];
                            $tt_line['DataUsuniecia'] = $row_out['DataUsuniecia'];
                            $tt_line['defgroup_id'] = $defg->getDefGroupID($row_out['Kod'], $row_out['Opis']);
                            $tt_line['author_id'] = $auth->getAuthorID($row_out['Odbierajacy']);
                            $tt_line['region_id'] = $region->getRegionID($row_out['Region']);
                            $tt_line['vendor_id'] = $vendor->getVendorID($row_out['Dostawca']);
                            $tt_line['DataZgloszenia'] = $row_out['DataZgloszenia'];
                            $tt_line['OsobaKontaktowa'] = $row_out['OsobaKontaktowa'];
//                            $tt_line['data_wyslania'] = $row_out['data_wyslania'];
                            $tt_line['liczbaodrzucenodbiorupousterk'] = $row_out['liczbaodrzucenodbiorupousterk'];
                            $tt->tryLoad($row_out['ID']);
                            
                            if (!$tt->loaded()) {
                                if ($tt->get('data_wyslania') == null) {
                                    $sub = $this->api->db->dsql()->table('po_subco')->field('subco_id')->where('po', $tt_line['Zamowienie'])->do_getOne();
                                    $sub_mail_list[$sub][] = $tt_line['id'];
                                }
                            }
                            $tt->set($tt_line);
                            $tt->save();
                        }
                    }
                }
                //die(var_dump($tt_out));
                unset($data_out[0]);
                foreach ($site_out as $s) {
                    if ($s['csc'] != null)
                        $site_id = $site->getSiteID($s['csc'], $s);
                }
                $i = 0;
                foreach ($sub_mail_list as $sub_id => $sub) {
                    $tt_upd = $this->add('Model_TroubleTicket');


                    $tt_repall = $this->add('Model_ReportAll', $headers)->addCondition('id', 'in', implode(',', $sub));
                    $tts = $tt_repall->getRows();
                    //if($i==2)die(var_dump($tts));
                    foreach ($tts as $ticket) {
                        if ($ticket['id'] != null && $ticket != '') {
                            $tt_upd->tryLoad($ticket['id']);
                            if (!$tt_upd->get('data_wyslania')) {
                                $tt_upd->set('data_wyslania', date("Y-m-d"));
                                $tt_upd->save();
                            }
                        }
                    }
                    $v = $this->add('View_listTTs');
                    $v->setModel($tt_repall);
                    $o = $v->getHTML();
                    $mail = $this->add('TMail');
                     $mail->addTransport('echo');
                    $mail->setHTML($o);
                    $dl = array();
                    $dl[] = $this->api->auth->get('email');
                    //$dl[] = 'willem.rheeder@baysidehw.com';
                    //load DL for sub_id
                    $model = $f->add('Model_SubcoDL')->addCondition('subco_id', $sub_id);
                    foreach ($model as $sub_email) {
                        $dl[] = $sub_email['email'];
                    }
                    $mail->set('subject','nProj Upload : '.date("Y-m-d"));
                    $mail->send(implode(',', $dl));
                    $i++;
                }

               // $f->removeClass('loading');
            } else {
                exit('Failed to open .xml.');
            }
            $js = array();
                    $js[]=$this->js()->univ()->setTimeout(null,200);
            $js[] = "document.body.style.cursor = 'auto'";
            $js[] = $info1->js()->html('<div class="atk-notification-text">
    <i class="ui-icon ui-icon-info"></i>Summary of upload :  Upload Complete !</br>' . $j . ' - Rows Handled</br>Site Uploaded </br><hr> ' . $site->getLog() . '<hr></br>TroubleTickets Uploaded </br><hr> ' . $tt->getLog() . '<hr>
  </div>');

            $js[] = $process->js()->attr("disabled", "disabled");
            $this->js(true,$js)->univ()->successMessage((string)$fs['original_filename'] . ' processed')->execute();
        }

        if ($f2->isSubmitted()) {
            $site = $this->add('Model_Site');
            $po = $this->add('Model_POSubco');
            $subco = $this->add('Model_Subco');
            $polist = $this->add('Model_poList');
            $fs1->tryLoad($upl2->form->data);
            $furl = './' . $fs1['dirname'] . '/' . $fs1['filename'];
            $fh = fopen($furl, "rb");
            $data = fread($fh, filesize($furl));

            fclose($fh);


            $data = str_replace(array("\r\n", "\t"), array("[NEW*LINE]", "[tAbul*Ator]"), $data);
            $rows = explode("[NEW*LINE]", $data);
            $row = 1;
            foreach ($rows as $lines) {
                $cols = explode("[tAbul*Ator]", $lines);
                $po_out = array();
                foreach ($cols as $li) {
                    $po_out[] = $li;
                }

                $po_record = array();
                $po_record['po'] = $po_out[2];
//                if(($po_out[4]!='' || $po_out[4]!=null) && $row!=1){
//                    $dte=new DateTime($po_out[4]);
//                    $po_record['data_wysłania']=$dte->format('Y-m-d H:i:s');
//                }
//                else
//                {
//                    $po_record['data_wysłania']=null;
//                }
                $subco_id = null;

                if ($po_out[3] != null && $po_out[3] != '') {
                    $subco_id = $subco->getSubcoID($po_out[3]);
                }
                $po_record['subco_id'] = $subco_id;
                if ($row > 1) {
                    if ($po_record['po'] != '' && $po_record['po'] != null) {
                        $polist->tryLoadBy('po',$po_record['po']);
                        if (!$polist->loaded()) {
                            $polist->set('po',$po_out[2]);
                            $polist->set('csc',$po_out[0]);
                            $polist->save();
                        }
                    }
                    if ($subco_id != '' && $subco_id != null && $po_record['po'] != '' && $po_record['po'] != null) {
                        $po->tryLoad($po_record['po']);
                        //die(var_dump($po_record));
                        $po->set($po_record);
                        $po->save();
                        //echo $row.'</t>';
                    }
                    if ($po_out[0] != null && $po_out[0] != '')
                        $site->getSiteID($po_out[0], array('csc' => $po_out[0]));
                }

                $row++;
            }
            //$unq_arr = array_unique($unq_arr);
            $f2->js(true)->univ()->successMessage($fs1['original_filename'] . '(' . count($rows) . ')processed')->execute();
        }
    }

    function XML2Array($xml, $recursive = false) {
        if (!$recursive) {
            $array = simplexml_load_string($xml);
        } else {
            $array = $xml;
        }

        $newArray = array();
        $array = (array) $array;
        foreach ($array as $key => $value) {
            $value = (array) $value;
            if (isset($value [0])) {
                $newArray [$key] = trim($value [0]);
            } else {
                $newArray [$key] = XML2Array($value, true);
            }
        }
        return $newArray;
    }

}

//$process->js(true)->removeAttr('disabled')->removeClass('ui-button-disabled ui-state-disabled')

//$fs->loadBy('id',(int)$upl->get());
//             var_dump($fs['id']);