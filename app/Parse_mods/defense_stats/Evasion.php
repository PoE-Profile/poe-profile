<?php

namespace App\Parse_mods\defense_stats;


use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;


class Evasion extends Stat
{
    public $baseVal = 0;
    public $shieldVal = 0;
    private $parentMod = ' to Evasion Rating';

    private $jewellery = ['Ring', 'Ring2', 'Amulet', 'Belt'];

    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='Evasion';
        $modValue = (int) filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        // From Base_Stats.php
        if ($this->currentType === 'base' && $modName === '+# to Evasion Rating') {
           $this->baseVal += $modValue;
        }

        // From tree // jews
        if ($this->currentType !== 'item' &&  $modName === '+# to Evasion Rating') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

        //flat Evasion from Jewelery
        if (in_array( $this->currentItemType , $this->jewellery)) {
            if ( $modName === '+# to Evasion Rating') {
                $this->setVal($modValue);
                $this->setItemVal($modValue, $mod);
            }
        }

        //flat Evasion shield from Property
        if ( preg_replace('/\d+/u', '', $mod) === ' Evasion Rating') {
            if ($this->currentItemType == 'Offhand') {
                $this->shieldVal = $modValue;
            }
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

        $this->total = $this->treeVal + $this->itemVal + $this->baseVal + $this->jewVal;

    }

}