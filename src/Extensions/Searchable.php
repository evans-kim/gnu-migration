<?php

namespace EvansKim\GnuMigration\Extensions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

trait Searchable
{
    private $rules = null;
    public function getSearchable(){
        return [
            ['name','like', '%{value}%']
        ];
    }

    public function getExtensionsAttribute()
    {
        return [
            //'users'
        ];
    }
    public function scopeFindWithRelations($query, $id)
    {
        $this->setExtensionRelationships($query);

        return $query->find($id);
    }

    /**
     * 데이터 조회 기능을 추상화
     * @param $query
     * @param Request $request
     * @param bool $or
     * @return Builder
     */
    public function scopeSearch($query, Request $request, $or = false){

        /**
         * @var $query Builder
         */
        $this->setSearchQuery($query, $request, $or);

        $this->setSortingQuery($query, $request);

        return $query;
    }

    /**
     * @param $query
     * @param Request $request
     */
    private function setSortingQuery($query, Request $request)
    {
        // 데이터 정렬
        if ($request->has('sort') && $request->has('order')) {
            $query->orderBy($request->sort, ($request->order == 'descending') ? 'desc' : 'asc');
        }
    }
    public static function getSearchRules()
    {
        $rules = [];
        $model = new self;
        foreach ($model->getSearchable() as $search){
            $rules = array_merge($rules, $model->getRole($search));
        }
        return $rules;
    }
    private function getRole($search)
    {

        if( is_string($search) ){

            return [ $search => 'nullable|string' ];
        }
        if( is_array($search) && isset($search[0]) && is_string($search[0]) ){

            return [ $search[0] => 'nullable|string'];
        }
        if( is_array($search) && is_string($field = key($search))){

            return [ $field => 'nullable|string' ];
        }

    }
    public function compiler($search, Request $request, $field=null)
    {

        if( is_string($search) ){
            // '컬럼값'
            $value = (!empty($field)) ? $request->input($field) : $request->input($search);
            if(is_null($value)){
                return null;
            }
            return [$search, '=', $value];
        }

        if( is_array($search) && isset($search[1]) && is_callable($search[1]) ){
            // [컬럼명, 클로저]
            $value = (!empty($field)) ? $request->input($field) : $request->input($search[0]);
            if(is_null($value))
                return null;

            return [$search[1], $value];
        }

        if( is_array($search) && isset($search[0])  && is_string($search[0]) ){
            // [컬럼, 조건, 값]
            $value = (!empty($field)) ? $request->input($field) : $request->input($search[0]);
            if(is_null($value)){
                return null;
            }
            if( strtolower($value) === 'null'){
                $field = $field ?: $search[0];
                return [ function($query, $val)use($field){
                    $query->whereNull($field)->orWhere($field, '=', '');
                }, null];
            }

            if ( !empty($search[2]) && preg_match('/{value}/i', $search[2]) ) {
                $search[2] = str_replace("{value}", $value, $search[2]);
            }
            if ( preg_match('/{value}/i', $search[1]) ) {
                $search[2] = str_replace("{value}", $value, $search[1]);
                $search[1] = 'like';
            }

            return $search;
        }

        if( is_array($search) && $field = key($search) ){
            $value = $request->input($field);
            if(!$value){
                return null;
            }
            $queries = [];
            foreach ( $search[$field] as $searchQuery ){
                $queries[] = $this->compiler( $searchQuery, $request, $field);
            }
            return $queries;
        }


    }
    /**
     * @param $query
     * @param Request $request
     */
    private function setSearchQuery($query, Request $request)
    {

        if(empty($this->getSearchable())) {
            return false;
        }
        $queries = [];
        foreach ( $this->getSearchable() as $search ){
            $queries[] = $this->compiler($search, $request);
        }
        $queries=array_values(array_filter($queries));

        foreach ($queries as $sql){
            if(is_callable($sql[0])){
                $query->where( function($query)use($sql, $queries){
                    $sql[0]($query, $sql[1], $queries);
                });
                continue;
            }
            if( isset($sql[0]) && is_array($sql[0]) ){
                $query->where($sql);
            }else{
                $query->where([$sql]);
            }

        }
    }

}
