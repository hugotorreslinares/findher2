<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Profile;
use App\Category;
use Illuminate\Support\Facades\Auth;
use Debugbar;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function admin(){

        $users = User::all();
        return view('admin.admin')->withUsers($users) ;
    }
    public function newCategory(Request $request){
        Debugbar::info($request);
        $validatedData = $request->validate([
            'id' => 'nullable|integer',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'image' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);

        try {
            $category = Category::updateOrCreate(
                ['id' => $request->id],
                $validatedData
            );
        } catch (\Throwable $th) {
            throw $th;
        }

        return redirect()->route('admin.categories' );

    }

    public function profileImageDelete(Request $request){
        $imageDelete = $request->image;

        $profile = Profile::where('user_id', '=', Auth::user()->id)->firstOrFail();
      $allImages = (array)json_decode($profile->image);

            // Search
            $pos = array_search($imageDelete, $allImages);


            // Remove from array
            unset($allImages[$pos]);


            $profile->image = json_encode($allImages);



        $profile->save();
        return response()->json(['success'=>'You have successfully deleted a file ','profile'=>$profile]);

    }

    public function users(){
        $profiles =Profile::all();
        $users = User::all();
        return view('admin.users')->withUsers($users)->withProfiles($profiles);
    }

    public function categories(){

        $categories = Category::all();

        return view('admin.categories')->withCategories($categories) ;
    }

    public function profile($id){
        Auth::user()->id;
        $user = User::find($id);
        $profile = Profile::where('user_id', '=', $user->id)->firstOrFail();
        $categories = Category::all();

        $today = date("Y-m-d");
        $diff = date_diff(date_create($user->bday), date_create($today));
        $profile['age']=$diff->format('%y');

        if($user->id === Auth::user()->id){
            return view('user.profile')->withUser($user)->withProfile($profile)->withCategories($categories);
        }

        else{
             return redirect()->back();
        }

    }

    public function uploadOne(UploadedFile $uploadedFile, $folder = null, $disk = 'public', $filename = null)
    {
        $name = !is_null($filename) ? $filename : Str::random(25);

        $file = $uploadedFile->storeAs($folder, $name.'.'.$uploadedFile->getClientOriginalExtension(), $disk);

        return $file;
    }
    public function deleteOne($folder = null, $disk = 'public', $filename = null)
    {
        Storage::disk($disk)->delete($folder.$filename);
    }
    public function upload(Request  $request){
     $request->validate([
         'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
     ]);

     $uploadedFile = $request->file('image');
     $filename = time().$uploadedFile->getClientOriginalName();

     Storage::disk('local')->putFileAs(
       'uploads/',
       $uploadedFile,
       $filename
     );



     return response()->json(['success'=>'You have successfully uploaded a file '.$filename]);

    }

    public function store(Request $request)
    {
        if($request->ajax()) {
            $validatedData = $request->validate([
                'id' => 'nullable|integer',
                'latitude' => 'nullable|string',
                'longitude' => 'nullable|string',
                'text' => 'nullable|string',
                'description' => 'nullable|string',
                'url_instagram' => 'nullable|string',
                'url' => 'nullable|string',
                'category_id' => 'nullable|integer',
                'phone_number' => 'nullable|string',
                'whatsapp' => 'nullable|string',
                'tarifa' => 'nullable|string',
                'horario' => 'nullable|string',
                'servicios' => 'nullable|string',
                'tags' => 'nullable|string',
                'rating' => 'nullable|integer',
                'gender' => 'nullable|string',
                'image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            $uploadedFiles = $request->file('image');
            $user =Auth::user();
            $profile =Profile::where('user_id', $user->id)->firstOrFail();
            $oldFiles = (array)(json_decode($profile->image)===null?[]:json_decode($profile->image));
            $fileNames=$oldFiles;
            $i=0;
            if($uploadedFiles)
            foreach ($uploadedFiles as &$uploadedFile) {


                $fileNameNew=  preg_replace("/[^a-z0-9\_\-\.]/i", '', basename($uploadedFile->getClientOriginalName()));
                $filename = time().$fileNameNew;
                Storage::disk('public')->putFileAs(
                  'profile',
                  $uploadedFile,
                  $filename
                );
               // $route= storage_path($url);
                $usrImage=$filename;
                array_push( $fileNames,$filename);
                $i++;
            }
            Debugbar::info($i);

            $profileData = $validatedData;
            $profileData['user_id'] = $user->id;

            $today = date("Y-m-d");
            $diff = date_diff(date_create($user->bday), date_create($today));

            $profileData['age']=$diff->format('%y');
            if($uploadedFiles)
            $profileData['image']=json_encode($fileNames);

            $profile = Profile::updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );

            if(isset($usrImage)){
                $user->foto =$usrImage;
                $user->save();
            }
            return response()->json(['success'=>'You have successfully created the profile','profile'=>$profile]);

        }
    }
    public function edit($id){
        $user = User::find($id);
        if($user){
            return view('user.profile')->withUser($user);
        }
        else{
             return redirect()->back();
        }
    }
    public function update(){

    }
    public function passwordEdit(){
        $user = Auth::user();
        if($user){
            return view('user.profile')->withUser($user);
        }
        else{
             return redirect()->back();
        }
    }
    public function passwordUpdate(){}
}
