<script>
  export default {
    name: 'InputTag',

    props: {
      tags: {
        type: Array,
        default: () => [],
      },
      placeholder: {
        type: String,
        default: '',
      },
      onChange: {
        type: Function,
      },
      readOnly: {
        type: Boolean,
        default: false,
      },
      suggestions: {
        type: Array,
        required: true
    },
    },

    data() {
      return {
        newTag: '',
        open: false,
        current: 0,
        oldText:'',
      };
    },
    computed: {

        //Filtering the suggestion based on the input
        matches() {
            // this.suggestions.unshift(this.newTag);
            if(this.oldText!==''){
                var result=this.suggestions.filter((str) => {
                    return str.toLowerCase().indexOf(this.oldText.toLowerCase()) >= 0;
                }).slice(0,15);
                result.unshift(this.oldText);
                return result;
            }
            // console.log('matches');
            var result=this.suggestions.filter((str) => {
                return str.toLowerCase().indexOf(this.newTag.toLowerCase()) >= 0;
            }).slice(0,15);
            result.unshift(this.newTag);
            return result;
        },

        //The flag
        openSuggestion() {
            return this.newTag !== "" &&
                   this.matches.length != 0 &&
                   this.open === true;
        }
    },

    methods: {
      focusNewTag() {
        if (this.readOnly) { return; }
        this.$el.nextElementSibling.querySelector('.new-tag').focus();
      },
      addNew() {
          this.enter();
        //   console.log("this.newTag:"+this.newTag);
        if (this.newTag && !this.tags.includes(this.newTag)) {
          this.tags.push(this.newTag);
          this.tagChange();
        }
        this.newTag = '';
      },
      remove(index) {
        this.tags.splice(index, 1);
        this.tagChange();
      },
      removeLastTag() {
        if (this.newTag) { return; }
        this.tagChange();
        this.tags.pop();
      },
      getPlaceholder() {
        if (!this.tags.length) {
          return this.placeholder;
        }
        return '';
      },
      tagChange() {
        if (this.onChange) {
          // avoid passing the observer
          this.onChange(JSON.parse(JSON.stringify(this.tags)));
        }
      },


      //When enter pressed on the input
    enter() {
        if(this.matches.length==0){
            return;
        }
        // console.log(this.current+" <curent< enterMethod"+this.matches.length);
        if(this.matches[this.current].length>0){
            this.newTag = this.matches[this.current];
            this.open = false;
        }
    },

    //When up pressed while suggestions are open
    up() {
        if(this.current > 0)
            this.current--;
        this.setInput()
    },

    //When up pressed while suggestions are open
    down() {
        if(this.current < this.suggestions.length - 1)
            this.current++;
        this.setInput()
    },
    // on change with arrows up down cal setInput to change val in input
    // set this.oldText to have old value and stil show old suggestions
    setInput(){
        // console.log('test setInput');
        if(this.oldText==''){
            this.oldText=this.newTag;
        }
        if(this.matches[this.current].length>0){
            this.newTag = this.matches[this.current];
            // console.log("update input field :"+this.newTag);
        }
    },

    //For highlighting element
    isActive(index) {
        return index === this.current;
    },

    //When the user changes input
    //on change in input remove this.oldText to force new sugestions
    change() {
        if (this.open == false) {
            this.open = true;
        }
        this.current = 0;
        // console.log('change curent to '+this.current);
        this.oldText='';
    },

    //When one of the suggestion is clicked
    suggestionClick(index) {
        this.newTag = this.matches[index];
        this.open = false;
        this.addNew()
    },



    },
  };
</script>

<template>

  <div @click="focusNewTag()" v-bind:class="{'read-only': readOnly}" class="vue-input-tag-wrapper">
    <span v-for="(index,tag ) in tags" class="input-tag">
      <span>{{ tag }}</span>
      <a v-if="!readOnly" @click.prevent.stop="remove(index)" class="remove"></a>
    </span>
    <input type="text" class="new-tag"
    v-if="!readOnly"
    v-bind:placeholder="getPlaceholder()"
    v-model="newTag"
    v-on:keydown.delete.stop="removeLastTag()"
    v-on:keydown.enter.prevent.stop="addNew()"
    @keydown.down = 'down'
    @keydown.up = 'up'
    @input = 'change'/>

  </div>


  <div style="position:relative" v-bind:class="{'open':openSuggestion}">
      <ul class="dropdown-menu" style="width:100%">
          <li class="dropdown-item suggestion-item" v-for="suggestion in matches"
              v-bind:class="{'active': isActive($index)}"
              @click="suggestionClick($index)"
               track-by="$index"
          >
              <a href="#" >{{ suggestion }}</a>
          </li>
      </ul>
  </div>
</template>

<style>

  .vue-input-tag-wrapper {
    background-color: #fff;
    border: 1px solid #ccc;
    overflow: hidden;
    padding-left: 4px;
    padding-top: 4px;
    cursor: text;
    text-align: left;
    -webkit-appearance: textfield;
  }

  .vue-input-tag-wrapper .input-tag {
    background-color: #cde69c;
    border-radius: 2px;
    border: 1px solid #a5d24a;
    color: #638421;
    display: inline-block;
    font-size: 13px;
    font-weight: 400;
    margin-bottom: 4px;
    margin-right: 4px;
    padding: 3px;
  }

  .vue-input-tag-wrapper .input-tag .remove {
    cursor: pointer;
    font-weight: bold;
    color: #638421;
  }

  .vue-input-tag-wrapper .input-tag .remove:hover {
    text-decoration: none;
  }

  .vue-input-tag-wrapper .input-tag .remove::before {
    content: " x";
  }

  .vue-input-tag-wrapper .new-tag {
    background: transparent;
    border: 0;
    color: #777;
    font-size: 1em;
    font-weight: 400;
    margin-bottom: 0px;
    margin-top: 1px;
    outline: none;
    padding: 4px;
    padding-left: 0;
    width: 400px;
  }

  .vue-input-tag-wrapper.read-only {
    cursor: default;
  }

  .active a{
      color:white;
  }
  .dropdown-menu .suggestion-item:hover{
      background:#0275d8!important;
  }
  .dropdown-menu .suggestion-item a:hover{
      background:#0275d8!important;
      color: white;
  }
</style>
