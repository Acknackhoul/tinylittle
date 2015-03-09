<?php

class tl_config {

  static public $config;
  
  function init() {
    $classname = __CLASS__;
    new $classname();
  }

  function __construct() {
    $path = realpath(SITE_CONFIG);
    $mask = $path."/*";
    $this->files = self::list_files($mask,0,true);
    $this->load_config();
    self::$config = $this->config;
  }


  function load_config() {
    $this->config = new stdClass();
    foreach($this->files as $file) {
      $file_data = file_get_contents($file);
      $data = json_decode($file_data); 
      if(!$data) {error_log("Error while loading config file : $file");continue;}
      self::merge_obj($data, $this->config);
   }

  }


  // Merge object A in object B
  static function merge_obj($objectA, $objectB) {
    foreach($objectA as $k=>$v) {
      if(isset($objectB->$k)) error_log("config : entry '$k' already exists in site config, duplicate entry.");
      $objectB->$k = $v;
    }
  }


  static function list_files($pattern, $flags = 0, $file_only=false) {
    $files = glob($pattern, $flags | GLOB_MARK);
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
      $files = array_merge($files, self::list_files($dir.'/'.basename($pattern), $flags));

    if($file_only) foreach($files as $k=>$file)
      if(is_dir($file)) unset($files[$k]);

    return $files;
  }

}
