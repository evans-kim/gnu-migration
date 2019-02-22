<template>
    <el-select :value="currentValue" @input="(val)=>{$emit('input', val)}">
        <el-option
            v-for="(option, idx) in options"
            :key="idx"
            :label="option.label"
            :value="option.value">
        </el-option>
    </el-select>
</template>

<script>
    export default {
        name: "BooleanSelect",
        data(){
            return {
                options : [{label:'전체',value:null},{label:'사용중',value:1},{label:'미사용',value:0}]
            }
        },
        props : [
            'value'
        ],
        computed : {
            currentValue(){
                return this.value;
            }
        }
    }
</script>

<style scoped>

</style>
