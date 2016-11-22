;(function(){
  'use strict';

  angular.module('bihu',[
    'ui.router',
    'common',
    'question',
    'user'
  ])
    .config(['$interpolateProvider','$stateProvider','$urlRouterProvider',
      function($interpolateProvider,$stateProvider,$urlRouterProvider){
      $interpolateProvider.startSymbol('[:');
      $interpolateProvider.endSymbol(':]');

      $urlRouterProvider.otherwise('/home');

      $stateProvider
        .state('home',{
          url:'/home',
          templateUrl:'tpl/page/home' //若在当前页面找不到home.tpl 就会到服务端去找
        })
        .state('login',{
          url:'/login',
          templateUrl:'tpl/page/login'
        })

        .state('signup',{
          url:'/signup',
          templateUrl:'tpl/page/signup'
        })

        .state('question',{
          abstract:true,
          url:'/question',
          template:'<div ui-view></div>'
        })

        .state('question.add',{
          url:'/add',
          templateUrl:'tpl/page/question_add'
        })

    }])






})();