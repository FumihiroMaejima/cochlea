<?php

declare(strict_types=1);

namespace App\Http\Resources\Admins;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleUpdateNotificationResource extends JsonResource
{
    public const RESOURCE_KEY_PRETEXT    = 'pretext';
    public const RESOURCE_KEY_TITLE      = 'title';
    public const RESOURCE_KEY_TITLE_LINK = 'titleLink';
    public const RESOURCE_KEY_CONTENT    = 'content';
    public const RESOURCE_KEY_COLOR      = 'color';
    public const RESOURCE_KEY_ID         = 'id';
    public const RESOURCE_KEY_NAME       = 'name';
    public const RESOURCE_KEY_STATUS     = 'status';
    public const RESOURCE_KEY_DETAIL     = 'detail';

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            self::RESOURCE_KEY_PRETEXT    => PHP_EOL,
            self::RESOURCE_KEY_TITLE      => 'Update Role Notification',
            self::RESOURCE_KEY_TITLE_LINK => '',
            self::RESOURCE_KEY_CONTENT    => 'content text' . PHP_EOL,
            self::RESOURCE_KEY_COLOR      => 'good',
            self::RESOURCE_KEY_ID         => $request->id,
            self::RESOURCE_KEY_NAME       => $request->name,
            self::RESOURCE_KEY_STATUS     => ':ok:',
            self::RESOURCE_KEY_DETAIL     => '```'. $this->resource . PHP_EOL . '```'
        ];
    }
}
