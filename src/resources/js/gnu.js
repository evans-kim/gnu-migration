window.Vue = require('vue');

import Vue from 'vue'

import ElementUI from 'element-ui';
import '../sass/element_variables.scss'
import locale from 'element-ui/lib/locale/lang/ko'
Vue.use(ElementUI, { locale });

import VueRouter from 'vue-router'
Vue.use( VueRouter );

import routes from './pages'
import NotFoundComponent from './pages/errors/404';
routes.push({ path: '*', name:'NotFoundPage', component: NotFoundComponent });

const scrollBehavior = function (to, from, savedPosition) {
    return { x: 0, y: 0 }
}
const router = new VueRouter({
    mode: 'history',
    routes: routes,
    scrollBehavior
});

router.beforeEach((to, from, next) => {

    if (to.matched.some(record => record.meta.memberOnly)) {
        // 이 라우트는 인증이 필요하며 로그인 한 경우 확인하십시오.
        // 그렇지 않은 경우 로그인 페이지로 리디렉션하십시오.

        if ( _.isEmpty(window.user) || window.user.mb_id === '@Guest' ) {

            setTimeout(()=>{
                window.vm.$message.warning("로그인이 필요한 서비스 입니다. 로그인 또는 회원가입해 주시기 바랍니다.");
            }, 1000);
            next({
                name : 'loginPage',
                query: { redirect: to.fullPath }
            });
        } else {

            next();
        }
    }
    if (to.matched.some(record => record.meta.guestOnly)) {
        // 손님만 접근 가능합니다.

        if (window.user && window.user.length > 0) {
            setTimeout(()=>{
                window.vm.$message.warning("로그인 하지 않은 손님만 접근할 수 있습니다.");
            }, 1000);

            next(false);

        }else{

            next();
        }
    }
    next(); // 반드시 next()를 호출하십시오!

});


import Master from './board/GnuBoardMaster';
Vue.mixin({
    computed : {
        isMobile(){
            return false;
        }
    }
});


const components = {
    Master
};

window.vm = new Vue({
    el : '#app',
    router,
    components,
    render: h => h(Master)
});

