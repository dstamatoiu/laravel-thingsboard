<?php

namespace JalalLinuX\Thingsboard\Tests\Unit\User;

use JalalLinuX\Thingsboard\Entities\User;
use JalalLinuX\Thingsboard\Enums\EnumAuthority;
use JalalLinuX\Thingsboard\Enums\EnumThingsboardEntityType;
use JalalLinuX\Thingsboard\Enums\EnumUserSortProperty;
use JalalLinuX\Thingsboard\infrastructure\Id;
use JalalLinuX\Thingsboard\infrastructure\PaginationArguments;
use JalalLinuX\Thingsboard\Tests\TestCase;

class GetTenantAdminsTest extends TestCase
{
    public function testTextSearch()
    {
        $user = $this->thingsboardUser(EnumAuthority::SYS_ADMIN());

        $tenantId = thingsboard()->tenant()->withUser($user)->getTenants(PaginationArguments::make())->data()->first()->id->id;
        $tenantUsers = thingsboard()->user()->withUser($user)->getTenantAdmins(PaginationArguments::make(), $tenantId);

        $tenantUsers->data()->each(fn ($user) => $this->assertInstanceOf(User::class, $user));
    }

    public function testPaginationData()
    {
        $pagination = $this->randomPagination(EnumUserSortProperty::class);
        $user = $this->thingsboardUser(EnumAuthority::SYS_ADMIN());
        $tenantId = thingsboard()->tenant()->withUser($user)->getTenants(
            PaginationArguments::make()
        )->data()->first()->id->id;

        $devices = thingsboard()->user(['tenantId' => new Id($tenantId, EnumThingsboardEntityType::TENANT())])->withUser($user)->getTenantAdmins(
            PaginationArguments::make(
                page: $pagination['page'], pageSize: $pagination['pageSize'],
                sortProperty: $pagination['sortProperty'], sortOrder: $pagination['sortOrder']
            )
        );

        $this->assertEquals($pagination['page'], $devices->paginator()->currentPage());
        $this->assertEquals($pagination['pageSize'], $devices->paginator()->perPage());
        $this->assertEquals($pagination['sortOrder'], $devices->paginator()->getOptions()['sortOrder']);
        $this->assertEquals($pagination['sortProperty'], $devices->paginator()->getOptions()['sortProperty']);
    }
}
