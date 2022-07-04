<?php

namespace App\Services\Admins;

use Illuminate\Support\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Repositories\Admins\Permissions\PermissionsRepositoryInterface;
use App\Http\Resources\Admins\PermissionsResource;

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
     * @return JsonResponse
     */
    public function getPermissionsList(Request $request): JsonResponse
    {
        $collection = $this->permissionsRepository->getPermissionsList();
        $resource = PermissionsResource::toArrayForGetTextAndValueList($collection);

        return response()->json($resource, 200);
    }
}
