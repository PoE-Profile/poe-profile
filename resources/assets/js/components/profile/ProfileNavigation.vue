<template>
<div class="navigation" style="padding-bottom: 0px;padding-top: 10px;background: #190a09;">
    <ul class="nav nav-tabs poe-profile-menu" style="padding-left: 10px;">
        <div class="profile" v-if="!build && !ranks">
            <li class="pull-left" >
                <h3 style="margin-right:20px;color:#eee;">
                    <div class="form-group">
                        {{account}}

                        <button href="#" class="btn btn-sm poe-btn show-tooltip"
                        data-toggle="tooltip" data-placement="bottom" v-if="twitch.streamer!=null"
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
                            type="button" data-toggle="tooltip" data-placement="bottom"
                            :title="favAccButtonText" @click.prevent="toggleFavAcc(account)">
                        <i class="fa fa-star" aria-hidden="true"></i></button>

                    </div>
                </h3>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="#">Characters</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" :href="'/profile/'+ account + '/ranks'">Ranks</a>
            </li>
            <li class="nav-item" style="display: block;">
                <div class="input-group " style="display:block;margin-left:auto;margin-right:auto;">
                    <input type="text" name="account" v-model="buildName" class="form-control" style="display: inline-block;width:80%;margin-left:auto;margin-right:auto;background:#202624; border-color: #CCCCCC;" placeholder="Enter Build name" v-on:keyup.enter="search()">
                    <span class="input-group-btn" style="display: inline-block;width:20%;">
                        <button type="submit" class="btn btn-outline-warning" style="display: inline-block;" @click.prevent="saveBuild()">Save</button>
                    </span>
                </div>
            </li>
            <li class="pull-right " style="padding-right:10px;">
                [<a class="link show-tooltip" target="_blank"
                data-toggle="tooltip" data-placement="top" title="Go to profil on pathofexile.com"
                :href="'https://www.pathofexile.com/account/view-profile/' + account + '/characters?characterName='+character.name">PoE profile</a>]
            </li>
        </div>

        <div class="public-build" v-else-if="ranks">
            <li class="pull-left" >
                <h3 style="margin-right:20px;color:#eee;">
                    <div class="form-group">
                        {{account}}

                        <button href="#" class="btn btn-sm poe-btn show-tooltip"
                        data-toggle="tooltip" data-placement="bottom" v-if="twitch.streamer!=null"
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
                            type="button" data-toggle="tooltip" data-placement="bottom"
                            :title="favAccButtonText" @click.prevent="toggleFavAcc(account)">
                        <i class="fa fa-star" aria-hidden="true"></i></button>

                    </div>
                </h3>
            </li>
            <li class="nav-item">
                <a class="nav-link " href="#">Characters</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" :href="'/profile/'+ account + '/ranks'">Ranks</a>
            </li>
            <li class="pull-right " style="padding-right:10px;">
                [<a class="link show-tooltip" target="_blank"
                data-toggle="tooltip" data-placement="top" title="Go to profil on pathofexile.com"
                :href="'https://www.pathofexile.com/account/view-profile/' + account + '/characters?characterName='+character.name">PoE profile</a>]
            </li>
        </div>

        <div class="public-build" v-else-if="favStore.isBuildPublic(account)">
            <li class="nav-item" style="display: block;">
                <div class="input-group " style="display:block;margin-left:auto;margin-right:auto;">
                    <input type="text" name="account" v-model="buildName" class="form-control" style="display: inline-block;width:80%;margin-left:auto;margin-right:auto;background:#202624; border-color: #CCCCCC;" placeholder="Enter Build name" v-on:keyup.enter="search()">
                    <span class="input-group-btn" style="display: inline-block;width:20%;">
                        <button type="submit" class="btn btn-outline-warning" style="display: inline-block;" @click.prevent="saveBuild()">Save</button>
                    </span>
                </div>
            </li>
        </div>
        
        <div class="local-build" v-else>
            <li class="pull-left">
                <div class="form-group">
                    <h3 style="margin-right:20px;color:#eee;">My Builds</h3>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="#">Builds</a>
            </li>

            <li class="nav-item" style="display: block;margin-left: 10px;">
                <div class="input-group " style="display:block;margin-left:auto;margin-right:auto;">
                    <input type="text" name="build" :value="buildLink" id="build" class="form-control" 
                    style="display: inline-block;width:80%;margin-left:auto;margin-right:auto;background:#202624; border-color: #CCCCCC;">
                    <span class="input-group-btn" style="display: inline-block;width:20%;">
                        <button type="submit" data-clipboard-target="#build" class="btn btn-outline-warning" style="display: inline-block;" @click.prevent="buildLink">Share</button>
                    </span>
                </div>
            </li>
        </div>
        
    </ul>

    <modal-twitch :stream="stream" v-show="isModalVisible" @close="closeModal" ></modal-twitch>
    
</div>
</template>


<script type="text/javascript">

var favStore = require('../../helpers/FavStore.js');
var profileStore = require('../../helpers/profileStore.js');
import {poeHelpers} from '../../helpers/poeHelpers.js';

export default {

    props: {
        account: {
            type: String,
            required: true,
            default: '',
        },

        character: {
            type: String,
            required: true,
            default: '',
        },

        build: {
            type: Boolean,
            required: true,
            default: false,
        },

        twitch: {
            type: Object,
            required: false,
            default: null,
        },

        ranks: {
            type: Boolean,
            default: false,
        },
    },

    components: {
        // 'item': Item, 
    },

    data: function() {
        return {
            buildLink: window.location.href,
            buildName: '',
            stream: '',
            favStore: favStore,
            profileStore: profileStore,
            isModalVisible: false,
        }
    },

    computed: {
        favAccButtonText: function (){
            if(this.favStore.checkAccIsFav(this.account)){
                return 'Remove from favorites.';
            }
            return 'Add to favorites.';
        },
    },

    watch: {},

    methods: {

        toggleFavAcc: function (acc) {
            this.showAlert=true;
            if (this.favStore.checkAccIsFav(acc)) {
                this.favStore.removeAcc(acc);
                // this.alertMsg="Account is removed from favorites .";
            }else{
                this.favStore.addAcc(acc);
                // this.alertMsg="Account is added to favorites . To see all favorites go to \"<a href='/home' class='about-link'>Home</a>\" ";
            }

            Vue.nextTick(function () {
                $('.show-tooltip').tooltip('dispose');
                $('.show-tooltip').tooltip();
            })
        },

        isTwitchOnline: function (){
            return this.twitch.streamer.online;
        },

        playTwitch: function(){
            this.stream = this.twitch.streamer;
            this.isModalVisible=true;
        },
        
        closeModal: function() {
            this.stream = null;
            this.isModalVisible = false;
        },

        saveBuild: function () {
            if(this.favStore.isBuildPublic(this.account)){
                var build = this.character;
                build.name = this.buildName;
                this.favStore.addBuild(build);
                this.redirectBuild(build);
            }
            var vm = this;
            var formData = new FormData();
            formData.append('account', vm.account);
            formData.append('char', vm.character);
            formData.append('name', vm.buildName);
            axios.post('/api/saveBuild', formData).then((response) => {
                // save to favStore Build comming from this response
                this.favStore.addBuild(response.data);
                this.redirectBuild(response.data);
            });
        },

        redirectBuild: function(build) {  
            location.replace((new poeHelpers).getBaseDomain() + '/builds/' + build.buildId + '/' + build.name);
        }
    }

};

</script>