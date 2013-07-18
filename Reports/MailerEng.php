<?php

class Page_ttManager_TroubleTickets_MailerEng extends Page {

    function init() {
        parent::init();
        $mail = $this->add('TMail_Compat');
        //$mail->loadTemplate('test');
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
       // $mail->addTransport('echo');
        $f = $this->add('Form');

        // $sel = array(explode(',', $sel));
        // $model=$this->add('Model_User',array('name','email'));
        // $model->addCondition('isEngineer',true);
        $crud = $f->add('Grid');
        $crud->setModel('User', array('name', 'email'))->addCondition('isEngineer', true);

        $f_checked = $f->addField('line', 'checked_eng_mails');

        $f_checked->js(true)->closest('.atk-form-row-line ')->hide();
        $crud->addSelectable($f_checked);

        $f->addSubmit('Send');

        $this->api->stickyGet('id');

        if ($f->isSubmitted()) {
            $dl = preg_replace($patterns, $repl, $f->get('checked_eng_mails'));
            $mailer_list = $this->api->db->dsql()->table('user')->field('email')->where('id in', $dl)->do_getAll();

            $dl = array();
//            $to='willem.rheeder@baysidehw.com';
//            $mail->send($to);
            $headers = array('id', 'Zamowienie', 'NrNetWorkS', 'NazwaStacji', 'Opis', 'Uwagi', 'Kategoria', 'Subco', 'ZgloszeniePL', 'DataWyslaniaDoSUB', 'DataZgloszeniaUsunieciaPL', 'DataZamknieciaUsterek', 'OsobaKontaktowa', 'author', 'region', 'Wlasciciel');
            $tt = $this->add('Model_ReportAll', $headers)->addCondition('csc', $_GET['id']);
//            $this->api->stickyGet('sel_tts');

            $this->writeReport($tt->getRows(), './home/nsnexter/public_html/nProject/Reports/TroubleTickets_Eng.xls', 'TTs');

            $v = $this->add('View_listTTsEng');
            $v->setModel($tt);
            $o = $v->getHTML();
            $mail->attachHTML($o);

            $dl[] = $this->api->auth->get('email');
            $dl[] = 'willem.rheeder@baysidehw.com';
            //$mail->AddAddress('willem.rheeder@baysidehw.com');
            foreach ($mailer_list as $contact) {
                $dl[] = $contact['email'];
            }
            $js = array();
            $js[] = $this->js(true)->univ()->closeDialog();
            $js[] = $this->js(true)->closest('.reloadable')->trigger('myreload');
            $mail->attachFile('./Reports/TroubleTickets_Eng.xls','application/vnd.ms-excel','TT_ENG');
            $mail->send(implode(',', $dl));
            $f->js(true, $js)->univ()->successMessage('TTs sent to selected Engineers')->execute();
            //$this->js(true, $this->js()->_selector("body")->trigger("reloadpage"))->univ()->closeDialog();
        }
    }

