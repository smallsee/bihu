<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
function user_ins(){
  return  new App\User;
};

function question_ins(){
  return  new App\Question;
};

function answer_ins(){
  return  new App\Answer;
};

function comment_ins(){
  return  new App\Comment;
};

function rq($key=null,$default=null){
  if (!$key) return Request::all();

  return Request::get($key,$default);
};

function paginate($page=1,$limit=16){
  $limit = $limit ?: 16;
  $skip = ($page ? $page -1 : 0) * $limit;
  return [$limit,$skip];
}

function err($msg=null){
  return ['status'=>0,'msg'=>$msg];
}

function success($data_to_add=[]){

  $data = ['status'=>1,'data'=>[]];
  if ($data_to_add)
    $data['data'] = $data_to_add;

  return $data;
}

function is_logged_in(){
  return session('user_id') ?: false;
}

//其他模块查找用户
function read_by_user_id($user_id){

  $user= user_ins()->find($user_id);
  if (!$user) return err('user not exists');

  $r =  $this->where('user_id',$user_id)
    ->get()->keyBy('id');

  return success($r->toArray());
}
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {

  Route::get('/', function () {
    return view('index');
  });

  //user
  Route::any('api/signup',function(){
    return user_ins()->signUp();
  });

  Route::any('api/login',function(){
    return user_ins()->login();
  });

  Route::any('api/logout',function(){
    return user_ins()->logout();
  });

  Route::any('api/user/change_password',function(){
    return user_ins()->change_password();
  });

  Route::any('api/user/reset_password',function(){
    return user_ins()->reset_password();
  });
  Route::any('api/user/validate_reset_password',function(){
    return user_ins()->validate_reset_password();
  });
  Route::any('api/user/exists',function(){
    return user_ins()->exists();
  });
  //获取用户信息
  Route::any('api/user/read',function(){
    return user_ins()->read();
  });

  //question
  Route::any('api/question/add',function(){
    return question_ins()->add();
  });

  Route::any('api/question/change',function(){
    return question_ins()->change();
  });

  Route::any('api/question/read',function(){
    return question_ins()->read();
  });

  Route::any('api/question/remove',function(){
    return question_ins()->remove();
  });

  //answer
  Route::any('api/answer/add',function(){
    return answer_ins()->add();
  });
  Route::any('api/answer/change',function(){
    return answer_ins()->change();
  });
  Route::any('api/answer/read',function(){
    return answer_ins()->read();
  });

  Route::any('api/answer/vote',function(){
    return answer_ins()->vote();
  });

  //comment
  Route::any('api/comment/add',function(){
    return comment_ins()->add();
  });

  Route::any('api/comment/read',function(){
    return comment_ins()->read();
  });
  Route::any('api/comment/remove',function(){
    return comment_ins()->remove();
  });

  //时间线
  Route::any('api/timeline','CommonController@timeline');

  //页面的路由

  Route::get('tpl/page/home',function(){
    return view('page.home');
  });
  Route::get('tpl/page/login',function(){
    return view('page.login');
  });
  Route::get('tpl/page/signup',function(){
    return view('page.signup');
  });
  Route::get('tpl/page/question_add',function(){
    return view('page.question_add');
  });
  Route::get('tpl/page/user',function(){
    return view('page.user');
  });

});
