<?php

namespace App\Interfaces;

interface IBaseStoreRepository
{
    public function setDataTable($request);
    public function getDataTable() : array;

    public function store($request, $pharmacy_store_id);
    public function update($request, $pharmacy_store_id);
    public function delete($id);
    
    public function search($request);
}