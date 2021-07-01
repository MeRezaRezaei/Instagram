<?php

namespace App\Http\Controllers;

use App\Actions\Response;
use App\Models\Messages;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Dialog extends Controller
{
    use Response;

    public function get_dialog(Request $request){
        $Validate = Validator::make($request->all(),[
            'peer_id'=>'required|integer|exists:App\Models\User,id'
        ]);
        if ($Validate->fails())
            return $this->Make_Error($Validate->errors(),400)
                ;


        DB::table('messages')
            ->where([
                ['from_id','=',$request->peer_id],
                ['to_id','=',$this->LoggedInUserId]
            ])
            ->update(['is_read'=>true]);


        $dialogs = Messages::where([
            ['from_id','=',$this->LoggedInUserId],
            ['to_id','=',$request->peer_id]
        ])
            ->orWhere([
                ['from_id','=',$request->peer_id],
                ['to_id','=',$this->LoggedInUserId]
            ])
            ->get();


        $messages = [];
        foreach ($dialogs as $dialog){
            $messages[] = [
                'id'=>$dialog->id,
                'message'=>$dialog->text,
                'created_at'=>$dialog->created_at,
                'updated_at'=>$dialog->updated_at,
                'is_read'=>$dialog->is_read,
                'from'=>[
                    'id'=>$dialog->from->id,
                    'name'=>$dialog->from->name,
                    'last_name'=>$dialog->from->last_name,
                    'email'=>$dialog->from->email
                ],
                'to'=>[
                    'id'=>$dialog->to->id,
                    'name'=>$dialog->to->name,
                    'last_name'=>$dialog->to->last_name,
                    'email'=>$dialog->to->email
                ]
            ];
        }


        return $this->Make_Response(
            'dialog messages',
            200,
            $messages);
    }

    public function send_dm(Request $request){
        $Validate = Validator::make($request->all(),[
            'message'=>'required|string|max:255',
            'peer_id'=>'required|integer|exists:App\Models\User,id',
        ],[
            'message.required'=>'message can not be empty',
            'message.string'=>'message must be string',
            'message.max'=>'message max length is 255',
            'peer_id.required'=>'peer_id can not be empty',
            'peer_id.integer'=>'peer_id must be integer'
        ]);

        if ($Validate->fails())
            return $this->Make_Error($Validate->errors(),400)
                ;


        $peer = User::find($request->peer_id);
        if ($peer == null)
            return $this->Make_Error('could not find peer_id',400)
                ;


        $message = $peer->received_messages()->create([
            'from_id'=> $this->LoggedInUserId,
            'text'=> $request->message,
            'is_read'=>false
        ]);


        return $this->Make_Response(
            'message sent successfully',
            201,
            [
                'message'=>'message sent successfully',
                'message_id'=> $message->id
            ]
        );
    }
}
