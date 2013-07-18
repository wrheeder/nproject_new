<?php

//adding a ItemList View as CompleteLister
class View_listTTsEng extends CompleteLister {
    var $comments;
    function init(){
        parent::init();
        $this->comments = $this->add('Model_Comments');
    }
//adding formatRow function
    function formatRow() {
        parent::formatRow();
        //die(var_dump($this->current_row));
        $this->current_row['tt'] = $this->current_row['id'];
        if ($this->current_row['Kategoria'] == '3') {
            $this->current_row['k3'] = 'x';
            $this->current_row['k2'] = ' ';
            $this->current_row['k1'] = ' ';
        }
        if ($this->current_row['Kategoria'] == '2') {
            $this->current_row['k3'] = ' ';
            $this->current_row['k2'] = 'x';
            $this->current_row['k1'] = ' ';
        }
        if ($this->current_row['Kategoria'] == '1') {
            $this->current_row['k3'] = ' ';
            $this->current_row['k2'] = ' ';
            $this->current_row['k1'] = 'x';
        }
        
        $this->current_row['cmt1'] = '';
        $this->current_row['cmt2'] = '';
        $this->current_row['cmt3'] = '';
        $this->current_row['cmt4'] = '';
        $this->current_row['cmt5'] = '';
        $this->current_row['cmt6'] = '';
        $this->current_row['cmt7'] = '';
        $this->current_row['cmt8'] = '';
        $this->current_row['cmt9'] = '';
        $this->current_row['cmt10'] = '';
        
        $cmts = $this->comments->tryLoadBy($this->comments->dsql()->expr("troubleticket_id = '" . $this->current_row['id'] . "'"));
        $col=1;
        if ($this->comments->loaded()) {
            
            foreach ($cmts as $comment) {
                //die(var_dump($comment));
                if ($comment['troubleticket_id'] == $this->current_row['id']) {
                    if($col<10){
                        $this->current_row['cmt'.$col] =  $comment['comment'] . '/' . $comment['def'] . ':' . $comment['made_by'];
                        $col++;
                    }else
                    {
                        $this->current_row['cmt10'] .= '....' . $comment['comment'] . '/' . $comment['def'] . ':' . $comment['made_by'];
                    }
                }
            }
        }
    }

//adding list/items as defaultTemplate 
    function defaultTemplate() {
        return array('tt_eng');
    }

}