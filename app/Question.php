<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    public function add(){

      if (!user_ins()->is_logged_in())
        return ['status'=>0,'msg' => 'login required'];


      if (!rq('title'))
        return ['status'=>0,'msg' => 'title required'];

      $this->title = rq('title');
      $this->user_id = session('user_id');
      if (rq('desc'))
        $this->desc = rq('desc');

      return $this->save() ? ['status' => 1,'id'=>$this->id]
                          : ['status' => 0,'db_insert_failed'];
    }


    //更新问题api
    public function change(){
      if (!user_ins()->is_logged_in())
        return ['status'=>0,'msg' => 'login required'];

      if (!rq('id'))
        return ['status'=>0,'msg' => 'id is required'];

//      $question = $this->where('id',rq('id'))->first();
//      dd($question);
      $question = $this->find(rq('id'));

      if ($question->user_id != session('user_id'))
        return ['status'=>0,'msg' => 'permission denied'];

      if (rq('title'))
        $question->title = rq('title');

      if (rq('desc'))
        $question->desc = rq('desc');

      return $question->save() ? ['status' => 1] : ['status' => 0,'db_update_failed'];
    }

//    查看问题api
    public function read(){
      if (rq('id'))
        return ['status',$this->find(rq('id'))];

//      $limit = rq('limit') ?: 15;
//      $skip = ((rq('page') ?: 1) -1)* $limit;

      list($limit,$skip) = paginate(rq('page'),rq('limit'));

      $r = $this->orderBy('created_at')
                  ->limit($limit)
                  ->skip($skip)
                  ->get(['id','title','desc','user_id','created_at','updated_at'])
                  ->keyBy('id');

      return ['status' => 1,'data'=>$r];

    }

//    删除问题api
    public function remove(){

      if (!user_ins()->is_logged_in())
        return ['status'=>0,'msg' => 'login required'];

      if (!rq('id'))
        return ['status'=>0,'msg' => 'id is required'];

      $question = $this->find(rq('id'));
      if (!$question) return ['status'=>0,'msg' => 'question no exists'];

      if ($question->user_id != session('user_id'))
        return ['status'=>0,'msg' => 'permission denied'];

      return $question->delete() ? ['status' => 1]
        : ['status' => 0,'db_delete_failed'];

    }
}
