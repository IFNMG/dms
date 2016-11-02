<?php

namespace app\facades\request;


class Status {
    public $code;
    public $data;
    

    function __construct($c,$d=null) {
        $this->code = $c;
        if($d)
        $this->data = $d; 
    }
}