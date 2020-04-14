<?php

namespace App\Parse_mods\offence_stats;


use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;


class Projectile_Damage extends Stat
{
    private $parentMod ='% increased Projectile Damage';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Projectile Damage';
        $modValue = (int) filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ( $modName === '#% increased Projectile Damage') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

    }

}