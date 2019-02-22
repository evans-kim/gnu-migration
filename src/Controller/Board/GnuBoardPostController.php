<?php

namespace EvansKim\GnuMigration\Controller\Board;

use App\Http\Controllers\Controller;
use EvansKim\GnuMigration\GnuModel\GnuBoard;
use EvansKim\GnuMigration\GnuModel\GnuPost;
use EvansKim\GnuMigration\Member;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class GnuBoardPostController extends Controller
{

    public function index(Request $request)
    {
        if(!$request->ajax())
            return view("gnu::board-index", ['user'=>auth()->user()]);


        if($request->has('bo_table')){
            $this->validate($request, ['bo_table'=>'string']);
            $board = $request->bo_table;
        }
        $config = GnuBoard::selectLevelConfig()->findOrFail($board);
        /**
         * @var $config GnuBoard
         */
        $config->checkAuthenticated("list");

        $this->validate( $request, [
            'perPage'=>'nullable|numeric'
        ]);

        if(!$perPage = $request->perPage){
            $perPage = 20;
        }
        if($perPage >= 100){
            $perPage = 100;
        }

        $posts = $config->posts();
        /**
         * @var $posts GnuPost
         */
        $result = $posts->search($request)->postOnly()->with(['files'])->paginate($perPage)->toArray();


        $result['notices'] = $config->getNotices();

        $result['config'] = $config;

        return $result;


    }

    public function store(Request $request, $board)
    {
        $config = GnuBoard::selectLevelConfig()->findOrFail($board);
        /**
         * @var $config GnuBoard
         */
        $config->checkAuthenticated("write");

        $this->validate( $request, [
            'wr_subject'=>'nullable|string',
            'wr_content'=>'required|string',
            'is_secret'=>'nullable|boolean'
        ]);

        $model = $config->posts($request->only(['wr_subject','wr_content','is_secret']));

        $model->mb_id = auth('gnu')->id();

        $model->build($request)->save();

        return [ 'message'=>'성공적으로 등록 되었습니다.', 'post'=>$model ];
    }

    /**
     * @param Request $request
     * @param $board
     * @param $post_id
     * @return array
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function show(Request $request, $board, $post_id)
    {
        $config = GnuBoard::find($board);

        $config->checkAuthenticated('read');


        $model = $config->posts()->with(['comments'])->find($post_id);

        //비밀글인지 확인하고 접근 유저의 권한을 확인합니다. 권한이 없다면 예외처리 합니다.
        if( $model->isSecretContent() ){
            $user = auth()->user();
            /**
             * @var $user Member
             */
            if( ! $user->isOwnerOf($model) ){
                abort(403, "이 게시글은 관리자 및 작성자만 읽을 수 있는 비밀글 입니다.");
            }
        }
        //비밀글이 아니면 권환확인 없이 접근할 수 있습니다.

        $latestPosts = $config->posts()->where('mb_id', $model->mb_id)->postOnly()->orderByDesc('wr_id')->limit(5)->get();

        return ['board'=>$config, 'post'=> $model, 'list'=> $latestPosts];
    }

    public function update(Request $request, $board, $post)
    {
        $config = GnuBoard::find($board);
        $config->checkAuthenticated('write');

        $this->validate($request, [
            'wr_content'=>'required|string',
            'wr_subject'=>'required|string'
        ]);

        $model = $config->posts()->find($post);

        $user = auth()->user();
        /**
         * @var $user Member
         */
        if( ! $user->isOwnerOf($model) ){
            abort(403, "이 글은 작성자만 수정할 수 있는 글 입니다.");
        }

        $model->wr_subject = $request->wr_subject;
        $model->wr_content = $request->wr_content;

        $model->save();

        return ['message'=>'성공적으로 변경되었습니다.', 'post'=>$model];
    }

    /**
     * @param Request $request
     * @param $board
     * @param $post
     * @return array
     * @throws AuthenticationException
     * @throws \Exception
     */
    public function destroy(Request $request, $board, $post)
    {
        $config = GnuBoard::find($board);
        $post = $config->posts()->find($post);

        $this->checkAuthentication();

        $user = auth()->user();
        /**
         * @var $user Member
         */
        if( !$user->isOwnerOf($post) ){
            abort(403, '이 글은 작성자만 삭제할 수 있습니다.');
        }

        if( $post->children()->count() > 0 ){
            abort(405, '이 글은 답변이 있어서 삭제할 수 없습니다.');
        }

        if(!$post->delete()){
            abort(405, '일시적인 오류로 삭제하지 못했습니다. 다시 한번 시도해 주세요.');
        }
        return['message'=>'성공적으로 변경되었습니다.', 'post'=>$post];
    }

    /**
     * @throws AuthenticationException
     */
    protected function checkAuthentication()
    {
        if ( !auth()->check() ) {
            throw new AuthenticationException("먼저 로그인이 필요한 기능입니다.");
        }
    }

}
