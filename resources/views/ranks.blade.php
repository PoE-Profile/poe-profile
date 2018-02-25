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

    <div class="navigation" style="padding-bottom: 0px;padding-top: 10px;background: #190a09;">
        <ul class="nav nav-tabs poe-profile-menu" style="padding-left: 10px;">

            <li class="pull-left">
                <h3 style="margin-right:20px;color:#eee;">
                    <form enctype="multipart/form-data" action="{{route('set.profile')}}"
                        method="post" class="form-inline">
                          <div class="form-group">
                             @{{account}}
                              <input type="hidden" name="account" :value="account">
                              <input type="hidden" name="_token" value="{{ csrf_token() }}">

                              <button href="#" class="btn btn-sm poe-btn show-tooltip"
                              data-toggle="tooltip" data-placement="top" v-if="hasTwitch()"
                              title="Load Twitch Stream" @click.prevent="playTwitch()">
                                  <span v-if="isTwitchOnline()" style="">
                                      <i class="fa fa-circle" aria-hidden="true" style="color:red;"></i>
                                      <strong>Live</strong>
                                      <i class="fa fa-twitch" aria-hidden="true"></i>
                                  </span>
                                  <span v-else style="color:gray;">
                                      <strong>Offline</strong>
                                      <i class="fa fa-twitch" aria-hidden="true"></i>
                                  </span>
                              </button>
                              <button class=""
                                  :class="['btn btn-sm poe-btn form-inline show-tooltip', favStore.checkAccIsFav(account) ? 'active' : '']"
                                  type="button" data-toggle="tooltip" data-placement="top"
                                  :title="favAccButtonText" @click.prevent="toggleFavAcc(account)">
                              <i class="fa fa-star" aria-hidden="true"></i></button>

                         </div>
                    </form>
                </h3>
            </li>

    	    <li class="nav-item">
    		    <a class="nav-link" href="{{route('get.profile',$acc)}}">Characters</a>
    	    </li>
            <li class="nav-item">
    		    <a class="nav-link active" href="#">Ranks</a>
    	    </li>
            <li class="pull-right " style="padding-right:10px;">
                [<a class="link show-tooltip" target="_blank"
                data-toggle="tooltip" data-placement="top" title="Go to profil on pathofexile.com"
                :href="'https://www.pathofexile.com/account/view-profile/' + account">PoE profile</a>]
            </li>
      </ul>

        @if(count($rankArchives) == 0)
            <div class="no-ranks">
                <h3>We don't have indexed ranks for this account from previous leagues!</h3>
            </div>
        @endif
            <list-characters-rank :archive="true" :char-data="rankArchives"></list-characters-rank>
    </div>

    

    

</div>
<modal-twitch :stream="stream" v-show="isModalVisible" @close="closeModal" ></modal-twitch>
@endsection
