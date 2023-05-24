<?php

namespace JalalLinuX\Thingsboard\Tests\Unit\Device;

use JalalLinuX\Thingsboard\Enums\ThingsboardUserRole;
use JalalLinuX\Thingsboard\Tests\TestCase;
use JalalLinuX\Thingsboard\ThingsboardPaginationArguments;

class GetDeviceByIdTest extends TestCase
{
    public function testExistUuid()
    {
        $user = $this->thingsboardUser(ThingsboardUserRole::TENANT_ADMIN());
        $deviceId = thingsboard($user)->device()->getTenantDeviceInfos(
            ThingsboardPaginationArguments::make()
        )->data()->first()->id->id;

        $device = thingsboard($user)->device()->getDeviceById($deviceId);
        $this->assertEquals($deviceId, $device->id->id);

        $device = thingsboard($user)->device(['id' => $deviceId])->getDeviceById();
        $this->assertEquals($deviceId, $device->id->id);
    }

    public function testInvalidUuid()
    {
        $user = $this->thingsboardUser(ThingsboardUserRole::TENANT_ADMIN());

        $this->expectException(\Exception::class);
        $this->expectExceptionCode(500);
        thingsboard($user)->device()->getDeviceById(substr_replace(fake()->uuid, 'z', -1));
    }

    public function testNonExistUuid()
    {
        $user = $this->thingsboardUser(ThingsboardUserRole::TENANT_ADMIN());

        $this->expectExceptionCode(404);
        thingsboard($user)->device()->getDeviceById(fake()->uuid);
    }
}
