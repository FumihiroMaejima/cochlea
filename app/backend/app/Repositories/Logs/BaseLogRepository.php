<?php

declare(strict_types=1);

namespace App\Repositories\Logs;

use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Models\Logs\BaseLogDataModel;
use Illuminate\Database\Query\Builder;

class BaseLogRepository
{
    protected BaseLogDataModel $model;

    protected const NO_DATA_COUNT = 0;
    protected const FIRST_DATA_COUNT = 1;

    /**
     * create instance.
     *
     * @param BaseLogDataModel $model
     * @return void
     */
    public function __construct(BaseLogDataModel $model)
    {
        $this->model = $model;
    }

    /**
     * get query builder by user id.
     *
     * @param int $userId user id
     * @return Builder
     */
    public function getQueryBuilder(): Builder
    {
        return $this->model->getQueryBuilder();
    }
}
