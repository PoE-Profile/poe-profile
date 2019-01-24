<template>
    <div class="list-group twitch-clips w-100 m-0">
        <a href="#" v-for="clip in tempClips" @click="openClip(clip)"
        class="list-group-item list-group-item-action p-1">
            <div class="d-flex w-100 pull">
                <img calss="pull-left p-2" style="height:70px;" alt=""
                :src="clip.img">
                <div class="p-1">
                    <!-- <h5 class="mb-1">Chain please (Mathil1)</h5> -->
                    <p class="mb-1">
                        {{clip.title}}
                    </p>
                    <small class="text-muted">
                        Jan 21, 2019 | 65,726 views.
                    </small>
                </div>
            </div>
          </a>
    </div>
</template>

<script>
export default {

  name: 'PulseLoader',

  props: {
    clips: {
      type: Array,
      required: false,
    },
  },
    data () {
        return {
          tempClips: [
            {
                title: 'Chain please',
                img:'https://clips-media-assets2.twitch.tv/AT-cm%7C383392266-preview-260x147.jpg',
                url: 'https://clips.twitch.tv/embed?clip=BlatantAttractivePeafowlPJSalt'
            },
            {
                title: 'He cant keep getting away with this',
                img:'https://clips-media-assets2.twitch.tv/AT-cm%7C381399302-preview-260x147.jpg',
                url: 'https://clips.twitch.tv/embed?clip=AliveOriginalRadicchioSoonerLater'
            },
            {
                title: 'Chain please',
                img:'https://static-cdn.jtvnw.net/jtv_user_pictures/mathil1-profile_image-39376f797063a87b-50x50.jpeg',
                url: 'https://clips.twitch.tv/embed?clip=AliveOriginalRadicchioSoonerLater'
            },
            {
                title: 'He cant keep getting away with this',
                img:'https://static-cdn.jtvnw.net/jtv_user_pictures/cutedog_-profile_image-72976cbe312ba0af-50x50.png',
                url: 'https://clips.twitch.tv/embed?clip=AliveOriginalRadicchioSoonerLater'
            },
          ],

        }
    },
    methods: {
        openClip(clip){
            this.$emit('open-clip', clip.url)
        }
    }

}
</script>

<style>
/*twitch-clips list*/
.twitch-clips {
    padding: 0px;
    position: relative;
    z-index: 0;
    float: left;
}

.twitch-clips:hover .list-group {
    z-index: 2;
}

.twitch-clips:hover {
    /*height: 845px;*/
    overflow: visible;
    z-index: 100;
}

.twitch-clips .list-group {
    /*height: 1000px;*/
    width: 100%;
    position: absolute;
    top: 95px;
    left: 0;
    z-index: 1;
}

.twitch-clips a.active,
.twitch-clips a:active,
.twitch-clips a:focus{
    background-color: #494535;
    color: #ebb16c;
}

.twitch-clips a{
    color: #eee;
    background-color: #211F18;
    opacity: 1;
    border: 2px solid rgba(255, 255, 255, 0.13);
}


.twitch-clips:hover a {
    opacity: 1;
}

.twitch-clips a:hover {
    background-color: #494535;
    color: #ebb16c;
}

.twitch-clips .showless {
    overflow: hidden;
    margin-bottom: 0;
}

.twitch-clips .showmore {
    overflow: auto;
    margin-bottom: 0;
}
</style>
