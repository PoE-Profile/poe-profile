@extends('layouts.main')

@section('jsData')
<script type="text/javascript">
    window.PHP = {
        account: '{!! $acc !!}',
        rankArchives: {!! $rankArchives !!},
        poe_leagues: "{{ env('POE_LEAGUES') }}",
        dbAcc: {!! $dbAcc !!},
        build: {!! $build or "null" !!}
    }
</script>
@stop

@section('title')
    PoE Profile Info Ranks / {{$acc}}
@endsection

@section('script')
<script type="text/javascript" src="{{ mix('/js/build/profile.js') }}"></script>
<script type="text/javascript" src="http://www.jqueryscript.net/demo/Base64-Decode-Encode-Plugin-base64-js/jquery.base64.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.5.12/clipboard.min.js"></script>
<script type="text/javascript">
$('.show-tooltip').tooltip();
$(function () {
    new Clipboard('.clipboard');
})
</script>
@endsection

@section('content')
<div class="container" v-cloak>
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
                selected-tab="ranks"
                :character="isBuild ? character : character.name">
    </profile-nav>

    <div class="noRanks bottom-info-content" style="text-align:center;" v-if="rankArchives.length ===0">
        @if(count($rankArchives) == 0)
            <br><br>
            <h3 >We have't have indexed ranks for this account from previous leagues!</h3>
            <br><br><br><br><br><br>
        @endif
    </div>
    <div class="list-ranks" v-else>
        <list-characters-rank league :char-data="rankArchives"></list-characters-rank>
    </div>
</div>
@endsection
