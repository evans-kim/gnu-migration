<?php

namespace EvansKim\GnuMigration\GnuModel;

use EvansKim\GnuMigration\Extensions\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GnuBoard extends Model
{
    use Searchable;

    protected $table = "g4_board";
    protected $primaryKey = 'bo_table';
    protected $keyType = 'string';
    public $incrementing = false;
    const UPDATED_AT = null;
    const CREATED_AT = null;
    private $_posts;
    private $_files;
    public function withFiles()
    {
        if(!$this->_files){
            $this->_files = \DB::table("g4_board_file")
                ->where("bo_table",  $this->bo_table)
                ->where("wr_id",  $this->wr_id);
        }
        return $this->_files;
    }

    public function getSearchable(){
        return [
            ['wr_subject','like','%{value}%'],
            ['wr_content','like','%{value}%'],
            'mb_id',
        ];
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function createModelClassFile()
    {
        $content = \File::get(app_path("/Boards/stubs/Post.stub"));

        $class = Str::studly($this->bo_table);
        $content = preg_replace("/{Name}/", $class, $content);

        if( ! \File::exists(app_path("/Boards/" . $class . ".php")) ){

            \File::put(app_path("/Boards/" . $class . ".php"), $content);
        }
    }
    public function getPostClass()
    {
        return "App\\Boards\\". Str::studly($this->bo_table);
    }

    /**
     * @return GnuPost
     */
    public function posts($attributes=[])
    {
        $model = $this->getPostClass();
        return (new $model($attributes));
    }
    public function getNotices()
    {
        if(empty($this->bo_notice)){
            return [];
        }
        $notices = explode("\n", $this->bo_notice);
        array_filter($notices, function($item){
            return !empty($item);
        });
        return $this->posts()->with(['files'])->whereIn('wr_id', $notices)->orderBy('wr_num')->get();
    }
    public function getPostQuery()
    {
        if(!$this->_posts){
            $this->_posts = \DB::table("g4_write_" . $this->bo_table)->select([
                'wr_id','wr_num','wr_subject','wr_content','wr_1','wr_2','wr_3'
            ]);
        }
        return $this->_posts;
    }
    public function checkAuthenticated($mode='list')
    {

        if( $user = auth()->user() ){
            $level = $user->mb_level;
        }else{
            $level = 1;
        }

        if( !$this->isAuthenticated($mode, $level) ){
            $message="접근할 수 없는 기능 입니다.";
            if( !auth("gnu")->check() ){
                $message .=" 로그인이 필요합니다.";
            }
            abort(403, $message);
        }
        return true;
    }
    public function scopeSelectLevelConfig($query)
    {
        return $query->select([
            'bo_table',
            'gr_id',
            'bo_theme',
            'bo_template',
            'bo_subject',
            'bo_list_level',
            'bo_read_level',
            'bo_write_level',
            'bo_reply_level',
            'bo_comment_level',
            'bo_upload_level',
            'bo_download_level'
        ]);
    }
    /**
     * @param $mode
     * @param $level
     * @return bool
     */
    protected function isAuthenticated($mode, $level)
    {
        switch ($mode) {
            case 'list':
                return $level >= $this->bo_list_level;
                break;
            case 'read':
                return $level >= $this->bo_read_level;
                break;
            case 'write':
                return $level >= $this->bo_write_level;
                break;
            case 'reply':
                return $level >= $this->bo_reply_level;
                break;
            case 'comment':
                return $level >= $this->bo_comment_level;
                break;
            case 'upload':
                return $level >= $this->bo_upload_level;
                break;
            case 'download':
                return $level >= $this->bo_download_level;
                break;
            default:
                return false;
                break;
        }
    }
}
