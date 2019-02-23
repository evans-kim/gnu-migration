<template>
    <div class="table-responsive" ref="gridWrapper">
        <el-form :inline="true" :model="filter" class="demo-form-inline">
            <el-form-item label="역할명">
                <el-input v-model="filter.mb_id" placeholder="이름"></el-input>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="onSearch">검색</el-button>
            </el-form-item>
        </el-form>
        <el-select v-model="perPage">
            <el-option :value="10"></el-option>
            <el-option :value="30"></el-option>
            <el-option :value="50"></el-option>
            <el-option :value="100"></el-option>
        </el-select>
        <div class="p-0" style="overflow: hidden">
            <el-table :data="items" :default-sort="sorting" ref="gridTable" style="width: 100%; height: 100%" @selection-change="(val)=>{selected = val}" @sort-change="(val)=>{ sorting = val }">
                <el-table-column type="selection" width="45"></el-table-column>
                <el-table-column prop="id" width="100" sortable label="번호">
                    <template slot-scope="scope">
                        <el-button type="primary" size="mini" @click=" $router.push(apiUrl + '/' + scope.row[primaryId])">{{ scope.row[primaryId] }}</el-button>
                    </template>
                </el-table-column>
                <el-table-column prop="name" width="120" sortable label="게시판">
                    <template slot-scope="scope">
                        <grid-input v-model="scope.row.name" :scope="scope" @reload-grid="changeCurrentPage" @editing="setSelectRow"></grid-input>
                    </template>
                </el-table-column>
                <el-table-column prop="slug" width="140" label="아이디">
                    <template slot-scope="scope">
                        <grid-input v-model="scope.row.slug" :scope="scope" @reload-grid="changeCurrentPage" @editing="setSelectRow"></grid-input>
                    </template>
                </el-table-column>
                <el-table-column prop="description" label="설명">
                    <template slot-scope="scope">
                        <grid-input v-model="scope.row.description" :scope="scope" @reload-grid="changeCurrentPage" @editing="setSelectRow"></grid-input>
                    </template>
                </el-table-column>
                <el-table-column prop="uses" label="사용" width="80">
                    <template slot-scope="scope">
                        <el-switch v-model="scope.row.uses" @change="setSelectRow(scope.row)"></el-switch>
                    </template>
                </el-table-column>
            </el-table>

            <el-button-group class="pt-4">
                <el-button type="danger" size="mini" @click="deleteSelected">
                    <i class="fa fa-trash"></i>
                </el-button>
                <el-button type="info" size="mini" @click="updateSelected">
                    <i class="fa fa-edit"></i>
                </el-button>
            </el-button-group>

            <el-pagination
                class="text-center"
                background
                layout="prev, pager, next"
                :total="total"
                :current-page="currentPage"
                @current-change="changeCurrentPage"
            >
            </el-pagination>

            <div style="position: relative">
                <div class="floating-button">
                    <el-button type="primary" circle @click="createDialog = true">
                        <el-icon name="circle-plus-outline"></el-icon>
                    </el-button>
                </div>
            </div>
        </div>

        <el-dialog
            title="처리중입니다."
            :show-close="false"
            :visible.sync="process.show"
            width="30%">
            <div class="text-center">

                <el-progress type="circle" :percentage="process.percent"></el-progress>

            </div>
        </el-dialog>
        <el-dialog
            title="생성"
            :visible.sync="createDialog"
            width="40%"
            center>
            <el-form ref="newItemForm" :model="newItem">
                <el-form-item label="게시판명" label-width="100" prop="name" :error="error.name">
                    <el-input v-model="newItem.name"></el-input>
                </el-form-item>
                <el-form-item label="게시판 아이디" label-width="100" prop="name" :error="error.slug">
                    <el-input v-model="newItem.slug" placeholder="영어만 입력하세요"></el-input>
                </el-form-item>
            </el-form>
            <span slot="footer" class="dialog-footer">
                <el-button @click="createDialog = false">닫기</el-button>
                <el-button type="primary" @click="createItem">생성하기</el-button>
              </span>
        </el-dialog>
    </div>
</template>

