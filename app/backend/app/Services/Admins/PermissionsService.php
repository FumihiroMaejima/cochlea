<?php

declare(strict_types=1);

namespace App\Services\Admins;

use Illuminate\Support\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Repositories\Masters\Permissions\PermissionsRepositoryInterface;
use App\Http\Resources\Admins\PermissionsResource;
use App\Library\Array\ArrayLibrary;

class PermissionsService
{
    protected PermissionsRepositoryInterface $permissionsRepository;

    /**
     * create PermissionsService instance
     * @param  \App\Repositories\Permissions\PermissionsRepositoryInterface  $permissionsRepository
     * @return void
     */
    public function __construct(PermissionsRepositoryInterface $permissionsRepository)
    {
        $this->permissionsRepository = $permissionsRepository;
    }

    /**
     * get permissions data for frontend parts
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function getPermissionsAsList(Request $request): array
    {
        $collection = $this->permissionsRepository->getPermissionsList();
        $resource = PermissionsResource::toArrayForGetTextAndValueList($collection);

        return $resource;
    }
}
