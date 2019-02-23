<template>
    <board-post-edit :post-id="postId" :board-id="boardId"></board-post-edit>
</template>

<script>
    import BoardPostEdit from "../../../components/Board/BoardPostEdit";
    export default {
        name: "DefaultShowPage",
        components: {BoardPostEdit},
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