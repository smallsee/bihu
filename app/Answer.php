<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
  //添加回答api
   public function add(){
     if (!user_ins()->is_logged_in())
       return ['status'=>0,'msg'=>'login required'];

     if (!rq('question_id') || !rq('content'))
       return ['status'=>0,'msg'=>'question_id and question_id are required '];

     $question = question_ins()->find(rq('question_id'));
     if (!$question) return ['status'=>0,'msg'=>'question not  exists'];

     $answered = $this->where(['question_id'=>rq('question_id'),'user_id'=>session('user_id')])
          ->count();

     if ($answered)
       return ['status' => 0,'msg'=>'duplicate answers'];

     $this->content = rq('content');
     $this->question_id = rq('question_id');
     $this->user_id = session('user_id');

     return $this->save() ? ['status' => 1,'id'=>$this->id]
       : ['status' => 0,'db_insert_failed'];
   }

   public function change(){
     if (!user_ins()->is_logged_in())
       return ['status'=>0,'msg'=>'login required'];

     if (!rq('id') || !rq('content'))
       return ['status'=>0,'msg' => 'id and content is required'];

     $answer = $this->find(rq('id'));

     if ($answer->user_id != session('user_id'))
       return ['status'=>0,'msg' => 'permission denied'];


     $answer->content = rq('content');

     return $answer->save() ? ['status' => 1] : ['status' => 0,'db_update_failed'];

   }

   public function read_by_user_id($user_id){

     $user= user_ins()->find($user_id);
     if (!$user) return err('user not exists');

     $r =  $this
          ->with('question')
          ->where('user_id',$user_id)
          ->get()->keyBy('id');

     return success($r->toArray());
   }

   public function read(){

     if (!rq('id') && !rq('question_id') && !rq('user_id'))
       return ['status'=>0,'msg'=>'id or content is required'];

     if (rq('user_id')){

       $user_id = rq('user_id') === 'self' ?
         session('user_id') :
         rq('user_id');

       return $this->read_by_user_id($user_id);
     }


     if (rq('id')){

       $answer = $this
         ->with('user')
         ->with('users')
         ->find(rq('id'));
       if (!$answer)
         return ['status'=>0,'msg'=>'answer not exists'];

       return ['status'=>1,'data'=>$answer];
     }

     if (!question_ins()->find(rq('question_id')))
       return ['status'=>0,'msg'=>'question not exists'];

     $answers = $this
       ->where('question_id',rq('question_id'))
       ->get()
       ->keyBy('id');

     return ['status'=>1,'data'=>$answers];
   }

   //投票
  public function vote(){
    if (!user_ins()->is_logged_in())
      return ['status'=>0,'msg'=>'login required'];

    if (!rq('id') || !rq('vote'))
      return ['status'=>0,'msg' => 'id and vote is required'];

    $answer = $this->find(rq('id'));
    if (!$answer) return ['status'=>0,'msg'=>'answer not exists'];

    /* 1.赞同 2.反对 3.清空*/
    $vote = rq('vote');
    if($vote != 1 && $vote!=2 && $vote!=3)
      return err('invalid');
    /*检查此用户是否相同问题下投过票,如果投过票清空删除投票*/
    $answer->users()
      ->newPivotStatement()
      ->where('user_id',session('user_id'))
      ->where('answer_id',rq('id'))
      ->delete();

    if ($vote==3)
      return success();

    $answer->users()->attach(session('user_id'),['vote'=>$vote]);

    return ['status'=>1];
  }

    //关联user表
    public function user(){
      return $this->belongsTo('App\User');
    }

   public function users(){
     return $this
       ->belongsToMany('App\User')
       ->withPivot('vote')
       ->withTimestamps();

   }

   public function question(){
     return $this->belongsTo('App\Question');
   }

}
