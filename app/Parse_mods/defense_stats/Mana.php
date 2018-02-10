<?php

namespace App\Parse_mods\defense_stats;


use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;


class Mana extends Stat
{
    public $intVal = 0;
    public $baseVal = 0;
    private $parentMod = ' to maximum Mana';
    public $note = '10 intelligence grants an additional 5 mana';
    private $br = '';
    public $sanctuary = false;
    public $sanctuaryVal = 0;
    public $shapers_touch = false;
    public $bm = false;

    private $grand_spectrum = [
        'count' => 0,
        'val' => 0
    ];

    public function parse($mod, $type)
    {
        if ($this->note !== '') {
            $this->br = '<br>';
        }
        $this->setType($type);
        $this->name ='Mana';
        $modValue = filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);


        // check for other Grand Spectrum jewels
        if (strpos($mod, 'Grand Spectrum') !== false) {
            $this->grand_spectrum['count'] += 1;
        }

        if (strpos($mod, 'Mana per Grand Spectrum')) {
            $this->grand_spectrum['val'] += $modValue;
        }

        // From Base_Stats.php
        if ($this->currentType === 'base' && $modName === '+# to maximum Mana') {
           $this->baseVal += $modValue;
        }

        // From items // tree // jews
        if ( $modName === '+# to maximum Mana') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

        // Sanctuary of Thought
        if ( str_contains($mod, 'Maximum Mana as Extra Maximum Energy Shield')) {
            $this->sanctuary = true;
        }

        // From items // tree // jews
        if ( str_contains($mod, 'Removes all mana. Spend Life instead of Mana for Skills')) {
            $this->bm = true;
            $this->note = $this->note . $this->br . '<br>Removes all mana. Spend Life instead of Mana for Skills (Blood Magic passive node)';
        }

        // Shaper's Touch unique gloves Stat Conversion
        if ( $mod === '+2 Mana per 4 Strength') {
            $this->shapers_touch = true;
        }
        
        $this->setJewVal($this->grand_spectrum);
        $this->total = $this->treeVal + $this->itemVal + $this->jewVal + $this->baseVal + $this->intVal;
        if ( $this->sanctuary ) {
            $this->sanctuaryVal = round((($this->total * 25) / 100));
        }
    }

    public function setJewVal($gs)
    {
        $this->jewVal = $gs['val'] * $gs['count'];
    }

    public function finalCalcualtion($allStats){
        //add IntVal
        $this->intVal = round($allStats['defense'][15]->total/2);
        $this->total += $this->intVal;

        if ($this->shapers_touch) {
            $shaper_touch = round($allStats['defense'][14]->total/2);
            $this->total +=  $shaper_touch;
            $this->note = $this->note . $this->br . '<br>Stat include ' . $shaper_touch . " to maximum Mana from Shaper's Touch";
        }

        if ($this->bm) {
           $this->total = 0;
        }
    }

}