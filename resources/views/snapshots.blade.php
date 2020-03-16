@extends('layouts.main')

@section('jsData')
<script type="text/javascript">
    window.PHP = {
        account: '{!! $acc !!}',
        rankArchives: {!! $snapshots !!},
        poe_leagues: "{{ env('POE_LEAGUES') }}",
        dbAcc: {!! $dbAcc !!},
        build: {!! $build or "null" !!},
    }
</script>
@stop

@section('title')
    PoE Profile Info Snapshots / {{$acc}}
@endsection

@section('script')
<script type="text/javascript" src="{{ mix('/js/build/profile.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/jquery.base64.js') }}"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.5.12/clipboard.min.js"></script>
<script type="text/javascript">
$('.show-tooltip').tooltip();
$(function () {
    new Clipboard('.clipboard');
})
</script>
@endsection

@section('content')
<div class="container ladder-bg" style="background-color: #211F18;" v-cloak>
    <div class="text-xs-center" style="padding-bottom:5px;">
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
                :twitch="isBuild ? null : dbAcc.streamer"
                selected-tab="snapshots"
                :character="isBuild ? character : character.name">
    </profile-nav>

    <div class="container ">
        <div class="row">
             <div class="p-1" style="width: 100%;color:white;">
                Filter by version : 
                <?php
                $versions = [
                    '3.5'=>'3.5 Betrayal',
                    '3.6'=>'3.6 Synthesis',
                    '3.7'=>'3.7 Legion',
                    '3.8'=>'3.8 Blight',
                    '3.9'=>'3.9 Metamorph',
                    '3.10'=>'3.10 Delirium'
                ];
                ?>
                @foreach($versions as $key=>$text)
                <a href="?version={{$key}}" class="btn btn-sm poe-btn">{{$text}}</a></li>
                @endforeach
            </div>
        </div>
    </div>

    <div class="noRanks bottom-info-content" style="text-align:center;" v-if="rankArchives.length ===0">
        <br><br>
        <h3 >We have't have indexed ranks for this account from previous leagues!</h3>
        <br><br><br><br><br><br>
    </div>
    <div class="list-ranks" v-else>
        <list-characters-rank league :account="false" :char-data="rankArchives"></list-characters-rank>
    </div>
</div>
@endsection
