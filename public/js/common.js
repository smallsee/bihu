;(function(){
  'use strict';
  angular.module('common',[])
    .service('TimeLineService',[
      '$http',
      function($http){
        var me =this;
        me.current_page = 1;
        me.data = [];
        me.get = function(conf){

          if (me.pending) return;

          me.pending = true;

          conf = conf || {page:me.current_page};

          $http.post('api/timeline',conf)
            .then(function(r){
              if (r.data.status){
                if (r.data.data.length){
                  me.data = me.data.concat(r.data.data);
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
        }
      }
    ])
    .controller('HomeController',[
      '$scope',
      'TimeLineService',
      function($scope,TimeLineService){
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

        })
      }
    ])


})();