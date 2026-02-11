<?php
namespace App\Domains\Wilayah\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Wilayah\UseCases\GetWilayahByUser;

class WilayahController extends Controller
{
    public function __construct(
        protected GetWilayahByUser $getWilayah
    ) {}

    public function index()
    {
        return $this->getWilayah->handle(auth()->user());
    }
}
