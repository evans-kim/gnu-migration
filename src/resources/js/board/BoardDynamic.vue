<template>
    <component :is="theme" :config="config" v-if="theme">
        <component :is="page" :board-id="board" :post-id="post" :config="config" v-if="page" />
    </component>
</template>

<script>
    export default {
        name: "BoardDynamic",
        props: {
            board:{required : true},
            post:{type:null},
            mode:{type:null,required:true},
            template:{type:null,default:'default'}
        },
        data() {
            return {
                theme : null,
                page: null,
                config : {},
                mutateTemplate : 'default',
                mutateTheme : null
            }
        },
        watch :{
            board(val){
                this.getParent();
            },
            post(val,oldVal){
                this.getComponent();
            },
            mode(val,oldVal){
                this.getComponent();
            }
        },
        computed: {
            loadMasterTheme(){
                if (!this.board) {
                    return null;
                }
                return () => import(/* webpackChunkName: "themes" */ `./themes/${this.mutateTheme}/MasterTheme`)
            },
            pageLoader() {
                if (!this.board) {
                    return null;
                }

                return () =>import(/* webpackChunkName: "templates" */ `./templates/${this.mutateTemplate}/${this.mode}Page`)
            },
        },
        methods:{
            getPage(){
                this.page = null;
                return this.pageLoader()
                    .then( () => {
                        this.page = () => this.pageLoader()
                    })
                    .catch( () => {
                        this.page = () => import( /* webpackChunkName: "templates" */  `./templates/default/${this.mode}Page`)
                    })
            },
            getTheme(){
                return this.loadMasterTheme()
                    .then(() => {
                        this.theme = () => this.loadMasterTheme()
                    }).catch(()=>{
                        this.theme = () => import(/* webpackChunkName: "themes" */ `./themes/DefaultTheme`);
                    })
            },
            getComponent(){

                this.getTheme();

                this.getPage();

            },
            getParent(){
                axios.get("/api/v1/board/"+this.board).then(
                    ( res )=>{

                        this.config = res.data;

                        this.mutateTemplate = this.config.bo_template;
                        this.mutateTheme = this.config.bo_theme;
                        if(!this.mutateTheme){
                            this.mutateTheme = this.board;
                        }
                        this.getComponent();

                    }
                )
            }
        },
        mounted(){
            this.getParent();
        }
    }
</script>