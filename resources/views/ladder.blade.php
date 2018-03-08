@extends('layouts.main')

@section('jsData')
<script type="text/javascript">
    window.PHP = {
        account: '',
        poe_leagues: "{{ env('POE_LEAGUES') }}"
    }
</script>
@stop


@section('title')
    PoE Profile Info Ladder
@endsection

@section('script')
<script type="text/javascript" src="{{ mix('/js/build/home.js') }}"></script>
@endsection

@section('styleSheets')
@endsection

@section('content')

<div class="text-xs-center" style="padding-bottom:5px;">
    <div style="margin: 0 auto;height: 91px;width: 729px;border: 1px solid #FFF;">
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
<div class="container" style="color: white;background: #000 url(https://web.poecdn.com/image/layout/atlas-bg.jpg?1476327587) no-repeat top center;" v-cloak>
    <div class="tab-pane" id="ladders" role="tabpanel" style="background-color: #211F18;opacity: 0.85;min-height:800px;">
        <div class="text-xs-center1">
            <ul class="nav nav-pills char-nav pull-right" style="background-color: #211F18;opacity: 0.85;">
                <li class="nav-item" v-for="league in poe_leagues">
                    <drop-down  v-on:selected="filterListCharacters" :list="leaguesDropDown(league)"
                        v-if="isLeagueDropDown(league)" :search="false" style="min-width:200px; padding: 2px;"
                        :lclass="''" :class="[isLeagueDropDownSelected(league)?'active nav-link':'nav-link ']">
                        <span v-if="isLeagueDropDownSelected(league)">@{{selectedLeague}}</span>
                        <span v-else>@{{league.split("::")[0]}}     </span>
                    </drop-down>
                    <a v-else href="#" class="nav-link" data-toggle="tab" role="tab"
                        :class="{'active': league==selectedLeague}"
                        @click.prevent="filterLeague(league)">
                        @{{league}}
                    </a>
                </li>
            </ul>
            <h3 class="" style="padding:7px;">Ladders</h3>
        </div>
        <list-characters v-on:filter-list="filterListCharacters" :char-data="(ladderPaginate.data !== 'Undefined') ? ladderPaginate.data : []"></list-characters>
        <loader :loading="isLoading" style="margin-left:auto;margin-right:auto;width:150px;"></loader>

        <div class="" v-if="ladderPaginate==null" style="height:100%;">
            <loader :loading="isLoading" style="margin-left:auto;margin-right:auto;width:150px;"></loader>
            <div class="" v-if="listCharsError.length>0">
                <h3 class="text-xs-center">@{{listCharsError}}</h3>
            </div>
        </div>
        <div class="prevNext text-xs-center" v-if="ladderPaginate.total > 0">

            <a class="page-link poe-btn" href="#" @click.prevent="changePage(1)">First</a>
            <a class="page-link poe-btn" href="#" @click.prevent="changePage(ladderPaginate.current_page -1)">Previous</a>

            <div class="sss" style="
                  left: 0;
                  right: 0;
                  margin-left: auto;
                  margin-right: auto;
                  width: 360px; ">
                <span v-for="n in pages" >
                    <a class="page-link poe-btn"  :class="(ladderPaginate.current_page === n) ? 'active' : ''" href="#" @click.prevent="changePage(n)">@{{n}}</a>
                </span>
            </div>


            <a class="page-link poe-btn pull-right" href="#" @click.prevent="changePage(ladderPaginate.last_page)">Last</a>
            <a class="page-link poe-btn pull-right" href="#" @click.prevent="changePage(ladderPaginate.current_page+1)">Next</a>

        </div>
    </div>
</div>

@endsection
