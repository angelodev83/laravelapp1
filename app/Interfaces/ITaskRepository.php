<?php

namespace App\Interfaces;

use App\Interfaces\IStoreDocumentRepository;

interface ITaskRepository extends IStoreDocumentRepository
{
    public function retrieveRecent($params);
    public function getTaskDataById($id, $relation);
    public function sendNotificationStatusChanged($employee, $task, $currentStatus, $previousStatus = null);
    public function sendNotificationOverDue();
    public function retrieveRecentMonthlyTasks($params);
    public function createArAgingReportTask($pharmacy_store_id, $assignee);
    public function createNewTask($pharmacy_store_id, $assignee, $user_id, $subject, $due_date = null, $description = null);
    public function watchers($request);
}