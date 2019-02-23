<template>
    <board-post-reply :board-id="boardId" :parent-id="postId"></board-post-reply>
</template>

<script>

    import BoardPostReply from "../../../components/Board/BoardPostReply";
    export default {
        name: "DefaultReplyPage",
        components: {BoardPostReply},
        props :{
            boardId : {
                required : true
            },
            postId :{
                required: true
            },
            config:{type:Object},
            theme:{type:Function}
        }
    }
</script>