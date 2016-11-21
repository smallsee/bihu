<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class CommonController extends Controller
{
    public function timeline(){

      list($limit,$skip) = paginate(rq('page'),rq('limit'));

      $questions = question_ins()
        ->limit($limit)
        ->skip($skip)
        ->orderBy('created_at','desc')
        ->get();

      $answers = question_ins()
        ->limit($limit)
        ->skip($skip)
        ->orderBy('created_at','desc')
        ->get();

      //合并数据
      $data = $questions->merge($answers);

      //讲合并的数据按时间排序
      $data = $data->sortByDesc(function($item){
        return $item->created_at;
      });

      $data = $data->values()->all();
      return ['status'=>1,'data'=>$data];
    }
}
