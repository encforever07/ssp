<?php

namespace App\Http\Controllers;

use Adldap\Laravel\Facades\Adldap;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function index()
    {
        $users = User::orderBy('username', 'ASC')->paginate(10);
        return [
            'usuarios' => $users
        ];

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search($field, $value)
    {
        $users = User::where($field, 'ilike', "%{$value}%")->paginate(10);
        return $users;
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $usuario = $request->get('username');
        if (Adldap::auth()->attempt(env('USERLDAP'), env('PASSLDAP'), $bindAsUser = true)) {
            $userldap = $usuario . "@ensena.intl.local";
            $ldapuser = Adldap::search()->findBy('userPrincipalName', $userldap);
//            return $ldapuser;
            if ($ldapuser) {
                $user = new User();
                $user->name = "-";
                $user->username = $request->get('username');
                $user->password = '';
                $user->email = '-';
                $user->department = '';
                $user->office = '';
                $user->rol = $request->get('rol');
                $user->isActive = true;
                $user->detail = null;
                $user->save();
                return response()->json($user);
            } else {
                return response()->json('error',404);
            }
        }

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $user->fill($request->all());
        $user->save();
        return response()->json($user);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = User::findOrFail($id);
        User::findOrFail($id)->delete();
    }
}
