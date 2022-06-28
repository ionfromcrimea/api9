<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Right;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function createNewTokenForActiveUser(Request $request)
    {
//        dd($request, $request['right1'], isset($request['right' . '1']));
        $rightsArray = [];
        $rightsCollection = Right::all();
        foreach ($rightsCollection as $right) {
            if (isset($request['right' . (string)$right->id]))
                $rightsArray[] = $right->right;
        }

//        dd($rightsArray);

//        $token = $request->user()->createToken($request->token_name, ['server:update', 'clients:edit']);
        $token = $request->user()->createToken($request->token_name, $rightsArray);
//        return ['token' => $token->plainTextToken];
        $plainToken = $token->plainTextToken;
        return view('showtoken', compact('plainToken'));
    }
}
