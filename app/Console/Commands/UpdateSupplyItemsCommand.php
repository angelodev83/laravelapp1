<?php

namespace App\Console\Commands;

ini_set('memory_limit', '1024M');
use App\Interfaces\UploadInterface;
use Illuminate\Console\Command;

class UpdateSupplyItemsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-supply-items-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private UploadInterface $repository;

    public function __construct(UploadInterface $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $wholesaler = 'Staples';
        $this->repository->uploadSupplyItems($wholesaler);
    }
}
