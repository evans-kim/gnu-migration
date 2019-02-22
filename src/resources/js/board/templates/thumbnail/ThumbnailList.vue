<template>
    <div class="table-responsive">
        <el-card v-loading="loading" style="overflow:unset;" body-style="padding:0px">
            <el-table :data="items" :default-sort="sorting" ref="gridTable" style="width: 100%; height: 100%" @selection-change="(val)=>{selected = val}" @sort-change="(val)=>{ sorting = val }">
                <el-table-column prop="wr_id" width="100" label="번호"></el-table-column>
                <el-table-column prop="thumbnail" width="150" >
                    <template slot-scope="scope">
                        <img :src="scope.row.thumbnail" style="width: 100%">
                    </template>
                </el-table-column>
                <el-table-column prop="wr_subject" label="제목">
                    <template slot-scope="scope">
                        <h5>
                            <span v-if="!!scope.row.wr_reply">[답변]</span>
                            <router-link :to="postRoute+'/'+scope.row.wr_id">{{scope.row.wr_subject}}</router-link>
                            <span v-if="!!['secret'].includes(scope.row.wr_option)"><i class="fa fa-lock"></i></span>
                        </h5>
                        <p v-html="scope.row.thumb_text" class="thumb-text"></p>
                    </template>
                </el-table-column>
                <el-table-column prop="wr_name" width="140" label="작성자" ></el-table-column>
                <el-table-column prop="wr_datetime" label="일시" width="150">
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
                    layout="prev, pager, next"
                    :total="total"
                    :current-page="currentPage"
                    @current-change="(val)=>{currentPage = val}"
            >
            </el-pagination>
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
    import BoardPostList from '../../../components/Board/BoardPostList';
    export default {
        name: "ThumbnailList",
        extends : BoardPostList,

    }
</script>

<style scoped>
    .thumb-text{
        height: 50px;
        overflow: hidden;
    }
</style>