<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    // Semua rute web dilindungi CSRF secara default (Laravel bawaan).
    // Tambahkan URI di sini hanya jika benar-benar perlu dikecualikan (mis. webhook).
    protected $except = [];
}
