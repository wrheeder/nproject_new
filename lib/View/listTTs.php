<?php

//adding a ItemList View as CompleteLister
class View_listTTs extends CompleteLister {

//adding formatRow function
	function formatRow(){
	parent::formatRow();

	$this->current_row['tt'] = $this->current_row['id'];
        //die(var_dump($this->current_row));

	}
//adding list/items as defaultTemplate 
	function defaultTemplate(){
		return array('tt_sub');

	}
}