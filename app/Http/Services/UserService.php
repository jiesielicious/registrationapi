<?php
namespace App\Http\Services;

use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\ImageManagerStatic as Image;

class UserService extends BaseService
{
    public function __construct(Request $request, User $user)
    {
        parent::__construct($user, $request);
    }

    public function updateProfile(Request $request)
    {
        
        if($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $filename = $avatar->getClientOriginalName();
            $image_resize = Image::make($avatar->getRealPath());              
            $image_resize->resize(300, 300);
            $image_resize->save(public_path('images/'.$filename));
         }

        $password = Hash::make($request->password);

        $user = $this->getModel()->updateOrCreate(['email' => $request->email],
            [
                'name' => $request->name ,
                'email' => $request->email,
                'user_role' => $request->user_role ,
                'password' => $password ,
                'user_name' => $request->user_name ,
                'avatar' => $filename,
            ]
        );

        return $user;
    }

    public function storeInitialUser($email)
    {
        $user = $this->getModel()->updateOrCreate(['email' => $email],
        [
            'email' => $email,
        ]);

        return $user;
    }

    public function createUsernamePassword($request)
    {
        $password = Hash::make($request->password);

        $user = $this->getModel()->updateOrCreate(['email' => $request->email],
        [
            'user_name' => $request->user_name,
            'password' => $password,
            'confirmation_pin' => rand(1,1000000)
        ]);

        return $user;
    }

    public function confirmUserPin($request)
    {
       $user = $this->getModel()->where('confirmation_pin', $request->confirmation_pin)->first();
        if($user) {
            $user->registered_at = Carbon::now();
            $user->save();
            return true;
        } else {
            return false;
        }
   
    }

} 