<?php
namespace App\PoB;

use App\PoB\Skills;
use App\PoB\Items;
use App\PoB\Tree;

// Store all items from Tree and Item requests then send them all to Item class

class PobXMLBuilder
{
    public $xml = '';
    public $currentItemStats = [];
    public $type = '';

    public function __construct($charDetails, $treeJson)
    {
        $classIds = ['Scion', 'Marauder', 'Ranger', 'Witch', 'Duelist', 'Templar', 'Shadow'];
        $this->xml = new \SimpleXMLElement('<PathOfBuilding/>');
        // Build
        $build = $this->xml->addChild('Build');
        $build->addAttribute('level', $charDetails['character']['level']);
        $build->addAttribute('targetVersion', '3_0');
        $build->addAttribute('banditNormal', 'None');
        $build->addAttribute('bandit', 'None');
        $build->addAttribute('banditCruel', 'None');
        $build->addAttribute('className', $classIds[$charDetails['character']['classId']]);
        $build->addAttribute('ascendClassName', $charDetails['character']['class']);
        $build->addAttribute('banditMerciless', 'None');
        $build->addAttribute('mainSocketGroup', '1');
        $build->addAttribute('viewMode', 'CALCS');

        $skills = new Skills($this->xml, $charDetails['items']);
        $this->xml = $skills->getXML();

        $allItems = array_merge($treeJson['items'], $charDetails['items']);
        $items = new Items($this->xml, $allItems);
        $this->xml = $items->getXML();

        $tree = new Tree($this->xml, $treeJson, $charDetails['character'], $this->getJewelsCoords($treeJson));
        $this->xml = $tree->getXML();

        // Calcs
        $calcs = $this->xml->addChild('Calcs');
        // Notes
        $notes = $this->xml->addChild('Notes');

        // TreeView
        $treeView = $this->xml->addChild('TreeView ');
        $treeView->addAttribute('searchStr', '');
        $treeView->addAttribute('zoomY', '-70.680703089277');
        $treeView->addAttribute('zoomX', '-274.36201953303');
        $treeView->addAttribute('showHeatMap', 'false');
        $treeView->addAttribute('zoomLevel', '2');
        $treeView->addAttribute('showStatDifferences', 'true');

        // Config
        $config = $this->xml->addChild('Config');
    }

    public function getXML()
    {
        return $this->xml->asXML();
    }

    private function getJewelsCoords($treeJson)
    {
        $jews = [];
        foreach ($treeJson['items'] as $k => $item) {
            $hash = $treeJson['jewel_slots'][$item['x']]['passiveSkill']['hash'];
            $jews[$hash] = $k+1;
        }

        return $jews;
    }

    // return encoded XML for PoB
    public function encodedXML()
    {
        $this->xml = gzcompress($this->xml->asXML());
        $this->xml = base64_encode($this->xml);
        $this->xml = str_replace("+", "-", $this->xml);
        $this->xml = str_replace("/", "_", $this->xml);
        return $this->xml;
    }
}
