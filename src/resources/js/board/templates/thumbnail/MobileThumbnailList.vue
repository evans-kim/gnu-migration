<template>
    <div class="table-responsive" ref="gridWrapper">

        <div v-if="isMobile">
            <div class="webzine-list">
                <div class="item" v-for="(item, index) in items">
                    <div class="item-inner">
                        <img v-if="item.thumbnail" :src="item.thumbnail" style="width: 100%; height:auto;" />

                        <h4 class="title">{{ item.wr_subject }}</h4>
                        <p class="content-text">
                            {{ item.thumb_text }}
                        </p>
                        <div class="profile-wrap">
                            <user-profile :user="parseUser(item)" :handler="(e)=>{ $router.push(postRoute+'/'+item.wr_id) }"></user-profile>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <el-card v-loading="loading" style="overflow:unset;" body-style="padding:0px" v-show="!isMobile">
            <el-table :data="items" :default-sort="sorting" ref="gridTable" style="width: 100%; height: 100%" @selection-change="(val)=>{selected = val}" @sort-change="(val)=>{ sorting = val }">
            </el-table>
        </el-card>
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
                :pager-count="5"
                :total="total"
                :current-page="currentPage"
                @current-change="(val)=>{currentPage = val}"
        >
        </el-pagination>
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
    import UserProfile from "../../../components/Board/UserProfile";

    export default {
        name: "MobileThumbnailList",
        components: {UserProfile},
        extends : BoardPostList,
        methods :{
            parseUser(item){
                return {mb_name:item.wr_name,mb_email:item.wr_datetime};
            }
        }
    }
</script>

<style lang="scss" scoped>

    .thumb-text {
        height: 50px;
        overflow: hidden;
    }

    .webzine-list {
        font-size: 14px;
        padding-left: 10px;
        padding-right: 10px;
        .item {
            //width : 307px;
            width: 24%;
            @media (max-width: 768px) {
                width: 100%;
            }
            display: inline-flex;
            background-color: #f2f3f6;
            margin-bottom: 1.3333%;
            @media (max-width: 768px) {
                margin-bottom: 10px;
            }
            .title {
                font-weight: 600;
                margin-top: 20px;
                font-size: 18px;

            }
            .content-text {
                font-size: 15px;
                text-justify: inter-character;
                margin-bottom: 20px;
            }
            .profile-wrap{
                border-top: 1px solid #efefef;
            }

        }
        .item-sizer {
            width: 24%;
            @media (max-width: 768px) {
                width: 100%;
            }
        }
        .gutter-sizer {
            width: 1.3333%;
            @media (max-width: 768px) {
                width: 0px;
            }
        }
        .item-inner {
            padding: 10px;
        }
        .thumb {
            float: left;
        }
        .grid-item-sub {
            position: relative;
            height: 60px;
            font-size: 15px;
            clear: both;
        }
        .thumb-text {
            padding-left: 10px;
            float: left;
            width: 82%;
            font-size: 15px;
        }
    }
</style>