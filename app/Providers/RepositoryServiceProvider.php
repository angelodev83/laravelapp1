<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Interfaces\IHistoriesRepository;
use App\Interfaces\IStoreStatusRepository;
use App\Interfaces\IDocumentRepository;
use App\Interfaces\ISelfAuditDocumentRepository;
use App\Interfaces\IInventoryReconciliationDocumentRepository;
use App\Interfaces\ITaskRepository;
use App\Interfaces\ITicketRepository;
use App\Interfaces\SearchInterface;
use App\Interfaces\UploadInterface;
use App\Interfaces\IPatientRepository;
use App\Interfaces\Common\AnnouncementInterface;

use App\Repositories\HistoriesRepository;
use App\Repositories\StoreStatusRepository;
use App\Repositories\SearchRepository;
use App\Repositories\UploadRepository;
use App\Repositories\PatientRepository;
use App\Repositories\Compliance\DocumentRepository;
use App\Repositories\Compliance\SelfAuditDocumentRepository;
use App\Repositories\Compliance\InventoryReconciliationDocumentRepository;
use App\Repositories\Bulletin\TaskRepository;
use App\Repositories\Escalation\TicketRepository;
use App\Repositories\Common\AnnouncementRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Bind Interface and Repository class together
        $this->app->bind(IStoreStatusRepository::class, StoreStatusRepository::class);
        $this->app->bind(IDocumentRepository::class, DocumentRepository::class);
        $this->app->bind(ISelfAuditDocumentRepository::class, SelfAuditDocumentRepository::class);
        $this->app->bind(IInventoryReconciliationDocumentRepository::class, InventoryReconciliationDocumentRepository::class);
        $this->app->bind(IHistoriesRepository::class, HistoriesRepository::class);
        $this->app->bind(ITicketRepository::class, TicketRepository::class);
        $this->app->bind(ITaskRepository::class, TaskRepository::class);
        $this->app->bind(AnnouncementInterface::class, AnnouncementRepository::class);
        $this->app->bind(SearchInterface::class, SearchRepository::class);
        $this->app->bind(UploadInterface::class, UploadRepository::class);
        $this->app->bind(IPatientRepository::class, PatientRepository::class);
    }
}