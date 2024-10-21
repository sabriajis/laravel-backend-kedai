<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //membuat agar bisa mengambil data base dan membuat fungsi cari berfungsi
        $users = DB::table('users')->when($request->input('name'),function($query,$name){
            return $query->where('name','like', '%' . $name . '%');
        })
        ->orderby('created_at','desc')
        ->paginate(5);
        return view('pages.user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.user.create');
    }

    /**
     * Store a newly created resource in storage.
     */

     //membuat inputan data
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'phone'=> 'required',
            'roles' => 'required',
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'roles' => $request->roles,
        ]);
        return redirect()->route('user.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view ('pages.dashboard');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
       $user = User::findOrFail($id);
       return view('pages.user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //update data
       $data = $request->all();
       $user = User::findOrFail($id);

       //cek password kalau kosong
       if($request->input('password')){
        $data['password'] = Hash::make($request->input('password'));
       }else{
        //membuat password agar tidak bisa kosong
        $data['password']= $user->password;
       }

       $user->update($data);
       return redirect()->route('user.index')->with('success', 'User Berhasil update successfully.');



    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('user.index')->with('success', 'User deleted successfully');
    }
}
