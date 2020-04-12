<?php

namespace App\Parse_mods\misc_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Traps extends Stat
{
    private $parentMod = 'Traps';

    public $baseVal = 0;
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='Traps';

        preg_match('/\d+/', $mod, $matches);
        $modValue = count($matches)>0 ? $matches[0] : 1;
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ($this->currentType === 'base' && \Str::contains($modName, 'additional Trap')) {
            $this->baseVal += $modValue;
        }

        if (\Str::contains($modName, 'additional Trap')) {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

        $this->total = $this->treeVal + $this->itemVal + $this->jewVal + $this->baseVal;
    }

}