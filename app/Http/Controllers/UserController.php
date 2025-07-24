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
        return response()->json($login, 200);
    }

    public function register(Request $request) {
        $req = $request->all();
        $register = $this->_userDao->register($req);
        return response()->json($register, 200);
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
