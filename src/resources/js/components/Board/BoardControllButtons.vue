<template>
    <div class="control-buttons">

        <el-button @click="goList()">목록</el-button>
        <el-button @click="goEdit()" v-show="editable">수정</el-button>
        <el-button @click="confirmDelete()" v-show="removable">삭제</el-button>
        <el-button @click="goReply()" v-show="reliable">답변</el-button>

    </div>
</template>
<script>
    import BoardAccessCheck from "../mixins/BoardAccessCheck"
    export default {
        name: 'BoardControlButtons',
        mixins : [BoardAccessCheck],
        props:{board:Object,post:Object}
    }
</script>
<style lang="scss" scoped>

    .control-buttons {
        margin-top: 10px;
        margin-bottom: 10px;
        text-align: right;
        @media screen and (max-width: 768px) {
            text-align: center;
        }
    }

</style>