<template>
    <!-- Modal -->
<transition name="modal-fade">
<div class="modal-backdrop2">
    <div class="modal2 bd-example-modal-lg2" style="top: 70px;" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog2 modal-lg2" role="document">
        <div class="modal-body">

            <div class="modal-header">
                <span>SNAPSHOTS : {{originalChar}}</span>
                <div class="pull-right">
                    <button type="button" class="btn btn-sm poe-btn active" @click="close">Close</button>
                </div>
            </div>
            <div class="modal-body">
                <table width="100%">
                    <tr>
                        <th>Hash</th>
                        <th>Original Character</th>
                        <th>Original Level</th>
                        <th>Created at</th>
                    </tr>
                    <tr v-for="snapshot in snapshots"
                        v-bind:class="[currentHash==snapshot.hash ? 'current-snap' : '']">

                        <td ><a href="#" @click.prevent="gotoSnapshot(snapshot.hash)">{{snapshot.hash}}</a></td>
                        <td>{{snapshot.original_char}}</td>
                        <td>{{snapshot.original_level}}</td>
                        <td>{{snapshot.created_at}}</td>
                    </tr>
                </table>
                <loader :loading="loading" style="margin-left:auto;margin-right:auto;width:150px;"></loader>

            </div>

        </div>
      </div>
    </div>
</div>
</transition>

</template>

<script type="text/javascript">
import {poeHelpers} from '../../helpers/poeHelpers.js';

export default {
    props: {
        originalChar: {
            type: String,
            default: null,
        },
        build: {
            type: Object,
            default: null,
        },
        loadData: {
            type: Boolean,
            default: false,
        },
    },

    data: function(){
        return {
            snapshots: {},
            loading: false,
            currentHash: '',
        }
    },
    computed: {

    },
    watch : {
        loadData: function (newVal, oldVal) {
                if(newVal){
                    this.loadSnapshots();
                }
           }
    },

    mounted: function() {
        if(this.build){
            this.currentHash=this.build.hash;
        }
    },

    methods: {
        close:function(event) {
            this.$emit('close');
        },

        loadSnapshots: function() {
            this.loading=true;
            axios.get('/api/snapshots/' + this.originalChar).then((response) => {
                this.snapshots = response.data;
                this.loading=false;
            });
        },

        gotoSnapshot: function(hash){
            location.replace((new poeHelpers).getBaseDomain() + '/build/' + hash);
        }
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
  box-shadow: 2px 2px 20px 1px;
  overflow-x: auto;
  display: flex;
  flex-direction: column;
  width: 950px;
  z-index: 99999;
}

.modal-header,
.modal-footer {
  padding: 5px;
}

.current-snap{
      background-color: #373a3c;
}

.modal-body {
  position: relative;
  padding: 10px 5px;
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
</style>
