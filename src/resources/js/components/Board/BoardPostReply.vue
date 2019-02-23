<template>
    <section v-loading="loading">
        <evans-box title="답변 작성">
            <el-form label-width="150px" :label-position="getLabelPosition">
                <el-form-item required label="내용">
                    <el-input type="textarea" v-model="post.wr_content" :autosize="{ minRows: 10, maxRows: 20}"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button @click="submitPost()">작성하기</el-button>
                    <el-button type="warning" @click="goList()">취소</el-button>
                </el-form-item>
            </el-form>
        </evans-box>
        <BoardControlButtons :board="board"/>
    </section>
</template>

<script>
    import EvansBox from "../Grids/EvansBox";

    import BoardAccessCheck from "../mixins/BoardAccessCheck";
    import BoardControlButtons from "./BoardControllButtons";
    import FormLabelPosition from "../mixins/FormLabelPosition";

    export default {
        name: "BoardPostReply",
        components: {BoardControlButtons, EvansBox},
        mixins : [BoardAccessCheck, FormLabelPosition],
        props:{
            boardId : {
                type : String,
                required : true
            },
            parentId : {
                type : null,
                required: true
            }
        },
        data(){
            return{
                post : {
                    parent_id : '',
                    wr_content : ''
                },
                board : {},
                parent : {},
                loading : true,
            }
        },
        computed : {
            apiUrl(){
                return "/bbs/board/"+this.boardId+"/post";
            },
        },
        methods : {
            submitPost(){
                this.loading = true;
                axios.post(this.apiUrl, this.post).then(
                    (res)=>{
                        this.$message.success(res.data.message);
                        this.loading = false;
                        this.$router.push({name:'boardPostShowPage', params:{board:this.boardId, post: res.data.post.wr_id}});
                    }
                )
            },
            getParent(){
                this.loading = true;
                axios.get("/bbs/board/"+this.boardId+"/post/"+this.parentId).then(
                    (res)=>{
                        this.board = res.data.board;
                        this.parent = res.data.post;
                        this.post.wr_content = '\n\n[ 이전 메시지 ] \n' + this.parent.wr_content;
                        this.post.parent_id = this.parent.wr_id;
                        this.loading = false;
                    }
                )
            }
        },
        mounted(){
            this.getParent();
        }
    }
</script>

<style lang="scss" scoped>
    @import "../../../sass/element";

    .title{
        border-bottom: 1px solid #efefef;
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