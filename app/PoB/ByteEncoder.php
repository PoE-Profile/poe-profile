<?php

namespace App\PoB;

class ByteEncoder
{
    protected $dataString = '';
    protected $position = 0;

    public function intToBytes($e, $t=4)
    {
        $e = (int)$e;
        $n = [];
        $r = $t-1;
        do {
            $n[$r] = $e & 255;
            $e = $e >> 8;
            $r=$r-1;
        } while ($r >= 0);
        return $n;
    }

    public function appendInt($e, $t=4)
    {
        $n = $this->intToBytes($e, $t);
        for ($r = 0; $r < $t; ++$r) {
            $this->dataString =$this->dataString.chr($n[$r]);
        }
    }

    public function appendInt8($e)
    {
        $this->appendInt($e, 1);
    }

    public function appendInt16($e)
    {
        $this->appendInt($e, 2);
    }

    public function getDataString()
    {
        return $this->dataString;
    }
}
