<?php

namespace App\Parse_mods\defense_stats;


use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;


class Percentage_Life extends Stat
{
    public $chaylaVal = 0;
    public $totalCalc = 0;
	private $parentMod = '% increased maximum Life';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Life';
        $modValue = filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        // From items // tree // jews
        if ( $modName === '#% increased maximum Life') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }
    }

    public function finalCalcualtion($allStats){
        $tempVal = 0;
        if ($allStats['defense'][8]->chaylaVal > 0) {
            $tempVal = $allStats['defense'][8]->chaylaVal;
            $tempVal = (int) ($tempVal * $this->total) / 100;
            $this->chaylaVal = $tempVal;
        }

        $tempVal = $allStats['defense'][8]->total;
        $tempVal = (int) (($tempVal * $this->total) / 100) + $tempVal;
        $this->totalCalc = round($tempVal);
    }
}