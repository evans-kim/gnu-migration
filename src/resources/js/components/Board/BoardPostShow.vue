<template>
    <section class="wrap">
        <evans-box :title="post.wr_subject">
            <div class="post-detail pure-text" v-if="isHTML">
                {{ post.wr_content.trim() }}
            </div>
            <div v-html="post.wr_content" class="post-detail" v-else></div>
            <div class="file-box" v-show="!!post.files">
                <el-row type="flex">
                    <el-col :span="8" :xs="24" v-for="(attachment, index) in post.files" :key="index">
                        <el-card :body-style="{padding:'0px'}">
                            <img :src="attachment.file_url" class="file-image" :ref="'imgBox' + attachment.wr_id" v-appear-box>
                            <div style="padding: 14px;">
                                <span>{{ attachment.bf_source }}</span>
                                <div class="bottom clearfix">
                                    <time class="time">{{ attachment.bf_datetime }}</time>
                                    <el-button type="text" class="pull-right"> 삭제</el-button>
                                </div>
                            </div>
                        </el-card>
                    </el-col>
                </el-row>
            </div>
        </evans-box>
        <board-control-buttons :board="board" :post="post"></board-control-buttons>
        <evans-box title="댓글" v-if="commendable">

            <ul class="comment-list">
                <transition-group name="el-fade-in" mode="out-in" appear>
                    <li v-for=" comment in post.comments " :key="comment.wr_id">
                        <div :class="'depth-'+getCommentDepth(comment)">
                            <div>
                                <span class="writer">{{comment.wr_name}}</span>
                                <span class="timer">
                                        {{ comment.wr_datetime }}
                                    </span>
                            </div>
                            <div class="comment-content">
                                <div v-html="comment.wr_content"></div>
                                <div class="reply-form" v-show="isShowCommentForm(comment.wr_id)">
                                    <comment-form :key="comment.wr_id" :post-id="postId" :board-id="boardId"
                                                  :comment-id="comment.wr_id"
                                                  @comment-created="refreshCommentList()"></comment-form>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <el-button size="mini" type="text" @click="showReplyForm(comment.wr_id)">답변</el-button>
                                <el-button size="mini" type="text" @click="deleteComment(comment.wr_id)"
                                           v-show="isRemovable(comment)">삭제
                                </el-button>
                            </div>
                        </div>
                    </li>
                </transition-group>
                <li v-show="isShowCommentForm(0)">
                    <comment-form :post-id="postId" :board-id="boardId"
                                  @comment-created="refreshCommentList()"></comment-form>
                </li>
                <li v-show="!isShowCommentForm(0)">
                    <el-button size="mini" type="primary" @click="showReplyForm(0)">새댓글쓰기</el-button>
                </li>
            </ul>

        </evans-box>

    </section>
</template>

