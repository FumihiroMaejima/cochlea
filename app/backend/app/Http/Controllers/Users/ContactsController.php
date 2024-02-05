<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

// use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Library\Response\ResponseLibrary;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Contacts\ContactCreateRequest;
use App\Services\Users\ContactsService;
use App\Trait\CheckHeaderTrait;

// use App\Trait\CheckHeaderTrait;

class ContactsController extends Controller
{
    use CheckHeaderTrait;
    private ContactsService $service;

    /**
     * Create a new RolesController instance.
     *
     * @return void
     */
    public function __construct(ContactsService $contactsService)
    {
        $this->middleware('auth:api-users', ['except' => ['index', 'categories', 'create']]);
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
        return ResponseLibrary::jsonResponse($this->service->getCategories());
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function categories(): JsonResponse
    {
        // サービスの実行
        return ResponseLibrary::jsonResponse($this->service->getCategories());
    }

    /**
     * create a resource.
     *
     * @param ContactCreateRequest $request
     * @return JsonResponse
     * @throws MyApplicationHttpException
     */
    public function create(ContactCreateRequest $request): JsonResponse
    {
        // ユーザーIDの取得
        $userId = $this->getUserId($request);

        // サービスの実行
        $this->service->createContact(
            $userId,
            $request->email,
            $request->name,
            $request->type,
            $request->detail,
            $request->failureDetail,
            $request->failureAt
        );
        return ResponseLibrary::jsonResponse(['data' => true]);
    }
}
