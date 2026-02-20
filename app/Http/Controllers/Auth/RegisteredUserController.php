<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisteredUserController extends Controller
{
    public function create(): never
    {
        throw new HttpResponseException(response()->notFound());
    }

    public function store(): never
    {
        throw new HttpResponseException(response()->notFound());
    }
}
