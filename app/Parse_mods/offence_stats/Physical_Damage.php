<?php

namespace App\Parse_mods\offence_stats;


use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;


class Physical_Damage extends Stat
{
    public $note = '';
    private $br = '';
    private $weaponType = '';
	private $parentMod = '% increased Physical Damage';
    private $weapons = ['Weapon', 'Weapon2', 'Offhand', 'Offhand2'];
    public function parse($mod, $type)
    {
        if ($this->note !== '') {
            $this->br = '<br>';
        }
        $this->setType($type);
        $this->name ='% Physical Damage';
        $modValue = filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        // Combined Physical Damage From different Weapon Types
        if (\Str::contains($mod, 'Weapon type:') && \Str::contains($this->currentItemType, 'Weapon')) {
            $this->weaponType = explode(':', $mod)[1];            
        }

        if ($this->checkWeaponTypes($modName)) {
            $this->setVal($modValue);
        }

        // From items // tree // jews
        if ($this->currentType !== 'item' && $modName === '#% increased Physical Damage' || $modName === '#% increased Physical Damage with Attacks') {
            $this->setVal($modValue);
        }

        if ($this->currentType === 'item' && !in_array( $this->currentItemType , $this->weapons)) {
            if ( $modName === '#% increased Physical Damage') {
                $this->setVal($modValue);
                $this->setItemVal($modValue, $mod);
            }
        }
    }

    private function checkWeaponTypes($modName){
        if ($this->currentType !== 'item' && \Str::contains($modName, '#% increased Physical Damage with')) {
            $modName = str_replace('#% increased Physical Damage with ', '', $modName);
            $modName = str_replace(' Melee Weapons', '', $modName);
            $modName = \Str::singular($modName);
            // var_dump($modName);
            if (\Str::contains($this->weaponType, $modName)) {
                if (!\Str::contains($this->note, $modName)) {
                   $this->note = $this->note . $this->br . 'Stat include ' . $modName . ' Physical Damage';
                }
                return true;
            }
        }
        return false;
    }

}