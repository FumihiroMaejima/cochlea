<?php

namespace App\Repositories\Users;

use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Models\Users\BaseUserDataModel;
use Illuminate\Database\Query\Builder;

class BaseUserRepository
{
    protected BaseUserDataModel $model;

    protected const NO_DATA_COUNT = 0;
    protected const FIRST_DATA_COUNT = 1;

    /**
     * create instance.
     *
     * @param BaseUserDataModel $model
     * @return void
     */
    public function __construct(BaseUserDataModel $model)
    {
        $this->model = $model;
    }

    /**
     * get query builder by user id.
     *
     * @param int $userId user id
     * @return Builder
     */
    public function getQueryBuilder(int $userId): Builder
    {
        return $this->model->getQueryBuilder($userId);
    }
}
