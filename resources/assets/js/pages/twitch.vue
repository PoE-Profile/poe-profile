<template>
<div class="">
    <div class="" style="background-color: #211F18;opacity: 0.85;min-height:800px;">
        <list-characters :char-data="twitchAccChars" :league="true"
            show-twitch @selected-twitch="openTwitch"></list-characters>
        <loader :loading="isLoading" style="margin-left:auto;margin-right:auto;width:150px; margin-top: 50px;" ></loader>
    </div>
    <modal-twitch :stream="stream" v-show="isModalVisible" @close="closeModal"></modal-twitch>
</div>
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
            twitchAccChars: [],
            isModalVisible: false,
            stream: null,
        }
    },

    watch: {},

    computed: {},

    created: function(){},

    mounted: function () {
        this.getTwitch();
    },

    methods: {
        getTwitch: function(){
            this.isLoading=true;
            axios.get('/api/twitch').then((response) => {
                this.twitchAccChars = response.data;
                this.isLoading=false;
                this.selectedTab='twitch';
                Vue.nextTick(function () {
                    $('.show-tooltip').tooltip();
                });
            });
        },
        openTwitch: function(stream){
            this.stream = stream;
            this.isModalVisible=true;
        },
        closeModal: function() {
            this.stream = null;
            this.isModalVisible = false;
        }
    }

}
</script>

<style lang="css">

</style>
