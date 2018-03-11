@extends('layouts.main')

@section('jsData')
<script type="text/javascript">
    window.PHP = {
        account: '{!! $acc !!}',
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
        <div style="margin: 0 auto;height: 91px;width: 971px;border: 1px solid #FFF;">
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
                selected-tab="stashes"
                :character="isBuild ? character : character.name">
    </profile-nav>

    @if(count($results) == 0)
        <div class="noSnapshots bottom-info-content" style="text-align:center;">
            <br><br>
            <h1 >No public stashes results from pathofexile.com/trade/!</h1>
            <br><br><br><br><br><br>
        </div>
    @else
            <div class="bottom-info-content">
                <br><br>
                <h2 style="color: #eee;text-align:center;">Results From Current Leagues:</h4>
                @foreach($results as $league)
                    <div class="list-group-item" style="color: #eee;background-color: #211F18;">
                        <h4>{{$league->league}} found {{$league->result->total}} items
                        <a href="https://www.pathofexile.com/trade/search/{{$league->league}}/{{$league->result->id}}"
                            class="btn btn-outline-warning pull-right" target="_blank">
                            <i class="fa fa-external-link" aria-hidden="true"></i>
                            Open results in pathofexile.com/trade</a>
                        </h2>
                    </div>
                @endforeach
                <br><br><br><br><br><br>
            </div>
    @endif

</div>
@endsection
