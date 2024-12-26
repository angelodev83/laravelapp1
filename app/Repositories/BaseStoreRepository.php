<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Auth;

class BaseStoreRepository
{
    protected $authUser;
    
    protected const BASE_PATH = 'upload/stores';

    public function __construct()
    {
        $this->authUser = Auth::user();
    }
}