<template lang="html">
    <main role="main" id="content">
        <div class="d-flex flex-column h-100">
          <div class="h-501" style="height:40%!important;">
              <div class="h-100 d-flex flex-row bd-highlight">
                  <iframe src="https://player.twitch.tv/?channel=ziggydlive"
                  frameborder="0" class="m-0 w-50 h-100"
                  allowfullscreen="true" scrolling="no"></iframe>
                  <iframe src="https://player.twitch.tv/?channel=zizaran"
                  frameborder="0" class="m-0 w-50 h-100 border-left"
                  allowfullscreen="true" scrolling="no"></iframe>
              </div>
          </div>
          <div class="h-501" style="height:60%!important;">
              <div class="h-100 d-flex flex-row page-bg">
                  <div class="overflow-auto" style="width:75%!important;opacity: 0.80;">

                    <ul class="nav nav-tabs sticky-top" style="padding-left: 10px;background-color: rgb(33, 31, 24);">
                        <li class="nav-item p-1 pr-4">
                            <a class="btn btn-sm btn-secondary poe-btn" href="/">
                              <i class="fa fa-chevron-left" aria-hidden="true"></i>
                              Back to Poe-Profile.info
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/ladders" class="nav-link active">
                                Kammell's Betrayal HC Duo Race to 85
                            </a>
                        </li>
                    </ul>

                    <div class="h-100 row" style="" ref="ladder">
                        <list-characters class="w-100 pb-5" :delve="true" :compact="true"
                            :load-profile="false"  @selected-char="openProfile" show-twitch
                            :char-data="(ladderPaginate.data !== 'Undefined') ? ladderPaginate.data : []" >
                        </list-characters>
                    </div>
                  </div>
                  <div class="h-100 overflow-auto " style="width:25%!important;opacity: 0.90;">
                        <ul class="nav nav-tabs sticky-top" style="padding-left: 10px;background-color: rgb(33, 31, 24);">
                            <li class="nav-item"><a href="/ladders" class="nav-link active">Recent clips from race:</a></li>
                        </ul>
                        <twitch-clips @open-clip="openClip"></twitch-clips>
                    </div>
              </div>
          </div>
        </div>
        <div ref="profile" class="profile-view">
            <div class="row">
                <div id="frameNav" class="text-center 1rounded-pill w-100 text-light 1bg-secondary">
                    <!-- Centered element -->
                    <a class="btn btn-outline-warning float-right bg-secondary closebtn" href="#"
                        style="" :class="{'show': profileVisible}"
                      @click="closeProfile" role="button">&times;</a>
                </div>
            </div>
          <iframe src="" ref="profileFrame" width="100%" height="100%" frameborder="0"></iframe>

        </div>
    </main>


</template>

<script>
import Loader from '../components/Loader.vue';
import ListCharacters from '../components/ListCharacters.vue';
import TwitchClips from '../components/TwitchClips.vue';
import {poeHelpers} from '../helpers/poeHelpers.js';

export default {
    components: {Loader, ListCharacters, TwitchClips},
    props: {
        race: {
            type: Object,
            required: true,
        },
    },
    data: function(){
        return{
            ladderPaginate: [],
            profileVisible: false,
        }
    },

    watch: {},

    computed: {},

    created: function(){},

    mounted: function () {
        this.loadListCharacters();
    },

    methods: {
        loadListCharacters() {
            this.isLoading = true;
            this.ladderPaginate = [];
            var league=this.race.league;
            var base_url = '/api/ladders/'+league.name;
            if(!league.indexed){
                base_url = '/api/private-ladders/'+league.name;
            }

            axios.get(base_url,{ params: this.filterParms }).then((response) => {
                var responsUrl =  new URL(response.request.responseURL);
                var curentUrl = new URL(location);
                var stateUrl = curentUrl.pathname+responsUrl.search;
                window.history.pushState("", "", stateUrl);
                this.ladderPaginate = response.data;
                this.isLoading = false;
                Vue.nextTick(function () {
                    $('.show-tooltip').tooltip();
                });
            });
        },

        openProfile(char){
            this.profileVisible = true;
            var charData = {'acc':char.account.name,'char':char.name};
            this.$refs.profileFrame.src=route('profile.char',charData)+"?race"
            /* Set the width of the side navigation to 250px and the left margin of the page content to 250px */
            this.$refs.profile.style.height = "80%";// "1050px";
        },
        closeProfile() {
          /* Set the width of the side navigation to 0 and the left margin of the page content to 0 */
          this.$refs.profile.style.height = "0";
          this.$refs.profileFrame.src=""
          this.profileVisible = false;
        },
        openClip(clipUrl){
            this.$refs.profileFrame.src=clipUrl;
            this.$refs.profile.style.height = "80%";// "1050px";

        }

    }

}
</script>

<style lang="css">
/* The side navigation menu */
.profile-view {
  height: 0px; /* 100% Full-height */
  width: 1155px; /* 0 width - change this with JavaScript */
  position: fixed; /* Stay in place */
  z-index: 99991; /* Stay on top */
  bottom: 0;
  left: 0;
  right: 0;
  margin: 0% auto;
  background-color: #111; /* Black*/
  padding-top: 0px; /* Place content 60px from the top */
  transition: 0.45s; /* 0.5 second transition effect to slide in the sidenav */
}
/* Position and style the close button (top right corner) */
.profile-view .closebtn {
  /* position: absolute;
  top: 25;
  padding-top: 10px;
  right: 25px; */
  /* font-size: 24px;
  background: white */
  border-radius: 50%;
  width:70px;height:70px;
  font-size: 34px;
  margin-top: 5px;
}
.closebtn.show{
    margin-right: -30px;
    margin-top: -30px;
}
.profile .row{
}

#frameNav{
    position: absolute;
    left: 0;
    right: 0;
    margin-left: auto;
    margin-right: auto;
}

.page-bg {
    color: white;
    background: #000 url(https://web.poecdn.com/image/layout/atlas-bg.jpg?1476327587) no-repeat top center;
}

</style>
