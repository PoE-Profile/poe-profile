<template>

<span class="item-box -gem">
    <span v-if="this.gemInfo.hasOwnProperty('vaal')" class="header -single">
        {{withEllipsis(vaalGem.baseTypeName,35)}}
        <small class="wiki-link" v-if="showWiki">
            <a :href="wikiLink" target="_blank" style="color: white;">[ wiki ]</a>
        </small>
    </span>
    <span v-else class="header -single">
        {{withEllipsis(gemInfo.typeLine,35)}}
        <small class="wiki-link" v-if="showWiki">
            <a :href="wikiLink" target="_blank" style="color: white;">[ wiki ]</a>
        </small>
    </span>

    <span v-if="this.gemInfo.hasOwnProperty('vaal')">
        <span class="item-stats">
            <span class="group">
                {{vaalGem.properties[0].name}}<br>
                <div v-for="(prop, index) in vaalGem.properties">
                    <span v-if="index > 1">
                        <em class="tc -default">{{ prop.name }}  </em><em class="tc -value">{{prop.values[0][0]}}</em>
                    </span>
                </div>
            </span>

            <span class="group" v-if="vaalGem.requirements">
                <em class="tc -default"> Requires </em>
                <div v-for="(req, index) in vaalGem.requirements" style="display: inline-block;">
                    <em class="tc -default">{{ req.name }} </em>
                    <em class="tc -value">{{ index+1 < vaalGem.requirements.length ? req.values[0][0]+',&nbsp;' : ''}}</em>
                </div>
            </span>

            <span class="group gem-textwrap tc -gemdesc ellipsiss"
                v-if="vaalGem.secDescrText">
                <span v-if="showFullDesc">{{vaalGem.secDescrText}}
                    <a href="#" @click.prevent="showFullDesc=!showFullDesc">[less]</a>
                </span>
                <span v-else>
                    {{withEllipsis(vaalGem.secDescrText,35)}}
                    <a href="#" @click.prevent="showFullDesc=!showFullDesc">[more]</a>
                </span>

            </span>
            <span class="group gem-textwrap tc -mod">
                <p v-for="mod in vaalGem.explicitMods" style="margin: 0;padding: 0;">
                    {{mod}}
                </p>
            </span>
            <span class="group gem-textwrap" style="color: rgb(26, 162, 155);">
                {{withEllipsis(gemInfo.typeLine,35)}}
            </span>
            <span class="group gem-textwrap" ></span>
        </span>
    </span>

    
    <span class="item-stats">
        <span class="group">
            {{gemInfo.properties[0].name}}<br>
            <div v-for="(prop, index) in gemInfo.properties">
                <span v-if="index > 0">
                    <em class="tc -default">{{ prop.name }}  </em><em class="tc -value">{{prop.values[0][0]}}</em>
                </span>
            </div>
        </span>

        <span class="group" v-if="gemInfo.requirements">
            <em class="tc -default"> Requires </em>
            <div v-for="(req, index) in gemInfo.requirements" style="display: inline-block;">
                <em class="tc -default">{{ req.name }} </em>
                <em class="tc -value">{{ index+1 < gemInfo.requirements.length ? req.values[0][0]+',&nbsp;' : ''}}</em>
            </div>
        </span>

        <span class="group gem-textwrap tc -gemdesc ellipsiss"
            v-if="gemInfo.secDescrText">
            <span v-if="showFullDesc">{{gemInfo.secDescrText}}
                <a href="#" @click.prevent="showFullDesc=!showFullDesc">[less]</a>
            </span>
            <span v-else>
                {{withEllipsis(gemInfo.secDescrText,35)}}
                <a href="#" @click.prevent="showFullDesc=!showFullDesc">[more]</a>
            </span>

        </span>
        <span class="group gem-textwrap tc -mod">
            <p v-for="mod in gemInfo.explicitMods" style="margin: 0;padding: 0;">
                {{mod}}
            </p>
        </span>
        <span class="group -textwrap tc -mod" v-if="gemInfo.corrupted">
            <span class="corrupted"> CORRUPTED </span>
        </span>

    </span>
</span>

</template>

<script type="text/javascript">

export default {

    props: {
        gemInfo: Object,
        showWiki:{
            type: Boolean,
            default: true
        },
    },

    data: function() {
        return {
            vaalGem: '',
            showFullDesc: false,
        }
    },

    created: function () {
        if(this.gemInfo.hasOwnProperty('vaal')){
           this.vaalGem = this.gemInfo.vaal;
        }
    },

    computed: {
        'wikiLink': function() {
            return 'http://pathofexile.gamepedia.com/' + this.gemInfo.typeLine.replace(' ', '_');
        }
    },

    methods: {
        withEllipsis: function(text,after){
            if(text.length<=after){
                return text;
            }
            return text.substring(0, after)+".."
        },
    }

};

</script>
