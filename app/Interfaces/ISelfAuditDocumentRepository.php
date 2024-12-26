<?php

namespace App\Interfaces;

interface ISelfAuditDocumentRepository
{
    public function setDataTable($request);
    public function getDataTable() : array;

    public function store($request);
    public function delete($id);
}