<?php

class page_reports extends Page_ApplicationPage {

    function init() {
        parent::init();
        $columns = $this->add('Columns');
        $c1 = $columns->addColumn(4);

        $c2 = $columns->addColumn(4);
        $c3 = $columns->addColumn(4);
        $f1 = $c1->add('Form');
        $f1->add('H3')->set('All Regions Report');
        $process1 = $f1->addSubmit('Refresh');
        $link1 = $f1->add('HTMLElement', 'link');
        $f2 = $c2->add('Form');
        $f2->add('H3')->set('Region 1 Report');
        $process2 = $f2->addSubmit('Refresh');
        $link2 = $f2->add('HTMLElement', 'link');
        $f3 = $c3->add('Form');
        $f3->add('H3')->set('Region 2 Report');
        $process3 = $f3->addSubmit('Refresh');
        $link3 = $f3->add('HTMLElement', 'link');
        $tt = $this->add('Model_ReportAll', array('id', 'Owner', 'csc', 'NrPTC', 'NazwaStacji', 'kod', 'opis', 'Uwagi', 'Kategoria', 'Zamowienie', 'subco', 'data_wyslania', 'DataWpisu', 'DataZgloszenia', 'DataUsunieciaPrzezSub','DataUsuniecia', 'OsobaKontaktowa', 'author', 'region', 'ttowner','liczbaodrzucenodbiorupousterk'));
        $this->add('Grid')->setModel($tt);
        if ($f1->isSubmitted()) {

            $f3->js()->reload();
            $f2->js()->reload();
            $this->writeReport($tt->getRows(), './Reports/TroubleTickets.xls', 'All', $link1);
        }
        if ($f2->isSubmitted()) {

            $f1->js()->reload();
            $f3->js()->reload();
            $tt->addCondition('region', 'W');
            $this->writeReport($tt->getRows(), './Reports/TroubleTickets.xls', 'Region1', $link2);
        }
        if ($f3->isSubmitted()) {
            $f1->js()->reload();
            $f2->js()->reload();
            $tt->addCondition('region', 'G');
            $this->writeReport($tt->getRows(), './Reports/TroubleTickets.xls', 'Region2', $link3);
        }
    }

    function testFunc($var) {
        $this->js()->univ()->SuccessMessage('tralala');
    }

    function writeReport($rs, $fn, $report_name, &$l) {
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
        $date_format->setNumFormat('yyyy/mm/dd');



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
        $row = 0;
        $max_col=0;
        // $arr = array(array('key1'=>'Row1,Col1','key2'=>'Row1,Col2'),array('key1'=>'Row2,Col1','key2'=>'Row2,Col2'),array('key1'=>'Row2,Col1','key2'=>'Row2,Col2'),array('key1'=>'Row2,Col1','key2'=>'Row2,Col2'),array('key1'=>'Row2,Col1','key2'=>'Row2,Col2'),array('key1'=>'Row2,Col1','key2'=>'Row2,Col2'),array('key1'=>'Row2,Col1','key2'=>'Row2,Col2'),array('key1'=>'Row2,Col1','key2'=>'Row2,Col2'),array('key1'=>'Row2,Col1','key2'=>'Row2,Col2'),array('key1'=>'Row2,Col1','key2'=>'Row2,Col2'),array('key1'=>'Row2,Col1','key2'=>'Row2,Col2'),array('key1'=>'Row2,Col1','key2'=>'Row2,Col2'),array('key1'=>'Row2,Col1','key2'=>'Row2,Col2'),array('key1'=>'Row2,Col1','key2'=>'Row2,Col2'),array('key1'=>'Row2,Col1','key2'=>'Row2,Col2'),array('key1'=>'Row2,Col1','key2'=>'Row2,Col2'),array('key1'=>'Row2,Col1','key2'=>'Row2,Col2'));
        $filterCols = array('id', 'Owner', 'Zamowienie', 'csc', 'NrPTC', 'NazwaStacji', 'kod', 'opis', 'Uwagi', 'Kategoria', 'subco', 'DataWpisu', 'data_wyslania', 'DataZgloszenia', 'DataUsunieciaPrzezSub','DataUsuniecia', 'OsobaKontaktowa', 'author', 'region', 'ttowner','liczbaodrzucenodbiorupousterk');
        foreach ($filterCols as $headers) {
            if ($headers == 'csc')
                $headers = 'NrNetWorkS';
            if ($headers == 'data_wyslania')
                $headers = 'DataWyslaniaDoSUB';
            if ($headers == 'ttowner')
                $headers = 'Wlasciciel';
            if($headers == 'DataWpisu')
                 $headers = 'ZgloszeniePL';
            if($headers == 'DataZgloszenia')
                 $headers = 'DataZgloszeniaUsunieciaPL';
            if($headers == 'DataUsuniecia')
                 $headers = 'DataZamknieciaUsterek';
            $worksheet->write($row, $col, $headers, $format_title);
            $col++;
        }
        $row = 1;
        foreach ($rs as $data_row) {
            $col = 0;
           // die(var_dump($data_row));
            foreach ($filterCols as $headers) {
                $v = $data_row[$headers];
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
                        $max_col=$max_col<=(($col-$c_col)+1)?(($col-$c_col)+1):$max_col;
                        $worksheet->write($row, $col, $comment['comment'].'/'.$comment['def'] . ':' . $comment['made_by']);
                        $col++;
                    }
                }
            }
            $row++;
        }
        for($i=0;$i<$max_col;$i++){
            $worksheet->write(0, $i+$col, 'Comment'.($i+1),$format_title_red);
        }
        $workbook->close();
        $js = array();
        $js[] = $l->js()->html('<a href="Reports/download.php?f=TroubleTickets.xls" target="_blank">Download Report</a>');
        $this->js(true, $js)->univ()->successMessage('Report Refreshed')->execute();
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
