<template>
    <board-post-list :board-id="boardId" ></board-post-list>
</template>

<script>
    import BoardPostList from '../../../components/Board/BoardPostList';

    export default {
        name: "DefaultListPage",
        components : {BoardPostList},
        props : {
            boardId:{type:String,required:true},
            config:{type:Object},
            theme:{type:Function}
        }
    }
</script>