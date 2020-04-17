<?php

namespace App\Parse_mods\offence_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Accuracity extends Stat
{
    public $baseVal = 0;
    public $dexVal = 0;
    public $note = '10 dexterity grants 20 accuracy rating';
    public $shapers_touch = false;

    private $parentMod = ' to Accuracy Rating';

    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='Accuracy Rating';
        $modValue = (int) filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ($this->currentType === 'base' && $modName === '+# to Accuracy Rating') {
           $this->baseVal += $modValue;
        }

        if ( $modName === '+# to Accuracy Rating') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

        // Shaper's Touch unique gloves Stat Conversion
        if ( $mod === '+4 Accuracy Rating per 2 Intelligence') {
            $this->shapers_touch = true;
        }

        $this->total = $this->treeVal + $this->itemVal + $this->baseVal + $this->jewVal + $this->dexVal;
    }

    public function finalCalcualtion($allStats){
        //add dexVal
        $this->dexVal = round($allStats['defense'][16]->total*2);
        $this->total += $this->dexVal;

        if ($this->shapers_touch) {
            $shaper_touch = round($allStats['defense'][15]->total*2);
            $this->total +=  $shaper_touch;
            $this->note = $this->note . '<br>Stat include ' . $shaper_touch . " Accuracy Rating from Shaper's Touch";
        }
    }

}