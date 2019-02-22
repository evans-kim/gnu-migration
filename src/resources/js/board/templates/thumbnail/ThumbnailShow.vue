<template>
    <section class="wrap">
        <evans-box :title="post.wr_subject">
            <div class="post-detail pure-text" v-if="isHTML">
                {{ post.wr_content.trim() }}
            </div>
            <div v-html="post.wr_content" class="post-detail" v-else></div>
            <div class="tags">
                <el-tag v-for="(tag, index) in tags" :key="index + 'tag'">{{tag}}</el-tag>
            </div>
        </evans-box>
        <board-control-buttons :board="board" :post="post"></board-control-buttons>
        <evans-box title="댓글" v-if="commendable">

            <ul class="comment-list">
                <transition-group name="el-fade-in" mode="out-in" appear >
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
                                    <comment-form :key="comment.wr_id" :post-id="postId" :board-id="boardId" :comment-id="comment.wr_id" @comment-created="refreshCommentList()" ></comment-form>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <el-button size="mini" type="text" @click="showReplyForm(comment.wr_id)">답변</el-button>
                                <el-button size="mini" type="text" @click="deleteComment(comment.wr_id)" v-show="isRemovable(comment)">삭제</el-button>
                            </div>
                        </div>
                    </li>
                </transition-group>
                <li v-show="isShowCommentForm(0)">
                    <comment-form :post-id="postId" :board-id="boardId" @comment-created="refreshCommentList()" ></comment-form>
                </li>
                <li v-show="!isShowCommentForm(0)">
                    <el-button size="mini" type="primary" @click="showReplyForm(0)">새댓글쓰기</el-button>
                </li>
            </ul>

        </evans-box>
    </section>
</template>


<script>

    import BoardPostShow from "../../../components/Board/BoardPostShow";
    export default {
        name: "ThumbnailShow",
        extends: BoardPostShow,
        computed : {
            tags(){
                if(!!this.post.wr_6)
                    return this.post.wr_6.split('|').filter((item)=>{
                        return !!item.trim();
                    })
            }
        }
    }
</script>

<style scoped>

</style>