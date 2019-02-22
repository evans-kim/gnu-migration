<?php

namespace EvansKim\GnuMigration\GnuModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GnuFile extends Model
{
    protected $table = "g4_board_file";
    protected $primaryKey = 'id';
    //protected $keyType = 'string';
    public $incrementing = true;
    const UPDATED_AT = null;
    const CREATED_AT = 'bf_datetime';
    protected $appends = ['file_url'];

    public function getPostClass(){
        return 'App\\Boards\\' . Str::studly($this->bo_table);
    }
    public function getFileUrlAttribute()
    {
        return \Storage::disk('public')->url( $this->bf_file );
    }
    public function post(){
        return $this->morphTo('post','wr_class', 'wr_id');
    }
    public function setupDLL()
    {
        /*
        ALTER TABLE g4_board_file ADD wr_class varchar(200) NULL;
        ALTER TABLE g4_board_file
        MODIFY COLUMN wr_class varchar(200) AFTER id,
        MODIFY COLUMN bf_no int(11) NOT NULL DEFAULT '0' AFTER bo_table;

        CREATE INDEX g4_board_file_wr_class_wr_id_index ON g4_board_file (wr_class, wr_id);

        $files = GnuFile::all();

        $files->each(function($item){
            $item->wr_class = $item->getPostClass();
            $item->save();
        });
         */
    }
}
