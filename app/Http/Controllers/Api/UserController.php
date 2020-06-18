<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Illuminate\Support\Facades\Hash;

use App\User;
use App\Book;

use Validator;
use Auth;

class UserController extends Controller {

    
    /**
     * Registration Module
    */
    public function register(Request $request)
    {
        $result = [];

        try{
            $rules = array(
                'name'              => 'required',
                'email'             => 'required|email',
                'mobile_no'         => 'required',
                'password'          => 'required',
            );

            $valid = Validator::make($request->all(), $rules);

            if ($valid->fails()) {

                $messages           = $valid->messages();
                $result['status']   = 0;
                $result['message'] = $messages;
                return response()->json($result);

            } else {
                $request_data  = $request->Input();
                            

                $users = User::where('email',$request_data['email']);

                //Email is already exists
                if ($users->exists() > 0) {
                    $result['status']     = 0;
                    $result['message']    = "User already exists";


                } else {
                    $request_data['password'] = Hash::make($request_data['password']);
                    User::create($request_data);
                    $result['status']   = 1;
                    $result['message']  = "Success";
                }
                                  
            }
        } catch (\Exception $ex){

            $result['status']  = 0;
            $result['message'] = 'Error:'.$ex->getMessage();
        }
        return response()->json($result,200);
    }

        
    /**
     * Login Module
     *
     * Logged the app using email and password 
     */
    public function login(Request $request)
    {   
        $result = [];

        try {
            $rules = array(
                'email'         => 'required|email',
                'password'      => 'required',
            );

            $valid = Validator::make($request->all(),$rules);

            if ($valid->fails()) {

                $messages           = $valid->messages();
                $result['status']   = 0;
                $result['message']  = $messages;
                return response()->json($result);

            } else{
                $request_data = $request->Input();

                $user_instance = User::where('email', $request_data['email']);
                if ($user_instance->exists() > 0) {

                    $user_details = $user_instance->get()->first();
                    // print_r($user_details);die;
                    if(Hash::check($request_data['password'], $user_details['password'])) {

                        $token =  $this->getuserToken();
                        $user_instance->update(['login_status' => (int)1, 'token' => (string)$token]);

                        $result['status']               = 1;
                        $result['message']              = "Success";
                        $result['users']['token']       = $token;

                    } else {
                        $result['status']  = 0;
                        $result['message'] = "Invalid Password";
                    }
                } else {
                    $result['status']  = 0;
                    $result['message'] = "Invalid Email";
                } 
            }
        } catch (\Exception $ex){

            $result['status']  = 0;
            $result['message'] = 'Error:'.$ex->getMessage();
        }
        return response()->json($result,200);
    }
    /*
    View all books
    */

    public function view_books(){
        try{

            $data = Book::orderBy('created_at', 'desc');
            $book_details = $data->get();

            $result['books']  = $book_details;
            $result['status']  = 1;
            $result['message'] = "Success";

        } catch (\Exception $ex){

            $result['status']  = 0;
            $result['message'] = 'Error:'.$ex->getMessage();
        }
        return response()->json($result,200);
      
    }

    /*
    Subcribe  books
    */

    public function subscribe_book(Request $request){
        try{
            $rules = array(
                'book_id'    => 'required',
            );

            $valid = Validator::make($request->all(), $rules);

            if ($valid->fails()) {

                $messages           = $valid->messages();
                $result['status']   = 0;
                $result['message'] = $messages;
                return response()->json($result);

            } else {
                $request_data = $request->Input();

                $users = User::where('id', Auth::User()->id);
                if($users->count()>0){

                    $userdata = $users->get()->toArray();
                    $current_book = json_decode($userdata[0]['sub_book']); 
                    $book_id= $request_data['book_id'];
                    if(empty($current_book) ){
                        $current_book = array();
                        array_push($current_book, (int)$book_id);
                        $users->update(['sub_book' => $current_book]);
                        Book::where('id', $book_id)->update(['subscription' => (int)1]);
                        $result['status'] = 1 ;
                        $result['message'] ="success";         
                    } else if(in_array($book_id, $current_book)){
                        $result['status']  = 0;
                        $result['message'] = "Book already Subscribed";
                    } else {
                        array_push($current_book, (int)$request_data['book_id']);
                        $users->update(['sub_book' => $current_book]);
                        Book::where('id', $book_id)->update(['subscription' => (int)1]);
                        $result['status'] = 1 ;
                        $result['message'] ="success";
                    }
                } else {
                    $result['status']  = 0;
                    $result['message'] = "Invalid User";
                } 


            }

        } catch (\Exception $ex){

            $result['status']  = 0;
            $result['message'] = 'Error:'.$ex->getMessage();
        }
        return response()->json($result,200);
    }

    public function getuserToken() {
        $token = md5(rand() . microtime()) . bin2hex(random_bytes(32));
        return $token;
    }
    
}
