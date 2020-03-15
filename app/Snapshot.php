<?php

namespace App;

use App\Helpers\Items;
use App\Parse_mods\Base_Stats;
use App\Parse_mods\Stats_Manager;
use App\Parse_mods\CharacterTreePoints;
use Illuminate\Database\Eloquent\Model;

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
        $stManager = new Stats_Manager($this->item_data,$this->tree_data, false, $this->poe_version);
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

    public static function getAccChar($acc, $char){
        $original_char = $acc.'/'.$char;
        return \App\Snapshot::where('original_char',$original_char)->latest()->first();
    }

    public function getSkills()
    {
        $items = Items::withMostSockets($this->item_data);
        return Items::getSkillsFrom($items);
    }

    public function toLadderChar()
    {
        // dump($this->tree_data);
        $accName = explode('/',$this->original_char)[0];
        $data=$this->item_data;
        return [
            "id" => 0,
            "snapshot" => $this->hash,
            "snapshot_name" => $data['character']['name'],
            "league" => $data['character']['league'],
            "name" => $data['character']['name'],
            "class" => $data['character']['class'],
            "level" => $data['character']['level'],
            "items_most_sockets" => Items::withMostSockets($data),
            "account_id" => 0,
            "dead" => false,
            "public" => true,
            "created_at" => now(),
            "updated_at" => now(),
            "unique_id" => "",
            "experience" => 0,
            "online" => false,
            "stats" => null,
            "delve_default" => 0,
            "delve_solo" => 0,
            "retired" => 0,
            "levelProgress" => 0,
            "account" => [
                "id" => 0,
                "user_id" => 0,
                "name" => $accName,
                "guild" => "",
                "poe_avatar_url" => "",
                "challenges_completed" => 0,
                "last_character" => "",
                "last_character_info" => [],
                "views" => 0
            ]
        ];
    }

}
