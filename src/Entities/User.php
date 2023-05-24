<?php

namespace JalalLinuX\Thingsboard\Entities;

use JalalLinuX\Thingsboard\Enums\ThingsboardEntityType;
use JalalLinuX\Thingsboard\Enums\UserSortProperty;
use JalalLinuX\Thingsboard\Interfaces\ThingsboardEntityId;
use JalalLinuX\Thingsboard\ThingsboardPaginatedResponse;
use JalalLinuX\Thingsboard\ThingsboardPaginationArguments;
use JalalLinuX\Thingsboard\Tntity;

/**
 * @property ThingsboardEntityId $id
 * @property \DateTime $createdTime
 * @property ThingsboardEntityId $tenantId
 * @property ThingsboardEntityId $customerId
 * @property string $email
 * @property string $name
 * @property string $authority
 * @property string $lastName
 * @property string $firstName
 * @property array $additionalInfo
 * @property array $phone
 */
class User extends Tntity
{
    protected $fillable = [
        'id',
        'createdTime',
        'tenantId',
        'customerId',
        'email',
        'name',
        'authority',
        'lastName',
        'firstName',
        'additionalInfo',
        'phone',
    ];

    protected $casts = [
        'id' => 'id',
        'createdTime' => 'timestamp',
        'tenantId' => 'id',
        'customerId' => 'id',
        'additionalInfo' => 'array',
    ];

    public function entityType(): ?ThingsboardEntityType
    {
        return ThingsboardEntityType::USER();
    }

    /**
     * Get Users
     *
     * @author JalalLinuX
     *
     * @group TENANT_ADMIN | CUSTOMER_USER
     */
    public function getUsers(ThingsboardPaginationArguments $paginationArguments): ThingsboardPaginatedResponse
    {
        $response = $this->api(true)->get('users', $paginationArguments->queryParams());

        return $this->paginatedResponse($response, $paginationArguments);
    }

    /**
     * Get Customer Users
     *
     * @throws \Throwable
     *
     * @author JalalLinuX
     *
     * @group TENANT_ADMIN
     */
    public function getCustomerUsers(ThingsboardPaginationArguments $paginationArguments, string $customerId = null): ThingsboardPaginatedResponse
    {
        $customerId = $customerId ?? $this->forceAttribute('customerId')->id;

        throw_if(
            ! uuid_is_valid($customerId),
            $this->exception('method "customerId" argument must be a valid uuid.'),
        );

        $paginationArguments->validateSortProperty(UserSortProperty::class);

        $response = $this->api(true)->get("customer/{$customerId}/users", $paginationArguments->queryParams());

        return $this->paginatedResponse($response, $paginationArguments);
    }
}