<script>
    import EvansBox from "../Grids/EvansBox";
    import CommentForm from "./CommentForm";

    import BoardAccessCheck from "../mixins/BoardAccessCheck";
    import BoardControlButtons from "./BoardControllButtons";

    export default {
        name: "BoardPostShow",
        mixins: [BoardAccessCheck],
        components: {BoardControlButtons, CommentForm, EvansBox},
        props: {
            boardId: {
                type: String,
                required: true
            },
            postId: {
                type: String,
                required: true
            }
        },
        data() {
            return {
                showPopupContent : false,
                selectedImgFile : {},
                selectedPosition : {
                    left:'0px',
                    top:'0px',
                    zIndex:0,
                    width:'0%',
                    height:'0%',
                },
                ImgSrc : null,
                post: {
                    comments: []
                },
                board: {},
                newItem: {
                    wr_content: ''
                },
                formController: [0]
            }
        },
        computed: {
            isLoaded() {
                return !!this.post.hasOwnProperty('wr_content');
            },
            isHTML() {
                if (this.isLoaded) {

                    var regex = /html/ig;
                    return this.post.wr_content.match(regex);
                }
                return '';
            },
            apiUrl() {
                return "/api/g4/board/" + this.boardId + "/post/" + this.postId + "/comment";
            },
        },
        methods: {
            closePopUp(){

                var rect = this.$refs['imgBox' + this.selectedImgFile.wr_id][0].getBoundingClientRect();

                setTimeout(()=>{
                    this.selectedPosition['left'] = rect.x + rect.width / 2 + 'px';
                    this.selectedPosition['top'] = rect.y + + rect.height / 2 + 'px';
                    this.selectedPosition['zIndex'] = 0;
                    this.selectedPosition['width'] = '0%';
                    this.selectedPosition['height'] = '0%';
                    this.selectedPosition['opacity'] = 0;
                    this.showImagePopup = false;
                    setTimeout(()=>{
                        this.ImgSrc = null;
                        this.showImagePopup = false;
                    },400);
                },100);
            },
            showImage(img) {
                this.selectedImgFile = img;
                var rect = this.$refs['imgBox' + img.wr_id][0].getBoundingClientRect();
                this.selectedPosition['left'] = rect.x + rect.width / 2 + 'px';
                this.selectedPosition['top'] = rect.y + + rect.height / 2 + 'px';
                setTimeout(()=>{
                    this.selectedPosition['transition'] = 'all 0.5s cubic-bezier(0.57, 0.01, 0.37, 0.99)';
                    setTimeout(()=>{
                        this.selectedPosition['left'] = '0px';
                        this.selectedPosition['top'] = '0px';
                        this.selectedPosition['position'] = 'fixed';
                        this.selectedPosition['zIndex'] = 2000;
                        this.selectedPosition['width'] = '100%';
                        this.selectedPosition['height'] = '100%';
                        this.selectedPosition['opacity'] = 0.5;
                        setTimeout(()=>{
                            this.ImgSrc = img.file_url;
                            this.showImagePopup = true;
                        },400);
                    },10);
                },10);
            },
            isRemovable(comment) {
                if (comment.mb_id === window.user.mb_id) {
                    return true;
                }
                if (window.user.is_admin) {
                    return true;
                }
                return false;
            },
            refreshCommentList() {
                axios.get(this.apiUrl).then(
                    (res) => {
                        this.formController = [0];
                        this.post.comments = res.data;
                    },
                    (error) => {
                        const res = error.response;
                        this.$message.warning(res.data.message);
                    }
                )
            },
            isShowCommentForm(wr_id) {
                return this.formController.includes(wr_id) && this.commendable;
            },
            deleteComment(wr_id) {
                axios.delete(this.apiUrl + "/" + wr_id).then(
                    (res) => {
                        this.$message.success("삭제되었습니다.");
                        this.refreshCommentList();
                    },
                    (error) => {
                        const res = error.response;
                        this.$message.warning(res.data.message);
                    }
                )
            },
            showReplyForm(wr_id) {
                this.formController = [0];
                this.formController.push(wr_id);
            },
            getCommentDepth(comment) {

                return comment.wr_comment_reply.length;
            },
            getPost() {
                return axios.get("/api/g4/board/" + this.boardId + "/post/" + this.postId).then(
                    (res) => {
                        this.board = res.data.board;
                        this.post = res.data.post;
                        this.list = res.data.list;
                    }
                ).then(() => {

                    let imgs = document.querySelectorAll(".post-detail img[src*='l1'], .post-detail img[src*='jpg']");
                    _.map(imgs, (img) => {
                        img.style['width'] = '100%';
                        img.style['height'] = 'auto';
                        return img;
                    });
                    imgs = document.querySelectorAll(".post-detail table");
                    _.map(imgs, (img) => {
                        img.style['width'] = '100%';
                        img.style['height'] = 'auto';
                        return img;
                    })
                })
            }
        },
        mounted() {
            this.getPost();

        }
    }
</script>

<style lang="scss" scoped>
    @import "../../../sass/element";
    .file-box-unmask{
        z-index: 0;
        opacity: 0;
        background : #000000;
        width: 0%;
        height: 0%;
        overflow: hidden;
        position: fixed;
        transition: all 0s cubic-bezier(0.57, 0.01, 0.37, 0.99);
    }
    .file-box-mask{
        z-index: 2000;
        background : #000000;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        opacity: 0.5;
        transition: $--all-transition;
    }
    .file-box-image{
        transition: $--all-transition;
        z-index: 2020;
        position: fixed;
        top: 10vh;
        //left: 50%;
        overflow: auto;
        .img-wrapper{
            position: relative;
            width: 100vw;
            text-align: center;
            overflow: scroll;
        }
    }
    .control-buttons {
        margin-top: 10px;
        margin-bottom: 10px;
    }

    .title {
        border-bottom: 1px solid $--border-color-base;
        padding: 10px 0px;
        font-size: 18px;
    }

    .post-detail {
        min-height: 300px;
        white-space: pre-wrap;
    }

    .pure-text {
        white-space: pre-wrap;
    }

    .comment-form {

    }

    .file-box {
        .file-image {
            cursor: pointer;
            width: 100%;
        }
    }

    .comment-list {
        list-style: none;
        padding-left: 10px;
        li {
            border-bottom: 1px solid $--border-color-base;
            padding-bottom: 10px;
            padding-top: 10px;
            .writer {
                font-weight: 600;
                padding-bottom: 10px;
            }
            .timer {
                float: right;
                color: $--color-primary;
            }
            .btn-set {
                text-align: right;
            }
        }
        li:last-child {
            border-bottom: none;
        }
        .depth-1 {
            padding-left: 5%;
        }
        .depth-2 {
            padding-left: 10%;
        }
        .depth-3 {
            padding-left: 15%;
        }
        .depth-4 {
            padding-left: 20%;
        }
    }
</style>