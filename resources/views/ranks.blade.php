@extends('layouts.profile')

@section('jsData')
<script type="text/javascript">
    window.PHP = {
        account: '{!! $acc !!}',
        rankArchives: {!! $rankArchives !!},
        poe_leagues: "{{ env('POE_LEAGUES') }}",
        dbAcc: {!! $dbAcc !!}
    }
</script>
@stop

@section('title')
    PoE Profile Info Ladder Archive / {{$acc}}
@endsection

@section('script')
<script type="text/javascript" src="/js/build/profile.js"></script>
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

    <profile-nav :build="isBuild" :account="account" :twitch="isBuild ? null : dbAcc" :character="isBuild ? character : character.name" :ranks="true"></profile-nav>
    @if(count($rankArchives) == 0)
        <div class="no-ranks">
            <h3>We don't have indexed ranks for this account from previous leagues!</h3>
        </div>
    @endif
    <list-characters-rank :archive="true" :char-data="rankArchives"></list-characters-rank>

    

</div>
<modal-twitch :stream="stream" v-show="isModalVisible" @close="closeModal" ></modal-twitch>
@endsection
