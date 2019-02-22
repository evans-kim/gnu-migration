<template>
    <el-input-number size="mini" :class="{
        editable:true
    }" :id="scope.column.property+'_input_'+scope.$index" :value="currentValue" @input="handleInput" @change="updateAutoToggle(scope.row)" @keyup.down="moveDown(scope)" @keyup.up="moveUp(scope)" :min="1"></el-input-number>
</template>

<script>
    import GridInput from "./GridInput";
    export default {
        name: "GridNumberInput",
        extends: GridInput,
        methods:{
            handleInput(e){
                this.$emit('input' , e);
            }
        }
    }
</script>

<style scoped>
    .el-input-number--mini{
        width: 100%;
    }
</style>