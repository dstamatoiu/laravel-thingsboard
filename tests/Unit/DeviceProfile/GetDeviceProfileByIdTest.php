<?php

namespace JalalLinuX\Thingsboard\Tests\Unit\DeviceProfile;

use JalalLinuX\Thingsboard\Enums\ThingsboardUserRole;
use JalalLinuX\Thingsboard\Tests\TestCase;
use JalalLinuX\Thingsboard\ThingsboardPaginationArguments;

class GetDeviceProfileByIdTest extends TestCase
{
    public function testExistUuid()
    {
        $user = $this->thingsboardUser(ThingsboardUserRole::TENANT_ADMIN());
        $deviceProfileId = thingsboard($user)->deviceProfile()->getDeviceProfiles(
            ThingsboardPaginationArguments::make()
        )->data()->first()->id->id;

        $deviceProfile = thingsboard($user)->deviceProfile()->getDeviceProfileById($deviceProfileId);
        $this->assertEquals($deviceProfileId, $deviceProfile->id->id);

        $deviceProfile = thingsboard($user)->deviceProfile(['id' => $deviceProfileId])->getDeviceProfileById();
        $this->assertEquals($deviceProfileId, $deviceProfile->id->id);
    }

    public function testInvalidUuid()
    {
        $user = $this->thingsboardUser(ThingsboardUserRole::TENANT_ADMIN());

        $this->expectException(\Exception::class);
        $this->expectExceptionCode(500);
        thingsboard($user)->deviceProfile()->getDeviceProfileById(substr_replace($this->faker->uuid, 'z', -1));
    }

    public function testNonExistUuid()
    {
        $user = $this->thingsboardUser(ThingsboardUserRole::TENANT_ADMIN());

        $this->expectExceptionCode(404);
        thingsboard($user)->deviceProfile()->getDeviceProfileById($this->faker->uuid);
    }
}