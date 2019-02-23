<template>
    <el-form v-once v-model="newComment" class="comment-form" v-loading="loading">
        <el-input type="textarea" v-model="newComment.wr_content"></el-input>
        <el-button size="mini" type="primary" class="submit-btn" @click="submit()">쓰기</el-button>
    </el-form>
</template>

<script>
    export default {
        name: "CommentForm",
        props : {
            boardId : String,
            postId : String,
            commentId : Number
        },
        data(){
            return {
                newItem: {
                    wr_content : '',
                    wr_is_comment : true,
                    wr_parent : ''
                },
                loading:false
            }
        },
        methods : {
            submit(){
                this.loading = true;
                axios.post("/bbs/board/"+this.boardId+"/post/"+this.postId + "/comment",this.newItem).then(
                    (res)=>{
                        this.newItem.wr_content = '';
                        this.loading = false;
                        this.$emit("comment-created", res.data)
                    },
                    (error)=>{
                        const res = error.response;
                        this.$message.warning(res.data.message);
                    }
                );
            }
        },
        mounted(){
            this.newItem.wr_parent = this.postId;
            this.newItem.comment_id = this.commentId;
        }
    }
</script>

<style lang="scss" scoped>
    $--border-color-base : #efefef;
    $--color-white : #ffffff;

    .comment-form{
        padding: 10px;
        background-color: mix($--border-color-base, $--color-white, 80%);
        border: 1px solid $--border-color-base;
        text-align: right;
        .submit-btn{
            margin-top : 10px;
        }
    }
</style>