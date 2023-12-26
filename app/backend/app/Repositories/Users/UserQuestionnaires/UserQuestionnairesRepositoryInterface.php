<?php

declare(strict_types=1);

namespace App\Repositories\Users\UserQuestionnaires;

use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;

interface UserQuestionnairesRepositoryInterface
{
    public function getByUserId(int $userId, bool $isLock = false): Collection|null;

    public function getByUserIdAndQuestionnaireId(int $userId, int $serviceTermId, bool $isLock = false): Collection|null;

    public function create(int $userId, array $resource): bool;

    public function update(int $userId, int $questionnaireId, array $resource): int;

    public function delete(int $userId, array $resource): int;
}
