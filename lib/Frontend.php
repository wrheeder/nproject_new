<?php

/**
 * Consult documentation on http://agiletoolkit.org/learn 
 */
class Frontend extends ApiFrontend {

    function init() {
        parent::init();
        // Keep this if you are going to use database on all pages
        $this->dbConnect();
        $this->requires('atk', '4.2.0');

        // This will add some resources from atk4-addons, which would be located
        // in atk4-addons subdirectory.
        $this->addLocation('atk4-addons', array(
                    'php' => array(
                        'mvc',
                        'misc/lib',
                        'filestore',
                    )
                ))
                ->setParent($this->pathfinder->base_location);
        $this->pathfinder->addLocation('.', array('addons' => array('ds-addons', 'wr-addons')));
        // A lot of the functionality in Agile Toolkit requires jUI
        $this->add('jUI');

        $auth = $this->add('ApplicationAuth');
        $auth->allowPage(array('index'));
        // Initialize any system-wide javascript libraries here
        // If you are willing to write custom JavaScritp code,
        // place it into templates/js/atk4_univ_ext.js and
        // include it here
        $this->js()
                ->_load('atk4_univ')
                ->_load('ui.atk4_notify')
        ;

        $menu = $this->add('Menu', null, 'Menu')
                ->addMenuItem('index', 'Welcome');

        $layout = $this->api->add('Layout/Layout');
        if ($auth->isLoggedIn()) {

            $menu->addMenuItem('ttManager', 'Manage TT\'s');
            $menu->addMenuItem('siteManager', 'Manage Sites');
            if ($auth->isUploadManager()) {
                $menu->addMenuItem('uploader', 'Uploader');
            }

            if ($auth->isAdmin()) {
                $menu->addMenuItem('admin', 'Admin');
            }

            if ($auth->isFileManager()) {
                //$menu->addMenuItem('fileadmin','File Manager');
            }
            $menu->addMenuItem('reports', 'Reporting');
            $menu->addMenuItem('logout');
        } else {
            $menu->addMenuItem('login');
        }
        // $menu->addMenuItem('contact','Contact');
    }

    function initLayout() {
        $this->auth->check();
        parent::initLayout();
        // $this->add('Text','blah','Welcome')->set('Logged In as '.$this->auth->get('username'));
    }

}