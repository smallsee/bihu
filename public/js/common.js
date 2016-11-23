;(function(){
  'use strict';
  angular.module('common',[])
    .service('TimeLineService',[
      '$http',
      'AnswerService',
      function($http,AnswerService){
        var me =this;
        me.current_page = 1;
        me.data = [];
        me.get = function(conf){

          if (me.pending) return;

          me.pending = true;

          conf = conf || {page:me.current_page};

          /*获取首页数据*/
          $http.post('api/timeline',conf)
            .then(function(r){
              if (r.data.status){
                if (r.data.data.length){
                  me.data = me.data.concat(r.data.data);
                  /*统计每一条回答的票数*/
                  me.data = AnswerService.count_vote(me.data);
                  me.current_page++;
                }
                else
                  me.no_more_data = true;

              }
              else
                console.error('network error');
            },function (e) {
              console.error(e);
            })
            .finally(function(){
              me.pending = false;
            })

          /*在时间线中点赞 */
          me.vote = function(conf){
            /*调用核心投票功能*/
              AnswerService.vote(conf)
                .then(function(r){
                  /*如果点赞成功就更新AnswerService 中的数据*/
                  if (r)
                    AnswerService.update_data(conf.id);
                })
          }
        }
      }
    ])
    .controller('HomeController',[
      '$scope',
      'TimeLineService',
      'AnswerService',
      function($scope,TimeLineService,AnswerService){
        var $win;
        $scope.Timeline = TimeLineService;
        TimeLineService.get();
        $win = $(window);

        //检测滚到底部加载数据
        $win.on('scroll',function(){
          $win.scrollTop()
          if ($win.scrollTop() - ($(document).height()-$win.height()) > -30){
            TimeLineService.get();
          }

        });


        /*监控我们数据的变化 如果回答数据又变化同时更新其他模块中的回答数据*/
        $scope.$watch(function(){
          return AnswerService.data;
        },function(new_data,old_data){
          var timeline_data = TimeLineService.data;
          /*更新时间线中的回答数据*/
          for (var k in new_data){
            for (var i=0;i<timeline_data.length;i++){
          if (k == timeline_data[i].id){
            timeline_data[i] = new_data[k];
          }
            }
          }

          timeline_data = AnswerService.count_vote(timeline_data);
        },true)
      }
    ])


})();