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

    <profile-nav :build="isBuild"
                :account="account"
                :twitch="isBuild ? null : dbAcc.streamer"
                selected-tab="snapshots"
                :character="isBuild ? character : character.name">
    </profile-nav>
    
    @if(count($snapshots) == 0)
        <div class="noSnapshots bottom-info-content" style="text-align:center;">
            <br><br>
            <h3 >We have't have indexed ranks for this account from previous leagues!</h3>
            <br><br><br><br><br><br>
        </div>
    @else
        <div class="list-snapshots">
            <table width="100%" class="table table-hover homapage-table" style="color:white">
                <tr>
                    <th>Hash</th>
                    <th>Original Character</th>
                    <th>Original Level</th>
                    <th>Created at</th>
                </tr>
                @foreach($snapshots as $snap)
                    <tr>
                        <td ><a href="/build/{{$snap->hash}}">{{ $snap->hash }}</a></td>
                        <td>{{ $snap->original_char }}</td>
                        <td>{{ $snap->original_level }}</td>
                        <td>{{ $snap->created_at }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endif

</div>
@endsection
