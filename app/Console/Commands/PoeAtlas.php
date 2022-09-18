<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class PoeAtlas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'poe:atlas';

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
     * @return int
     */
    public function handle()
    {
        $userAgent="Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:97.0) Gecko/20100101 Firefox/97.0";
        $response = Http::withHeaders(['User-Agent' => $userAgent])->get('https://www.pathofexile.com/fullscreen-atlas-skill-tree/');
        $html = $response->body();
        $tempHtml = trim(preg_replace('/\s\s+/', '', $html));
        // dd($html);
        
        //set version
        $version='1.0';
        $pattern = '/version: \'([1-9]*\.[1-9]*)\./';
        preg_match ($pattern, $tempHtml, $matches2);
        if(count($matches2)>0){
            $version = $matches2[1];
        }
        $this->info("update Cache atlas_version to ".$version);
        \Cache::forever('atlas_version', $version);



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


        // \Storage::put('atlas-skill-tree.html', (string) $html);
        \Storage::put('atlas-skill-tree.html', (string) $dom->saveHTML());
        $this->info("update storage cache for atlas-skill-tree.html ");

        return 0;
    }
}
