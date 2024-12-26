<?php

namespace App\Repositories;

use App\Interfaces\IHistoriesRepository;
use App\Models\Histories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoriesRepository implements IHistoriesRepository
{
    private $history;
    private $user;

    public function __construct(Histories $history)
    {
        $this->history = $history;
        $this->user = Auth::user();
    }

    /**
     * @param Request $request
     * @return $this|false|string
     */

    public function store_history($header, $body, $type, $type_id)
    {   
        $user = Auth::user();
        $name = $user->name .' '. $header['method'] .$header['class'] .$header['name'].$header['id'];
        $history = $this->history;
        $history->name = $name;
        $history->value = json_encode($body, true);
        $history->user_id = $user->id;

        switch ($type) {
            case 'order':
                $history->order_id = $type_id;
                break;
            case 'invoice':
                $history->invoice_id = $type_id;
                break;
            case 'monthly_revenue':
                $history->monthly_revenue_id = $type_id;
                break;
            case 'file':
                $history->file_id = $type_id;
                break;
            case 'inmar':
                $history->inmar_id = $type_id;
                break;
            default:
                # code...
                break;
        }

        $history->save();
    }
    
    public function store_historyV2($header, $body, $type, $type_id)
    {   
        $user = Auth::user();
        $name = ($user->name ?? '') .' '. $header['method'] .$header['class'] .$header['name'].' with id ' .$header['id'];
        $history = $this->history;
        $history->name = $name;
        $history->value = json_encode($body, true);
        $history->user_id = $user->id;

        switch ($type) {
            case 'order':
                $history->order_id = $type_id;
                break;
            case 'invoice':
                $history->invoice_id = $type_id;
                break;
            case 'monthly_revenue':
                $history->monthly_revenue_id = $type_id;
                break;
            case 'file':
                $history->file_id = $type_id;
                break;
            case 'inmar':
                $history->inmar_id = $type_id;
                break;
            default:
                $history->type = $type;
                $history->type_parent_id = $type_id;
                break;
        }

        $history->save();
    }

    public function update_history($header, $body, $type, $type_id)
    {
        $user = Auth::user();
        $name = $user->name .' '. $header['method']. $header['old'] .' to '.$header['new'].' in ' .$header['class'] .' with id ' .$header['id'];
        $history = $this->history;
        $history->name = $name;
        $history->value = json_encode($body, true);
        $history->user_id = $user->id;

        switch ($type) {
            case 'order':
                $history->order_id = $type_id;
                break;
            case 'invoice':
                $history->invoice_id = $type_id;
                break;
            case 'monthly_revenue':
                $history->monthly_revenue_id = $type_id;
                break;
            case 'file':
                $history->file_id = $type_id;
                break;
            case 'inmar':
                $history->inmar_id = $type_id;
                break;
            default:
                # code...
                break;
        }

        $history->save();
    }

    public function update_historyV2($header, $body, $type, $type_id)
    {
        $user = Auth::user();
        $name = $user->name .' '. $header['method']. $header['name'].' in ' .$header['class'] .' with id ' .$header['id'];
        $history = $this->history;
        $history->name = $name;
        $history->value = json_encode($body, true);
        $history->user_id = $user->id;

        switch ($type) {
            case 'order':
                $history->order_id = $type_id;
                break;
            case 'invoice':
                $history->invoice_id = $type_id;
                break;
            case 'monthly_revenue':
                $history->monthly_revenue_id = $type_id;
                break;
            case 'file':
                $history->file_id = $type_id;
                break;
            case 'inmar':
                $history->inmar_id = $type_id;
                break;
            default:
                $history->type = $type;
                $history->type_parent_id = $type_id;
                break;
        }

        $history->save();
    }

    public function delete_history($header, $body, $type, $type_id)
    {
        $user = Auth::user();
        $name = $user->name .' '. $header['method']. $header['name'] .' in ' .$header['class'] .' with id ' .$header['id'];
        $history = $this->history;
        $history->name = $name;
        $history->value = json_encode($body, true);
        $history->user_id = $user->id;

        switch ($type) {
            case 'order':
                $history->order_id = $type_id;
                break;
            case 'invoice':
                $history->invoice_id = $type_id;
                break;
            case 'monthly_revenue':
                $history->monthly_revenue_id = $type_id;
                break;
            case 'file':
                $history->file_id = $type_id;
                break;
            case 'inmar':
                $history->inmar_id = $type_id;
                break;
            default:
                # code...
                break;
        }

        $history->save();
    }

    public function upload_file_history($header, $body, $type, $type_id)
    {
        $user = Auth::user();
        $name = $user->name .' '. $header['method']. ' '. $header['filename'] .' in ' .$header['class'] .' with id ' .$header['id'];
        $history = $this->history;
        $history->name = $name;
        $history->value = json_encode($body, true);
        $history->user_id = $user->id;

        switch ($type) {
            case 'order':
                $history->order_id = $type_id;
                break;
            case 'invoice':
                $history->invoice_id = $type_id;
                break;
            case 'monthly_revenue':
                $history->monthly_revenue_id = $type_id;
                break;
            case 'file':
                $history->file_id = $type_id;
                break;
            case 'inmar':
                $history->inmar_id = $type_id;
                break;
            default:
                # code...
                break;
        }

        $history->save();
    }

    public function download_file_history($header, $body, $type, $type_id)
    {
        $user = Auth::user();
        $name = $user->name .' '. $header['method']. ' '. $header['filename'] .' in ' .$header['class'] .' with id ' .$header['id'];
        $history = $this->history;
        $history->name = $name;
        $history->value = json_encode($body, true);
        $history->user_id = $user->id;

        switch ($type) {
            case 'order':
                $history->order_id = $type_id;
                break;
            case 'invoice':
                $history->invoice_id = $type_id;
                break;
            case 'monthly_revenue':
                $history->monthly_revenue_id = $type_id;
                break;
            case 'file':
                $history->file_id = $type_id;
                break;
            case 'inmar':
                $history->inmar_id = $type_id;
                break;
            default:
                # code...
                break;
        }

        $history->save();
    }

    public function delete_file_history($header, $body, $type, $type_id)
    {
        $user = Auth::user();
        $name = $user->name .' '. $header['method']. ' '. $header['filename'] .' in ' .$header['class'] .' with id ' .$header['id'];
        $history = $this->history;
        $history->name = $name;
        $history->value = json_encode($body, true);
        $history->user_id = $user->id;

        switch ($type) {
            case 'order':
                $history->order_id = $type_id;
                break;
            case 'invoice':
                $history->invoice_id = $type_id;
                break;
            case 'monthly_revenue':
                $history->monthly_revenue_id = $type_id;
                break;
            case 'file':
                $history->file_id = $type_id;
                break;
            case 'inmar':
                $history->inmar_id = $type_id;
                break;
            default:
                # code...
                break;
        }

        $history->save();
    }
}