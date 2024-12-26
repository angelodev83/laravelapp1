<?php

namespace App\Interfaces;

interface IBaseRepository
{
    public function setDataTable($request);
    public function getDataTable() : array;

    public function store($request);
    public function update($request);
    public function delete($id);
    
    public function search($request);
}