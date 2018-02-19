@extends('layouts.profile')

@section('jsData')
<script type="text/javascript">
    window.PHP = {
        poe_leagues: "{{ env('POE_LEAGUES') }}"
    }
</script>
@stop


@section('script')
<script type="text/javascript" src="/js/build/home.js"></script>
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

        <h3 class="" style="padding:7px;">Favorites</h3>
        <list-characters :char-data="favAccChars"></list-characters>

        <div class="" v-if="favAccChars.length==0" style="height:100%;">
            <loader :loading="isLoading" style="margin-left:auto;margin-right:auto;width:150px;"></loader>
            <div class="" v-if="listCharsError.length>0">
                <h3 class="text-xs-center">@{{listCharsError}}</h3>
            </div>
        </div>

    </div>
</div>
@endsection
