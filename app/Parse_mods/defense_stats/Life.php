<?php

namespace App\Parse_mods\defense_stats;


use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;


class Life extends Stat
{
    public $strVal = 0;
    public $reducedPercent = 0;
    public $baseVal = 0;
    public $ci = false;
    public $chayla = false;
    public $chaylaVal = 0;
    public $shapers_touch = false;
    private $parentMod = ' to maximum Life';
    private $br = '';
    public $note = '10 strength grants an additional 5 life';

    public function parse($mod, $type)
    {
        if ($this->note !== '') {
            $this->br = '<br>';
        }
        $this->setType($type);
        $this->name ='Life';
        $modValue = filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        // From Base_Stats.php
        if ($this->currentType === 'base' && $modName === '+# to maximum Life') {
           $this->baseVal += $modValue;
        }

        // From items // tree // jews
        if ( $modName === '+# to maximum Life') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

        if ($modName === '#% of Maximum Life Converted to Energy Shield') {
            $this->chayla = true;
            $this->reducedPercent += $modValue;
            $this->note = $this->note . $this->br .'20% of Maximum Life Converted to Energy Shield from Presence of of Chayla';
        }

        if ($modName === '#% reduced maximum Life') {
            $this->chayla = true;
            $this->reducedPercent += $modValue;
        }

        // From items // tree // jews
        if ( \Str::contains($mod, 'Maximum Life becomes 1, Immune to Chaos Damage')) {
            $this->ci = true;
            $this->note = $this->note . $this->br . 'Maximum Life becomes 1, Immune to Chaos Damage (Chaos Inoculation passive node)';
        }

        // Shaper's Touch unique gloves Stat Conversion
        if ( $mod === '+1 Life per 4 Dexterity') {
            $this->shapers_touch = true;
        }
        
        $this->total = $this->treeVal + $this->itemVal + $this->jewVal + $this->baseVal + $this->strVal;
        if ( $this->chayla ) {
            $this->chaylaVal = round((($this->total * 20) / 100));
            $this->chaylaVal = \Str::contains($this->note, 'Presence of of Chayla') ? $this->chaylaVal : 0;
        }
        $this->total = $this->total - round((($this->total * $this->reducedPercent) / 100));
        
    }

    public function finalCalcualtion($allStats){
        //add strVal
        $this->strVal = round($allStats['defense'][14]->total/2);
        $this->total += $this->strVal;

        if ($this->shapers_touch) {
            $shaper_touch = round($allStats['defense'][16]->total/4);
            $this->total +=  $shaper_touch;
            $this->note = $this->note . $this->br . 'Stat include ' . $shaper_touch . " to maximum Life from Shaper's Touch";
        }
        
        if ($this->ci) {
           $this->total = 1;
        }
    }
}