<?php

namespace App\Interfaces;

interface IDocumentRepository
{
    public function setDataTable($request, $is_audit);
    public function getDataTable() : array;

    public function store($request, $pharmacy_store_id, $is_audit);
    public function delete($id);
}