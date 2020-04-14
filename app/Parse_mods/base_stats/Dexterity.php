<?php

namespace App\Parse_mods\base_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;


class Dexterity extends Stat
{
    public $baseVal = 0;
    public $note = '';

    private $parentMod = ' to Dexterity';
    private $noteBase = '10 dexterity grants 2% evasion rating<br>10 dexterity grants 20 accuracy rating';
    private $tempNote = '';
    private $dexPercent = 0;
    private $associatedMods = [
        '+# to Dexterity',
        '+# to Strength and Dexterity',
        '+# to Dexterity and Intelligence',
        '+# to all Attributes'
    ];

    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='Dexterity';
        if ($this->note == '') {
            $this->note =  $this->noteBase;
        }
        $modValue = (int) filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        // From Base_Stats.php
        if ($this->currentType === 'base' && $modName === '+# to Dexterity') {
           $this->baseVal += $modValue;
        }

        // From items // tree // jews
        if ( in_array($modName, $this->associatedMods ) ) {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

        // Percent Dexterity on items / tree
        if ($modName === '#% increased Dexterity' || $modName === '#% reduced Dexterity' || $modName === '#% increased Attributes') {
            $this->dexPercent += $modValue;
            $this->tempNote = '<br>Stat include ' . $this->dexPercent . '% Dexterity';
            $this->note =  $this->noteBase . $this->tempNote;
        }

        $this->total = $this->treeVal + $this->itemVal + $this->jewVal + $this->baseVal;
        $this->total = round((($this->total * $this->dexPercent) / 100)) + $this->total;
    }
}