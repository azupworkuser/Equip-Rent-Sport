<?php

namespace App\Models;

class Permission extends \Spatie\Permission\Models\Permission
{
    public const GUARD = 'api';

    public const PERM_BILLING = 'billing';

    public const PERM_SETTINGS = 'settings';

    public const PERM_DOMAIN = 'domain';

    public const PERM_DOMAIN_CREATE = 'domain.create';

    public const PERM_USER_INVITE = 'user.invite';

    public const PERM_USER_ROLE = 'user.role';

    public const PERM_INVENTORY_PRODUCT = 'inventory.product';

    public const PERM_INVENTORY_PROMOCODE = 'inventory.promocode';

    public const PERM_INVENTORY_ASSET_CREATE = 'inventory.asset.create';

    public const PERM_INVENTORY_ASSET_UPDATE = 'inventory.asset.update';

    public const PERM_INVENTORY_ASSET_DELETE = 'inventory.asset.delete';

    public const PERM_INVENTORY_ASSET_VIEW = 'inventory.asset.view';

    public const PERM_INVENTORY_PROMOCODE_CREATE = 'inventory.promocode.create';

    public const PERM_INVENTORY_PROMOCODE_UPDATE = 'inventory.promocode.update';

    public const PERM_INVENTORY_PROMOCODE_DELETE = 'inventory.promocode.delete';

    public const PERM_INVENTORY_PROMOCODE_VIEW = 'inventory.promocode.view';

    public const PERM_INVENTORY_CATEGORY = 'inventory.category';

    public const PERM_INVENTORY_DEDUCTIBLE = 'inventory.deductible';

    public const PERM_SESSION = 'session';

    public const PERM_TEAM = 'team';

    public const PERM_APIKEY = 'apikey';

    //new permissions
    public const PERM_DASHBOARD = 'dashboard';

    public const PERM_INVENTORY = 'inventory';

    public const PERM_ORDER = 'order';

    public const PERM_CUSTOMER = 'customer';

    public const PERM_TRANSACTION = 'transaction';

    public const PERM_CALENDAR = 'calendar';

    public const PERM_REPORT = 'report';

    public const PERM_BUSINESS = 'business';

    public const PERM_PAYMENT = 'payment';

    public const PERM_USER = 'user';

    public const PERM_AFFILIATE = 'affiliate';

    public const PERM_DEVELOPER = 'developer';

    public const PERM_EMAIL = 'email';

    public const PERM_ACTION = 'action';

    public const PERMISSIONS = [
        self::PERM_DASHBOARD,
        self::PERM_INVENTORY,
        self::PERM_ORDER,
        self::PERM_CUSTOMER,
        self::PERM_TRANSACTION,
        self::PERM_CALENDAR,
        self::PERM_REPORT,
        self::PERM_BUSINESS,
        self::PERM_PAYMENT,
        self::PERM_USER,
        self::PERM_AFFILIATE,
        self::PERM_DEVELOPER,
        self::PERM_EMAIL,
        self::PERM_ACTION,
    ];
}
