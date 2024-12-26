<?php

namespace App\Interfaces\Common;

use App\Interfaces\IBaseRepository;

interface AnnouncementInterface extends IBaseRepository
{
    public function setModel($model);
    public function retrieveRecent($params);
    public function retrieveStoreAnnouncement();
}