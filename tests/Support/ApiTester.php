<?php

declare(strict_types=1);

namespace App\Tests\Support;

use Codeception\Actor;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
 */
class ApiTester extends Actor
{
    use _generated\ApiTesterActions;

    /**
     * Define custom actions here
     */

    public const ACCOUNT_STRUCTURE = [
        'id'         => 'integer|null',
        'email'      => 'string',
        'phone'      => 'string|null',
        'name'       => 'string|null',
        'photo'      => 'string|null',
        'uiLanguage' => 'string',
        'roles'      => 'array',
        'group'      => 'integer|null',
        'groupName'  => 'string',
        'jaid'       => 'string|null',
        'logged'     => 'boolean',
        'timeZone'   => 'string',
    ];

    public const PROJECT_STRUCTURE = [
        'jpid'   => 'integer',
        'group'  => 'integer|null',
        'name'   => 'string',
        'domain' => 'string',
        'apikey' => 'string',
    ];

    public const VONAGE_STRUCTURE = [
        'key'    => 'string',
        'secret' => 'string',
    ];

    public const VONAGE_CREDS_STRUCTURE = [
        'domain'    => 'string',
        'password'  => 'string',
        'partnerId' => 'string',
        'keyword'   => 'string',
    ];

    public const POPUP_STRUCTURE = [
        'title'  => 'string',
        'button' => 'string',
        'img'    => 'string|null',
    ];

    public const PROJECTS_WITH_PUSH_SETTINGS_STRUCTURE = [
        'id'           => 'integer',
        'jpid'         => 'integer',
        'name'         => 'string',
        'domain'       => 'string',
        'apiKey'       => 'string',
        'pushSettings' => self::PUSH_SETTINGS_STRUCTURE,
    ];

    private const PUSH_SETTINGS_STRUCTURE = [
        'id'            => 'integer',
        'ico'           => 'string',
        'resourceGroup' => 'string',
        'popupTitle'    => 'string',
        'popupButton'   => 'string',
        'popupImg'      => 'string|null',
        'domainStatus'  => 'integer|null',
    ];

    public const PROVIDER_KEYS_STRUCTURE = [
        'jpid'      => 'integer',
        'vonage_id' => 'integer',
        'slooce_id' => 'integer',
    ];

    public const AUTH_STRUCTURE = [
        'access' => [
            'content' => [
                'read'  => 'boolean',
                'write' => 'boolean',
            ],
            'manage'  => [
                'read'  => 'boolean',
                'write' => 'boolean',
            ],
            'send'    => [
                'read'  => 'boolean',
                'write' => 'boolean',
            ],
        ],
    ];

    public const SLOOCE_CREDS_STRUCTURE = [
        'domain'    => 'string',
        'password'  => 'string',
        'partnerId' => 'string',
        'keyword'   => 'string',
    ];

    public const METRIC_STRUCTURE = [
        'refid'       => 'string',
        'utmMedium'   => 'string',
        'utmSource'   => 'string',
        'utmCampaign' => 'string',
    ];

    public const PROJECT_FULL_STRUCTURE = [
        'jpid'             => 'integer',
        'name'             => 'string',
        'domain'           => 'string',
        'apiKey'           => 'string',
        'contentSettings'  => self::CONTENT_SETTINGS_STRUCTURE,
        'pushSettings'     => self::PUSH_SETTINGS_STRUCTURE,
        'smsSettings'      => self::SMS_SETTINGS_STRUCTURE,
        'userSettings'     => self::USER_SETTINGS_STRUCTURE,
        'shortLinkDomains' => 'array|null',
    ];

    private const CONTENT_SETTINGS_STRUCTURE = [
        'id'             => 'integer',
        'banners'        => 'array|null',
        'defaultUrl'     => 'string',
        'additionalUrls' => 'array|null',
    ];

    private const SMS_SETTINGS_STRUCTURE = [
        'id'            => 'integer',
        'resourceGroup' => 'string',
        'fromNumbers'   => 'array|null',
        'slooce'        => self::SLOOCE_CREDS_STRUCTURE,
        'vonage'        => self::VONAGE_STRUCTURE,
    ];

    private const USER_SETTINGS_STRUCTURE = [
        'id'              => 'integer',
        'externalSystem'  => 'array|null',
        'defaultTimeZone' => 'string',
        'subKeywords'     => 'array|null',
    ];

    public const PROJECTS_WITH_CONTENT_SETTINGS_STRUCTURE = [
        'jpid'   => 'integer',
        'name'   => 'string',
        'domain' => 'string',
        'apiKey' => 'string',
        'contentSettings' => 'array|null',
    ];

}
