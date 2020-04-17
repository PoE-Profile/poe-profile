<?php

namespace App\Parse_mods\defense_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Percentage_ES extends Stat
{
    public $intVal = 0;
    public $note = '10 intelligence grants 2% maximum energy shield';
    public $sanctuaryVal = 0;
    public $chaylaVal = 0;
    public $totalCalc = 0;
    public $defencesFromShield = 0;
    public $infusedShield = 0;
    public $shapers_touch = false;
    public $reducedPercent = 0;
    private $parentMod = '% increased maximum Energy Shield';
    private $jewellery = ['Ring', 'Ring2', 'Amulet', 'Belt', 'Weapon', 'Weapon2'];

    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Energy Shield';
        $modValue = (int) filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);
        // From  tree // jews
        if ($this->currentType !== 'item' &&  $modName === '#% increased maximum Energy Shield') {
            $this->setVal($modValue);
        }

        //Percent Energy Shield from Jewelery
        if (in_array( $this->currentItemType , $this->jewellery)) {
            if ( $modName === '#% increased maximum Energy Shield') {
                $this->setVal($modValue);
                $this->setItemVal($modValue, $mod);
            }
        }

        if ( $modName === '#% increased Global Defences') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $modName);
        }

        if ($modName === '#% reduced maximum Energy Shield') {
            $this->setVal($modValue*-1);
        }

        if ($modName === '#% increased Defences from equipped Shield') {
            $this->defencesFromShield += $modValue;
        }
        
        // Shaper's Touch unique gloves Stat Conversion
        if ( $mod === '2% increased Energy Shield per 10 Strength') {
            $this->shapers_touch = true;
        }

        //if Chaos Inoculation
        if ( $modName === '#% more maximum Energy Shield') {
            $this->infusedShield = $modValue;
        }

        $this->total = $this->treeVal + $this->itemVal + $this->intVal + $this->jewVal;

    }

    public function finalCalcualtion($allStats){
        //add IntVal
        $this->intVal = round($allStats['defense'][15]->total/5);
        $this->total += $this->intVal;

        //Shaper's Touch unique Gloves
        if ($this->shapers_touch) {
            $shaper_touch = floor($allStats['defense'][14]->total/10)*2;
            $this->total +=  $shaper_touch;
            $this->note = $this->note . '<br>Stat include ' . $shaper_touch . "% Energy Shield from Shaper's Touch";
        }

        $tempVal = 0;
        // Extra energy Shield from Presence of Chayula
        if ($allStats['defense'][8]->chaylaVal > 0) {
            $tempVal = $allStats['defense'][8]->chaylaVal;
            $tempVal = (int) ($tempVal * $this->total) / 100;
            $this->chaylaVal = $tempVal + $allStats['defense'][8]->chaylaVal + $allStats['defense'][9]->chaylaVal;
            $this->chaylaVal = (int) (($this->chaylaVal * $this->infusedShield) / 100) + $this->chaylaVal;
            // $this->note = $this->note . '<br>Included ' .  $this->chaylaVal . ' Extra energy Shield from Presence of Chayula';
        }

        // Extra energy Shield from Sanctuary of Thought
        if ($allStats['defense'][10]->sanctuaryVal > 0) {
            $tempVal = $allStats['defense'][10]->sanctuaryVal;
            $tempVal = (int) ($tempVal * $this->total) / 100;
            $this->sanctuaryVal = $tempVal + $allStats['defense'][10]->sanctuaryVal + $allStats['defense'][11]->sanctuaryVal;
            $this->sanctuaryVal = (int) (($this->sanctuaryVal * $this->infusedShield) / 100) + $this->sanctuaryVal;
            // $this->note = $this->note . '<br>Included ' .  $this->sanctuaryVal . ' Extra energy Shield from Sanctuary of Thought';
        }
        $tempVal = $allStats['defense'][12]->total;
        $tempVal = (int) (($tempVal * $this->total) / 100) + $tempVal;
        $this->totalCalc = round($tempVal + $this->sanctuaryVal + $this->chaylaVal);

        //add Shield Bonus if exist
        $tempVal = ($allStats['defense'][12]->shieldVal * $this->defencesFromShield) / 100;
        $tempVal = ($tempVal * $this->infusedShield) / 100 + $tempVal;
        $this->totalCalc += round($tempVal);
    }

}