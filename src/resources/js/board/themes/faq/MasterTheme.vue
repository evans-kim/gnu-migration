<template>
    <section>
        <h1>자주 묻는 질문</h1>
        <el-row :gutter="10">
            <el-col :lg="18" :xs="24" class="main-content">

                <slot></slot>

            </el-col>
        </el-row>
    </section>
</template>

<script>
    export default {
        name: "FaqMasterTheme",

    }
</script>