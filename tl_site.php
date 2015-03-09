<?php

// mosite class
class tl_site {

  static $auto_post= "";
  static $action = "";

  public static function instanciate() {

    self::$auto_post = $_SERVER['REQUEST_URI'];
    self::$action = (string)$_POST['action'];

    //new moconfig(); // use moconfig::$config as an accessor.

    $class_name = __CLASS__;
    return new $class_name();
  }

  function render() {
    $this->url_parse();

    foreach(array("subs"=>"php", "tpls"=>"tpl") as $page_dir=>$page_ext) 
      foreach($this->url_elements as $element) {
        extract($this->prm_to_extract($element), EXTR_OVERWRITE);
        //echo "==>$prm0 - $prm1 - $prm2 - $prm3  \n";
        $page = "../{$page_dir}{$element}.$page_ext";
        if(!file_exists($page) && $page_dir == 'subs')
          $page = "../{$page_dir}{$element}/root.$page_ext";
          //error_log("==>".$page);
        if(file_exists($page)) {
          error_log( " ADD : $page");
          include_once($page);
        }
    }
  }

  private function url_parse() {
   $this->url_elements[] = '';
   $this->url_elements[] = '/top';
   //$this->url_elements[] = '';
   foreach($_GET as $get1=>$get2) $path .= $get1.($get2 ?"=$get2":"");
   $path_elements = array_filter(explode('/',$path));
   foreach($path_elements as $path_element) {
     $aarg = explode(';', $path_element);
     $page_name = array_shift($aarg);

     $compound_path .= "/$page_name";
     $this->url_elements[] = $compound_path;
     $this->url_args[$compound_path] = $aarg;
   }
   $this->url_elements[] = '/footer';

   return $this->url_elements;

  }
  
  private function prm_to_extract($compound_path) {
    $i = 0;
    if(!$this->prms) $this->prms = array(); //!
    foreach($this->prms as $k=>$v) $this->prms[$k] = null;
    if($this->url_args[$compound_path]) foreach($this->url_args[$compound_path] as $arg) {
      $this->prms['prm'.$i] = $arg;
      $i++;
    }
    return $this->prms;
  }


}
