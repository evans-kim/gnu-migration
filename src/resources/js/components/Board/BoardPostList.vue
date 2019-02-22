<template>
    <div class="table-responsive"  ref="gridWrapper">
        <el-card v-loading="loading" style="overflow:unset;" body-style="padding:0px">
            <div class="p-0" style="overflow: hidden">
                <el-table :data="items" :default-sort="sorting" ref="gridTable" style="width: 100%; height: 100%" @selection-change="(val)=>{selected = val}" @sort-change="(val)=>{ sorting = val }">
                    <el-table-column prop="wr_id" :width="widths.wr_id" label="번호" v-if="!isMobile">
                    </el-table-column>
                    <el-table-column prop="wr_subject" label="제목">
                        <template slot-scope="scope">
                            <div class="wr_subject">

                                <span v-if="!!scope.row.wr_reply">[답변]</span>
                                <router-link :to="postRoute+'/'+scope.row.wr_id">{{scope.row.wr_subject}}</router-link>
                                <span v-if="!!['secret'].includes(scope.row.wr_option)"><i class="fa fa-lock"></i></span>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="wr_name" :width="widths.wr_name" label="작성자" >
                    </el-table-column>
                    <el-table-column prop="wr_datetime" :width="widths.wr_datetime" label="일시" v-if="!isMobile">
                        <template slot-scope="scope">
                            {{ scope.row.wr_datetime | shortDate }}
                        </template>
                    </el-table-column>
                </el-table>
                <div class="pt-4" style="text-align: right; padding-right:20px; ">
                    <el-button type="info" v-show="writable" @click="goCreate()">
                        <i class="fa fa-edit"></i>
                    </el-button>
                </div>
                <el-pagination
                        style="padding-bottom: 10px;"
                        class="text-center"
                        background
                        :pager-count="5"
                        layout="prev, pager, next"
                        :total="total"
                        :current-page="currentPage"
                        @current-change="(val)=>{currentPage = val}"
                >
                </el-pagination>
            </div>

        </el-card>
        <el-dialog
                title="처리중입니다."
                :show-close="false"
                :visible.sync="process.show"
                width="30%">
            <div class="text-center">

                <el-progress type="circle" :percentage="process.percent"></el-progress>

            </div>
        </el-dialog>
    </div>
</template>

<script>
    import Base from '../Grids/BaseGrid';
    import BoardAccessCheck from "../mixins/BoardAccessCheck";

    export default {
        name: "BoardPostList",
        extends : Base,
        mixins :[BoardAccessCheck],
        props:{
            boardId : {
                type:String,
                required : true
            }
        },
        computed:{
            widths(){
                let width = {wr_id:'90px',wr_name:'150px',wr_datetime:'150px'};
                if(this.isMobile){
                    width.wr_name = '100px';
                }
                return width;
            },
            postRoute(){
                return '/api/shop/board/'+this.boardId+'/post';
            }
        },
        data() {
            return {
                apiUrl : '/bbs/board.php',
                process : {
                    show : false,
                    total : 1,
                    done : 0,
                    percent : 0
                },
                primaryId : 'wr_id',
                filter: {
                    bo_table : null
                },
                filtering : true,
                sorting: {prop: 'wr_num', order: 'ascending'},
                perPage : 10,
                board : {}

            }
        },
        filters: {
            shortDate: (value)=>{
                return value.substring(0,16);
            }
        },
        methods :{
            getItems() {
                this.loading = true;
                let url = this.apiUrl + "?";

                let query = this.getQueries();

                url = url + query;

                axios.get(url).then((res) => {
                    this.board = res.data.config;
                    this.items = res.data.data;
                    this.total = res.data.total;
                    this.lastPage = res.data.last_page;
                    this.loading = false;
                }, (error) => {
                    let res = error.response;
                    console.log(res);
                    this.$message({
                        type:'error',
                        message : res.data.message
                    });
                    this.loading = false;
                });
            },
        },
        mounted(){
            if(this.boardId){
                this.filter['bo_table'] = this.boardId;
            }
            this.getItems();
        }
    }
</script>

<style scoped>
    .wr_subject{
        text-align: left;
    }
</style>