<?php

namespace App\Http\Controllers;

use App\Actions\Response;
use App\Models\Posts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Eloquent_Latency_Test extends Controller
{
    use Response;
    public function Pure_Query(){
        $start = microtime(true);
        $stop = microtime(true);



        $start = microtime(true);

        DB::table('posts')
            ->join('pictures','post_id','=','posts.id')->where('post_id',1)
            ->select('*')->get()
        ;

        $stop = microtime(true);



        return $this->toJSON([
            'start'=>$start,
            'stop'=>$stop,
            'stop - start'=>$stop - $start
        ]);
    }

    public function Model_Query(){
        $start = microtime(true);
        $stop = microtime(true);



        $start = microtime(true);

        $post = Posts::find(1);
        $post->pics;

        $stop = microtime(true);



        return $this->toJSON([
            'start'=>$start,
            'stop'=>$stop,
            'stop - start'=>$stop - $start
        ]);
    }

    public function Full_Test(){
        $pure_start = microtime(true);
        $pure_stop = microtime(true);


        $pure_start = microtime(true);

        DB::table('posts')
            ->join('pictures','post_id','=','posts.id')->where('post_id',1)
            ->select('*')->get()
        ;
        $pure_stop = microtime(true);

        $pure_diff = $pure_stop - $pure_start;



        $model_start = microtime(true);
        $model_stop = microtime(true);


        $model_start = microtime(true);

        $post = Posts::find(1);
        $post->pics;

        $model_stop = microtime(true);


        $model_diff = $model_stop - $model_start;


        return $this->toJSON(
            [
                'pure_start'=>$pure_start,
                'pure_stop'=>$pure_stop,

                'model_start'=>$model_start,
                'model_stop'=>$model_stop,

                'pure_diff'=>$pure_diff,
                'model_diff'=>$model_diff,

                'pure - model'=>$pure_diff - $model_diff
            ]
        );
    }
}
