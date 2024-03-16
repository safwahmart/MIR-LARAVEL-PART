<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UnitResource;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $units = User::all();
        return UnitResource::collection($units);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $brands = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return new UnitResource($brands);
    }
    public function storePermission(Request $request)
    {
        $permissions = $request->permissions;
        $user_id = $request->user_id;
        // return $request;
        // $validator = Validator::make($request->all(), [
        //     'name' => 'required|string|max:255',
        //     'email' => 'required|string|email|max:255|unique:users',
        //     'password' => 'required|confirmed|min:6',
        // ]);

        // if ($validator->fails()) {
        //     return response(['errors' => $validator->errors()->all()], 422);
        // }
        // $brands = User::create([
        //     'name' => $request->name,
        //     'email' => $request->email,
        //     'password' => bcrypt($request->password),
        // ]);

        // return new UnitResource($brands);

        foreach ($permissions as $req) {
            $parentName = $req['name'];
            foreach ($req['fields'] as $permission) {
                // return $permission['checked'];
                // if ($permission->checked == true) {
                //     Permission::create([
                //         'name' => $parentName,
                //         'guard_name' => $permission->name
                //     ]);
                // }
                if (array_key_exists('checked', $permission)) {
                    if ($permission['checked'] == true) {
                        Permission::create([
                            'name' => $parentName,
                            'user_id' => $user_id,
                            'guard_name' => $permission['name']
                        ]);
                    }
                }
            }
        }
        // return 'save successfully';
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $unit
     * @return \Illuminate\Http\Response
     */
    public function show(User $unit)
    {
        $unit = User::find($unit->id);

        if (is_null($unit)) {
            return $this->sendError('Brand not found.');
        }
        $response = ['status' => true, 'data' => $unit];
        return response($response, 200);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $unit
     * @return \Illuminate\Http\Response
     */
    public function edit(User $unit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $unit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $unit = User::find($id);
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $unit->update($request->all());

        return new UnitResource($unit);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $unit
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $unit = User::find($id);
        $unit->delete();
        $response = ['status' => true, 'message' => 'Deleted successfully.'];
        return response($response, 200);
    }
}
