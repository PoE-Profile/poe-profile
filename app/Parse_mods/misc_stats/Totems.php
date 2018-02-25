<?php

namespace App\Parse_mods\misc_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Totems extends Stat
{
    private $parentMod = 'Totems';

    public $baseVal = 0;
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='Totems';

        preg_match('/\d+/', $mod, $matches);
        $modValue = count($matches)>0 ? $matches[0] : 1;
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ($this->currentType === 'base' && str_contains($modName, 'additional Totem')) {
            $this->baseVal += $modValue;
        }

        if (str_contains($modName, 'additional Totem')) {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

        $this->total = $this->treeVal + $this->itemVal + $this->jewVal + $this->baseVal;
    }

}