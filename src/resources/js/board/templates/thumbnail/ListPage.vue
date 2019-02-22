<template>
    <ThumbnailList :board-id="boardId" v-if="!isMobile"/>
    <MobileThumbnailList :board-id="boardId" v-else/>
</template>

<script>

    import ThumbnailList from "./ThumbnailList";
    import MobileThumbnailList from "./MobileThumbnailList";

    export default {
        name: "ThumbnailListPage",
        components : {MobileThumbnailList, ThumbnailList},
        props : {
            boardId:{type:String,required:true},
            config:{type:Object},
            theme:{type:Function}
        }
    }
</script>