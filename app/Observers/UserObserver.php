<?php

// app/Observers/SubAdminObserver.php

// app/Observers/ModelObserver.php

namespace App\Observers;

use App\Services\SubAdminActivityService;

class ModelObserver
{
    protected $logger;

    public function __construct(SubAdminActivityService $logger)
    {
        $this->logger = $logger;
    }

    public function created($model)
    {   
        $this->logger->log($model->id, 'created', class_basename($model) . " {$model->name} created.");
    }

    public function updated($model)
    {
        $this->logger->log($model->id, 'updated', class_basename($model) . " {$model->name} updated.");
    }

    public function deleted($model)
    {
        $this->logger->log($model->id, 'deleted', class_basename($model) . " {$model->name} deleted.");
    }

    public function restored($model)
    {
        $this->logger->log($model->id, 'restored', class_basename($model) . " {$model->name} restored.");
    }

    public function forceDeleted($model)
    {
        $this->logger->log($model->id, 'forceDeleted', class_basename($model) . " {$model->name} permanently deleted.");
    }
}