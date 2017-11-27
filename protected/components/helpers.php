<?php

function strResume($str){
    if (strlen($str) > 100) {
        return substr($str,0,100)."...";
    } else {
        return $str;
    }
}

function cycle ($val, $vals = "*") {
    static $counters = array();
    $values = func_get_args();
    $namespace = '';
    if (substr($val,0,1) === '#') {
        $namespace = substr($val,1);
        array_shift($values);
    }
    if (empty($counters[$namespace])) {
        $counter = 0;
    } else {
        $counter = $counters[$namespace];
    }
    $value = $values[$counter];
    $counters[$namespace] = ($counter + 1) % (count($values));
    return $value;
}

function excerpt($str, $max = 100) {
    if(strlen($str) > $max) {
        $excerpt   = substr($str, 0, $max-3);
        $lastSpace = strrpos($excerpt, ' ');
        $excerpt   = substr($excerpt, 0, $lastSpace);
        $excerpt  .= '...';
    } else {
        $excerpt = $str;
    }

    return $excerpt;
}
?>