<?php

namespace App\Http\Controllers;

use App\Dao\UserDao;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $_userDao;

    public function __construct(UserDao $userDao)
    {
        $this->_userDao = $userDao;
    }

    public function login(Request $request) {
        $req = $request->all();
        $login = $this->_userDao->login($req);
        if (!$login['success']) {
            return response()->json($login, 400);
        }
        return response()->json($login, 200);
    }

    public function register(Request $request) {
        $req = $request->all();
        $register = $this->_userDao->register($req);
        if (!$register['success']) {
            return response()->json($register, 400);
        }
        return response()->json($register, 201);
    }

    public function logout() {
        $logout = $this->_userDao->logout();
        return response()->json($logout, 200);
    }

    public function me() {
        $me = $this->_userDao->getUser();
        return response()->json($me, 200);
    }
}
