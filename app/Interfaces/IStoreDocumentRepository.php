<?php

namespace App\Interfaces;

use App\Interfaces\IBaseStoreRepository;

interface IStoreDocumentRepository extends IBaseStoreRepository
{
    public function setDocumentDataTable($request);
    public function getDocumentDataTable() : array;
    public function storeDocument($request);
    public function deleteDocument($id);
}