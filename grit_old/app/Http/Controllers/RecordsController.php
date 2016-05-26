<?php namespace App\Http\Controllers;

use App\Record,App\Assignment,App\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class RecordsController extends Controller
{

    public function index(){
        $records = Record::all();
        return Response::json($records);
    }

    public function store(Request $request){
        $record = new Record($request->all());
        $record->save();
        return $record;
    }
    
    public function failedAssignment()
    {
       $assignment_list = Assignment::select('id','user_id','title')
       ->where('last_bidding_date','<',date('Y-m-d'))
       ->where('last_bidding_date','!=','0000-00-00')
       ->get();
       
       if(COUNT($assignment_list) >0)
       {
        foreach($assignment_list as $v)
        {
            $assignment                 = Assignment::where("id","=",$v->id)->first();
            $assignment->status_id      = 8;
            $assignment->status_comment = 'Last bidding date has passed';
            
            $Notification               = new Notification;
            $Notification->to_id        = $v->user_id;
            $Notification->category_id  = 7;
            $Notification->url          = '';
            $Notification->notification_text = 'An assignment named "'.$v->title.'" has been failed due to last bidding date crossed.';
            $Notification->read         = 0;
            $Notification->save();
        }
       }
        echo 'Done';exit;
    }
    
}
