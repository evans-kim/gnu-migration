<template>
    <thumbnail-show :board-id="boardId" :post-id="postId"></thumbnail-show>
</template>

<script>
    import BoardPostShow from "../../../components/Board/BoardPostShow";
    import ThumbnailShow from "./ThumbnailShow";
    export default {
        name: "ThumbnailShowPage",
        components: {ThumbnailShow, BoardPostShow},
        props :{
            boardId : {
                type : String,
                required : true
            },
            postId : {
                type : String,
                required : true
            },
            config:Object,
            theme:{type:Function}
        }
    }
</script>