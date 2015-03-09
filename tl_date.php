<?

function pick($val1, $val2) {
  if($val1) return $val1;
  else return $val2;
  return null;
}

class tl_date {

  static function validate($date, $format=DATE_MASK, $zero_time = false){
    $format=preg_replace("#[a-z]#i","%$0",strtr($format,array('i'=>'M','s'=>'S')));

    if(!($tm=self::strptime($date,$format)))
        return false;
    $tm['tm_mon']+=1;

    $date = gmmktime($tm['tm_hour'],$tm['tm_min'],$tm['tm_sec'],
        $tm['tm_mon'], pick($tm['tm_mday'],1), 1900+pick($tm['tm_year'], 70));

    /*if(!$zero_time && $format != DATE_MASK)
        $date -= exyks::retrieve("USER_TZ");*/

    return $date;
  }

  static function strptime($date, $format) {
    $masks = array(
      '%d' => '(?P<d>[0-9]{2})',
      '%m' => '(?P<m>[0-9]{2})',
      '%Y' => '(?P<Y>[0-9]{4})',
      '%H' => '(?P<H>[0-9]{2})',
      '%M' => '(?P<M>[0-9]{2})',
      '%S' => '(?P<S>[0-9]{2})',
    );
    $rexep = "#".strtr(preg_quote($format), $masks)."#";
    if(!preg_match($rexep, $date, $out))
      return false;

    $ret = array(
      "tm_sec"  => (int) $out['S'],
      "tm_min"  => (int) $out['M'],
      "tm_hour" => (int) $out['H'],
      "tm_mday" => (int) $out['d'],
      "tm_mon"  => $out['m']?$out['m']-1:0,
      "tm_year" => $out['Y'] > 1900 ? $out['Y'] - 1900 : 0,
    );
    return $ret;
  }
    
}