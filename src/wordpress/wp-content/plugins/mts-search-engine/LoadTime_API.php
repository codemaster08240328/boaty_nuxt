<?php

/**
 * @author Olga Zhilkova
 * @copyright 2014
 */
if(!class_exists('loadTime')){
class loadTime{
    private $time_start     =   0;
    private $time_end       =   0;
    private $time           =   0;
    public function __construct(){
        $this->time_start= microtime(true);
    }
    public function was(){
        $this->time_end = microtime(true);
        $this->time = $this->time_end - $this->time_start;
        return "$this->time seconds\n";
    }
}
}

?>