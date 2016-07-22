<?php
/**
 * Created by PhpStorm.
 * User: tthrockmorton
 * Date: 6/20/2016
 * Time: 3:37 PM
 */

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $redirectPath = '/home';
    protected $loginPath = '/auth/login';

}