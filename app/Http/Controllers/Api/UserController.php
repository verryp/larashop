<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::paginate(5);

        $filterKeyword = $request->get('keyword');
        $status = $request->get('status');

        // if($filterKeyword){
            if($status){
                $users = User::where('email', 'LIKE', "%$filterKeyword%")
                    ->where('status', $status)
                    ->paginate(5);
            } else {
                $users = User::where('email', 'LIKE', "%$filterKeyword%")
                        ->paginate(5);
            }
        // }

        // if($status)
        //     $users = User::where('status', $status)->paginate(5);
        // else
        //     $users = User::paginate(5);

        return view('users.index', compact('users'));
                // ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("users.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:5|max:10',
            'username' => 'required|min:5|max:20|unique:users',
            'roles' => 'required',
            'phone' => 'numeric|digits_between:10,12|unique:users',
            'address' => 'required|min:10|max:200',
            'avatar' => 'required|image',
            'email' => 'email|unique:users',
            'password' => 'required',
            'password_confirmation' => 'required|same:password'
        ]);

        //CARA PERTAMA
        $new_user = new User; 

        $new_user->name = $request->get('name');
        $new_user->username = $request->get('username');
        $new_user->roles = json_encode($request->get('roles'));
        $new_user->name = $request->get('name');
        $new_user->address = $request->get('address');
        $new_user->phone = $request->get('phone');
        $new_user->email = $request->get('email');
        $new_user->password = \Hash::make($request->get('password'));

        //CARA KEDUA
        // ! Entah kenapa ini masih agak error
        // $new_user = new User([
        //     'name' => $request->get('name'),
        //     'username' => $request->get('username'),
        //     'roles' => json_encode($request->get('roles')),
        //     'name' => $request->get('name'),
        //     'address' => $request->get('address'),
        //     'phone' => $request->get('phone'),
        //     'email' => $request->get('email'),
        //     'password' => \Hash::make($request->get('password')),
        // ]);

        if($request->file('avatar')){
            $file = $request->file('avatar')->store('avatars', 'public');
        
            $new_user->avatar = $file;
        }

        $new_user->save();

        //CARA KETIGA
        // * mungkin ini perlu jika tidak membutuhkan request yang spesifik
        //User::create($request->all());

        //CARA KEEMPAT
        // * agar ribet seperti cara pertama dan kedua
        // $new_user = [
        //     'name' => $request->get('name'),
        //     'username' => $request->get('username'),
        //     'roles' => json_encode($request->get('roles')),
        //     'name' => $request->get('name'),
        //     'address' => $request->get('address'),
        //     'phone' => $request->get('phone'),
        //     'email' => $request->get('email'),
        //     'password' => \Hash::make($request->get('password')),
        // ];

        // if($request->file('avatar')){
        //     $file = $request->file('avatar')->store('avatars', 'public');
        
        //     $new_user->avatar = $file;
        // }

        // DB::transaction(function() use ($new_user){
        //     User::create($new_user);
        // });

        return redirect()->route('users.index')->with('success', 'User successfully created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|min:5|max:100',
            'roles' => 'required',
            'address' => 'required|min:10|max:200',
            'phone' => 'numeric|digits_between:10,12',
        ]);

        $user = User::findOrFail($id);

        $user->name = $request->get('name');
        $user->roles = json_encode($request->get('roles'));
        $user->address = $request->get('address');
        $user->phone = $request->get('phone');
        $user->status = $request->get('status');

        if($request->file('avatar')){
            if($user->avatar && file_exists(storage_path('app/public/' . $user->avatar))){
                \Storage::delete('public/'.$user->avatar);
            }

            $file = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $file;
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'User succesfully updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User successfully delete');
    }
}

