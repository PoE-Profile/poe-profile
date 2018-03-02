@extends('layouts.profile')

@section('jsData')
<script type="text/javascript">
    window.PHP = {
        account: '',
        csrf_token: "{{ csrf_token() }}",
        poe_leagues: "{{ env('POE_LEAGUES') }}",
    }
</script>
@endsection

@section('script')
<script type="text/javascript" src="/js/build/home.js"></script>
<script type="text/javascript">
$('.show-tooltip').tooltip();
</script>
@endsection

@section('content')
<div class="container text-center" style="color: white;background: #000 url(https://web.poecdn.com/image/layout/atlas-bg.jpg?1476327587) no-repeat top center;" v-cloak>
    <div class="account" style="width:100%">
        <div class="wrapper" style="width: 100%;padding-bottom: 10px">
            <div class="progress-bar" v-if="progress > 0">
                <span class="progress-bar-fill" v-bind:style="{width: progress+'%'}"></span>
            </div>
        </div>

        <div class="inner cover">
            <p class="lead" style="text-align:center">
                Welcome to PoE-Profile.info, ultimate PoE profile Page. <br>
                Here you can see all of your characters
                from <a href="https://www.pathofexile.com/" class="about-link" >PathOfExile.com</a> with combined stats data from passive skill tree and items. <br>
                <a href="{{ url('/about') }}" class="btn btn-sm btn-outline-warning">Learn more</a> <br>
            </p>
            <br>
        </div>
        <form enctype="multipart/form-data" action="{{route('view.post.profile')}}" method="post">
            <div class="input-group " style="width:50%;margin-left:auto;margin-right:auto;background:#202624;">
              <!-- <span class="input-group-addon" id="basic-addon1">https://www.pathofexile.com/account/view-profile/</span> -->
              <input type="text" name="account" class="form-control" style="border-color: #CCCCCC;" placeholder="Account Name...">
              <span class="input-group-btn">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <button type="submit" class="btn btn-outline-warning" >View Profile!</button>
              </span>
            </div>
        </form>
        <div class="text-xs-center" style="margin-top: 20px;">
            You can test out with this popular streamer accounts
            <a href="/profile/ziggyd/" class="about-link">ziggyd</a>,
            <a href="/profile/nugiyen/" class="about-link">nugiyen</a>,
            <a href="/profile/mathil/" class="about-link">mathil</a>,
            <a href="/profile/Zizaran/" class="about-link">zizaran</a>
            <br>
            <a class="nav-link" href="{{ url('/profile_tutorial') }}" style="color:lightblue">
                How to change your profile characters tab to public.
            </a>
        </div>

         @include('flash::message')
        <br>

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

        <div class="navigation" style="padding-bottom: 0px;margin-top: 10px;">
            <ul class="nav nav-tabs poe-profile-menu" style="padding-left: 10px;background-color: #211F18;">
                <li class="nav-item">
        		    <a class="nav-link " data-toggle="tab" @click.prevent="getFavs()"
                    href="#favs" role="tab" aria-controls="favs">Favorites</a>
        	    </li>
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" @click.prevent="getTwitch()"
                    href="#twitch" role="tab" aria-controls="twitch">Twitch</a>
        	    </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab"
                    href="#ladders" role="tab" aria-controls="ladders"
                    @click.prevent="getLadder()">League Ladders</a>
        	    </li>
            </ul>
        </div>
        <div class="tab-content" style="background-color: #211F18;min-height:800px;">
            <ul class="nav nav-pills char-nav" style=""
                v-if="selectedTab=='ladder'">
                <li class="nav-item" v-for="(league, index) in poe_leagues">
                    <drop-down v-on:selected="filterListCharacters" :list="leaguesDropDown(league)"
                        v-if="isLeagueDropDown(league)" :search="false"
                        :lclass="''" :class="[isLeagueDropDownSelected(league)?'active nav-link':'nav-link']">
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

            <list-characters v-on:filter-list="filterListCharacters" :char-data="listChars"></list-characters>

            <div class="" v-if="listChars.length==0" style="height:100%;">
                <loader :loading="isLoading" style="margin-left:auto;margin-right:auto;width:150px;"></loader>
                <div class="" v-if="listCharsError.length>0">
                    <h3 class="text-xs-center">@{{listCharsError}}</h3>
                </div>
            </div>
            <div class="prevNext text-xs-center" v-if="selectedTab=='ladder' && ladderPaginate.total > 15">

                <a class="page-link poe-btn" href="#" @click.prevent="changePage(1)">First</a>
                <a class="page-link poe-btn" href="#" @click.prevent="changePage(ladderPaginate.current_page -1)">Previous</a>

                <div class="sss" style="
                    left: 0;
                    right: 0;
                    margin-left: auto;
                    margin-right: auto;
                    width: 340px; ">
                    <span v-for="n in pages" >
                        <a class="page-link poe-btn"  :class="(ladderPaginate.current_page === n) ? 'active' : ''" href="#" @click.prevent="changePage(n)">@{{n}}</a>
                    </span>
                </div>


                <a class="page-link poe-btn pull-right" href="#" @click.prevent="changePage(ladderPaginate.last_page)">Last</a>
                <a class="page-link poe-btn pull-right" href="#" @click.prevent="changePage(ladderPaginate.current_page+1)">Next</a>

            </div>


        </div>


    </div>
</div>

@endsection
