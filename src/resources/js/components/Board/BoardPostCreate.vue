<template>
    <section v-loading="loading">
        <el-row class="twins-column" :gutter="10">
            <el-col class="left">

                <evans-box title="글작성">
                    <el-form label-width="150px" :label-position="getLabelPosition">
                        <el-form-item required label="제목">
                            <el-input type="text" v-model="post.wr_subject"></el-input>
                        </el-form-item>
                        <el-form-item label="비밀글">
                            <el-switch v-model="post.is_secret"></el-switch>
                        </el-form-item>
                        <el-form-item required label="내용">
                            <el-input type="textarea" v-model="post.wr_content" :autosize="{ minRows: 10, maxRows: 20}"></el-input>
                        </el-form-item>
                        <el-form-item>
                            <el-button @click="submitPost()">작성하기</el-button>
                            <el-button type="warning" @click="goList()">취소</el-button>
                        </el-form-item>
                    </el-form>
                </evans-box>
            </el-col>
        </el-row>

        <BoardControlButtons :board="board" />

    </section>
</template>

<script>
    import EvansBox from "../Grids/EvansBox";

    import BoardAccessCheck from "../mixins/BoardAccessCheck";
    import BoardControlButtons from "./BoardControllButtons";

    export default {
        name: "BoardPostCreate",
        components: {BoardControlButtons, EvansBox},
        mixins : [BoardAccessCheck],
        props:{
            boardId : {
                type : String,
                required : true
            },
            board : {
                type : Object
            },
        },
        data(){
            return{
                post : {},
                loading : false,
                labelPosition : 'left',
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
                        this.$router.push({name:'boardPostShowPage', params:{board:this.boardId, post:res.data.post.wr_id}});
                    }
                )
            },
            getBoard()
            {
                axios.get("/api/")
            }
        },
        mounted(){

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