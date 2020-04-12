<?php

namespace App\Parse_mods\offence_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Attack_Speed extends Stat
{
    public $note = '';

    private $dualWield = false;
    private $br = '';
    private $weaponType = '';
	private $parentMod = '% increased Attack Speed';
    private $weapons = ['Axes', 'Bows', 'Claws', 'Daggers', 'Maces', 'Staves', 'Swords', 'Wands'];
    public function parse($mod, $type)
    {
        if ($this->note !== '') {
            $this->br = '<br>';
        }

        $this->setType($type);
        $this->name ='% Attack Speed';
        $modValue = filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        // Combined Attack Speed From different Weapon Types
        if (\Str::contains($mod, 'Weapon type:') && \Str::contains($this->currentItemType, 'Weapon')) {
            $this->weaponType = explode(':', $mod)[1];
        }

        if (\Str::contains($mod, 'Weapon type:') && \Str::contains($this->currentItemType, 'Offhand')) {
            $this->dualWield = true;
            $this->note =  $this->note  . $this->br . 'Included 10% More Attack Speed while dual wielding';
        }

        if ($this->checkWeaponTypes($modName)) {
            $this->setVal($modValue);
        }

        //Attack Speed from Tree
        if ($this->currentType !== 'item' && $modName === '#% increased Attack Speed') {
            $this->setVal($modValue);
        }
        
        //Attack Speed from Items
        if ($this->currentType === 'item' && !in_array( $this->realItemType , $this->weapons)) {
            if ( $modName === '#% increased Attack Speed') {
                $this->setVal($modValue);
                $this->setItemVal($modValue, $mod);
            }
        }

        if ($modName === '#% increased Attack and Cast Speed') {
            $this->setVal($modValue);
        }

        if ($modName === '#% increased Melee Attack Speed' && !\Str::contains($this->weaponType, ['Bow', 'Wand'])) {
            $this->setVal($modValue);
        }

        $this->total = $this->treeVal + $this->itemVal + $this->jewVal;
        if ($this->dualWield) {
            $this->total = round((($this->total * 10) / 100)) + $this->total;
        }
    }

    private function checkWeaponTypes($modName){
        if ($this->currentType !== 'item' && \Str::contains($modName, '#% increased Attack Speed with')) {
            $modName = str_replace('#% increased Attack Speed with ', '', $modName);
            $modName = str_replace(' Melee Weapons', '', $modName);
            $modName = \Str::singular($modName);
            // var_dump($modName);
            if (\Str::contains($this->weaponType, $modName)) {
                if (!\Str::contains($this->note, $modName)) {
                   $this->note = $this->note . $this->br . 'Stat include ' . $modName . ' Attack Speed';
                }
                return true;
            }
        }
        return false;
    }
}