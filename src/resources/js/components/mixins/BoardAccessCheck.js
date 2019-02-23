//그누보드 뷰 컨트롤을 위한 컴퓨트
export default {
    computed :{
        getUser(){
            if(window.user)
                return window.user;
            else
                return {mb_id : null, mb_level : 1, is_admin : false}
        },
        editable(){
            if( _.isEmpty(this.board) || _.isEmpty(this.post)){
                return null
            }
            return (this.board.bo_write_level <= this.getUser.mb_level && this.post.mb_id === this.getUser.mb_id) || !!this.getUser.is_admin;
        },
        removable(){
            if(!this.post || _.isEmpty(this.post)){
                return null
            }
            return this.post.mb_id === this.getUser.mb_id || !!this.getUser.is_admin;
        },
        reliable(){
            if( _.isEmpty(this.board) || _.isEmpty(this.post) ){
                return null
            }
            return this.board.bo_reply_level <= this.getUser.mb_level || !!this.getUser.is_admin;
        },
        writable(){
            if(!this.board || _.isEmpty(this.board) ){
                return null
            }
            return this.board.bo_write_level <= this.getUser.mb_level || !!this.getUser.is_admin;
        },
        commendable(){
            if( _.isEmpty(this.post) || _.isEmpty(this.board) ){
                return null
            }
            return this.board.bo_comment_level <= this.getUser.mb_level && !!this.board.bo_comment_use;
        }
    },
    methods:{
        goCreate(){
            return this.$router.push({name:'boardPostCreatePage', params:{ board:this.board.bo_table }});
        },
        goList(){
            return this.$router.push({name:'BoardPostListPage', query:{ bo_table:this.board.bo_table }});
        },
        goEdit(){
            return this.$router.push({name:'boardPostEditPage', params:{ board:this.board.bo_table, post:this.post.wr_id }});
        },
        confirmDelete(){
            this.$confirm( '정말로 삭제하시겠습니까? 삭제된 데이터는 복구할 수 없습니다.', 'Warning', {
                confirmButtonText: '예, 삭제합니다.',
                cancelButtonText: '아니요, 취소합니다.',
                type: 'warning'
            }).then(() => {
                axios.delete( "/api/g4/board/"+this.board.bo_table+"/post/"+this.post.wr_id ).then(
                    (res)=>{
                        this.$message({
                            type: 'success',
                            message: this.__('성공적으로 삭제되었습니다.','Complete')
                        });
                        this.goList();
                    }
                );

            }).catch(() => {
                this.$message({
                    type: 'info',
                    message: this.__('취소되었습니다.','Canceled')
                });
            });
        },
        goReply(){
            return this.$router.push({name:'boardPostReplyPage',params:{ board:this.board.bo_table, post:this.post.wr_id }});
        },
    }
}