<?php

namespace App\Parse_mods\defense_stats;


use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;


class Armor extends Stat
{
    public $note = '';
    private $br = '';

    public $shieldVal = 0;
    public $acrobatics = false;
    private $parentMod = ' to Armour';
    private $jewellery = ['Ring', 'Ring2', 'Amulet', 'Belt'];
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
        $this->name ='Armour';
        $modValue = filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        // check for other Grand Spectrum jewels
        if (strpos($mod, 'Grand Spectrum') !== false) {
            $this->grand_spectrum['count'] += 1;
        }

        if (strpos($mod, 'Armour per Grand Spectrum')) {
            $this->grand_spectrum['val'] += $modValue;
        }

        // From  tree // jews
        if ( $this->currentType !== 'item' && $modName === '+# to Armour') {
            $this->setVal($modValue);
        }

        //flat Armour from Jewelery
        if (in_array( $this->currentItemType , $this->jewellery)) {
            if ( $modName === '+# to Armour') {
                $this->setVal($modValue);
                $this->setItemVal($modValue, $mod);
            }
        }

        //Check for Acrobatics Keystone
        if (\Str::contains($mod, '50% less Armour and Energy Shield')) {
            $this->acrobatics = true;
            $this->note = $this->note . $this->br .'50% less Armour from Acrobatics Keystone';
        }

        //flat Armour from Property
        if ( preg_replace('/\d+/u', '', $mod) === ' Armour') {
            if ($this->currentItemType == 'Offhand') {
                $this->shieldVal = $modValue;
            }
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

        $this->setJewVal($this->grand_spectrum);
        $this->total = $this->itemVal + $this->treeVal + $this->jewVal;

    }

    public function setJewVal($gs)
    {
        $this->jewVal = $gs['val'] * $gs['count'];
    }

    public function finalCalcualtion($allStats){
        if ($this->acrobatics) {
            $this->total = round($this->total/2);
        }
     }

}