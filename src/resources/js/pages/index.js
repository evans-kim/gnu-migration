
const routes = [
    /**
     * 게시판 라우트
     */
    { path : '/bbs/board.php', name : 'BoardPostListPage', component : require('../board/BoardDynamic').default, props: (route)=>{
        return {board:route.query.bo_table, mode:'List'}
    }},
    { path : '/bbs/board.php/:board/post/create', name : 'boardPostCreatePage', component : require('../board/BoardDynamic').default, props: (route)=>{
        return {board:route.params.board, mode:'Create'}
    }},
    { path : '/bbs/board.php/:board/post/:post/reply', name : 'boardPostReplyPage', component : require('../board/BoardDynamic').default, props: (route)=>{
        return {board:route.params.board, post:route.params.post,  mode:'Reply'}
    }},
    { path : '/bbs/board.php/:board/post/:post/edit', name : 'boardPostEditPage', component : require('../board/BoardDynamic').default, props: (route)=>{
        return {board:route.params.board, post:route.params.post,  mode:'Edit'}
    }},
    { path : '/bbs/board.php/:board/post/:post', name : 'boardPostShowPage', component : require('../board/BoardDynamic').default, props: (route)=>{
        return {board:route.params.board,post:route.params.post,   mode:'Show'}
    }},
];

export default routes;