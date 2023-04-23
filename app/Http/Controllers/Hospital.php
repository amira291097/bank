<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HospitalS;

class hospital extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $hospitals = Hospital::all();
        return view('hospitals\index' , ['hospitals' => $hospitals]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //Bank::create(['hosoital_id' => $hospital[0]->id]);
        return view('hospitals\create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $attributes = request()->validate([
            'name' => ['required', 'max:50'],
            'email' => ['required', 'email', 'max:50', Rule::unique('users', 'email')],
            'password' => ['required', 'min:5', 'max:20'],
            'phone' => ['required', 'min:10', 'max:12'],
            'location' => ['required', 'address', Rule::unique('users', 'address')],
        ]);
        $attributes['password'] = bcrypt($attributes['password'] );
        $attributes['role'] = 'Hospital';
        $user = User::create($attributes);
        Hospital::create(['hospital_id' => $user->id]);
        Bank::create(['hospital_id' => $user->id,'type'=>0 ,'amount'=>0]);
        session()->flash('success', 'Hospital created successfully');
        return redirect('hospitals');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

     /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,Hospital $hospital)
    {
        $data = $request->only(['name', 'email', 'phone', 'role']);
        $user = User::where('id' , $hospital->user_id)->get();
        if ($request->role != 'Hospital') {
            $hospital->delete();
            switch ($request->role) {
                case 'Admin':
                    Admin::create(['user_id' => $user[0]->id]);
                    break;
                case 'Tester':
                    Tester::create(['user_id' => $user[0]->id]);
                    break;
                case 'Client':
                    Client::create(['user_id' => $user[0]->id]);
                    break; 
                case 'Commissary':
                    Commissary::create(['user_id' => $user[0]->id]) ; 
                    break; 
                }
            }
        }
    /**
     * Remove the specified resource from storage.
     */
   /* public function destroy(string $id)
    {
        //
    }*/
    
    public function removeHospital(Request $request)
    {
        $id = $request->input('userid');
        $hospital = Hospital::where('id' , $id)->first();
        $user = User::where('id' , $hospital->user_id)->get();
       Hospital::destroy($id);
        $user[0]->delete();
        return redirect()->back();
    }
}

