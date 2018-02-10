<template>
    {{ stats.total }}
    {{ stats.name }}
</template>

<script type="text/javascript">


export default {

    props: [
        'stats',
        'showStat',
    ],

    data: function(){
        return {

        }
    },

};

</script>