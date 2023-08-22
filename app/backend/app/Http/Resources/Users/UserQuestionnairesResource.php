<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use App\Library\Time\TimeLibrary;
use App\Models\Masters\Questionnaires;
use App\Models\Users\UserQuestionnaires;

class UserQuestionnairesResource extends JsonResource
{
    public const RESOURCE_KEY_DATA = 'data';
    public const RESOURCE_KEY_TEXT = 'text';
    public const RESOURCE_KEY_VALUE = 'value';

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // $this->resourceはcollection
        // $this->resource->itemsは検索結果の各レコードをstdClassオブジェクトとして格納した配列
        return [
            self::RESOURCE_KEY_DATA => $this->resource->toArray($request)
        ];
    }

    /**
     * Transform the resource into an array for list.
     *
     * @param array $questionnaires 解答情報配列
     * @return array
     */
    public static function toArrayForList(array $questionnaires): array
    {
        $response = [];
        foreach($questionnaires as $questionnaire) {
            $response[] = [
                Questionnaires::ID => $questionnaire[Questionnaires::ID],
                Questionnaires::NAME => $questionnaire[Questionnaires::NAME],
                Questionnaires::START_AT => $questionnaire[Questionnaires::START_AT],
                Questionnaires::END_AT => $questionnaire[Questionnaires::END_AT],
                Questionnaires::EXPIRED_AT => $questionnaire[Questionnaires::EXPIRED_AT],
            ];

        }

        return $response;
    }

    /**
     * Transform the resource into an array for list.
     *
     * @param array $questionnaire 解答情報
     * @return array
     */
    public static function toArrayForDetail(array $questionnaire): array
    {
        return [
            Questionnaires::ID => $questionnaire[Questionnaires::ID],
            Questionnaires::NAME => $questionnaire[Questionnaires::NAME],
            Questionnaires::DETAIL => $questionnaire[Questionnaires::DETAIL],
            Questionnaires::QUESTIONS => json_decode($questionnaire[Questionnaires::QUESTIONS], true),
            Questionnaires::START_AT => $questionnaire[Questionnaires::START_AT],
            Questionnaires::END_AT => $questionnaire[Questionnaires::END_AT],
            Questionnaires::EXPIRED_AT => $questionnaire[Questionnaires::EXPIRED_AT],
        ];
    }

    /**
     * Transform the resource into an array for create.
     *
     * @param int $userId ユーザーID
     * @param int $questionnaireId アンケートID
     * @param array $questions 解答情報
     * @return array
     */
    public static function toArrayForCreate(int $userId, int $questionnaireId, array $questions): array
    {
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            UserQuestionnaires::USER_ID => $userId,
            UserQuestionnaires::QUESTIONNAIRE_ID => $questionnaireId,
            UserQuestionnaires::QUESTIONS => json_encode($questions),
            UserQuestionnaires::CREATED_AT => $dateTime,
            UserQuestionnaires::UPDATED_AT => $dateTime,
            UserQuestionnaires::DELETED_AT => null,
        ];
    }

    /**
     * Transform the resource into an array for update.
     *
     * @param int $userId ユーザーID
     * @param int $questionnaireId アンケートID
     * @param array $questions 解答情報
     * @return array
     */
    public static function toArrayForUpdate(int $userId, int $questionnaireId, array $questions): array
    {
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            UserQuestionnaires::USER_ID => $userId,
            UserQuestionnaires::QUESTIONNAIRE_ID => $questionnaireId,
            UserQuestionnaires::QUESTIONS => json_encode($questions),
            UserQuestionnaires::UPDATED_AT => $dateTime,
        ];
    }

    /**
     * Transform the resource into an array for delete.
     *
     * @return array
     */
    public static function toArrayForDelete(): array
    {
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            UserQuestionnaires::UPDATED_AT => $dateTime,
            UserQuestionnaires::DELETED_AT => $dateTime,
        ];
    }
}
