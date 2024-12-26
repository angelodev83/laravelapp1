<?php

namespace App\Interfaces;

interface IHistoriesRepository 
{
    public function store_historyV2($history_header, $history_body, $order, $id);
    public function update_historyV2($history_header, $history_body, $order, $id);
    public function delete_history($history_header, $history_body, $order, $id);
}