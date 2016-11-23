;(function(){
  'use strict';
  angular.module('answer',[])
    .service('AnswerService',[
      '$http',
      function($http){
        var me =this;
        me.data = {};
        //统计看票数
        me.count_vote = function(answers){

          for (var i=0;i<answers.length;i++){
            var votes,item = answers[i];

            if (!item['question_id']) continue;

            me.data[item.id] = item;

            if (!item['users']) continue;

            item.upvote_count=0;
            item.downvote_count=0;
            votes = item['users'];

            if (votes)
              for (var j=0;j<votes.length;j++){
                var v = votes[j];
                if (v['pivot'].vote === 1)
                  item.upvote_count++;
                  if (v['pivot'].vote === 2)
                    item.downvote_count++;
              }
          }
          return answers;
        };
        //点赞功能
        me.vote = function(conf){
          if (!conf.id || !conf.vote){
            console.log('id and vote are required');
            return;
          }

          var answer = me.data[conf.id],
              users = answer.users;
          for (var i =0;i<users.length;i++){
            if (users[i].id == his.id && conf.vote == users[i].pivot.vote){
              conf.vote = 3;
            }
          }


          return $http.post('api/answer/vote',conf)
            .then(function(r){
              if (r.data.status ){
                return true;
              }
              return false;
            },function(e){
              return false;
            })

        };
        me.update_data =function(id){
          // if (angular.isNumeric(input))
          //   var id =input;
          // if (angular.isArray(input))
          //   var id_set = input;
          return $http.post('api/answer/read',{id:id})
            .then(function(r){
               me.data[id] =r.data.data;
            })

        }
      }

    ])


})();