<?php

namespace JalalLinuX\Thingsboard\Tests\Unit\User;

use JalalLinuX\Thingsboard\Entities\User;
use JalalLinuX\Thingsboard\Enums\ThingsboardAuthority;
use JalalLinuX\Thingsboard\Enums\UserSortProperty;
use JalalLinuX\Thingsboard\infrastructure\PaginationArguments;
use JalalLinuX\Thingsboard\Tests\TestCase;

class GetUsersTest extends TestCase
{
    public function testTextSearch()
    {
        $user = $this->thingsboardUser($this->faker->randomElement([ThingsboardAuthority::TENANT_ADMIN(), ThingsboardAuthority::CUSTOMER_USER()]));

        $users = thingsboard($user)->user()->getUsers(
            PaginationArguments::make()
        );

        $users->data()->each(fn ($user) => $this->assertInstanceOf(User::class, $user));
    }

    public function testPaginationData()
    {
        $pagination = $this->randomPagination(UserSortProperty::class);
        $user = $this->thingsboardUser(ThingsboardAuthority::TENANT_ADMIN());

        $devices = thingsboard()->user()->withUser($user)->getUsers(
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
