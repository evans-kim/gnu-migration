<template>
    <div class="box-wrapper" :class="{ 'mobile-border':isMobile }" :style="noTitleStyle">
        <h5 v-if="title && isMobile " :class="{divider:isDivided}">
            {{title}}
        </h5>
        <slot name="header"></slot>
        <div class="memo">
            <h5 v-if="title && !isMobile">
                <i class="fa fa-quote-left double-quote" v-show="quoted"></i>
                {{title}}
                <i class="fa fa-quote-right double-quote" v-show="quoted"></i>
            </h5>

            <div ref="container" :class="{content:true, isFixed:isExpendable, 'content-box':true}" :style="{height:mutateHeight}" v-show="isToggle" >
                <slot></slot>

                <div class="expendable-handler" v-show="isExpendable" @click="showContainer()" :style="{'line-height':mutateHeight + 'px'}">
                    보기
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'EvansBox',
        data(){
            return {
                mutateToggle : true,
                mutateHeight : ''
            }
        },
        props : {
            title : String,
            quoted : Boolean,
            expendableHeight : {
                type : String,
                default : 'auto'
            },
            divided : Boolean
        },
        methods : {
            showContainer(){
                this.mutateHeight = 'auto';
            }
        },
        computed : {
            noTitleStyle(){
                if(!this.title)
                    return {
                        'margin-top' : '0px',
                        'padding-top': '0px'
                    }
            },
            isDivided(){
                return this.divided;
            },
            isExpendable(){
                return this.mutateHeight.length > 0 && this.mutateHeight !== 'auto';
            },
            isToggle(){
                return this.mutateToggle;
            }
        },
        created(){
            this.mutateHeight = this.expendableHeight;
        }
    }
</script>
<style lang="scss" scoped>
    @import "../../../sass/element";

    .mobile-border{
        padding-top: 0px !important;
        margin-top: 0px !important;
        margin-bottom: 4px;
        .divider{
            padding-left: 10px;
            background-color: #eee;
            margin-bottom: 0px !important;
            padding-bottom: 6px;
            padding-top: 6px;
            font-size: 16px;
        }
        h5{
            font-weight: 600;
            font-size: 18px !important;
            margin-bottom: 0.2rem;
            @media screen and (max-width: $--sm) {
                padding: 10px 0px;
                border: none;
                margin-bottom: 10px;
            }
        }
        .memo {
            border-right: 0px !important;
            border-left: 0px !important;
            border-radius: 0px !important;

        }
    }

    .box-wrapper {
        position : relative;
        overflow : visible;
        margin-top: 40px;
        margin-bottom : 20px;
        background-color: #FFFFFF;
        @media screen and (max-width: $--sm) {
            padding: 0;
            padding-top: 10px;
            padding-bottom : 10px;
        }
        .memo{
            position: relative;
            border: 1px solid $--border-color-base;
            border-radius: 4px;

            min-height : 60px;
            h5{
                position: absolute;
                top: -30px;
            }
            .content{

                padding: 10px;
                @media screen and (max-width: $--sm) {
                    padding: 0px;
                }
                height: 0px;
                transition: height 1s;
            }
            .isFixed{
                overflow: hidden;
            }
            .expendable-handler{
                transition: all 0.5s ease-out;
                cursor: pointer;
                font-size: 18px;
                text-align: center;
                width: 100%;
                background: linear-gradient(to bottom, transparent, $--border-color-base);
                position: absolute;
                bottom: 0px;
                left: 0px;
                border-radius: 4px;
                height: 100%;

                color: transparent;
            }
            .expendable-handler:hover{
                color: $--color-primary;
            }
        }
    }
</style>