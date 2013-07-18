<?php

class View_imageView extends CompleteLister {

    function formatRow() {
        parent::formatRow();    
        $this->current_row['thumb_url'] = 
$this->model->ref('file_id')->ref('thumb_file_id')->get('url'); 
    }

    function defaultTemplate() {
        return array('imageview');
    }

}