<?php

namespace App\Interfaces;

use App\Interfaces\IStoreDocumentRepository;

interface ITicketRepository extends IStoreDocumentRepository
{
    public function sendNotificationStatusChanged($employee, $ticket, $currentStatus, $previousStatus = null);
    public function sendNotificationOverDue();
    public function storeAttachments($request);
    public function storeComment($request);
    public function retrieveRecent($params);
    public function assignees($request);
    public function watchers($request);
}