<?php

namespace JalalLinuX\Tntity\Entities;

/**
 * @property array $id
 * @property \DateTime $createdTime
 * @property array $tenantId
 * @property array $customerId
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
        'id' => 'array',
        'createdTime' => 'timestamp',
        'tenantId' => 'array',
        'customerId' => 'array',
        'additionalInfo' => 'array',
    ];
}