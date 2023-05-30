<?php

namespace JalalLinuX\Thingsboard\Tests\Unit\DeviceProfile;

use JalalLinuX\Thingsboard\Enums\EnumAuthority;
use JalalLinuX\Thingsboard\Infrastructure\PaginationArguments;
use JalalLinuX\Thingsboard\Tests\TestCase;

class GetAttributesKeysTest extends TestCase
{
    public function testGetAttributesKeysSuccess()
    {
        $tenantUser = $this->thingsboardUser(EnumAuthority::TENANT_ADMIN());
        $deviceProfile = thingsboard($tenantUser)->deviceProfile()->getDeviceProfiles(PaginationArguments::make())->data()->first()->id->id;
        $attributesKeys = thingsboard($tenantUser)->deviceProfile()->getAttributesKeys($deviceProfile);
        $this->assertIsArray($attributesKeys);
    }
    public function testGetAttributesKeysWithoutUuidSuccess()
    {
        $tenantUser = $this->thingsboardUser(EnumAuthority::TENANT_ADMIN());
        $attributesKeys = thingsboard($tenantUser)->deviceProfile()->getAttributesKeys();
        $this->assertIsArray($attributesKeys);
    }
}
