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
    protected $signature = 'poe:tree {--add-nodes}';

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
        $userAgent="Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:97.0) Gecko/20100101 Firefox/97.0";
        $response = Http::withHeaders(['User-Agent' => $userAgent])->get('https://www.pathofexile.com/fullscreen-passive-skill-tree');
        $html = $response->body();
        $tempHtml = trim(preg_replace('/\s\s+/', '', $html));
        ///set latest_nodes.json
        $nodes='';
        $pattern = '/"nodes"\: (\{.*\})\,"extraImages"/';
        preg_match ($pattern, $tempHtml, $matches);
        if(count($matches)==0){
            $this->info("no nodes mach send mail");
        }else{
            $nodes = (string) $matches[1];
            \Storage::put('latest_nodes.json', $nodes);
            $this->info("update latest_nodes.json ");
        }

        //set version
        $version='';
        $pattern = '/version: \'([1-9]*\.[1-9]*)\./';
        preg_match ($pattern, $tempHtml, $matches2);
        if(count($matches2)>0){
            $version = $matches2[1];
            $this->info("update Cache current_version to ".$version);
        }
        \Cache::forever('current_version', $version);

        if($this->option('add-nodes')){
            $fileName = str_replace('.','_',$version).".json";
            \Storage::disk('nodes')->put($fileName,$nodes);
            $this->info("add new nodes file for v.".$version);
        }

        $dom = new \domDocument;
        
        // set error level
        $internalErrors = libxml_use_internal_errors(true);
        
        $dom->loadHTML($html); 
        
        // Restore error level
        libxml_use_internal_errors($internalErrors);

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
        $this->info("update storage cache for passive-skill-tree.html ");
    }
}
