<?php

namespace App\Parse_mods\offence_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Attack_Critical_Chance extends Stat
{
    public $note = '';
    private $weaponType = '';
	private $parentMod = '% Attack Critical Chance';
    private $weapons = ['Weapon', 'Weapon2', 'Offhand', 'Offhand2'];
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Critical Chance for Attacks';
        $modValue = filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        // Combined Critical Chance From different Weapon Types
        if (str_contains($mod, 'Weapon type:') && str_contains($this->currentItemType, 'Weapon')) {
            $this->weaponType = explode(':', $mod)[1];            
        }

        if ($this->checkWeaponTypes($modName)) {
            $this->setVal($modValue);
        }

        // From items // tree // jews
        if ( $modName === '#% increased Global Critical Strike Chance') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

        // From items // tree // jews
        if ($this->currentType !== 'item' && $modName === '#% increased Critical Strike Chance') {
            $this->setVal($modValue);
        }

        if ($this->currentType === 'item' && !in_array( $this->currentItemType , $this->weapons)) {
            if ( $modName === '#% increased Critical Strike Chance') {
                $this->setVal($modValue);
                $this->setItemVal($modValue, $mod);
            }
        }
    }

    private function checkWeaponTypes($modName){
        if ($this->currentType !== 'item' && str_contains($modName, '#% increased Critical Strike Chance with')) {
            $modName = str_replace('#% increased Critical Strike Chance with ', '', $modName);
            $modName = str_replace(' Melee Weapons', '', $modName);
            $modName = str_singular($modName);
            // var_dump($modName);
            if (str_contains($this->weaponType, $modName)) {
                if (!str_contains($this->note, $modName)) {
                   $this->note = $this->note . 'Stat include ' . $modName . ' Critical Chance <br>';
                }
                return true;
            }
        }
        return false;
    }
}