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
        $value=json_encode($value);
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
        $value=json_encode($value);
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

    static public function create($acc, $char){
        $itemsData = PoeApi::getItemsData($acc, $char);
        $treeData = PoeApi::getTreeData($acc, $char);

        if (!array_key_exists('items', $itemsData)) {
            return;
        }
        $itemsNoFlasks = array_filter($itemsData['items'], function ($item){
            return $item['inventoryId']!="Flask";
        });
        $itemsNoFlasks=json_encode($itemsNoFlasks);
        $hash = md5(json_encode($treeData).'/'.$itemsNoFlasks);

        $snapshot = \App\Snapshot::firstOrNew(['hash' => $hash]);
        $snapshot->item_data = $itemsData;
        $snapshot->tree_data = $treeData;
        $snapshot->original_char = $acc .'/'. $char;
        $snapshot->poe_version = config('app.poe_version');
        $snapshot->original_level = $itemsData['character']['level'];
        $snapshot->save();
        return $snapshot;
    }

}
