<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Illuminate\Support\Facades\Hash;

use App\Book;
use App\User;

use Validator;
use Auth;
use Excel;

class AdminController extends Controller {
    
    /**
     * Book details add
    */   
    public function add_book(Request $request)
    {
        $result = [];

        try{
            $rules = array(
                'book_name'    => 'required',
                'author'       => 'required',
                'year'         => 'required',
            );

            $valid = Validator::make($request->all(), $rules);

            if ($valid->fails()) {

                $messages           = $valid->messages();
                $result['status']   = 0;
                $result['message'] = $messages;
                return response()->json($result);

            } else {
                $request_data  = $request->Input();
                            

                $users = Book::where('book_name',$request_data['book_name']);

                //Email is already exists
                if ($users->exists() > 0) {
                    $result['status']     = 0;
                    $result['message']    = "Book already exists";


                } else {
                    Book::create($request_data);
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

    /*
    Book Count
    */

    public function book_count(){
    	try{

    		$book = Book::count();

    		$result['book_count'] = $book;
    		$result['status'] = 1 ;
            $result['message'] ="success";
    	} catch (\Exception $ex){

            $result['status']  = 0;
            $result['message'] = 'Error:'.$ex->getMessage();
        }
        return response()->json($result,200);
    }

    /*
    All subscrption book count
    */

 	public function sub_count(){
    	try{

    		$sub_count = Book::where('subscription', (int)1)->count();

    		$result['subscription_count'] = $sub_count;
    		$result['status'] = 1 ;
            $result['message'] ="success";
    	} catch (\Exception $ex){

            $result['status']  = 0;
            $result['message'] = 'Error:'.$ex->getMessage();
        }
        return response()->json($result,200);
    }

    /*
    User subscribed book details
    */

	public function user_book_sub(){
    	try{

			$user = User::orderBy('created_at', 'desc')->get()->toArray();
			foreach ($user as $key => $value) {
				$book_detail = Book::where('id', $value);
				
			}
			print_r($book_detail->get());
    		$result['status'] = 1 ;
            $result['message'] ="success";
    	} catch (\Exception $ex){

            $result['status']  = 0;
            $result['message'] = 'Error:'.$ex->getMessage();
        }
        return response()->json($result,200);
    }
    /*
	bulk upload file
    */
    public function bulk_upload(Request $request){
        $result = [];
        try{
            if(\Request::hasFile('import_file')) {
                $data = Excel::toCollection(new \App\Book, $request->file('import_file'));
                $data_array = $data[0]->toArray();
                unset($data_array[0]);
                if(!empty($data_array))
                {
                    foreach($data_array as $key => $value)
                    {
                        $book = Book::where('book_name', $value[1])->count();
                        if($book == 0){
                            $insertArray = array();
                            $insertArray['book_name'] = $value[1];
                            $insertArray['author'] = $value[2];
                            $insertArray['year'] = $value[3];
                            $insertArray['subscription'] = $value[4];

                            $book = Book::create($insertArray);
                            unset($data_array[$key]);
                            $result['status'] =1;
                            $result['message'] ="Successfully Insert";

                        } else {
                            $result['status'] =0;
                            $result['message'] ="book already exists";
                        }
                    }
                } else {
                    $result['status'] =0;
                    $result['message'] ="No record found";
                }
            } else {
                $result['status'] =0;
                $result['message'] ="File not found";
            }
        } catch (\Exception $ex){

            $result['status']  = 0;
            $result['message'] = 'Error:'.$ex->getMessage();
        }
            
            return response()->json($result,200);  
    }


}
