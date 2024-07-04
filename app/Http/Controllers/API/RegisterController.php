<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RegisterController extends BaseController
{
    const ADMIN_EMAIL = 'root@gmail.com';
    const STAFF_EMAIL = ['staff1@gmail.com', 'nhanvien@gmail.com'];
    const ROLE_ADMIN = 1;
    const ROLE_STAFF = 2;
    const ROLE_GUEST = 3;
    /**
     * Register api
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors()->messages());
        }

        $input = $request->all();
        // Assign role based on email
        $input['role_id'] = $this->assignRole($input['email']);
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->plainTextToken;
        $success['name'] =  $user->name;

        return $this->sendResponse($success, 'User register successfully.');
    }

    /**
     * Login api
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')->plainTextToken;
            $success['name'] =  $user->name;

            return $this->sendResponse($success, 'User login successfully.');
        }
        else{
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        }
    }

    /**
     * Assign role based on email.
     *
     * @param string $email
     * @return int
     */
    private function assignRole(string $email): int
    {
        if ($email == self::ADMIN_EMAIL) {
            return self::ROLE_ADMIN;
        } elseif (in_array($email, self::STAFF_EMAIL)) {
            return self::ROLE_STAFF;
        }
        return self::ROLE_GUEST;
    }
}
