<?php

namespace App\Services\Users;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Repositories\Masters\Questionnaires\QuestionnairesRepositoryInterface;
use App\Repositories\Users\UserQuestionnaires\UserQuestionnairesRepositoryInterface;
use App\Http\Resources\Users\UserQuestionnairesResource;
use App\Library\Array\ArrayLibrary;
use App\Library\Cache\MasterCacheLibrary;
use App\Library\Questionnaire\QuestionnaireLibrary;
use App\Library\User\UserLibrary;
use App\Models\Masters\Questionnaires;
use App\Models\Users\UserQuestionnaires;
use Exception;

class QuestionnairesService
{
    protected QuestionnairesRepositoryInterface $questionnairesRepository;
    protected UserQuestionnairesRepositoryInterface $userQuestionnairesRepository;

    /**
     * create service instance
     *
     * @param QuestionnairesRepositoryInterface $questionnairesRepository
     * @param UserQuestionnairesRepositoryInterface $userQuestionnairesRepository
     * @return void
     */
    public function __construct(
        QuestionnairesRepositoryInterface $questionnairesRepository,
        UserQuestionnairesRepositoryInterface $userQuestionnairesRepository
    ) {
        $this->questionnairesRepository = $questionnairesRepository;
        $this->userQuestionnairesRepository = $userQuestionnairesRepository;
    }

    /**
     * get questionnaires
     *
     * @return JsonResponse
     */
    public function getQuestionnaires(): JsonResponse
    {
        $questionnaireList = $this->getQuestionnaireList();
        return response()->json(['data' => UserQuestionnairesResource::toArrayForList($questionnaireList)]);
    }

    /**
     * get questionnaire datail.
     *
     * @param int $userId user id
     * @param int $questionnaireId questionnaire id.
     * @return JsonResponse
     */
    public function getQuestionnaire(int $userId, int $questionnaireId): JsonResponse
    {
        $questionnaireList = $this->getQuestionnaireList();
        $questionnaireList = array_column($questionnaireList, null, Questionnaires::ID);
        $questionnaire = $questionnaireList[$questionnaireId] ?? null;
        if (empty($questionnaire)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_404,
                'questionnaire Not Exist.'
            );
        }
        $userQuestionnaire = $this->getUserQuestionnaire($userId, $questionnaireId);

        return response()->json(['data' => UserQuestionnairesResource::toArrayForDetail($questionnaire)]);
    }

    /**
     * create user rercord.
     *
     * @param int $userId user id
     * @param int $questionnaireId questionnaire id.
     * @param array $userQuestions user answer questions informations.
     * @return JsonResponse
     */
    public function createUserQuestionnaire(int $userId, int $questionnaireId, array $userQuestions): JsonResponse
    {
        // アンケート情報の取得
        $questionnaireList = $this->getQuestionnaireList();
        $questionnaireList = array_column($questionnaireList, null, Questionnaires::ID);
        $questionnaire = $questionnaireList[$questionnaireId] ?? null;
        // TODO 期間判定
        if (empty($questionnaire)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_404,
                'questionnaire Not Exist.'
            );
        }

        // DB 登録
        DB::beginTransaction();
        try {
            // ロックの実行
            UserLibrary::lockUser($userId);

            // ユーザー情報取得
            $userQuestionnaire = $this->getUserQuestionnaire($userId, $questionnaireId);
            if ($userQuestionnaire) {
                throw new MyApplicationHttpException(
                    StatusCodeMessages::STATUS_500,
                    'User Questionnaire is Aready Exist.'
                );
            }

            // 入力値の検証
            QuestionnaireLibrary::validateQuestionnaireAnswer(
                $userQuestions,
                json_decode($questionnaire[Questionnaires::QUESTIONS], true)
            );

            $resource = UserQuestionnairesResource::toArrayForCreate($userId, $questionnaireId, $userQuestions);
            $createCount = $this->userQuestionnairesRepository->create($userId, $resource);

            if ($createCount <= 0) {
                throw new MyApplicationHttpException(
                    StatusCodeMessages::STATUS_500,
                    'Create record failed.'
                );
            }

            // ログの設定

            DB::commit();
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();
            throw $e;
        }

        return response()->json(
            [
                'code' => StatusCodeMessages::STATUS_201,
                'message' => 'Successfully Create!',
                'data' => true,
            ],
            StatusCodeMessages::STATUS_201
        );
    }

    /**
     * update user rercord.
     *
     * @param int $userId user id
     * @param int $questionnaireId questionnaire id.
     * @param array $userQuestions user answer questions informations.
     * @return JsonResponse
     */
    public function updateUserQuestionnaire(int $userId, int $questionnaireId, array $userQuestions): JsonResponse
    {
        // アンケート情報の取得
        $questionnaireList = $this->getQuestionnaireList();
        $questionnaireList = array_column($questionnaireList, null, Questionnaires::ID);
        $questionnaire = $questionnaireList[$questionnaireId] ?? null;
        // TODO 期間判定
        if (empty($questionnaire)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_404,
                'questionnaire Not Exist.'
            );
        }

        $userQuestionnaire = $this->getUserQuestionnaire($userId, $questionnaireId);
        if (is_null($userQuestionnaire)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_404,
                'User Questionnaire is Not Exist.'
            );
        }

        // 入力値の検証
        QuestionnaireLibrary::validateQuestionnaireAnswer(
            $userQuestions,
            json_decode($questionnaire[Questionnaires::QUESTIONS], true)
        );

        // DB 登録
        DB::beginTransaction();
        try {
            $resource = UserQuestionnairesResource::toArrayForUpdate($userId, $questionnaireId, $userQuestions);
            $updateCount = $this->userQuestionnairesRepository->update($userId, $questionnaireId, $resource);

            if ($updateCount <= 0) {
                throw new MyApplicationHttpException(
                    StatusCodeMessages::STATUS_500,
                    'Update record failed.'
                );
            }

            // ログの設定

            DB::commit();
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();
            throw $e;
        }

        return response()->json(
            [
                'code' => StatusCodeMessages::STATUS_200,
                'message' => 'Successfully Update!',
                'data' => true,
            ],
            StatusCodeMessages::STATUS_200
        );
    }

    /**
     * get record list & sort by date.
     *
     * @return array
     */
    private function getQuestionnaireList(): array
    {
        $cache = MasterCacheLibrary::getQuestionnairesCache();

        // キャッシュチェック
        if (is_null($cache)) {
            $collection = $this->questionnairesRepository->getRecords();
            if (empty($collection)) {
                // 空配列もキャッシュとして設定する
                MasterCacheLibrary::setServiceTermsCache([]);
                return [];
            }
            $records = ArrayLibrary::toArray($collection->toArray());

            MasterCacheLibrary::setQuestionnairesCache($records);
        } else {
            $records = $cache;
        }
        return Questionnaires::sortByStartAt($records, SORT_DESC);
    }

    /**
     * get user questionnaire by user id & questionnaire id.
     *
     * @param int $userId user id
     * @param string $questionnaireId questionnaire id.
     * @return ?array
     */
    private function getUserQuestionnaire(int $userId, int $questionnaireId): ?array
    {
        $userQuestionnaire = $this->userQuestionnairesRepository->getByUserIdAndQuestionnaireId($userId, $questionnaireId);
        if (empty($userQuestionnaire)) {
            return null;
        }

        return ArrayLibrary::getFirst(ArrayLibrary::toArray($userQuestionnaire->toArray()));
    }
}
