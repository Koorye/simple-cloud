<template>
    <div id="container">
        <img v-if="type == '1image'" :src="previewUrl" />
        <video v-if="type == '2video'" :src="previewUrl" 
         controls style="width: 100%;height: 100%;" />
        <audio v-if="type == '3audio'" :src="previewUrl"
         controls currentTime />
        <VuePdf v-if="type == '4doc'" :src="previewUrl" />
        <VueMarkdown v-if="type == '5text'"> {{ previewUrl }}</VueMarkdown>
    </div>
</template>

<script>
import VueMarkdown from 'vue-markdown'
import VuePdf from "vue-pdf"

export default {
    props: ['previewUrl', 'type'],
    data() {
        return {
        }
    },
    components: {
        VuePdf, VueMarkdown
    }
}
</script>

<style scoped>
#container {
    background-color: white;
}

img {
    width: 100%;
    height: auto;
}
</style>