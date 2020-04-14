<?php

namespace App\Parse_mods\offence_stats;


use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;


class Melee_Phys_Damage extends Stat
{
    public $strVal = 0;
    public $note = '10 strength grants 2% melee physical damage';
    public $shapers_touch = false;
	private $parentMod = '% increased melee physical damage';

    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Melee Phys Damage';
        $modValue = (int) filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        // From items // tree // jews
        if ( $modName === '#% increased Melee Physical Damage') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

        // Shaper's Touch unique gloves Stat Conversion
        if ( $mod === '2% increased Melee Physical Damage per 10 Dexterity') {
            $this->shapers_touch = true;
        }

        $this->total = $this->treeVal + $this->itemVal + $this->jewVal + $this->strVal;

    }

    public function finalCalcualtion($allStats){
        //add strVal
        $this->strVal = round($allStats['defense'][14]->total/5);
        $this->total += $this->strVal;

        if ($this->shapers_touch) {
            $shaper_touch = round($allStats['defense'][16]->total/5);
            $this->total +=  $shaper_touch;
            $this->note = $this->note . '<br>Stat include ' . $shaper_touch . "% Melee Phys Damage from Shaper's Touch";
        }
    }

}