<?php

class indexController extends viewcontroller {
    private $cat_server_online = 0;
    private $mt_server_online = 0;
    private $msg = '';

    public function __construct() {
        parent::__construct();
       	parent::makeTemplate("index.html");
    }
    
    public function doAction(){

      if (array_key_exists("do",$_GET)) {
        if ($_GET['do'] == 'start-mt-server') {
          exec('scripts/update-language-setting-in-web-server.perl');
          exec('scripts/start-mt-server.perl');
          usleep(2000000);
        }
        if ($_GET['do'] == 'start-cat-server') {
          exec('scripts/start-cat-server.sh');
          usleep(2000000);
        }
        if ($_GET['do'] == 'reset-server') {
          exec('scripts/update-language-setting-in-web-server.perl');
          exec('scripts/start-mt-server.perl');
          exec('scripts/start-cat-server.sh');
          usleep(2000000);
        }
        if ($_GET['do'] == 'start-ol-server') {
          exec('scripts/itp-server.sh /ssd/models/home-edition-ol.conf 8765');
        }
        if ($_GET['do'] == 'stop-ol-server') {
          exec('scripts/itp-server.sh stop');
        }
        if ($_GET['do'] == 'update') {
          exec('/opt/casmacat/install/update.sh');
        }
      }

      $ret = array();
      exec('/bin/ps -ef',$ret);
      foreach($ret as $line) {
        if (strpos($line,"/opt/casmacat/cat-server/cat-server.py --port 9999") !== false) {
          $this->cat_server_online++;
        }
        if (strpos($line,"/opt/moses/bin/mosesserver") !== false) {
          $this->mt_server_online++;
        }
        if (strpos($line,"/opt/casmacat/mt-server/python_server/server.py") !== false) {
          $this->mt_server_online++;
        }
        if (strpos($line,"/opt/casmacat/itp-server/server/casmacat-server.py") !== false && strpos($line,"8765") !== false) {
          $this->ol_server_online++;
        }
      }
    }
    
    public function setTemplateVars() {
      global $ip;
      $this->template->show_start_cat_server = ! $this->cat_server_online;
      $this->template->show_start_mt_server = ($this->mt_server_online != 2);
      $this->template->show_start_ol_server = ! $this->ol_server_online;
      $this->template->show_translate_document = 
        ($this->cat_server_online == 1 && $this->mt_server_online == 2);
      $this->template->url = "http://$ip:8000/";
      $this->template->url_list = "http://$ip:8000/?action=listDocuments";
      $this->template->msg = $this->msg;
    }
}

?>
