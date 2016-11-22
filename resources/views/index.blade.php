<!doctype html>
<html lang="zh" ng-app="bihu">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>逼乎</title>
  <link rel="stylesheet" href="{{asset('/node_modules/normalize-css/normalize.css')}}">
  <link rel="stylesheet" href="{{asset('/css/base.css')}}">
  <script src="{{asset('/node_modules/jquery/dist/jquery.js')}}"></script>
  <script src="{{asset('/node_modules/angular/angular.js')}}"></script>
  <script src="{{asset('/node_modules/angular-ui-router/release/angular-ui-router.js')}}"></script>
  <script src="{{asset('/js/base.js')}}"></script>
  <script src="{{asset('/js/user.js')}}"></script>
  <script src="{{asset('/js/question.js')}}"></script>
  <script src="{{asset('/js/common.js')}}"></script>
</head>
<body>
<div class="navbar clearfix">
  <div class="container">
    <div class="fl">
      <div ui-sref="home" class="navbar-item brand">逼乎</div>
      <form ng-submit="Question.go_add_question()" id="quick_ask" ng-controller="QuestionAddController">
        <div class="navbar-item">
            <input type="text" ng-model="Question.new_question.title"/>
        </div>
        <div class="navbar-item">
          <button class="primary">提问</button>
        </div>
      </form>
    </div>
    <div class="fr">
      <a ui-sref="home" class="navbar-item">首页</a>
      @if(is_logged_in())
        <a ui-sref="login" class="navbar-item">{{session('username')}}</a>
        <a href="{{url('/api/logout')}}" class="navbar-item">登出</a>
      @else
        <a ui-sref="login" class="navbar-item">登录</a>
        <a ui-sref="signup" class="navbar-item">注册</a>
      @endif

    </div>
  </div>

</div>

<div class="page">
  <div ui-view></div>
</div>

</body>








</html>