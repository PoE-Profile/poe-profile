<?php

namespace App\Parse_mods\defense_stats;


use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;


class Percentage_Evasion extends Stat

{
    public $dexVal = 0;
    public $note = '10 dexterity grants 2% evasion rating';
    public $totalCalc = 0;
    public $defencesFromShield = 0;
    public $shapers_touch = false;

    private $parentMod = '% increased Evasion Rating';
    private $jewellery = ['Ring', 'Ring2', 'Amulet', 'Belt'];

    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Evasion Rating';
        $modValue = (int) filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);
        
        // From tree // jews
        if ( $this->currentType !== 'item' && ($modName === '#% increased Evasion Rating' || $modName === '#% increased Evasion Rating and Armour')) {
            $this->setVal($modValue);
        }

        if ( $modName === '#% increased Global Defences') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $modName);
        }

        //percent Evasion from Jewelery
        if (in_array( $this->currentItemType , $this->jewellery)) {
            if ( $modName === '#% increased Evasion Rating') {
                $this->setVal($modValue);
                $this->setItemVal($modValue, $mod);
            }
        }

        if ($modName === '#% increased Defences from equipped Shield') {
            $this->defencesFromShield += $modValue;
        }

        // Shaper's Touch unique gloves Stat Conversion
        if ( $mod === '2% increased Evasion Rating per 10 Intelligence') {
            $this->shapers_touch = true;
        }

        $this->total = $this->treeVal + $this->itemVal + $this->dexVal + $this->jewVal;
    }

    public function finalCalcualtion($allStats){
        //add dexVal
        $this->dexVal = round($allStats['defense'][16]->total/5);
        $this->total += $this->dexVal;

        if ($this->shapers_touch) {
            $shaper_touch = round($allStats['defense'][15]->total/5);
            $this->total +=  $shaper_touch;
            $this->note = $this->note . '<br>Stat include ' . $shaper_touch . "% Evasion Rating from Shaper's Touch";
        }

        $tempVal = 0;
        $tempVal = $allStats['defense'][6]->total;
        $tempVal = (int) (($tempVal * $this->total) / 100) + $tempVal;
        $this->totalCalc = round($tempVal);
        
        //add Shield Bonus if exist
        $tempVal = ($allStats['defense'][6]->shieldVal * $this->defencesFromShield) / 100;
        $this->totalCalc += round($tempVal);
    }

}