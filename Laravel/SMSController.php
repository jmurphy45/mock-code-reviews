<?php


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
/**
 *  As a user, I want to send an SMS message to a phone number.
 *  Using twillo API to send SMS messages and hav the ability to validate the request.
 *  
 * Acceptance Criteria:
 *  1. The user must own the phone number to send the message.
 *  2. The message must be less than 160 characters.
 *  3. The message must be sent successfully.
 *  4. The message must be saved in the database for monitoring.
 * 
 * 
 */
/*
Summary
|--------------------------------------------------------------------------|
This is a sample controller from a project I worked on. It is a simple SMS 
controller that sends SMS messages to a phone number. The controller validates
the request, checks if the user owns the phone number, and sends the message.
|--------------------------------------------------------------------------|
DB Schema - Users
|--------------------------------------------------------------------------|
id | name | email | password | created_at | updated_at
|--------------------------------------------------------------------------|
DB Schema - Phones
|--------------------------------------------------------------------------|
id | user_id | number | created_at | updated_at
|--------------------------------------------------------------------------|
DB Schema - Messages
|--------------------------------------------------------------------------|
id | from | to | message | status | created_at | updated_at

*/

class SMSController
{
   public function handle(Request $request)
   {
       //FormRequest
       $validator = Validator::make($request->all(), [
           'sender_id' => 'required|exists:users,id',
           'from' => 'required|string|min:10|max:10',
           'to' => 'required|string|min:10|max:10',
           'message' => 'sometimes|required|min:1|max:160',
       ]);
  
       if ($validator->fails()) {
           return response()->json([
               'status' => 404,
               'message' => 'Your request is not valid.'
           ]);
       }
  
       $phone = null;
       $user = User::where([
           ['user_id', '=', auth()->id()],
       ])->first();

       for($user->phones as $phone){
           if($phone->number == $request->to){
               $phone = $phone;
           }
       }


       if (! is_null($user) && $phone !== $request->from) {
           return response()->json([
               'status' => 500,
               'message' => 'The user does not own this phone.'
           ]);
       }
  
       $message = Message::create([
          
           'from' => $request->from,
           'to' => '+1' . $request->to,
           'status' => 'sent',
           'message' => $request->input('message'),
       ]);
  
       try {
              $client = new Client();
              $response = $client->request('POST', 'https://api.twilio.com/2010-04-01/Accounts/test1234secret' . '/Messages.json', [
                'auth' => [env('TWILIO_SID'), env('TWILIO_TOKEN')],
                'form_params' => [
                     'From' => $request->from,
                     'To' => '+1' . $request->to,
                     'Body' => $request->input('message'),
                ]
              ]);
       } catch (Exception $e) {
           $message->status = 'failed';
           return response()->json([
               'status' => 500,
               'message' => 'The message could not be sent.'
           ]);
       }
  
       return response()->json([
           'status' => 200,
           'message' => 'SMS Sent.'
       ]);
   }
}