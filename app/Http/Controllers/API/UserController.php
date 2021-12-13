<?php

namespace App\Http\Controllers\API;
//use Illuminate\Support\Facades\Validator;

use App\Helpers\ImageUploading;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteUserRequest;
use App\Http\Requests\EmailVerificationRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Jobs\EmailVarificationMailJob;
use App\Jobs\ForgotPasswordMailJob;
use App\Models\AuthToken;
use App\Services\JwtAuthentication as ServicesJwtAuthentication;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{


    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
    */
    public function register(RegistrationRequest $request)
    {
        try{
            $request_data = $request->all();
            $email_verification_token=md5(request('user_name'));  //for encoding

            $user= new User;
            $user->name = $request_data['name'];
            $user->email = $request_data['email'];
            $user->age = $request_data['age'];
            $user->password=bcrypt(request('password'));
            $user->email_verification_token=$email_verification_token;

            // converting base64 decoded image to simple image if exist
            $user->_id=new \MongoDB\BSON\ObjectId();

            Storage::disk('local')->makeDirectory('public/users/'.strval($user->_id).'/profile',0777,true,true );
            Storage::disk('local')->makeDirectory('public/users/'.strval($user->_id).'/posts',0777,true,true );

            if(!empty(request('profile_image'))){
                $file_name=ImageUploading::imageUploading(request('profile_image'),'users',strval($user->_id));
                $user->profile_image=$file_name;
            }
            $user->created_at = date('Y-m-d h:i:s');
            $user->save();

            dispatch(new EmailVarificationMailJob(['email'=>$request_data['email'] ,'name'=>$request_data['name'] ,'link'=> url('user/emailConfirmation/'.$request_data['email'].'/'.$email_verification_token)]));
            // data creation for response

            $response_data['message']=strtoupper($request_data['name']).'token:'.$email_verification_token.',Please check your mail ('.$request_data['email'].') for Email Varification';
            $response_data['data']=null;
            return response()->success($response_data,200);
        }
        catch (\Exception $e) {
            return response()->error(['message'=>$e->getMessage()],500);
        }
    }


    /**
     * Verifying Email api
     *
     * @return \Illuminate\Http\Response
    */
    public function verifyingEmail(EmailVerificationRequest $request,$email,$token){
        try{
            $user=User::where('email',$email)->first();
            if (!empty($user->name)) {
                if ($token == $user->email_verification_token) {
                    $user->unset('email_verification_token');
                    $user->email_verified_at = date('Y-m-d h:i:s');
                    $user->update();

                    $response_data['data']=null;
                    $response_data['message']='Email Verified!!!';
                    return response()->success($response_data,200);
                }
                else{
                    $response_data['error']="Verification Link Already used";
                    $response_data['message']="Someting went Worng";
                    return response()->error($response_data,401);
                }
            }
            else{

                $response_data['error']="Email Not Register";
                $response_data['message']="Someting went Worng";
                return response()->error($response_data,401);
            }
        }catch (\Exception $e) {
            $response_data['error']="Server Error Please Try Again Later";
            $response_data['message']="Someting went Worng";
            return response()->error($response_data,500);
        }
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
    */
    public function LoginUser(LoginRequest $request)
    { // after checking password and email in loginrequest
        try{
            $user=request('user_data');
            $user_data['email'] = $user->email;
            $user_data['name'] = $user->name;
            $user_data['password'] = $user->password;
            $user_data['id'] = (string)$user->_id;

            // create Jwt token
            $Auth_key=ServicesJwtAuthentication::createJwtToken($user_data);
            //insert data in Auth Token
            $auth_token= new AuthToken;
            $auth_token->token=$Auth_key;
            $auth_token->user_id=$Auth_key;
            $auth_token->User()->associate((string)$user->_id);
            $auth_token->save();

            $response_data['data']['Authentication']=$Auth_key['token'];
            $response_data['data']['token_type']='Bearer';
            $response_data['data']['User_data']=new UserResource($user);
            $response_data['message']='Welcome to System!!!';
            return response()->success($response_data,200);

        }catch(Execption $ex){
            return response()->error(['message'=>"Someting went Worng"],500);
        }
    }

    /**
     * Logout api
     *
     * @return \Illuminate\Http\Response
    */
    public function logout(Request $request){
        try {

            $Auth_token=AuthToken::where("user_id",request()->user_data->id)->Delete();

            $response_data['data']=null;
            $response_data['message']='Logout Successfully!!!';
            return response()->success($response_data,200);
        }
        catch (\Exception $e) {
            return response()->error(['message'=>"Someting went Worng"],500);
        }
    }


    /**
     * ForgotPassword api
     *
     * @return \Illuminate\Http\Response
    */
    public function forgotPassword(ForgotPasswordRequest $reqeust)
    {
        try{
            $user_data= request('user_data');
            $string="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz!@#$%^&*1234567890";
            $password=substr(str_shuffle(str_repeat($string, 12)), 0, 12);
            $user_data->password=bcrypt($password);
            //for generate link in URL
            $user_data->update();
            dispatch(new ForgotPasswordMailJob(['email'=>request('email'),'new_password'=>$password]));

            //response
            return response()->success(['data'=>null,'message'=>$password.'/New Password Send to Your Mail!'],200);

        }catch (\Exception $e) {
            return response()->error(['message'=>$e->getMessage()],500);
        }
    }

    /**
     * Upadate User api
     *
     * @return \Illuminate\Http\Response
    */
    public function UpdateUser(UpdateUserRequest $request, $id){
        try{

            // dd($request->all());
            $user=User::find($id);
            $data_to_update = [];
            foreach ($request->all() as $key => $value) {
                if (in_array($key, ['name', 'age'])) {
                    $data_to_update[$key]=$value;
                }
            }
            if (!empty(request('profile_image'))) {
                unlink(storage_path('app/public/users/'.strval($user->_id).'/profile//'.$user->profile_image));
                $data_to_update['profile_image']=ImageUploading::imageUploading(request('profile_image'),'users',strval($user->_id));

            }
            $user->update($data_to_update);
            $response_data['data']['User_data']=new UserResource($user);
            $response_data['message']='Update successfully!!';
            return response()->success($response_data,200);

        }
        catch (\Exception $e) {
            return response()->json(['error'=>$e->getMessage()], 500);
        }
    }

    /**
     * Update Password api
     *
     * @return \Illuminate\Http\Response
    */
    public function updateUserPassword(UpdatePasswordRequest $request, $id){
        try{
            $user=request()->user_data;
            $user->password=bcrypt(request('password'));
            $user->update();
            $response_data['data']['User_data']=new UserResource($user);
            $response_data['message']='Password Updated!!';
            return response()->success($response_data,200);
        }
        catch (\Exception $e) {
            return response()->error(['message'=>$e->getMessage()], 500);
        }
    }

    /**
     * Deactivate user api
     *
     * @return \Illuminate\Http\Response
    */
    public function DeleteUser($id){
        try{
            $user=request()->user_data;
            if($user){
                $user->delete($user);
                return response()->success(['data'=>null,"message" => "Deactivated Successfully !!"],200);
            }
            else{
                return response()->error(['message'=>"User not Exists"], 400);
            }
        }
        catch (\Exception $e) {
            return response()->error(['message'=>$e->getMessage()], 500);

        }
    }

    /**
     * Activate user api
     *
     * @return \Illuminate\Http\Response
    */
    public function ActiveUser(DeleteUserRequest $reqeust,$id){
        try{
            $user=request()->user_data;
            if($user){
                $user->restore($user);
                return response()->success(['data'=>null,"message" => "Activated Successfully !!"],200);
            }
            else{
                return response()->error(['message'=>"User not Exists"], 400);
            }
        }
        catch (\Exception $e) {
            return response()->error(['message'=>$e->getMessage()], 500);

        }
    }
}
