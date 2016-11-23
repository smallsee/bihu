;(function(){
  'use strict';
  angular.module('question',[])
    .service('QuestionService',[
      '$state',
      '$http',
      function($state,$http){
        var me = this;
        me.new_question = {};
        me.go_add_question = function(){
          $state.go('question.add')
        };
        me.add = function(){
          if (!me.new_question.title)
            return;

          $http.post('api/question/add',me.new_question)
            .then(function(r){

              if (r.data.status){
                me.new_question = {};
                location.href = '/';
              }

            },function(e){

            })
        };
        me.read = function(param){
          return $http.post('api/question/read',param)
            .then(function(r){
              if (r.data.status){
                me.data = angular.merge({},me.data,r.data.data);
                return r.data.data;
              }
                return false;
            })

        };
      }
    ])
    .controller('QuestionAddController',[
      '$scope',
      'QuestionService',
      function($scope,QuestionService){
        $scope.Question = QuestionService;
      }
    ])


})();