<?php

  error_log( "TL LOADER loaded!" );

  // Usefull const
  $www_root = dirname($_SERVER['SCRIPT_FILENAME']);
  if($relativ_path = ltrim(dirname($_SERVER['SCRIPT_NAME']),'\/'))
    $www_root = substr($www_root, 0, -strlen($relativ_path)-1);

  define('SITE_WWW', realpath($www_root));
  define('SITE_ROOT', dirname(SITE_WWW));
  define('SITE_CONFIG', realpath(SITE_ROOT."/config"));

  //echo SITE_WWW." ===  ".SITE_ROOT." === ".SITE_CONFIG;


  // Autoloader
  function tl_autoloader($class) {

    $lib_path = SITE_ROOT."/libs/";

    $file_path = strtolower($class.".php");
    $file_path = str_replace('_','/',$file_path); //include_path


    // Search in tinnylittle
    $lib_file_path = stream_resolve_include_path(str_replace('tl/','tl_',$file_path)); // tinnylittle
    if(file_exists($lib_file_path)) {
      $class_path = $lib_file_path;
    }
    // Search in libs
    else {
      $extra = array("","exception/");
      foreach($extra as $exte) {
        $local_file_path = $lib_path.$exte.$file_path;
        if(!file_exists($local_file_path)) continue;
        $class_path = $local_file_path;
        break;
      }
    }

    if(!isset($class_path)) { error_log("tl_autoloader : class $class not found.");return; };

    // Load static constructor.
    include_once $class_path;
    if(method_exists($class, '__construct_static'))
      call_user_func(array($class, '__construct_static'));
    elseif(method_exists($class, 'init')) //deprecated
      call_user_func(array($class, 'init'));

  }


  /* function tl_spl_autoload($class_name){
  spl_autoload($class_name);

  if(!class_exists($class_name))
  return false; //class_exists

  //self::init($class_name);

  }*/

  spl_autoload_register('tl_autoloader');
  //spl_autoload_register('tl_spl_autoload');



  /////////////////////////////////////////

  // Zero fct
  function first($obj) {

    if(!is_array($obj)) {
      if(!is_a($obj, 'Traversable'))
        return null;
      foreach($obj as $tmp)
        return $tmp;
      return null;
    }
    $keys = array_keys($obj);
    if(!count($keys))
      return null;
    return $obj[$keys[0]];
  }

  function array_extract($array, $col, $clean=false){

    $ret = array();
    if(is_array($col)) foreach($array as $k=>$v) $ret[$k] = array_sort($v, $col);
    elseif($array instanceof simplexmlelement) foreach($array as $k=>$v) $ret[] = (string)$v[$col];
    elseif($array) foreach($array as $k=>$v) $ret[$k]=$v[$col];
    return $clean ? array_filter(array_unique($ret)):$ret;

  }
  
  function mask_join($glue,$array,$mask){
    foreach($array as $k=>&$v) $v=sprintf($mask,$v,$k);
    return join($glue,$array);
  }
  
  function array_key_map($callback, $array){
    if(!$array) return $array;
    return array_combine(array_map($callback, array_keys($array)), $array);
  }

