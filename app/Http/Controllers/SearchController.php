<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Interfaces\SearchInterface;

class SearchController extends Controller
{
    private SearchInterface $repository;

    public function __construct(SearchInterface $repository)
    {
        $this->repository = $repository;
    }

    public function search($name, Request $request)
    {
        $call = str_replace('-', '', ucwords($name, '-'));
        $call = 'search'.$call;
        // dd($this->repository->searchPharmacyStaff());
        return $this->repository->$call($request);
    }
}
