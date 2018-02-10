<?php

namespace App\Parse_mods;

use App\Parse_mods\ModParse;

class Stat implements ModParse
{
    public $total = 0;
    public $itemVal = 0;
    public $treeVal = 0;
    public $jewVal = 0;
    public $name = '';

    public $items = [];

    protected $currentType;
    protected $currentItemType;
    protected $realItemType;

    public function parse($mod, $type){}
    public function finalCalcualtion($allStats){}

    protected function setVal($val)
    {   
        switch ($this->currentType) {
            case 'item':
                // $this->setItemVal($val, '');
                $this->itemVal += $val;
                break;
            case 'tree':
                $this->treeVal += $val;
                break;
            case 'jewel':
                $this->jewVal += $val;
                break;

            default:
                # code...
                break;
        }
        $this->total = $this->itemVal + $this->treeVal + $this->jewVal;
    }

    public function setType($type)
    {
        $type = explode('/', $type);
        $this->currentItemType = '';
        if (count($type) > 1) {
            $this->currentItemType = $type[1];
            // set current itemType in items array
            if (!array_key_exists($type[1], $this->items)) {
                $this->items[$type[1]] = [
                    'total' => 0,
                    'mods' => []
                ];
            }
            $this->realItemType = $type[2];
        }
        $this->currentType = $type[0];
    }

    public function setItemVal($val, $mod) {
        if(isset($this->items[$this->currentItemType])){
            $this->items[$this->currentItemType]["total"] += $val;
            array_push($this->items[$this->currentItemType]["mods"], $mod);
        }
    }
    
}