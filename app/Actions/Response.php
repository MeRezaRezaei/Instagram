<?php


 namespace App\Actions;


 trait Response
 {
     public $LoggedInUserId = 1;

     public function toJSON($D){
         return json_encode($D,JSON_PRETTY_PRINT);
     }
     public function Make_Error($message, $code){
         return response($this->toJSON([
             '_'=>'error',
             'response'=>[
                 'code'=>$code,
                 'status'=>'fail'
             ],
             'errors'=> $message
         ]),$code) ;
     }
     public function Make_Response($message,$code,array $information = []){
         return response($this->toJSON([
             '_'=>'response',
             'response'=>[
                 'code'=>$code,
                 'status'=>'success',
                 'message'=>$message
             ],
             'data'=>$information
         ]),$code);
     }
 }
