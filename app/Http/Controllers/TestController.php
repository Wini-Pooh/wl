<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test()
    {
        return view('test', [
            'message' => 'Система документооборота работает!',
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
    }
}