<script>

    import GridInput from './GridInput';
    import BooleanSelect from "./BooleanSelect";
    export default {
        name: "boards",
        components :{
            BooleanSelect,
            GridInput
        },
        props :{
            syncData : { type : Array , default(){ return [] }},
            lazy : {type:Boolean, default : false}
        },
        data() {
            return {
                apiUrl : '/bbs/board',
                process : {
                    show : false,
                    total : 1,
                    done : 0,
                    percent : 0
                },
                primaryId : 'id',
                filter: {},
                filtering : true,
                sorting: {prop: this.primaryId, order: 'descending'},
                loading: true,
                selected: [],
                total: 0,
                lastPage : 1,
                currentPage: 1,
                perPage : 10,
                items: [],
                newItem : {},
                createDialog : false,
                error:{},
                errorRows:{},
                message:{
                    unit: "데이터",
                    action : {
                        updated: "변경",
                        created: "생성",
                        deleted: "삭제"
                    },
                    created:"생성하시겠습니까?",
                    updated: "변경하시겠습니까?",
                    deleted:"삭제하시겠습니까? 게시글에 포함된 모든 첨부 파일 및 댓글도 함께 삭제 됩니다.",
                    question : '하시겠습니까?',
                    response : '했습니다.'
                },

            }
        },
        watch : {
            syncData(items){
                if(items.length > 0){
                    this.items = items;
                    this.loading = false;
                }
            },
            errorRows : {
                handler(val){
                    console.log(val);
                },
                deep: true
            },
            currentPage(val, old){
                if(old && val !== old) {
                    this.loading = true;
                    this.selected = [];
                    this.getItems();
                    setTimeout(()=>{
                        this.goToTopScroll();
                    },500);
                }
            },
            sorting(val, old) {
                if(old && val !== old){

                    this.currentPage = 1;
                    this.loading = true;
                    this.getItems();
                }
            }
        },
        computed : {
            getURL(){
                return this.apiUrl + "?" + this.getQueries();
            }
        },
        methods : {
            changeCurrentPage(val){
                this.currentPage = val;
            },
            getInput(id, field, msg){
                console.log(field + '_input_' + id);
                document.querySelector('[data-index='+field + '_input_' + id+']').select();
                this.errorRows[field] = {};
                this.errorRows[field][id] = msg;
            },
            goToTopScroll(){
                console.log([this.$refs['gridWrapper'].clientHeight, 'scroll']);
                if(!this.$refs['gridWrapper'])
                    return;

                window.scrollTo({
                    // 지금 스크롤값에서 그리드의 최상단으로 스크롤을 이동시킵니다.
                    top: window.scrollY - this.$refs['gridWrapper'].clientHeight  - (window.innerHeight*0.1),
                    left: 0,
                    behavior: 'smooth'
                });

            },
            setSelectRow(row){
                this.$refs.gridTable.toggleRowSelection(row, true);
            },
            onSearch: function () {
                this.loading = true;
                this.currentPage = 1;
                this.getItems();
            },
            getQueries: function () {
                let params = {};
                if (this.perPage > 1) {
                    params['perPage'] = this.perPage.toString();
                }
                if (this.currentPage > 1) {
                    params['page'] = this.currentPage.toString();
                }
                if (this.sorting && this.sorting.prop) {
                    params['sort'] = this.sorting.prop;
                }else{
                    params['sort'] = this.primaryId;
                }
                if (this.sorting && this.sorting.order) {
                    params['order'] = this.sorting.order;
                }
                params = this.getFilterParams(params);

                return Object.keys(params).map(function (key) {
                    return key + '=' + params[key];
                }).join('&');

            },
            getFilterParams(params){
                if(this.filtering)
                    for (var key in this.filter) {
                        params[key] = this.filter[key];
                    }
                return params;
            },
            getItems() {

                return axios.get(this.getURL).then((res) => {

                    this.items = res.data.data;
                    this.total = res.data.total;
                    this.lastPage = res.data.last_page;
                    this.loading = false;
                    return res.data;

                }, (error) => {
                    let res = error.response;

                    this.$message({
                        type: 'error',
                        showClose: true,
                        message: res.data.message
                    });
                    this.loading = false;
                });
            },
            koreanSuffix(txt, josa){
                // 원본 문구가 없을때는 빈 문자열 반환

                if ( typeof txt !== 'string' || txt.length === 0) return '';

                var jong = (txt.charCodeAt(txt.length - 1) - 0xac00) % 28 > 0;

                if (josa === '을' || josa === '를') josa = (jong?'을':'를');
                if (josa === '이' || josa === '가') josa = (jong?'이':'가');
                if (josa === '은' || josa === '는') josa = (jong?'은':'는');
                if (josa === '와' || josa === '과') josa = (jong?'와':'과');

                return txt + josa;
            },
            checkIsSelected: function () {
                if (this.selected.length < 1) {
                    this.$message({
                        type: 'warning',
                        message: "선택된 " + this.koreanSuffix( this.message.unit, '이') + " 없습니다."
                    });
                    throw new Error('noSelectError');
                }
            },
            confirmPromise: function (func, message=null) {
                const defaultMessage = this.selected.length + ' 개의 선택된 ' + this.koreanSuffix( this.message.unit, '을');
                if(message){
                    message = defaultMessage + " " + message;
                }else{
                    message = defaultMessage + " 선택하셨습니다.";
                }
                this.$confirm( message, '확인', {
                    confirmButtonText: '확인',
                    cancelButtonText: '취소',
                    type: 'warning'
                }).then(() => {

                    this.setProgress(func, this.selected);

                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: '취소되었습니다.'
                    });
                });
            },
            updateSelected(){

                this.checkIsSelected();

                return this.confirmPromise(this.updateItemPromise, this.message.updated );
            },
            deleteSelected(){

                this.checkIsSelected();

                this.confirmPromise(this.deleteItemPromise, this.message.deleted );
            },
            createItem(){
                this.createDialog = false;
                this.setProgress( this.createItemPromise, [this.newItem] );
            },
            createItemPromise(item){
                return new Promise(resolve => {
                    axios.post(this.apiUrl, item).then((res)=>{
                        this.newItem = {};
                        resolve(res);
                        this.$message.success( this.message.action.created + this.message.response );
                    }, (error)=>{

                        const result = error.response;
                        if( result.status === 422 ){

                            let errors = result.data;

                            for ( let f in errors ){
                                let msg = errors[f];
                                this.error[f] = msg[0];
                            }
                            this.createDialog = true;
                            this.setTerminateProgress();
                            throw new Error('유효성 체크 에러');

                        }
                        resolve(result);
                    });
                });
            },
            updateItemPromise(item){
                return new Promise(resolve => {
                    const url = this.apiUrl + "/" +item[this.primaryId];
                    return axios.put(url, item ).then((res)=>{

                        resolve(res);
                        this.$message.success( this.message.action.updated + this.message.response );
                    }, (error) => {

                        resolve(error.response);

                    });
                });
            },
            deleteItemPromise(item){
                return new Promise(resolve => {
                    const url = this.apiUrl + "/" + item[this.primaryId];
                    return axios.delete(url).then((res)=>{

                        resolve(res);
                        this.$message.success( this.message.action.deleted + this.message.response );
                    }, (error) => {

                        resolve(error.response);

                    });
                });

            },

            /**
             * 비동기 함수와 배열을 이용해 배열값 만큼 함수를 수행하고
             * 로딩창을 보여 줍니다.
             * @param promise Promise
             * @param args Array
             * @param unreload Boolean
             *
             * @returns {Promise<void>}
             */
            async progressing( promise, args, unreload ){

                this.error = {};
                for ( var i in args ){
                    var result = await promise( args[i] );
                    if( result.status === 422 ){

                        for ( var field in result.data){
                            this.getInput(args[i][this.primaryId], field );
                            this.$message.warning( result.data[field][0] );
                            this.setTerminateProgress();
                            return false;

                        }
                    }
                    if( !(result.status === 200 || result.status === 201) && result.data.message ){
                        this.process.show = false;
                        this.$message({
                            type : 'error',
                            message : result.data.message
                        });
                    }
                    this.process.done = this.process.done + 1;
                    this.process.percent = parseInt(this.process.done / this.process.total * 100);
                }
                if(!unreload)
                    this.getItems();
                this.setTerminateProgress();
            },
            setTerminateProgress(){
                this.process.show = false;
                setTimeout(()=>{
                    this.process.percent = 0;
                    this.$emit("terminated-process", true);
                }, 1500);
            },
            /**
             * 로딩창을 띄웁니다.
             *
             * @param promise
             * @param args
             * @param unreload
             */
            setProgress( promise, args, unreload ){
                this.process.total = args.length;
                this.process.done = 0;
                this.process.show = true;
                this.progressing( promise , args, unreload);
            }
        }
    }
</script>

<style>
    .el-table th, .el-table td {
        padding: 6px 0 !important;
    }
    .grid-main-container{
        position: relative;
    }
    .floating-button{
        position: absolute;
        right: 30px;
        bottom: 15px;
        z-index: 1;
    }
    .floating-button button{
        box-shadow: 0 8px 12px 0 rgba(0,0,0,.1);
        width: 50px;
        height: 50px;
        font-size: 22px;
        outline: none;
    }
</style>