    function writeReport($rs, $fn, $report_name) {
        $comments = $this->add('Model_Comments');
        //$comments->debug();
        set_time_limit(0);
        $format = 'Y-m-d H:i:s';
        $seconds_in_a_day = 86400;
        // Unix timestamp to Excel date difference in seconds
        $ut_to_ed_diff = 2209161600; //$seconds_in_a_day * 25569;

        if (file_exists($fn)) {
            unlink($fn);
        }
        $workbook = new Spreadsheet_Excel_Writer($fn);
        $workbook->setVersion(8);
        $worksheet = & $workbook->addWorksheet($report_name);
        $worksheet->setInputEncoding('UTF-8');
        if (PEAR::isError($worksheet)) {
            die($worksheet->getMessage());
        }



        //$worksheet->setInputEncoding('UTF-8');

        $format_bold = & $workbook->addFormat();
        $format_bold->setBold();

        $date_format = & $workbook->addFormat();
        $date_format->setNumFormat('dd/mm/yyyy');



        $format_title = & $workbook->addFormat();
        $format_title->setBold();
        $format_title->setColor('white');
        $format_title->setPattern(1);
        $format_title->setFgColor('blue');

        $format_title_red = & $workbook->addFormat();
        $format_title_red->setBold();
        $format_title_red->setColor('white');
        $format_title_red->setPattern(1);
        $format_title_red->setFgColor('red');

        $col = 0;
        $row = 1;
        $max_col = 0;
        // $arr = array(array('key1'=>'Row1,Col1','key2'=>'Row1,Col2'),array('key1'=>'Row2,Col1','key2'=>'Row2,Col2'),array('key1'=>'Row2,Col1','key2'=>'Row2,Col2'),array('key1'=>'Row2,Col1','key2'=>'Row2,Col2'),array('key1'=>'Row2,Col1','key2'=>'Row2,Col2'),array('key1'=>'Row2,Col1','key2'=>'Row2,Col2'),array('key1'=>'Row2,Col1','key2'=>'Row2,Col2'),array('key1'=>'Row2,Col1','key2'=>'Row2,Col2'),array('key1'=>'Row2,Col1','key2'=>'Row2,Col2'),array('key1'=>'Row2,Col1','key2'=>'Row2,Col2'),array('key1'=>'Row2,Col1','key2'=>'Row2,Col2'),array('key1'=>'Row2,Col1','key2'=>'Row2,Col2'),array('key1'=>'Row2,Col1','key2'=>'Row2,Col2'),array('key1'=>'Row2,Col1','key2'=>'Row2,Col2'),array('key1'=>'Row2,Col1','key2'=>'Row2,Col2'),array('key1'=>'Row2,Col1','key2'=>'Row2,Col2'),array('key1'=>'Row2,Col1','key2'=>'Row2,Col2'));
        $filterCols = array('csc', 'id', '1', '2', '3', 'Uwagi', 'DataUsunieciaPrzezSUB');
        $worksheet->write(0, 2, 'Usterki stopnia', $format_title);
        foreach ($filterCols as $headers) {
                
                if($col!=2 && $col!=3 && $col!=4){
                    $worksheet->write(0, $col, $headers, $format_title);
                    $worksheet->setMerge(0,$col,1,$col);
                    $worksheet->setMerge(0,2,0,4);
                }else
                {
                    $worksheet->write(1, $col, $headers, $format_title);
                }
                    
            $col++;
        }
        
        $row = 2;
        foreach ($rs as $data_row) {
            $col = 0;
            // die(var_dump($data_row));
            foreach ($filterCols as $headers) {
                
                $v = $data_row[$headers];
                if($headers=='1'){
                    if($data_row['Kategoria']=='1'){
                        $v='x';
                    }
                }
                if($headers=='2'){
                    if($data_row['Kategoria']=='2'){
                        $v='x';
                    }
                }
                if($headers=='3'){
                    if($data_row['Kategoria']=='3'){
                        $v='x';
                    }
                }
                if ($this->isDateTime($v)) {
                    $v = ((strtotime($v) + $ut_to_ed_diff) / $seconds_in_a_day) + 0.0833333333333;
                    $worksheet->write($row, $col, $v, $date_format);
                } else {
                    $worksheet->write($row, $col, $v);
                }
                $col++;
            }
            $cmts = $comments->tryLoadBy($comments->dsql()->expr("troubleticket_id = '" . $data_row['id'] . "'"));
            $c_col = $col;
            if ($comments->loaded()) {
                foreach ($cmts as $comment) {
                    //die(var_dump($comment));
                    if ($comment['troubleticket_id'] == $data_row['id']) {
                        $max_col = $max_col <= (($col - $c_col) + 1) ? (($col - $c_col) + 1) : $max_col;
                        $worksheet->write($row, $col, $comment['comment'] . '/' . $comment['def'] . ':' . $comment['made_by']);
                        $col++;
                    }
                }
            }
            $row++;
        }
        for ($i = 0; $i < $max_col; $i++) {
            $worksheet->write(0, $i + 7, 'Uwagi_' . ($i + 1), $format_title_red);
            $worksheet->setMerge(0,$i + 7,1,$i + 7);
        }
        $workbook->close();
//        $js = array();
//        $js[] = $l->js()->html('<a href="Reports/download.php?f=TroubleTickets.xls" target="_blank">Download Report</a>');
//        $this->js(true, $js)->univ()->successMessage('Report Refreshed')->execute();
//        $f->add('HTMLElement')->setHTML('<a href="Reports/download.php?f=TroubleTickets.xls" target="_blank">Trouble Tickets Report</a>');
    }

    function convert_locale_for_xls($text) {
        $return = $text; //iconv('UTF-8', 'cp1250', $text);
        return preg_replace("/([\xC2\xC4])([\x80-\xBF])/e", "chr(ord('\\1')<<6&0xC0|ord('\\2')&0x3F)", $return);
    }

    function isDateTime($dateTime_in) {
        /*
          Purpose: Return truth about $dateTime_in. Is it a MySQL datetime string formatted
          as ccyy-mm-dd hh:mm:ss ... huh?
          Author: Sameh Labib
          Date: 09/16/2010
         */

        $strIsValid = TRUE;

        if ($dateTime_in == "0000-00-00 00:00:00") {
            return $strIsValid;
        }

        // check the format first
        if (!ereg("^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$", $dateTime_in)) {
            $strIsValid = FALSE;
        } else {
            // format is okay, check that years, months, days, hours, minutes , seconds
            // are okay
            $dateTimeAry = explode(" ", $dateTime_in); // break up string by space into date, time
            $dateStr = $dateTimeAry[0];
            $timeStr = $dateTimeAry[1];

            $dateAry = explode("-", $dateStr); // break up date string by hyphen
            $yearVal = $dateAry[0];
            $monthVal = $dateAry[1];
            $dayVal = $dateAry[2];

            $timeAry = explode(":", $timeStr); // break up time string by colon
            $hourVal = $timeAry[0];
            $minVal = $timeAry[1];
            $secVal = $timeAry[2];

            $dateValIsDate = checkdate($monthVal, $dayVal, $yearVal);

            if ($hourVal > -1 && $hourVal < 24 && $minVal > -1 && $minVal < 60
                    && $secVal > -1 && $secVal < 60) {
                $timeValIsTime = TRUE;
            } else {
                $timeValIsTime = FALSE;
            }

            if (!$dateValIsDate || !$timeValIsTime) {
                $strIsValid = FALSE;
            }
        }

        return ($strIsValid);
    }

}