<template>
    <section>
        <evans-box title="글수정">
            <el-form label-width="150px" :label-position="getLabelPosition">
                <el-form-item required label="제목">
                    <el-input type="text" v-model="post.wr_subject"></el-input>
                </el-form-item>
                <el-form-item required label="내용">
                    <el-input type="textarea" v-model="post.wr_content" :autosize="{ minRows: 2, maxRows: 20}"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button @click="confirmEdit()">수정하기</el-button>
                </el-form-item>
            </el-form>
        </evans-box>
        <board-control-buttons :post="post" :board="board" ></board-control-buttons>
    </section>
</template>

<script>
    import EvansBox from "../Grids/EvansBox";
    import CommentForm from "./CommentForm";
    import BoardAccessCheck from "../mixins/BoardAccessCheck";
    import BoardControlButtons from "./BoardControllButtons";
    import FormLabelPosition from "../mixins/FormLabelPosition";
    export default {
        name: "BoardPostEdit",
        components: {BoardControlButtons, CommentForm, EvansBox},
        mixins : [BoardAccessCheck,FormLabelPosition],
        props:{
            boardId : {
                type : String,
                required : true
            },
            postId : {
                type : String,
                required : true
            }
        },
        data(){
            return{
                post : {
                    comments : []
                },
                board : {},
                newItem : {
                    wr_content: ''
                },
                formController : [0]
            }
        },
        computed : {
            apiUrl(){
                return "/bbs/board/"+this.boardId+"/post/"+this.postId;
            },
        },
        methods : {
            isRemovable(comment){
                if(comment.mb_id === window.user.mb_id){
                    return true;
                }
                if(window.user.is_admin){
                    return true;
                }
                return false;
            },
            confirmEdit(){
                axios.put(this.apiUrl, this.post).then(
                    (res)=>{
                        this.$message.success(res.data.message);
                        this.$router.push({name:'boardPostShowPage'});
                    }
                )
            },
            postCanBeEditedByUser(){
                return this.board.bo_write_level <= window.user.mb_level;
            },
            getPost(){
                axios.get(this.apiUrl).then(
                    (res)=>{
                        this.board = res.data.board;

                        if ( !this.postCanBeEditedByUser() ){
                            this.$message.warning("이 게시판은 글을 수정하거나 작성할 수 없습니다.");
                            this.$router.back();
                        }

                        this.post = res.data.post;
                    }
                )
            }
        },
        mounted(){
            this.getPost();
        }
    }
</script>

<style lang="scss" scoped>
    @import "../../../sass/element";
    .title{
        border-bottom: 1px solid $--border-color-base;
        padding: 10px 0px;
        font-size: 18px;
    }
    .post-detail{
        white-space: pre-wrap;
    }
    .comment-form{

    }

    .comment-list{
        list-style: none;
        padding-left: 10px;
        li{
            border-bottom: 1px solid $--border-color-base;
            padding-bottom: 10px;
            padding-top : 10px;
            .writer{
                font-weight: 600;
                padding-bottom : 10px;
            }
            .timer{
                float: right;
                color: $--color-primary;
            }
            .btn-set{
                text-align: right;
            }
        }
        li:last-child{
            border-bottom: none;
        }
        .depth-1{
            padding-left: 5%;
        }
        .depth-2{
            padding-left: 10%;
        }
        .depth-3{
            padding-left: 15%;
        }
        .depth-4{
            padding-left: 20%;
        }
    }
</style>