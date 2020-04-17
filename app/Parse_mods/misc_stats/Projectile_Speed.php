<?php

namespace App\Parse_mods\misc_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Projectile_Speed extends Stat
{
	private $parentMod = '% increased Projectile Speed';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Projectile Speed';

        $modValue = (int) filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        // dd('modValue');
        if ( $modName === '#% increased Projectile Speed') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }
    }

}