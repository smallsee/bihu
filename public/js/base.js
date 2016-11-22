;(function(){
  'use strict';

  angular.module('bihu',['ui.router'])
    .config(['$interpolateProvider','$stateProvider','$urlRouterProvider',
      function($interpolateProvider,$stateProvider,$urlRouterProvider){
      $interpolateProvider.startSymbol('[:');
      $interpolateProvider.endSymbol(':]');

      $urlRouterProvider.otherwise('/home');

      $stateProvider
        .state('home',{
          url:'/home',
          templateUrl:'home.tpl' //若在当前页面找不到home.tpl 就会到服务端去找
        });

      $stateProvider
        .state('login',{
          url:'/login',
          templateUrl:'login.tpl'
        })

      $stateProvider
        .state('signup',{
          url:'/signup',
          templateUrl:'signup.tpl'
        })
    }])

    .service('UserService',[
      '$state',
      '$http',
      function($state,$http){
        var me = this;
        me.signup_data = {};
        me.signup = function(){
          $http.post('api/signup',me.signup_data)
            .then(function(r){

              if (r.data.status){
                me.signup_data = {};
                $state.go('login');
              }



            },function(e){

            })
        };


        me.username_exists = function(){
          $http.post('api/user/exists',
            {username:me.signup_data.username})
            .then(function(data){

              if (data.data.status && data.data.data.count){
                me.signup_username_exists = true;
              } else{
                me.signup_username_exists = false;
              }

            },function(e){

              console.log('e',e);
            })
        }
      }
    ])
    .controller('SignupController',[
      '$scope',
      'UserService',
      function($scope,UserService){
        $scope.User = UserService;

        $scope.$watch(function(){
          return UserService.signup_data;
        },function(n,o){

          if (n.username != o.username)
            UserService.username_exists();
        },true);
      }
    ])


})();