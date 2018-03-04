<template>
<div>
    <button class="btn btn-sm poe-btn show-tooltip po-pob-link"
            @click.prevent="getPoBCode()"
            data-toggle="tooltip" data-placement="top"
            title="Generate PoB import code">
        <i class="fa fa-share-square-o" aria-hidden="true"></i> PoB Code
    </button>
    <div  style="display:none;">
        <div class="po-body col-lg-12" ref="popover" style="width:380px;">
            <h4>PoB import Code:
                <a href="#" class="pull-right" onclick="$('.po-pob-link').trigger('click')">
                    <i class="fa fa-times-circle" aria-hidden="true"></i>
                </a>
            </h4>
            <loader v-if="loading" :loading="loading" style="margin-left:auto;margin-right:auto;"></loader>
            <div class="input-group" v-else>
                <input class="form-control" id="pobCode" placeholder="Generating code ..."
                aria-label="" aria-describedby="" :value="pobXml">
                <span class="input-group-btn">
                    <button class="btn btn-outline-secondary btn-outline-warning clipboard" type="button"
                        data-clipboard-target="#pobCode" onclick="">
                        <i class="fa fa-clipboard" aria-hidden="true"></i> Copy
                    </button>
                </span>
            </div>
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
            type: String,
            required: true,
            default: '',
        },

    },

    components: {
        // 'item': Item,
    },

    data: function() {
        return {
            pobXml: '',
            loading: false,
        }
    },

    computed: {
        favAccButtonText: function (){

        },
    },

    watch: {},
    mounted: function () {
        $('.po-pob-link').popover({
           trigger: 'click',
           html: true,
           title: $('.po-title').html(),
           content: this.$refs['popover'],
           container: 'body',
           placement: 'left'
       });
    },
    methods: {
        getPoBCode: function() {
            this.loading=true;
            var formData = new FormData();
            formData.append('account', this.account);
            formData.append('char', this.character);
            axios.post('/api/getPoBCode', formData).then((response) => {
                this.pobXml = response.data;
                this.loading=false;
                new Clipboard('.clipboard');
            });
        },
    }

};

</script>
