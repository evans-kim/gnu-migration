<template>
    <board-post-show :board-id="boardId" :post-id="postId"></board-post-show>
</template>

<script>
    import BoardPostShow from "../../../components/Board/BoardPostShow";
    export default {
        name: "DefaultShowPage",
        components: {BoardPostShow},
        props :{
            boardId : {
                type : String,
                required : true
            },
            postId : {
                type : String,
                required : true
            },
            config:{type:Object},
            theme:{type:Function}
        }
    }
</script>