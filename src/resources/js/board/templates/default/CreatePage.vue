<template>
    <board-post-create :board-id="boardId" :board="config"></board-post-create>
</template>

<script>
    import BoardPostCreate from "../../../components/Board/BoardPostCreate";
    export default {
        name: "DefaultCreatePage",
        components: {BoardPostCreate},
        props :{
            boardId : {
                type : String,
                required : true
            },
            config : Object,
            theme:{type:Function}
        }
    }
</script>