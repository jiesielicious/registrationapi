<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Services\UserService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendInvitation;
use App\Mail\SendConfirmationPin;
use PhpParser\Node\Stmt\TryCatch;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        try {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'user_name' => $request->user_name,
                'password' => $request->password,
                'user_role' => $request->user_role,
                'avatar' => $request->avatar,
            ];
    
            $validator =  Validator::make($data, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255'], 
                'password' => ['required', 'string', 'max:255'],
                'user_role' => ['required', 'string'],
                'user_name' =>  ['required', 'string', 'min:4|max:20'],
                'avatar' => 'required|image:jpeg,png,jpg,gif,svg|max:2048'
            ]);
    
            if ($validator->fails()) {
                return $this->sendError('Error updating user.',$validator->errors(), 400);
            }
    
            
            $user = $this->userService->updateProfile($request);
    
            return $this->sendResponse($user, 'User successfully updated.');
        } catch (\Exception $e) {
            return $this->sendError('Error updating user.', null, 400);  
        }
   
    }


    //change password
    public function changePassword(Request $request){

        $user = $this->userService->find($this->getCurrentUser()->id);
        if (!Hash::check($request->old_password, $user->password))
        {
            return response('Password invalid. Old password.', 400);
        }
        $user->password = Hash::make($request->new_password);
        
        if($user->save()) {
            return $this->sendResponse($user, 'User sucessfully deleted.');   
        } else {
            return $this->sendError('Error changing password.', null, 400);   
        }
    }

    //logout
    public function logout(Request $request){
        Auth::user()->tokens->each(function ($token, $key) {
            $token->delete();
        });
        return $this->sendResponse('success', 'User successfully loggedout.');
    }

    //send invitation
    public function sendInvitation($email)
    {
        $user = $this->userService->storeInitialUser($email);
        try {
            Mail::to($email)->send(new SendInvitation($user));
            return $this->sendResponse('success', 'Email invitation sent successfully.');
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
        } 
    }

    //create username and password
    public function createUsernamePassword(Request $request)
    {
        $user = $this->userService->createUsernamePassword($request);
        try {
            Mail::to($request->email)->send(new SendConfirmationPin($user));
            return $this->sendResponse('success', 'Confirmation pin sent to your email successfully.');
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
        }
    }

    //confirm pin
    public function confirmUserPin(Request $request)
    {
        if($this->userService->confirmUserPin($request)) {
            return $this->sendResponse('success', 'User confirmed successfully.');
        } else {
            return $this->sendError('Error confirming user registration.', null, 400);  
        }
    }
}
