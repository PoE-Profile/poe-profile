<?php

namespace App\Parse_mods\defense_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Percentage_Armor extends Stat
{
    public $totalCalc = 0;
    public $defencesFromShield = 0;

	private $parentMod = '% increased Armour';
    private $jewellery = ['Ring', 'Ring2', 'Amulet', 'Belt'];

    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Armour';
        $modValue = (int) filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ( $this->currentType !== 'item' && ($modName === '#% increased Armour') || $modName === '#% increased Evasion Rating and Armour') {
            $this->setVal($modValue);
        }

        if ( $modName === '#% increased Global Defences') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $modName);
        }

        if ($modName === '#% increased Defences from equipped Shield') {
            $this->defencesFromShield += $modValue;
        }

        //percent Armour from Jewelery
        if (in_array( $this->currentItemType , $this->jewellery)) {
            if ( $modName === '#% increased Armour') {
                $this->setVal($modValue);
                $this->setItemVal($modValue, $mod);
            }
        }

        $this->total = $this->treeVal + $this->itemVal + $this->jewVal;
    }

    public function finalCalcualtion($allStats){
        $tempVal = 0;
        $tempVal = $allStats['defense'][4]->total;
        $tempVal = (int) (($tempVal * $this->total) / 100) + $tempVal;
        $this->totalCalc = round($tempVal);

        //add Shield Bonus if exist
        $tempVal = ($allStats['defense'][4]->shieldVal * $this->defencesFromShield) / 100;
        $this->totalCalc += round($tempVal);
    }

}