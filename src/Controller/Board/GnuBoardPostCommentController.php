<?php

namespace App\Http\Controllers;

use EvansKim\GnuMaigraion\GnuModel\GnuBoard;
use EvansKim\GnuMaigraion\GnuModel\GnuPost;
use EvansKim\GnuMigration\Member;
use Illuminate\Http\Request;


class GnuBoardPostCommentController extends Controller
{

    public function index(Request $request, $board_id, $post_id)
    {
        $config = GnuBoard::find($board_id);

        $config->checkAuthenticated("comment");

        $post = $config->posts()->find($post_id);

        return $post->comments()->get();
    }

    public function store(Request $request, $board_id, $post_id)
    {
        $config = GnuBoard::find($board_id);

        $config->checkAuthenticated("comment");

        $post = $config->posts()->find($post_id);
        /**
         * @var $post GnuPost
         */
        if(!$post){
            abort(404, "댓글이 작성될 해당 글을 찾을 수 없습니다.");
        }

        $this->validate( $request, [
            'wr_content' => 'required|string|',
            'wr_secret' => 'nullable|string',
            'comment_id' => 'nullable|numeric'
        ]);

        $writer = auth("gnu")->user();
        if(!$writer){
            abort(402, '글을 작성하기 위해서는 로그인이 필요합니다.');
        }


        if( $request->has("comment_id") && !empty($request->comment_id)){
            //  코멘트에 대한 답변을 다는 경우

            $comment = $post->comments()->find($request->comment_id);
            //최상위 코멘트 인가요?

            $children = $this->getChildrenComments($comment, $post);
            $reply_char = "A";
            $reply_count = $children->count();

            $reply_char = chr(ord( $reply_char ) + $reply_count);

            if(!$comment){
                abort(404, "답변이 작성될 해당 글을 찾을 수 없습니다.");
            }
            if(strlen($comment->wr_comment_reply) > 3 ){
                abort(402, "3단 이상의 답변을 달 수 없습니다.");
            }
            $wr_comment = $comment->wr_comment;
            $wr_comment_reply = $comment->wr_comment_reply . $reply_char;
        }else{
            // 답변없는 새 코멘트

            $wr_comment_reply = '';
            $wr_comment = $post->comments()->max('wr_comment') + 1;
        }

        $model = $config->getPostClass();

        $comment = new $model;

        $comment->ca_name = $post->ca_name;

        $comment->wr_num = $post->wr_num;
        $comment->wr_subject = '';

        $comment->wr_reply = '';
        $comment->wr_parent = $post_id;
        $comment->wr_is_comment = 1;
        $comment->wr_comment = $wr_comment;
        $comment->wr_comment_reply = $wr_comment_reply;

        $comment->wr_option = ($request->wr_secret) ? $request->wr_secret : '';
        $comment->wr_content = $request->wr_content;

        $comment->mb_id = $writer->mb_id;
        $comment->wr_password = $writer->mb_password;
        $comment->wr_name = $writer->mb_name;
        $comment->wr_email = $writer->mb_email;

        $comment->wr_ip = $request->ip();
        $comment->wr_link1 = '';
        $comment->wr_link2 = '';

        $comment->save();

        return $comment;
    }

    public function show(Request $request, $board, $post_id)
    {
        $config = GnuBoard::find($board);

        $config->checkAuthenticated('read');

        return['message'=>__METHOD__];
    }

    public function update(Request $request, $board_id, $post_id, $comment_id)
    {

    }
    public function destroy(Request $request, $board_id, $post_id, $comment_id)
    {
        $config = GnuBoard::find($board_id);

        $config->checkAuthenticated("comment");

        $post = $config->posts()->find($post_id);
        $comment = $post->comments()->find($comment_id);

        if(!$comment) {
            abort(404, "해당 글을 찾을 수 없습니다." + $comment_id);
        }

        $children = $this->getChildrenComments($comment, $post);

        if($children->count() > 0){
            abort(401, "답변된 글이 없어야 이 글이 지울 수 있습니다.");
        }

        $writer = auth("gnu")->user();
        /**
         * @var $writer Member
         */
        // 관리자가 아니라면
        if( !$writer->is_admin ){
            // 작성자여야 합니다.
            if( $comment->mb_id !== $writer->mb_id ){
                abort(402, "작성자만 지울 수 있습니다.");
            }
        }

        $comment->delete();

        return ['message'=>'삭제되었습니다.', 'status'=>true];
    }

    /**
     * @param $comment
     * @param $post GnuPost
     * @return mixed
     */
    protected function getChildrenComments($comment, $post)
    {
        $depth = strlen($comment->wr_comment_reply);

        // 자식 글이 있다면 삭제할 수 없습니다.
        if ($depth == 0) {
            $children = $post->comments()->where('wr_id', '!=', $comment->wr_id)
                ->where("wr_comment", $comment->wr_comment)
                ->where(\DB::raw("LENGTH(wr_comment_reply)"), 1)
                ->get();
        } else if ($depth > 0) {
            $children = $post->comments()->where('wr_id', '!=', $comment->wr_id)
                ->where("wr_comment", $comment->wr_comment)
                ->where(\DB::raw("LENGTH(wr_comment_reply)"), 1+$depth)
                ->where("wr_comment_reply", 'like', $comment->wr_comment_reply . "%")->get();
        }
        return $children;
    }

}
