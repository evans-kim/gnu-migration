<template>
    <input :class="{ editable:true }"
           :id="scope.column.property+'_input_'+scope.$index"
           :data-index="scope.column.property+'_input_'+scope.row[primaryId]"
           :value="currentValue"
           @input="handleInput"
           @change="updateAutoToggle(scope.row)"
           @keyup.down="moveDown(scope)"
           @keyup.up="moveUp(scope)"
    />
</template>

<script>
    export default {
        name: "GridInput",
        props : {
            value : { required: true},
            scope : {
                type : Object,
                required : true
            },
            error : Array,
        },
        data(){
            return {
                origin : null,
                page : 1,
                isEdited : false,
                errorMessage : '',
            }
        },
        computed:{
            lastColumn(){
                return this.scope._self.items.length;
            },
            lastPage(){
                return this.scope._self.lastPage;
            },
            currentPage(){
                return this.scope._self.currentPage;
            },
            currentValue(){
                return this.value;
            },
            getField(){
                return this.scope.column.property;
            },
            getIdValue(){
                return this.scope.row[this.primaryId];
            },
            primaryId(){
                if( !this.scope ){
                    return 'id';
                }
                return this.scope._self.primaryId
            }
        },
        methods : {
            /** 네비게이션 **/
            moveUp(scope){
                let index = scope.$index;
                index -= 1;
                if(index < 0){
                    if( 1 === this.currentPage ){
                        this.$message({
                            type:'warning',
                            message :  '처음 페이지 입니다.'
                        });
                        throw "처음 페이지 입니다.";
                        return;
                    }
                    let page = this.currentPage;
                    page -= 1;
                    this.$emit("reload-grid", page);
                    return;
                }
                this.focusOther(scope, index);
            },
            focusOther: function (scope, index) {
                document.querySelector("#" + scope.column.property + '_input_' + index).select();
            },
            moveDown(scope){
                let index = scope.$index;
                index += 1;
                if(index >= this.lastColumn){
                    if( this.lastPage === this.currentPage ){
                        this.$message({
                            type:'warning',
                            message :  '마지막 페이지 입니다.'
                        });
                        throw "마지막 페이지 입니다.";
                        return;
                    }
                    let page = this.currentPage;
                    page += 1;
                    this.$emit("reload-grid", page);
                    document.querySelector("#" + scope.column.property + '_input_' + '0').focus();
                    return;
                }
                this.focusOther(scope, index);

            },
            updateAutoToggle(row) {
                this.$emit("editing", row);
            },
            handleInput(e){
                this.$emit('input' , e.target.value);
            }
        }
    }
</script>

<style lang="scss" scoped>
    @import "../../../sass/element";
    .editable{
        border: none;
        width: 100%;
        padding: 4px 8px;
        border-radius: 4px;
        background-color: mix($--color-white, $color-secondary-2-1, 80%);
        transition: background-color ease 0.3s;
    }
    .editable:focus{
        outline: none !important;
        border-color: $color-secondary-2-1;
        background-color: mix($--color-white, $--color-warning, 80%);
    }
    .isEdited{

    }
</style>
