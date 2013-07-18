<?php

$config['atk']['base_path']='./atk4/';
//$config['dsn']='mysql://nProject:Star!2405@localhost/nproject';
$config['dsn']='mysql://nProject@localhost/nproject';

$config['url_postfix']='';
$config['url_prefix']='?page=';

$config['logger']['log_output']='c:\\wamp\\www\\nProject\\log';
$config['logger']['log_dir']='c:\\wamp\\www\\nProject\\log';

$config['tmail']['from']='admin@nsn-extern.com';
$config['tmail']['smtp']['host']='server.noisehosting.co.za';
$config['tmail']['smtp']['port']=465;
$config['tmail']['phpmailer']['reply_to']='admin@nsn-extern.com';
$config['tmail']['phpmailer']['reply_to_name']='admin';
# Agile Toolkit attempts to use as many default values for config file,
# and you only need to add them here if you wish to re-define default
# values. For more options look at:
#
#  http://www.atk4.com/doc/config

