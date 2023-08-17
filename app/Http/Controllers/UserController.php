<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string',
            'lastname' => 'nullable|string',
            'birthdate' => 'nullable|string',
            'username' => 'nullable|string|unique:users,username,' . $request->user()->id,
            'email' => 'nullable|email|unique:users,email,' . $request->user()->id,
        ]);

        if ($validator->fails()) {
            return response()->json(["data" => ['error_message' => $validator->errors(), 'message' => 'Error al registrarse']], 422);
        }

        $user = $request->user();
        $update = $user->update($validator->validated());
        if ($update) {
            if ($request->hasFile('image')) {
                $user->image = $this->uploadFile($request->file('image'));
                $user->save();
            }
            return response()->json(['data' => $user], 200);
        }

        return response()->json(["data" => ['message' => 'Error al actualizar']], 422);
    }

    public function me(Request $request)
    {
        return response()->json(['data' => $request->user()], 200);
    }

    protected function uploadFile($file, $path = "uploads")
    {
        $fileName = time() . $file->getClientOriginalName();
        $file->move(public_path($path), $fileName);
        return $fileName;
    }
}
