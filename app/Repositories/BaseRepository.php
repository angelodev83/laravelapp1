<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Auth;

class BaseRepository
{
    protected $authUser;
    
    protected const BASE_PATH = 'upload/admin';

    public function __construct()
    {
        $this->authUser = Auth::user();
    }
}