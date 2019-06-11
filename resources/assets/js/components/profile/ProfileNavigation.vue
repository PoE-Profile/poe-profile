<template>
<div class="navigation" style="padding-bottom: 0px;padding-top: 10px;background: #190a09;">
     <div class="alert alert-success" v-if="showAlert">
        <button type="button" class="close" @click.prevent="showAlert=false">&times;</button>
        <span v-html="alertMsg"></span>
    </div>
    <ul class="nav nav-tabs poe-profile-menu" style="padding-left: 10px;">
        <div class="profile" v-if="selectedTab=='profile'||selectedTab=='ranks' ||selectedTab=='snapshots' ||selectedTab=='stashes'">
            <li class="pull-left" >
                <h3 style="margin-right:20px;color:#eee;">
                    <span :class="'profile-icon platform-'+realm"></span>
                    {{account}}
                    <button href="#" class="btn btn-sm poe-btn show-tooltip"
                    data-toggle="tooltip" data-placement="bottom" v-if="twitch!=null"
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
                    <i :class="[favStore.checkAccIsFav(account) ? favIcon.is : favIcon.not]" aria-hidden="true"></i></button>
                </h3>
            </li>
            <li class="nav-item">
                <a class="nav-link"
                    v-bind:class="[selectedTab=='profile' ? 'active' : '']"
                    :href="route('profile.acc', account)">Characters</a>
            </li>
            <li class="nav-item">
                <a class="nav-link"
                    v-bind:class="[selectedTab=='ranks' ? 'active' : '']"
                    :href="route('profile.ranks', account)">Ranks</a>
            </li>
            <li class="nav-item">
                <a class="nav-link"
                    v-bind:class="[selectedTab=='snapshots' ? 'active' : '']"
                    :href="route('profile.snapshots', account)">Snapshots</a>
            </li>
            <li class="nav-item">
                <a class="nav-link"
                    v-bind:class="[selectedTab=='stashes' ? 'active' : '']"
                    :href="route('profile.stashes', account)">Public Stash</a>
            </li>
            <li class="pull-right " style="padding-right:10px;" v-if="selectedTab=='profile'">
                [<a class="link show-tooltip" target="_blank"
                data-toggle="tooltip" data-placement="bottom" title="Go to profile on pathofexile.com"
                :href="'https://www.pathofexile.com/account/view-profile/' + account + '/characters?characterName='+character.name">PoE profile</a>]
            </li>
            <li class="pull-right " v-if="selectedTab=='profile'||favStore.isBuildPublic(account)" style="display: block;">
                <span class="badge badge-success" style="background-color: #f0ad4e;color: black;">New</span>
                <a href="#" class="btn btn-sm poe-btn po-save-build-link "
                        @click.prevent="" style="color:white; margin-right:10px;">
                    <i class="fa fa-plus-square" aria-hidden="true"></i> Save Build/Snapshot
                </a>
            </li>
        </div>


        <!-- For Builds nav -->
        <div class="local-build" v-if="selectedTab=='builds'">
            <li class="pull-left">
                <h3 style="margin-right:20px;color:#eee;">My Builds</h3>
            </li>
            <!-- <li class="nav-item">
                <a class="nav-link active" href="#">Builds</a>
            </li> -->
            <li class="pull-right">
            </li>
            <li v-if="favStore.isBuildPublic(account)" class="pull-right" style="display: block;">
                <a href="#" class="btn btn-sm poe-btn po-save-build-link"
                        @click.prevent="" style="color:white; margin-right:10px;">
                    <i class="fa fa-plus-square" aria-hidden="true"></i> SaveBuild
                </a>
            </li>
            <li v-else class="pull-right" style="margin-right: 15px">
                <a href="#" class="btn poe-btn btn-sm pull-right show-tooltip" style="margin-left: 15px;"
                    data-toggle="tooltip" data-placement="bottom"
                    title="Remove current build from local storage" @click.prevent="removeBuild(buildHash)">
                <i class="fa fa-trash" aria-hidden="true"></i> Remove Build</a>

                <div class="input-group input-group-sm" style="border-color: #ebb16c;width: 75%;">
                    <span class=" text-white pull-left" id="basic-addon1">Share Build: </span>
                    <input type="text" id="build" :value="buildLink"
                        class="form-control" style="width:380px;background:#FFFFFF; ">
                    <span class="input-group-btn" style="display: inline-block;">
                        <button type="submit" data-clipboard-target="#build"
                        class="btn poe-btn clipboard" @click.prevent="">
                        <i class="fa fa-clipboard" aria-hidden="true"></i>Copy</button>
                    </span>
                </div>
            </li>
        </div>

    </ul>



    <!-- helpers -->
    <modal-twitch :stream="stream" v-show="isModalVisible" @close="closeModal" ></modal-twitch>

    <div class="po-body" id="popper-save-build" style="display:none;">
        <div class="col-lg-12" ref="popover" style="width:350px;">
            <strong>Save Build:
                <a href="#" class="pull-right" onclick="$('.po-save-build-link').trigger('click')">
                    <i class="fa fa-times-circle" aria-hidden="true"></i>
                </a>
            </strong>
            <span>Make snapshot of items and skill tree of "{{character.name}}" and save to My Builds with name:</span>
            <div class="input-group">
                <input class="form-control" placeholder="Build name" v-model="buildName" id="buildName" aria-label="" aria-describedby="" v-on:keyup.enter="saveBuild()">
                <span class="input-group-btn">
                    <button class="btn btn-outline-secondary btn-outline-warning clipboard" type="button"
                        data-clipboard-target="#pobCode"  @click.prevent="saveBuild()">
                        <i class="fa fa-clipboard" aria-hidden="true"></i> Save
                    </button>
                </span>
            </div>
            <loader :loading="saving" style="margin-left:auto;margin-right:auto;"></loader>
            <br>
        </div>
    </div>

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
            type: Object,
            required: true,
            default: '',
        },
        selectedTab: {
            type: String,
            required: false,
        },
        twitch: {
            type: Object,
            required: false,
            default: null,
        },
    },

    components: {
        // 'item': Item,
    },

    data: function() {
        return {
            buildLink: window.location.href,
            buildName: '',
            realm:window.PHP.realm,
            stream: {},
            saving: false,
            favStore: favStore,
            profileStore: profileStore,
            isModalVisible: false,
            favIcon: {
                is:'fa fa-star',
                not:'fa fa-star-o'
            },
            alertMsg: '',
            showAlert: false,
        }
    },

    computed: {
        favAccButtonText: function (){
            if(this.favStore.checkAccIsFav(this.account)){
                return 'Remove from favorites.';
            }
            return 'Add to favorites.';
        },

        buildHash: function() {
            if (!this.account.includes('build::')) {
                return '';
            }
            return this.account.split('::')[1];
        }
    },

    watch: {},
    mounted: function () {
        $('.po-save-build-link').popover({
           trigger: 'click',
           html: true,
           title: "SaveBuild",
           content: this.$refs['popover'],
           container: 'body',
           placement: 'bottom'
        }).on('shown.bs.popover	', function () {
            $("#buildName").focus();
        });
        this.$nextTick(function () {
            $('.show-tooltip').tooltip();
        })

        if (location.pathname.split('/')[1] === 'build') {
            new Clipboard('.clipboard');
        }
    },
    methods: {

        toggleFavAcc: function (acc) {
            this.showAlert=true;
            if (this.favStore.checkAccIsFav(acc)) {
                this.favStore.removeAcc(acc);
                this.alertMsg="Account is removed from favorites .";
            }else{
                this.favStore.addAcc(acc);
                this.alertMsg="Account is added to favorites . To see all favorites go to \"<a href='/favorites' class='about-link'>Favorites</a>\" ";
            }

            Vue.nextTick(function () {
                $('.show-tooltip').tooltip('dispose');
                $('.show-tooltip').tooltip();
            })
        },

        isTwitchOnline: function (){
            return this.twitch.online;
        },

        playTwitch: function(){
            this.stream = this.twitch;
            this.isModalVisible=true;
        },

        closeModal: function() {
            this.stream = null;
            this.isModalVisible = false;
        },

        saveBuild: function () {
            //if public snapshot to save in local store
            if(this.buildHash.length>0){
                var build=this.character;
                build.name = this.buildName.replace(/ /g,"_");
                build.league = 'localBuild';
                build.buildId=this.buildHash;
                this.favStore.addBuild(build);
                this.redirectBuild(build);
                return;
            }

            this.saving=true;
            var formData = new FormData();
            formData.append('account', this.account);
            formData.append('char', this.character.name);
            axios.post('/api/build/save', formData).then((response) => {
                // save to favStore Build comming from this response
                var build=response.data;
                this.saving=false;
                build.name = this.buildName.replace(/ /g,"_");

                if (this.favStore.isBuildPublic('build::'+build.buildId)) {
                    this.favStore.addBuild(build);
                    this.redirectBuild(response.data);
                } else {
                    $('.po-save-build-link').trigger('click')
                    this.showAlert=true;
                    var localBuild = this.favStore.getBuild(build.buildId);
                    this.alertMsg="You have this shanpshot already added in 'My builds' with name " + localBuild.name;
                    setTimeout(function(){ this.showAlert=false; }.bind(this), 3000);
                }

            });
        },

        removeBuild: function(hash){
            this.favStore.removeBuild(hash);
            var lastBuild = _.last(this.favStore.favBuilds);
            if (lastBuild) {
                location.replace(route('build.show',lastBuild.buildId));
            }
            location.replace(route('builds',lastBuild.buildId));
        },

        redirectBuild: function(build) {
            location.replace(route('build.show',build.buildId));
        }
    }

};

</script>
