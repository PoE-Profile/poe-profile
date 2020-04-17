<?php

namespace App\Parse_mods\defense_stats;


use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Energy_Shield extends Stat
{
    private $jewellery = ['Ring', 'Ring2', 'Amulet', 'Belt', 'Weapon', 'Weapon2'];
    private $infusedShield = 0;


    public $note = '';
    private $br = '';
    public $chaylaVal = 0;
    public $geofris = 0;
    public $shieldVal = 0;
    public $fromRadiantFaith = 0;
    public $radiantFaith = false;
    public $shapers_touch = false;
    public $acrobatics = false;
    private $parentMod = ' to maximum Energy Shield';

    public function parse($mod, $type)
    {
        if ($this->note !== '') {
            $this->br = '<br>';
        }

        $this->setType($type);
        $this->name ='Energy Shield';
        $modValue = (int) filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        //flat Energy Shield from Jewelery
        if (in_array( $this->currentItemType , $this->jewellery) || $this->realItemType == 'Quivers') {
            if ( $modName === '+# to maximum Energy Shield') {
                $this->setVal($modValue);
                $this->setItemVal($modValue, $mod);
            }
        }

        //flat Energy Shield from Tree
        if ($this->currentType === 'tree' && $modName === '+# to maximum Energy Shield') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

        //flat Energy shield from Property
        if ( $modName === '# Energy Shield') {
            
            if ($this->currentItemType == 'Offhand') {
                $this->shieldVal = $modValue;
            }
            
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

        //if Chaos Inoculation
        if ( $modName === '#% more maximum Energy Shield') {
            $this->infusedShield = $modValue;
            $this->note = $this->note . $this->br .'Included ' . $this->infusedShield . '% more maximum Energy Shield from Infused Shield passive tree point';
        }

        if (\Str::contains($mod, 'Grants maximum Energy Shield equal to 15% of your Reserved Mana ')) {
            $this->radiantFaith = true;
        }

        if (\Str::contains($mod, '50% less Armour and Energy Shield')) {
            $this->acrobatics = true;
            $this->note = $this->note . $this->br .'50% less Energy Shield from Acrobatics Keystone';
        }

        // Shaper's Touch unique gloves Stat Conversion
        if ( $mod === '+2 maximum Energy Shield per 5 Strength') {
            $this->geofris = true;
        }

        $this->total = $this->treeVal + $this->itemVal + $this->jewVal;
    }

     public function finalCalcualtion($allStats){
        if ($this->geofris) {
            $geofris = round($allStats['defense'][14]->total/2.5);
            $this->total +=  $geofris;
            $this->note = $this->note . $this->br . 'Stat include ' . $geofris . " to maximum Energy Shield from Geofri's Sanctuary";
        }

        if ($this->acrobatics) {
            $this->total = $this->total/2;
        }

        $this->total = (($this->total * $this->infusedShield) / 100) + $this->total;
        $this->total = round($this->total);
     }

}