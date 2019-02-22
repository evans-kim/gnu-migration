<template>
    <div class="profile">
        <div class="avatar">
            <img :src="public_url(avatarUrl)">
        </div>
        <div class="user-desc">
            {{ member.mb_name }}
            <div class="sub-title">{{member.mb_email}}</div>
        </div>
        <div class="times">
            <el-button size="small" @click="handler">자세히 보기</el-button>
        </div>
    </div>
</template>

<script>
    export default {
        name: "UserProfile",
        props :{
            member_id : String,
            user : Object,
            handler : {
                type:Function,
                default : ()=>{
                    return;
                }
            }
        },
        computed:{
            member(){
                if(this.user)
                    return this.user
                else
                    return window.user;
            },
            avatarUrl(){
                return '/image/noimage.png';
            }
        },
        mounted(){

        }
    }
</script>

<style lang="scss" scoped>

    .profile{

        display: flex;
        justify-content: space-between;
        padding: 12px;

        .avatar{
            flex-grow: 1;
            max-width: 40px;
            img{
                width: 100%;
                border-radius: 50%;
            }
        }
        .user-desc{
            flex-grow: 1;
            padding-left: 10px;
            .sub-title{
                font-size: 15px;
                font-weight: 500;
            }
        }
        .times{
            flex-grow: 1;
            align-self: center;
            text-align: right;
            img{
                width: 25px;
            }
        }

    }
</style>