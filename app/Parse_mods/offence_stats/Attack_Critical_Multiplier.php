<?php

namespace App\Parse_mods\offence_stats;


use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;


class Attack_Critical_Multiplier extends Stat
{
    public $note = '';
    private $weaponType = '';
	private $parentMod = '% Attack Critical Multiplier';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Critical Multiplier for Attacks';
        $modValue = filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        // Combined Critical Multiplier From different Weapon Types
        if (str_contains($mod, 'Weapon type:') && str_contains($this->currentItemType, 'Weapon')) {
            $this->weaponType = explode(':', $mod)[1];            
        }

        if ($this->checkWeaponTypes($modName)) {
            $this->setVal($modValue);
        }

        // From items // tree // jews
        if ( $modName === '+#% to Global Critical Strike Multiplier') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

        // From items // tree // jews
        if ( $modName === '+#% to Critical Strike Multiplier') {
            $this->setVal($modValue);
        }
    }

    private function checkWeaponTypes($modName){
        if ($this->currentType !== 'item' && str_contains($modName, '#% to Critical Strike Multiplier with')) {
            $modName = str_replace('+#% to Critical Strike Multiplier with ', '', $modName);
            $modName = str_replace(' Melee Weapons', '', $modName);
            $modName = str_singular($modName);
            // var_dump('From Crit Multi:' . $modName);
            if (str_contains($this->weaponType, $modName)) {
                if (!str_contains($this->note, $modName)) {
                   $this->note = $this->note . 'Stat include ' . $modName . ' Critical Multiplier <br>';
                }
                return true;
            }
        }
        return false;
    }
}