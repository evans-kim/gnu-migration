<template>
    <section>
        <h1>기본 게시판</h1>
        <slot></slot>
    </section>
</template>

<script>

    export default {
        name: "DefaultTheme",
        props : {
            config : {type : Object, default(){ return {};}}
        }
    }
</script>

<style scoped>
    .header{
        margin-bottom: 10px;
    }
</style>