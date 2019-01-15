@extends('layouts.main')

@section('jsData')
<script type="text/javascript">
    window.PHP = {
        account: '{{ $acc }}',
        char: '{{ $char or "" }}',
        chars: {!! $chars or "null" !!},
        dbAcc: {!! $dbAcc or 'null' !!},
        loadBuild: {{ $loadBuild or 'false' }},
        build: {!! $build or "null" !!}
    }
</script>
@stop

@section('title')
    PoE Profile Info {{$acc or "" }} / {{$char or "" }}
@endsection

@section('script')
<script type="text/javascript" src="http://www.jqueryscript.net/demo/Base64-Decode-Encode-Plugin-base64-js/jquery.base64.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.5.12/clipboard.min.js"></script>
<script type="text/javascript" src="{{ mix('/js/build/profile.js') }}"></script>
<script type="text/javascript">
$('.show-tooltip').tooltip();
$(function () {
    new Clipboard('.clipboard');
})
</script>
@endsection

@section('content')
<div class="container" v-cloak>
    @include('flash::message')

    <div class="text-xs-center" style="padding-bottom:4px;">
        <div style="margin: 0 auto;">
            <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- exile_profile_big -->
            <ins class="adsbygoogle"
                 style="display:inline-block;width:970px;height:90px"
                 data-ad-client="ca-pub-5347674045883414"
                 data-ad-slot="8430954096"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>
    </div>

    <profile-nav :build="isBuild"
                :account="account"
                :selected-tab="isBuild ? 'builds' : 'profile'"
                :twitch="isBuild ? null : dbAcc.streamer"
                :character="character"></profile-nav>

    <list-characters :characters="accountCharacters" :current-character="character" :is-build="isBuild"></list-characters>

    <div class="wrap"  v-if="checkBuilds()">
        <div :class="['row', getCharacterClass()+'-panel']">
            <div class="row">
                <div class=" all-stats">
                    <character-stats @toggle-stick="toggleStickStat" @stat-loaded="calcTotals"
                        :character="character" :account="account" :off-hand="showOffHand"></character-stats>
                </div>
                <div style="padding:4px 4px 0 0;">
                    <pob-code :account="account" :character="character.name" class="pull-right" style=""></pob-code>

                    <button class="btn btn-sm poe-btn show-tooltip pull-right"
                            style="margin-right: 10px;"
                            @click.prevent="isSnapshotsVisible = true"
                            data-toggle="tooltip" data-placement="top" title="Show snapshots">
                        <i class="fa fa-clone" aria-hidden="true"></i> Snapshots
                    </button>
                </div>

                <div class="right-panel-info">
                <div class="row character-info">
                    <h2 v-if="skillTreeReseted" style="color:darkred;background-color: black;opacity: 0.6"> No skill tree data.</h2>
                    <h2 class="name">@{{character.name}}</h2>
                    <h2 class="info1">Level @{{character.level}} @{{character.class}}
                        <small style="color:white;" v-if="isBuild">(patch @{{ build.poe_version }})</small>
                    </h2>
                    <a :href="'/ladders/'+character.league">
                        <h2 class="info2" v-if="!isBuild">
                        @{{character.league}} League @{{characterRank}}</h2>
                    </a>
                    <h2 class="info2" v-if="isBuild"> Original: <a :href="'/profile/'+original_char">@{{original_char}}</a></h2>
                    <h2 class="info2" v-if="!isBuild"> @{{delveDepth}} </h2>
                </div>
                <div class="inventory ">
                        <div class="inventoryPanel">
                            <loader :loading="loadingItems" style="position:absolute;margin:auto;"></loader>

                            <button class="btn btn-sm poe-btn show-tooltip pull-right"
                                    style="margin-right: 25px;"
                                    @click.prevent="switchWeapons()"
                                    type="submit" data-toggle="tooltip" data-placement="top"
                                    title="Switch to Off Hand">
                                <i class="fa fa-clone" aria-hidden="true"></i>
                            </button>

                            <item v-for="(item, index) in computedItems" :item="item" :key="index"></item>
                        </div>

                        <div style="position: absolute;">
                            <div class="item-info">
                                <item-info v-if="showItem" :item-info="hoveredItem"></item-info>
                            </div>
                        </div>
                        <div class="mod-info" v-show="showStat" style="opacity: 0.8">
                            <span class="mod-header">
                                <a href="#" @click.prevent="showStat=false" class="pull-right">
                                    <i class="fa fa-times-circle" style="font-size: 1em;" aria-hidden="true"></i>
                                </a>
                                @{{hoveredStat.text !== '' ? hoveredStat.text : hoveredStat.total+' '+hoveredStat.name}}
                            </span>
                            <span class="item-stats" style="max-width: 350px;">
                                <span class="group -textwrap tc -stat" v-if="hoveredStat.itemVal > 0">
                                    <span class="title">From Items:</span> : @{{hoveredStat.itemVal}}
                                    <br>
                                    <span class="group -textwrap tc -mod" v-for="(item, index) in stickedStatItems" >
                                        <div :class="[twoRolls() ? 'twoRolls' : '']">
                                            <a href="#" @mouseover="toggleItemInfo(index)" @mouseleave="toggleItemInfo(index)" @click.prevent="null">
                                            @{{index}}
                                            </a>: @{{item.total}}
                                        </div>
                                    </span>
                                </span>

                                <span class="group -textwrap tc -stat" v-if="hoveredStat.strVal">
                                    <span class="title">From Strength: </span>@{{hoveredStat.strVal}}
                                </span>
                                <span class="group -textwrap tc -stat" v-if="hoveredStat.intVal">
                                    <span class="title">From Intellingence: </span>@{{hoveredStat.intVal}}
                                </span>
                                <span class="group -textwrap tc -stat" v-if="hoveredStat.dexVal">
                                    <span class="title">From Dexterity: </span>@{{hoveredStat.dexVal}}
                                </span>
                                <span class="group -textwrap tc -stat" v-if="hoveredStat.jewVal > 0">
                                    <span class="title">From jewels: </span>@{{hoveredStat.jewVal}}
                                </span>
                                <span class="group -textwrap tc -stat" v-if="hoveredStat.treeVal > 0">
                                    <span class="title">From Tree: </span>@{{hoveredStat.treeVal}}
                                </span>
                                <span class="group -textwrap tc -stat" v-if="hoveredStat.baseVal">
                                    <span class="title">From Base: </span>@{{hoveredStat.baseVal}}
                                </span>
                                <span class="group gem-textwrap tc -gemdesc" v-if="hoveredStat.note" style="max-width:350px;">
                                    <span v-html="hoveredStat.note"></span>
                                    {{-- @{{hoveredStat.note}} --}}
                                </span>
                            </span>
                        </div>
                </div>
                <div class="ad-main-pole" style="border: 1px solid #FFF;">
                    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                    <!-- exileMainAd -->
                    <ins class="adsbygoogle"
                        style="display:inline-block;width:728px;height:90px"
                        data-ad-client="ca-pub-5347674045883414"
                        data-ad-slot="2036252705"></ins>
                    <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                </div>
                </div>
            </div>
        </div>
        <div class="row bottom-info-content">

            <div v-if="showBubbles">
                <bubble title='Life'
                    :auras="profileStore.getAuras()"
                    :reduced="profileStore.getReducedManaStat()"
                    :total="parseInt(moreInfoStats[0].total)"
                    :blood-magic='moreInfoStats[1].total==0'
                    label-position='right'></bubble>
                <bubble title='Mana'
                    @set-reserved-mana="calcReserved"
                    :total="parseInt(moreInfoStats[1].total)"
                    :auras="profileStore.getAuras()"
                    :reduced="profileStore.getReducedManaStat()"
                    :blood-magic='moreInfoStats[1].total==0'
                    label-position='left'></bubble>
            </div>

            <div class="bottom-center-info">
                <div class="info-panel">
                    <!-- <h3><span>Total Stats</span></h3> -->
                    <ul style="color:white !important;font-size: 18px;padding:0px;">
                        <li style="list-style-type: none;">
                            <!-- <a href="#" class="po-link" @click.prevent="">Bandits</a> -->
                            <a class="btn btn-sm poe-btn show-tooltip po-bandits-link"
                                    data-toggle="tooltip" data-placement="right"
                                    title="Select Bandit quest Rewards">
                                <i class="fa fa-plus-square" aria-hidden="true"></i> Bandits
                            </a>
                        </li>

                        <li  v-for="stat in moreInfoStats.slice(2)"
                            style="list-style-type: none;">
                            @{{stat.name}}: @{{stat.total}}
                            <small style="font-style: italic;" v-if="stat.aura !== ''"> / @{{stat.aura}} /</small>
                            <span class="show-tooltip"
                                    v-if="stat.tooltip!=''"
                                    aria-hidden="true" data-toggle="tooltip" data-placement="right"
                                    :title="stat.tooltip"
                            >
                                <i class="fa fa-question-circle"></i>
                            </span>
                        </li>
                    </ul>
                </div>
                <div class="flasks-panel">
                    <ul class="flask-list" style="padding: 0px;text-align: center;">
                        <li style="display: inline-block;margin: 5px;" v-for="flask in flasks">
                            <item :item="flask" :show-flask="true"></item>
                        </li>
                    </ul>
                </div>
            </div>
            <div id="navmoreinfo" class="row more-info-nav" style="margin-top: 10px;">
                <ul class="nav nav-tabs"  style="padding-left:10px;">
                    <li class="nav-item">
                        <a class="nav-link" href="#nav-more-info" @click.prevent="navMoreInfo"
                            data-toggle="more-info" :class="{ active: moreInfoTabActive }">
                            Skills
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#nav-more-info" @click.prevent="navMoreInfo"
                            data-toggle="skill-tree" target="_blank" :class="{ active: skillTreeActive }">
                            Skill Tree
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" @click.prevent="navMoreInfo"
                            data-toggle="jewels" target="_blank" :class="{ active: jewelsTabActive }">
                            Jewels
                        </a>
                    </li>
                </ul>
            </div>

            <div class="row tab-content more-info-content">
                <div class="tab-pane" role="tabpanel" :class="{ active: moreInfoTabActive }">
                    <div class="skill-panel" style="padding-top: 10px;">
                        <span ></span>
                        <list-skills :items="computedItems"></list-skills>
                    </div>
                </div>

                <div class="tab-pane" role="tabpanel" :class="{ active: skillTreeActive }">
                    <div class="row" style="position:absolute; padding:0px;width:1120px;">
                        <a class="btn poe-btn show-tooltip clipboard"
                            style="position:absolute;top:5px; right:10px;border: 1px solid #ddd;" data-toggle="tooltip" data-placement="top"
                            :href="'https://www.pathofexile.com/fullscreen-passive-skill-tree/' + skillTreeUrl.replace('/passive-skill-tree/','')"
                            title="Open official skill tree" target="_blank">
                            <i class="fa fa-external-link" aria-hidden="true"></i>
                        </a>

                    </div>
                    <iframe :src="skillTreeUrl" scrolling="yes" width="100%" height="850" frameborder="0"></iframe>
                </div>

                <div class="tab-pane" role="tabpanel" :class="{ active: jewelsTabActive }">
                    <div class="row" style="padding-top: 10px; min-height: 700px;">
                        <div class="items">
                            <h3 style="text-align: left;padding-left: 15px;">Jewels from Items</h3>
                            <div class="jewel-result" v-for="jewel in itemJewels">
                                <jewel :jewel="jewel"></jewel>
                            </div>
                        </div>
                        <div style="text-align:center; color:white;">
                            <strong v-if="itemJewels.length==0"><br>No Jewels</strong>
                        </div>
                        <div class="tree" style="padding-top: 25px;"  v-if="treeData!==null">
                            <h3 style="text-align: left;padding-left: 15px;">Skill Tree Jewels</h3>
                            <div class="jewel-result" v-for="jewel in treeData.items">
                                <jewel :jewel="jewel"></jewel>
                            </div>
                        </div>
                        <div style="text-align:center; color:white;"  v-if="treeData!==null">
                            <strong v-if="treeData.items.length==0"><br>No Jewels</strong>
                        </div>
                        <div style="text-align:center; color:white;" >
                            <h2 v-if="treeData==null"><br><br>Loading ...</h2>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="po-content" style="display: none;" >
            <div class="po-title" ></div>
            <div class="po-body" id="popper-content-bandits">
                <div>
                    <bandits :show="showBandits"></bandits>
                </div>
            </div>
        </div>
    </div>

    <div class="no-builds bottom-info-content"  style="text-align:center;" v-else>
        <br><br>
        <h3 >You havent saved any builds yet!</h3>
        <span> <a href="{{route('tutorial.build')}}">How to save a build</a></span>
        <br><br><br><br><br><br>
    </div>

    <modal-snapshots v-show="isSnapshotsVisible" :build="build"
        :load-data="isSnapshotsVisible"
        @close="isSnapshotsVisible = false"
        :original-char="original_char">
    </modal-snapshots>

@endsection
