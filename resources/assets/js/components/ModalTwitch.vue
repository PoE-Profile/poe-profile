<template>
<transition name="modal-fade">
<div class="modal-backdrop2">
    <div class="modal2 bd-example-modal-lg2" style="top: 70px;" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog2 modal-lg2" role="document">
        <div v-if="loadStream" class="modal-body">

          <div class="modal-header">
              <div class="pull-right">
                  <span v-if="stream.account">
                      <a :href="route('profile.acc', stream.account.name)" class="btn btn-sm poe-btn poe-btn-twitch">Go to Profile</a>
                  </span>
                  <a :href="('https://www.twitch.tv/' + stream.name)" class="btn btn-sm poe-btn poe-btn-twitch">Go to Twitch</a>
                  <button type="button" class="btn btn-sm poe-btn active" @click="close">Close</button>
              </div>
              {{stream.status}}
          </div>

          <div class="modal-body">
            <iframe :src="streamUrl" class="twitch-iframe"
                frameborder="0" allowfullscreen="true" scrolling="no"></iframe>
          </div>

        </div>
      </div>
    </div>
</div>
</transition>

</template>

<script type="text/javascript">

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
            isModalVisible: false,
            streamUrl:"",
        }
    },
    computed: {
        profileUrl: function(){
            if(stream.account)
            return '/profile/' + stream.account.name;
        },
    },
    watch : {
        stream : function (val) {
            if(val==null){
                console.log("watch stream");
                return;
            }
            console.log("watch stream");
            this.streamUrl='https://player.twitch.tv/?channel=' + this.stream.name;
            this.loadStream=true;
        },
        loadStream : function (value) {
            // console.log("loadStream change to "+value);
        },
    },

    mounted: function() {
    },

    methods: {
        close:function(event) {
            this.loadStream=false;
            this.streamUrl='';
            this.$emit('close');
        },
    },
};

</script>

<style>
.modal-backdrop2 {
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  background-color: rgba(0, 0, 0, 0.4);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 99999;
}

.modal2 {
  /* background: #ffffff; */
  box-shadow: 2px 2px 10px 1px;
  overflow-x: auto;
  display: flex;
  flex-direction: column;
  width: 950px;
  z-index: 99999;
}

.modal-header{
  padding: 5px;
  overflow: hidden;
  text-overflow: ellipsis;
  height: 40px;
  font-weight: bold;
  font-size: 20px;
}


.modal-body {
  position: relative;
  padding: 0px 2px;
  color: #FFF;
  background-color: rgb(33, 31, 24);
}

.btn-close {
  border: none;
  font-size: 20px;
  padding: 20px;
  cursor: pointer;
  font-weight: bold;
  color: #4aae9b;
  background: transparent;
}

.modal-fade-enter,
.modal-fade-leave-active {
  opacity: 0;
}

.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.5s ease;
}
.twitch-iframe {
    height:300px;
    width:100%;
}
@media (min-width: 750px) {
    .twitch-iframe {
        height:540px;
    }
}
</style>
