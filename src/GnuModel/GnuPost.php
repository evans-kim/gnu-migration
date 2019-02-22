<?php

namespace EvansKim\GnuMigration\GnuModel;

use EvansKim\GnuMigration\Extensions\Searchable;
use EvansKim\GnuMigration\Member;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class GnuPost extends Model
{
    use Searchable;

    protected $table = "g4_write";
    public $board_id = 'notice';
    protected $primaryKey = 'wr_id';
    //protected $keyType = 'string';
    public $incrementing = true;
    const UPDATED_AT = null;
    const CREATED_AT = 'wr_datetime';

    protected $hidden = ['wr_password'];

    protected $appends = ['thumb_text', 'thumbnail'];
    protected $relations = ['files'];

    protected $fillable = ['wr_subject','wr_content'];

    private $_config;

    public function member()
    {
        return $this->belongsTo(Member::class, 'mb_id', 'mb_id');
    }
    public function children()
    {
        return $this->hasMany($this->getPostClass(), 'wr_num', 'wr_num')
            ->where(function($query){
                if($this->wr_is_comment){
                    $reply_field = 'wr_comment_reply';
                }else{
                    $reply_field = 'wr_reply';
                }
                $char = $this->wr_reply;
                $length = strlen($char)+1;
                $query->where($reply_field, 'like', $char . "%")->where(\DB::raw("LENGTH(".$reply_field.")"), '=', $length);
            })->orderBy('wr_reply');
    }
    public function getPostClass()
    {
        return "App\\Boards\\". Str::studly($this->board_id);
    }
    public function parent(){
        return $this->belongsTo($this->getPostClass(), 'wr_parent', 'wr_id');
    }
    public function comments(){
        return $this->hasMany( $this->getPostClass(), 'wr_parent', 'wr_id')
            ->where('wr_is_comment', 1)->orderBy('wr_comment')->orderBy('wr_comment_reply');
    }
    public function files()
    {
        return $this->morphMany(GnuFile::class, 'post', 'wr_class', 'wr_id', 'wr_id');
    }
    public function getThumbTextAttribute()
    {
        $thumbText = [];
        $message = strip_tags($this->wr_content);
        $message = str_replace('&nbsp;', '', $message);
        return mb_substr($message, 0, 300);
    }
    public function getThumbnailAttribute()
    {
        if ($this->files->count() > 0) {

            return "https://candleworks.co.kr/data/file/" . $this->config->bo_table . "/" . $this->files->first()->bf_file;

        } else {

            $matches = [];
            preg_match_all("/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i", $this->wr_content, $matches);
            return (!empty($matches[1][0])) ? $matches[1][0] : null;
        }
    }
    public function getConfigAttribute(){
        if(!$this->_config)
            $this->_config = GnuBoard::find($this->board_id);
        return $this->_config;
    }
    public function checkAuthenticated($mode='list')
    {
        if( $user = auth("gnu")->user() ){
            $level = $user->mb_level;
        }else{
            $level = 1;
        }

        if( !$this->config->isAuthenticated($mode, $level) ){
            abort(403, "접근할 수 없는 페이지 입니다. 로그인 하신 후에 접속해 보세요. 지속적으로 문제 발생시 고객센터로 연락 바랍니다.");
        }
        return true;
    }
    public function isSecretContent()
    {
        $isMatched = preg_match("/secret/", $this->wr_option);
        if($isMatched){
            $user = auth("gnu")->user();
            if(!$user){
                throw new AuthenticationException("이 포스트는 비밀글 입니다. 로그인 후 다시 시도해 주세요.");
            }

        }

        return $isMatched;
    }
    public function attach(UploadedFile $file)
    {
        if ( !$file->isValid() ) {
            abort(406, ko_en("올바른 파일이 아닙니다.",'It`s not available file.'));
        }
        $newFile = new GnuFile();
        $newFile->bo_table = $this->board_id;
        $newFile->wr_class = get_class($this);
        $newFile->wr_id = $this->wr_id;
        $newFile->bf_source = $file->getClientOriginalName();
        $newFile->bf_file = $file->store($this->board_id.'/'. $this->wr_id , 'public');
        $newFile->bf_download = 0;
        $newFile->bf_content = '';
        $newFile->bf_width = 0;
        $newFile->bf_height = 0;
        $newFile->bf_type = $file->getType();
        $newFile->bf_filesize = $file->getSize();
        $newFile->save();



    }
    public function build(Request $request)
    {

        if( $request->has("parent_id") && !empty($request->parent_id)){
            //답변을 다는 경우
            // 이전에도 답변이 있었습니까?
            $parentModel = new static;
            $parent = $parentModel->find($request->parent_id);
            /**
             * @var $parent GnuPost
             */
            $reply_char = "A";
            $reply_count = $parent->children()->count();

            $reply_char = chr(ord( $reply_char ) + $reply_count);

            if(!$parent){
                abort(404, "답변의 원 글을 찾을 수 없습니다.");
            }
            if(strlen($parent->wr_reply) > 3 ){
                abort(402, "3단 이상의 답변을 달 수 없습니다.");
            }

            $wr_reply = $parent->wr_reply . $reply_char;
            $wr_num = $parent->wr_num;
            $this->wr_subject = $parent->wr_subject;

        }else{
            // 새글

            $wr_reply = '';
            $wr_num = \DB::table($this->table)->min('wr_num') - 1;
        }
        $this->wr_num = $wr_num;
        $this->wr_reply = $wr_reply;

        $options = [];
        if($request->has('is_secret') && $request->is_secret){
            $options[] = 'secret';
        }
        $this->wr_option = implode(',', $options);


        $user = auth('gnu')->user();

        $this->wr_name = $user->mb_name;
        $this->wr_email = $user->mb_email;
        $this->wr_password = $user->mb_password;
        $this->wr_is_comment = 0;
        $this->wr_parent = '';

        $this->wr_link1 = '';
        $this->wr_link2 = '';
        $this->wr_ip = $request->ip();

        return $this;
    }

    // Scope functions //

    public function scopeExceptSecretContent($query)
    {

    }
    public function scopePostOnly($query)
    {
        return $query->where('wr_is_comment', 0)->select([
            'wr_id','wr_num','wr_subject','wr_name','wr_datetime','wr_option','wr_reply','wr_content',
            'wr_1','wr_2','wr_3','wr_4','wr_5','wr_6','wr_7','wr_8','wr_9','wr_10'
        ]);
    }

}
