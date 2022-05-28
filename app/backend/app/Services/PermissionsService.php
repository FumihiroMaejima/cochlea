<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Repositories\Admins\Permissions\PermissionsRepositoryInterface;
use App\Http\Resources\Admins\PermissionsListResource;

class PermissionsService
{
    protected $permissionsRepository;

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
        $resource = app()->make(PermissionsListResource::class, ['resource' => $collection]);

        return response()->json($resource->toArray($request), 200);
    }
}
