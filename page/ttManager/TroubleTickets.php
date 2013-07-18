<?php

class Page_ttManager_TroubleTickets extends Page_ApplicationPage {

    function init() {
        parent::init();
        $tabs = $this->add('Tabs');
        //  die($_GET['id']);
        $model = $this->add('Model_TroubleTicket')->addCondition('site_id', $_GET['id']);
        $model_open = $this->add('Model_TroubleTicketOpen');
        $model_closed = $this->add('Model_TroubleTicketClosed');
        $this->api->stickyGet('id');
        //$this->api->stickyGet('site_id');
        $crud_all = $tabs->addTab('All')->add('CRUD');
        $this->js(true)->addClass('reloadable');
        
//
        $this->js('myFunc')->reload();
        $crud_open = $tabs->addTab('Open')->add('CRUD', array('allow_add' => false, 'allow_edit' => true, 'allow_del' => false));
//
        $crud_closed = $tabs->addTab('Closed')->add('CRUD', array('allow_add' => false, 'allow_edit' => false, 'allow_del' => false));
//
//        
//        $crud_all->api->stickyGet('id');
//        $crud_open->api->stickyGet('id');
        $m = $this->api->auth->model;
        if (!$m['isManualAdd']) {
            $crud_all->allow_add = false;
            $crud_open->allow_del = false;
        }

        if (!$m['isManualEdit']) {
            $crud_all->allow_edit = false;
            $crud_open->allow_edit = false;
        }
        if (!$m['isManualDelete']) {
            $crud_all->allow_del = false;
            $crud_open->allow_del = false;
        }

        //// adding all joined table fields

        $defgroup = $model->join('defgroup');
        $defgroup->addField('opis')->type('text')->editable(false);


        $crud_all->setModel($model);
        $crud_open->setModel($model_open);
        $crud_closed->setModel($model_closed);
//
        $row_cnt = $crud_all->model->count()->where('site_id', 'is not', null)->where('ttowner_id', '=', '2')->getOne();
        $checked = $crud_all->model->count()->where('DataUsunieciaPrzezSub', 'is not', null)->where('ttowner_id', '=', '2')->getOne();
//         die($row_cnt."_".$checked);
        if ($_GET['photos']) {
            $crud_all->grid->js()->univ()->frameURL('Upload TT Photos', $this->api->getDestinationURL('UploadPhotos', array('tt' => $_GET['photos'])))->execute();
        }
        if ($crud_all->grid) {

            $crud_all->grid->addColumn('button', 'photos', 'Upload Photos');
            $f = $crud_all->add('Form', 'mailform');
            $f_checked = $f->addField('line', 'checked');
            $f_checked->addClass('please_remove');
            $this->js(true)->_selector('label[for="3__ement_crud_mailform_checked"]')->hide();
            $f->getElement('checked')->js(true)->hide();
            $crud_all->grid->controller->importField('id');
            // $crud->grid->getColumn('Subco');
            $crud_all->grid->addOrder()->move('id', 'first')->now();
            $crud_all->grid->addOrder()->move('data_wyslania', 'after', 'DataWpisu')->now();
            $crud_all->grid->addOrder()->move('opis', 'after', 'defgroup')->now();
            $crud_all->grid->addClass("zebra bordered");
            // $crud->grid->addPaginator(5);
            $crud_all->grid->addSelectable($f_checked);
            $opts = array('option', 'appendTo', 'input');
            $crud_all->grid->js(true)->find('tbody')->selectable();
            $url_eng = $this->api->url('ttManager/TroubleTickets/MailerEng', array('checked' => $f->get('checked')));
            $url = $this->api->url('ttManager/TroubleTickets/Mailer', array('checked' => 'tralala'));


            $eng_but = $f->add('Button')->set('Send to engineer');
            $eng_but->js(true)->closest('button')->hide();
            $eng_but->js('click')->univ()->frameURL('Mail TT', $url_eng);



            if (($row_cnt - $checked == 0) && ($row_cnt != 0)) {
//                echo $row_cnt - $checked;
                $eng_but->js(true)->closest('button')->show();
            }
            $SUB = $f->addSubmit('mail');

            if ($m['isCommentUpdate']) {
                $comments = $crud_all->grid->addColumn('Button', 'Comments');
            }
            if ($_GET['Comments']) {
                $this->api->stickyGet('Comments');
                $url1 = $this->api->url('ttManager/TroubleTickets/Comments');
                $comments->js()->univ()->frameURL('Comments', $url1)->execute();
            }
            if ($f->isSubmitted()) {
                $test = $f->get('checked');
                $url->set('checked', $test);
                $f->js(true)->univ()->frameURL('Mail TTs', $url)->execute();
            }


            //$crud_all->grid->addColumn('Expander', 'TTImages');
//            if ($_GET['Pictures']) {
//                $crud_all->js()->univ()->dialogURL('Add Images to TroubleTicket', $this->api->getDestinationURL(
//                                        'Pictures', array(
//                                    'cut_object' => 'form'
//                        )))
//                        ->execute();
//            }
        }
        if ($crud_all->form) {
            if ($crud_all->form->isSubmitted()) {
                $js = array();
                $js[] = $this->js(true)->trigger('myFunc');

                $crud_all->form->js(true, $js)->univ()->closeDialog()->execute();
            }
        }
        if ($crud_open->grid) {
            $f = $crud_open->add('Form', 'mailform');
            $f_checked = $f->addField('line', 'checked');
            $f_checked->addClass('please_remove');
            $f->getElement('checked')->js(true)->hide();
            $this->js(true)->_selector('label[for="3__ement_2_crud_mailform_checked"]')->hide();
            $crud_open->grid->controller->importField('id');
            $crud_open->grid->controller->importField('data_wyslania');
            $crud_open->grid->addOrder()->move('id', 'first')->now();
            $crud_open->grid->addOrder()->move('data_wyslania', 'after', 'DataWpisu')->now();
            $crud_open->grid->addClass("zebra bordered");
            // $crud->grid->addPaginator(5);
            $crud_open->grid->addSelectable($f_checked);
            //$url = $this->api->url('ttManager/TroubleTickets/Mailer', array('checked' => 'tralala'));
            $url = $this->api->getDestinationURL('TroubleTickets_Mailer', array('checked' => $f->get('checked')));
            $SUB = $f->addSubmit('mail');
            if ($m['isCommentUpdate']) {
                $comments = $crud_open->grid->addColumn('Button', 'Comments');
            }
            if ($_GET['Comments']) {
                $this->api->stickyGet('Comments');
                $url1 = $this->api->url('ttManager/TroubleTickets/Comments');
                $comments->js()->univ()->frameURL('Comments', $url1)->execute();
            }
            if ($f->isSubmitted()) {
//                $test = $f->get('checked');
//                $url->set('checked', $test);
                $f->js()->univ()->frameURL('Mail TTs', $url)->execute();
            }
        }
        if ($crud_closed->grid) {
            $crud_closed->grid->controller->importField('id');
            $crud_closed->grid->controller->importField('data_wyslania');
            $crud_closed->grid->addOrder()->move('id', 'first')->now();
            $crud_closed->grid->addOrder()->move('data_wyslania', 'after', 'DataWpisu')->now();
            $crud_closed->grid->addClass("zebra bordered");
        }
    }

}