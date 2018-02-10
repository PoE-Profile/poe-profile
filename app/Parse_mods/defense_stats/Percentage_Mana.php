<?php

namespace App\Parse_mods\defense_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Percentage_Mana extends Stat
{
	private $parentMod = '% increased maximum Mana';
    public $sanctuaryVal = 0;
    public $totalCalc = 0;
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Mana';
        $modValue = filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ( $modName === '#% increased maximum Mana') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

        if ( $modName === '#% reduced maximum Mana') {
            $this->setVal(-$modValue);
            $this->setItemVal(-$modValue, $mod);
        }
    }

    public function finalCalcualtion($allStats){
        $tempVal = 0;
        if ($allStats['defense'][10]->sanctuaryVal > 0) {
            $tempEs = $allStats['defense'][10]->sanctuaryVal;
            $tempEs = (int) ($tempEs * $this->total) / 100;
            $this->sanctuaryVal = $tempEs;
        }

        $tempVal = $allStats['defense'][10]->total;
        $tempVal = (int) (($tempVal * $this->total) / 100) + $tempVal;
        $this->totalCalc = round($tempVal);
    }
}