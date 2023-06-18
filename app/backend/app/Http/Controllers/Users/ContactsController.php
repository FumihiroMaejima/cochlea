<?php

namespace App\Http\Controllers\Users;

// use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Users\ContactsService;

// use App\Trait\CheckHeaderTrait;

class ContactsController extends Controller
{
    private ContactsService $service;

    /**
     * Create a new RolesController instance.
     *
     * @return void
     */
    public function __construct(ContactsService $contactsService)
    {
        $this->middleware('auth:api-users', ['except' => ['index', 'categories']]);
        $this->service = $contactsService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // サービスの実行
        return $this->service->getCategories();
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function categories(): JsonResponse
    {
        // サービスの実行
        return $this->service->getCategories();
    }
}
