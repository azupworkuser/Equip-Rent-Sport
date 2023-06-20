<?php

namespace App\Models;

use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Role extends \Spatie\Permission\Models\Role
{
    use BelongsToTenant;

    public const OWNER = 'owner';
    public const ADMIN = 'admin';
    public const BOOKING_MANAGER = 'booking_manager';
    public const BOOKING_ASSISTANT = 'booking_assistant';

    public const DEFAULT_ROLES = [
        self::OWNER => 'Owner',
        self::ADMIN => 'Admin',
        self::BOOKING_MANAGER => 'Booking Manager',
        self::BOOKING_ASSISTANT => 'Booking Assistant',
    ];

    public const ROLES = [
        self::OWNER => [
            Permission::PERM_DASHBOARD,
            Permission::PERM_INVENTORY,
            Permission::PERM_ORDER,
            Permission::PERM_CUSTOMER,
            Permission::PERM_TRANSACTION,
            Permission::PERM_CALENDAR,
            Permission::PERM_REPORT,
            Permission::PERM_BUSINESS,
            Permission::PERM_PAYMENT,
            Permission::PERM_USER,
            Permission::PERM_AFFILIATE,
            Permission::PERM_DEVELOPER,
            Permission::PERM_EMAIL,
            Permission::PERM_ACTION,
        ],
        self::ADMIN => [
            Permission::PERM_DASHBOARD,
            Permission::PERM_INVENTORY,
            Permission::PERM_ORDER,
            Permission::PERM_TRANSACTION,
            Permission::PERM_CALENDAR,
            Permission::PERM_REPORT,
            Permission::PERM_USER,
            Permission::PERM_AFFILIATE,
            Permission::PERM_DEVELOPER,
            Permission::PERM_EMAIL,
            Permission::PERM_CUSTOMER,
        ],
        self::BOOKING_MANAGER => [
            Permission::PERM_DASHBOARD,
            Permission::PERM_INVENTORY,
            Permission::PERM_ORDER,
            Permission::PERM_TRANSACTION,
            Permission::PERM_CALENDAR,
            Permission::PERM_REPORT,
            Permission::PERM_USER,
            Permission::PERM_PAYMENT,
            Permission::PERM_EMAIL,
            Permission::PERM_ACTION,
            Permission::PERM_CUSTOMER,
        ],
        self::BOOKING_ASSISTANT => [
            Permission::PERM_DASHBOARD,
            Permission::PERM_ORDER,
            Permission::PERM_TRANSACTION,
            Permission::PERM_CALENDAR,
            Permission::PERM_CUSTOMER,
        ],
    ];
}
