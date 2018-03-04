<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Parse_mods\Stats_Manager;
use App\Parse_mods\Base_Stats;
use App\Parse_mods\CharacterTreePoints;

class Snapshot extends Model
{
    protected $fillable = ['hash', 'tree_data', 'item_data', 'poe_version', 'original_char', 'original_level'];

    // Tree_Data Mutator & Accessor
    public function setTreeDataAttribute($value)
    {
        $tree_data = gzcompress($value);
        $tree_data = base64_encode($tree_data);
        $this->attributes['tree_data'] = $tree_data;
    }

    public function getTreeDataAttribute($value)
    {
        // dd($value);
        $tree_data = base64_decode($value);
        $tree_data = gzuncompress($tree_data);
        return json_decode($tree_data, true);
    }

    // Item_Data Mutator & Accessor
    public function setItemDataAttribute($value)
    {
        $item_data = gzcompress($value);
        $item_data = base64_encode($item_data);
        $this->attributes['item_data'] = $item_data;
    }

    public function getItemDataAttribute($value)
    {
        $item_data = base64_decode($value);
        $item_data = gzuncompress($item_data);
        return json_decode($item_data, true);
    }

    public function getStats()
    {
        // var_dump($this->item_data);
        $stManager = new Stats_Manager($this->item_data,$this->tree_data);
        return $stManager->getStats();
    }
}
