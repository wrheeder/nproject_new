<?php
class page_fileadmin extends filestore\Page_FileAdmin{
	function init(){
		if(!$this->api->auth->isFileManager()){
			$this->api->redirect('index');
		}
		parent::init();
	}
}