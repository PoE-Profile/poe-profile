<template>
    <!-- Modal -->
    <div class="modal fade bd-example-modal-lg" id="twitchModal" style="top: 30px;" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" v-if="loadStream">

          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">
                <div class="pull-right pl-1">
                    <a :href="('/profile/' + stream.account.name)" class="btn btn-sm poe-btn poe-btn-twitch">Go to Profile</a>
                    <a :href="('https://www.twitch.tv/' + stream.name)" class="btn btn-sm poe-btn poe-btn-twitch">Go to Twitch</a>
                    <button type="button" class="btn btn-sm poe-btn active" data-dismiss="modal">Close</button>
                </div>
                {{stream.status}}
            </h5>
          </div>

          <div class="modal-body">
            <iframe  :src="('https://player.twitch.tv/?channel=' + stream.name)" frameborder="0" allowfullscreen="true" scrolling="no" height="550" width="100%"></iframe>
          </div>

        </div>
      </div>
    </div>
</template>

<script type="text/javascript">
import { event } from '../../helpers/eventHub.js';

export default {
    props: {
        stream: {
            type: Object,
            default: null,
        },
    },

    data: function(){
        return {
            loadStream: false,
        }
    },

    watch : {
        stream : function (val) {
            this.playTwitch(val)
        },
        loadStream : function (value) {
            // console.log("loadStream change to "+value);
        },
    },

    mounted: function() {
        var vm = this;
        $('#twitchModal').on('hidden.bs.modal', function (e) {
            vm.loadStream=false;
            console.log("hidden.bs.modal");
        })
    },

    methods: {
        playTwitch: function(stream){
            this.stream=stream;
            this.loadStream=true;
            $('#twitchModal').modal('show')
        }
    },
};

</script>

<style>
#twitchModal .modal-content{
    background-color: #211F18;
    color: white !important;
}
.modal-body{
    padding: 0px;
}
.poe-btn-twitch:hover {
    background-color: #ebb16c!important;
    color: white;
}
.modal-header{
    border: 0px;
    padding: 5px;
}
.modal-footer{
    border: 0px;
    padding: 5px;
}
.poe-btn-twitch{
    color: #ebb16c;
}
</style>
