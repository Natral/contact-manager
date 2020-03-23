<?php

namespace App\Http\Controllers\User;

use App\Contacts;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;

class ContactController extends Controller
{
    //
    protected $contacts;
    protected $base_url;

    /**
     * ContactController constructor.
     * @param UrlGenerator $urlGenerator
     */
    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->middleware("auth:users");
        $this->contacts = new Contacts();
        $this->base_url = $urlGenerator->to("/");
    }

    /**
     * This endpoint is to create a new contact specific to a user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add_contact(Request $request)
    {
        $validation = \Validator::make($request->all(),[
            "token"         => 'required',
            "first_name"    => 'required|string',
            "contact_number"=> 'required|string'
        ]);

        if($validation->fails()){
            return response()->json([
                "success"   => false,
                "message"   => $validation->errors()->toArray()
            ],500);
        }

        $contact_avatar = $request->contact_avatar;
        $file_name = "";
        if($contact_avatar == null){
            $file_name = "default-avatar.png";
        }else{
            $generate_name = uniqid()."_".time().date("Ymd")."_IMG";
            $base64Image = $contact_avatar;
            $fileBin = file_get_contents($base64Image);
            $mime_type = mime_content_type($base64Image);
            switch ($mime_type){
                case "image/png":
                    $file_name = $generate_name.".png";
                    break;
                case "image/jpeg":
                    $file_name = $generate_name.".jpeg";
                    break;
                case "image/jpg":
                    $file_name = $generate_name.".jpg";
                    break;
                default:
                    return response()->json([
                        "success" => false,
                        "message" => "Only png, jpg, and jpeg are accepted as profile pictures"
                    ], 500);
            }
        }

        $user_token = $request->token;
        $user = auth("users")->authenticate($user_token);

        $this->contacts->user_id = $user->id;
        $this->contacts->contact_number = $request->contact_number;
        $this->contacts->first_name = $request->first_name;
        $this->contacts->last_name = $request->last_name;
        $this->contacts->email = $request->email;
        $this->contacts->contact_avatar = $file_name;
        $this->contacts->save();

        if($contact_avatar != null){
            file_put_contents("./contact_avatars/".$file_name, $fileBin);
        }

        return response()->json([
            "success"   => true,
            "message"   => "Contact saved successfully"
        ], 200);

    }

    /**
     * This endpoint will return contacts for a specific user
     *
     * @param $token
     * @param null $pagination
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_paginated_data($token, $pagination=null)
    {
        $file_directory = $this->base_url."/contact_avatars";
        $user = auth("users")->authenticate($token);
        $user_id = $user->id;

        if($pagination == null || $pagination == ""){
            $contacts = $this->contacts->where("user_id",$user_id)
                                        ->orderBy("id","DESC")
                                        ->get()
                                        ->toArray();
            return response()->json([
                "success"           => true,
                "data"              => $contacts,
                "file_directory"    => $file_directory
            ], 200);
        }

        $contacts_paginated = $this->contacts->where("user_id", $user_id)
                                                ->orderBy("id","DESC")
                                                ->paginate($pagination);

        return response()->json([
            "success"           => true,
            "data"              => $contacts_paginated,
            "file_directory"    => $file_directory
        ], 200);
    }

    /**
     * Update a contact given it's $id
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit_single_data(Request $request, $id)
    {
        $validation =  \Validator::make($request->all(),[
            'first_name'        => 'required|string',
            'contact_number'    => 'required|string'
        ]);

        if($validation->fails()){
            return response()->json([
                "success"   => false,
                "message"   => $validation->errors()->toArray()
            ],500);
        }

        $find_data = $this->contacts::find($id);

        if(!$find_data){
            return response()->json([
                "success"   => false,
                "message"   => "Contact not found"
            ],500);
        }

        $get_file = $find_data->contact_avatar;
        $get_file=="default-avatar.png"? : unlink("./contact_avatars/".$get_file);

        $contact_avatar = $request->contact_avatar;
        $file_name = "";
        if($contact_avatar == null){
            $file_name = "default-avatar.png";
        }else{
            $generate_name = uniqid()."_".time().date("Ymd")."_IMG";
            $base64Image = $contact_avatar;
            $fileBin = file_get_contents($base64Image);
            $mime_type = mime_content_type($base64Image);
            switch ($mime_type){
                case "image/png":
                    $file_name = $generate_name.".png";
                    break;
                case "image/jpeg":
                    $file_name = $generate_name.".jpeg";
                    break;
                case "image/jpg":
                    $file_name = $generate_name.".jpg";
                    break;
                default:
                    return response()->json([
                        "success" => false,
                        "message" => "Only png, jpg, and jpeg are accepted as profile pictures"
                    ], 500);
            }
        }

        $find_data->contact_number = $request->contact_number;
        $find_data->first_name = $request->first_name;
        $find_data->last_name = $request->last_name;
        $find_data->email = $request->email;
        $find_data->contact_avatar = $file_name;
        $find_data->save();

        if($contact_avatar != null){
            file_put_contents("./contact_avatars/".$file_name, $fileBin);
        }

        return response()->json([
            "success"   => true,
            "message"   => "Contact updated successfully"
        ], 200);
    }

    /**
     * Delete a contact given it's $id
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function delete_contact($id)
    {
        $find_data = $this->contacts::find($id);

        if(!$find_data){
            return response()->json([
                "success"   => false,
                "message"   => "Contact not found"
            ],500);
        }

        $get_file = $find_data->contact_avatar;
        if($find_data->delete()){
            $get_file=="default-avatar.png"? : unlink("./contact_avatars".$get_file);

            return response()->json([
                "success"   => true,
                "message"   => "Contact deleted successfully"
            ], 200);
        }else{
            return response()->json([
                "success"   => false,
                "message"   => "Unexpected error, try again."
            ],500);
        }
    }

    /**
     * Return a single contact
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_single_data($id)
    {
        $find_data = $this->contacts::find($id);

        if(!$find_data){
            return response()->json([
                "success"   => false,
                "message"   => "Contact not found"
            ],500);
        }

        $file_directory = $this->base_url."/contact_avatars";

        return response()->json([
            "success"           => true,
            "data"              => $find_data,
            "file_directory"    => $file_directory
        ], 200);
    }

    /**
     * Search contacts endpoint
     *
     * @param $search
     * @param $token
     * @param null $pagination
     * @return \Illuminate\Http\JsonResponse
     */
    public function search_data($search, $token, $pagination=null)
    {
        $file_directory = $this->base_url."/contact_avatars";
        $user = auth()->authenticate($token);
        $user_id = $user->id;
        if($pagination == null || $pagination==""){
            $non_paginated_search_query = $this->contacts::where("user_id", $user_id)
                                                            ->where(function($query) use ($search){
                                                                $query->where("first_name","LIKE","%$search%")
                                                                    ->orwhere("last_name","LIKE","%$search%")
                                                                    ->orwhere("email","LIKE","%$search%")
                                                                    ->orwhere("contact_number","LIKE","%$search%")
                                                                    ->orderBy("id","DESC");
                                                            })
                                                            ->get()
                                                            ->toArray();

            return response()->json([
                "success"           => true,
                "data"              => $non_paginated_search_query,
                "file_directory"    => $file_directory
            ], 200);
        }

        $paginated_search_query = $this->contacts::where("user_id", $user_id)
                                                    ->where(function($query) use ($search){
                                                        $query->where("first_name","LIKE","%$search%")
                                                            ->orwhere("last_name","LIKE","%$search%")
                                                            ->orwhere("email","LIKE","%$search%")
                                                            ->orwhere("contact_number","LIKE","%$search%")
                                                            ->orderBy("id","DESC");
                                                    })
                                                    ->paginate($pagination);

        return response()->json([
            "success"           => true,
            "data"              => $paginated_search_query,
            "file_directory"    => $file_directory
        ], 200);

    }
}
