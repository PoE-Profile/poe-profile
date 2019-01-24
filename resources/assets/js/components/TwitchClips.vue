<template>
    <div class="list-group twitch-clips w-100 m-0">
        <a href="#" v-for="clip in tempClips" :key="clip.broadcast_id" @click="openClip(clip)"
        class="list-group-item list-group-item-action p-1">
            <div class="d-flex w-100 pull">
                <img calss="pull-left p-2" style="height:70px;" alt=""
                :src="clip.thumbnails.tiny">
                <div class="p-1">
                    <!-- <h5 class="mb-1">Chain please (Mathil1)</h5> -->
                    <p class="mb-1">
                        {{clip.title}}
                    </p>
                    <small class="text-muted">
                        {{ formatDate(clip.created_at) }} | {{clip.views}} views.
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
            tempClips: [],

        }
    },
    mounted: function() {
        this.twitchPoeLatest()
    },

    methods: {
        twitchPoeLatest() {
            let twitchApi = axios.create({
                baseURL: 'https://api.twitch.tv/kraken',
                headers: {
                    Accept: 'application/vnd.twitchtv.v5+json',
                    'Client-ID': 'gi3es6sr9cmscw4aww6lbt309dyj8e'
                },
            })

            let query = {
                game: 'Path of Exile',
                trending: true,
                period: 'day',
                limit: 5
            }
            
            twitchApi.get('clips/top', { params: query }).then((response) => {
                this.tempClips = response.data.clips;
            })
        },

        formatDate(date) {
            let tempDate = new Date(date);
            let monthNames = [
                "Jan", "Feb", "March",
                "Apr", "May", "June", "July",
                "Aug", "Sep", "Oct",
                "Nov", "Dec"
            ];

            let day = tempDate.getDate();
            let monthIndex = tempDate.getMonth();
            let year = tempDate.getFullYear();

            return monthNames[monthIndex] + ' ' + day + ', ' + year;
        },

        openClip(clip){
            this.$emit('open-clip', clip.embed_url)
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
