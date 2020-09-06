<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class PoeTree extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'poe:tree';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $response = Http::get('https://www.pathofexile.com/fullscreen-passive-skill-tree');
        $html = $response->body();
        $dom = new \domDocument; 
        $dom->loadHTML($html); 
        
        $xpath = new \DOMXPath($dom);
        // $results = $xpath->query("//*[@class='topBar first last']");
        //remove node passiveSkillTreeControlsContainer
        $node = $xpath->query("//*[@id='passiveSkillTreeControlsContainer']");
        $node->item(0)->parentNode->removeChild($node->item(0));

        /*
        $newdoc = new \DOMDocument;
        // Add some markup
        $newdoc->loadXML('<script type="text/javascript" src="/js/skill_tree_patch.js" ></script>');
        $scriptNode = $newdoc->getElementsByTagName("script")->item(0);
        $scriptNode = $dom->importNode($scriptNode, true);
        $bodyNode = $dom->getElementsByTagName("body")->item(0);
        // And then append it to the "<root>" node
        $bodyNode->appendChild($scriptNode);
         */

        //remove all scripts for test
        // while (($r = $dom->getElementsByTagName("script")) && $r->length) {
        //     $r->item(0)->parentNode->removeChild($r->item(0));
        // }
        \Storage::put('passive-skill-tree.html', (string) $dom->saveHTML());
    }
}
