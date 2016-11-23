<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Request;
use Hash;

class User extends Model
{
  public function signUp(){

    $has_username_and_password = $this->has_username_and_password();

    if (!$has_username_and_password)
      return err('用户名密码不能为空');

    $username = $has_username_and_password[0];
    $password = $has_username_and_password[1];
    /*检查用户名是否存在*/
    $user_exists = $this::where('username',$username)
      ->exists();

    if ($user_exists)
      return err('用户名已存在');

    /*加密密码*/
    $hashed_password = Hash::make($password);

    /*存入数据库*/
    $user = $this;
    $user->password = $hashed_password;
    $user->username = $username;
    if(!$user->save()){
      return err('存储失败');
    }

    return success(['id'=>$user->id]);
  }

  //登录
  public function login(){



    $has_username_and_password = $this->has_username_and_password();

    if (!$has_username_and_password)
      return err('用户名密码不能为空');

    $username = $has_username_and_password[0];
    $password = $has_username_and_password[1];

    $user = $this->where('username',$username)->first();

    if (!$user){
      return err('用户名不存在');
    }

    $hashed_password = $user->password;
    if (!Hash::check($password,$hashed_password)){
      return err('密码有误');
    }

    session()->put('username',$user->username);
    session()->put('user_id',$user->id);

    return success(['id'=>session('user_id')]);

  }

  //判断用户名存在
  public function has_username_and_password(){
    $username = rq('username');
    $password = rq('password');

    /*检查用户名和密码是否为空*/

    if ($username && $password){
      return [$username,$password];
    }else{
      return false;
    }
  }

//  登出
  public function logout(){
//    session()->flush();
//    session()->put('username',null);
//    session()->put('user_id',null);
    session()->forget('username');
    session()->forget('user_id');
//    return success();
    return redirect('/');
  }

  //判断用户是否登录
  public function is_logged_in(){
    return is_logged_in();
  }

  //更改密码
  public function change_password(){
    if (!user_ins()->is_logged_in())
      return err('login required');

    if (!rq('old_password') || !rq('new_password'))
      return err('old_password and new_password are required ');

    $user = $this->find(session('user_id'));

    if(!Hash::check(rq('old_password'),$user->password))
      return err('invalid old_password');

    $user->password = bcrypt(rq('new_password'));
    return $user->save() ? success()
      :err('db_update_failed');

  }

  //找回密码api
  public function reset_password(){


    if($this->is_robot())
      return err('max frequency reached');

    if (!rq('phone'))
      return err('phone is required');

    $user = $this->where('phone',rq('phone'))->first();

    if (!$user)
      return err('invalid phone number');

    $captcha = $this->generate_captcha();
    $this->send_sms();
    $user->phone_captcha = $captcha;


    if ($user->save()){
      $this->send_sms();
      //方便下一次机会做检查
      $this->update_robot_time();
      return success();
    }
    return err('db_update_failed');

  }

  public function is_robot($time=10){
    if (!session('last_sms_time'))
      return false;
    $current_time = time();
    $last_active_time =session('last_sms_time');
    return !($current_time - $last_active_time > $time);
  }
  public function update_robot_time(){
    session()->set('last_sms_time',time());
  }
  //验证找回密码
  public function validate_reset_password(){

    if($this->is_robot(2))
      return err('max frequency reached');

    if (!rq('phone') || !rq('phone_captcha'))
      return err('phone and phone_captcha are required ');

    $user = $this->where([
      'phone'=>rq('phone'),
      'phone_captcha' => rq('phone_captcha')
    ])->first();

    if (!$user)
      return err('invalid phone or invalid phone_captcha');

    $user->password = bcrypt(rq('new_password'));
    $this->update_robot_time();
    return $user->save() ?
      success() : err('db_updated_failed');

  }

  //发送短信
  public function send_sms(){
    return true;
  }

  //生成验证码
  public function generate_captcha(){
    return rand(1000,9999);
  }

  //获取用户信息
  public function read(){
    if (!rq('id'))
      return err('required id');


      $id = rq('id') === 'self' ?
         session('user_id') : rq('id');

    $get = ['id','username','avatar_url','intro'];
    $user = $this->find($id,$get);

    $data = $user->toArray();
    $answer_count = answer_ins()->where('user_id',$id)->count();
    $question_count = question_ins()->where('user_id',$id)->count();
//    $answer_count = $user->answers()->count();
//    $question_count = $user->questions()->count();

    $data['answer_count'] = $answer_count;
    $data['question_count'] = $question_count;

    return success($data);
  }
  public function answers(){
    return $this
      ->belongsToMany('App\Answer')
      ->withPivot('vote')
      ->withTimestamps();

  }

  //检查用户是否存在
  public function exists(){
    return success(['count'=>$this->where(rq())->count()]);
  }

  public function questions(){
    return $this
      ->belongsToMany('App\Question')
      ->withPivot('vote')
      ->withTimestamps();

  }

}
