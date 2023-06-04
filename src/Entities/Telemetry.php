<?php

namespace JalalLinuX\Thingsboard\Entities;

use Illuminate\Support\Str;
use JalalLinuX\Thingsboard\Enums\EnumEntityType;
use JalalLinuX\Thingsboard\Enums\EnumSortOrder;
use JalalLinuX\Thingsboard\Enums\EnumTelemetryAggregation;
use JalalLinuX\Thingsboard\Enums\EnumTelemetryScope;
use JalalLinuX\Thingsboard\Thingsboard;
use JalalLinuX\Thingsboard\Tntity;

class Telemetry extends Tntity
{
    public function entityType(): ?EnumEntityType
    {
        return null;
    }

    protected $fillable = [
        'scope',
    ];

    protected $casts = [
        'scope' => EnumTelemetryScope::class,
    ];

    /**
     * Creates or updates the device attributes based on device id and specified attribute scope.
     * The request payload is a JSON object with key-value format of attributes to create or update.
     * For example:
     * {
     * "stringKey":"value1",
     * "booleanKey":true,
     * "doubleKey":42.0,
     * "longKey":73,
     * "jsonKey": {
     * "someNumber": 42,
     * "someArray": [1,2,3],
     * "someNestedObject": {"key": "value"}
     * }
     * }
     *
     * @param  array  $payload
     * @param  EnumTelemetryScope  $scope
     * @param  string  $deviceId
     * @return bool
     *
     * @throws \Throwable
     *
     * @author Sabiee
     *
     * @group TENANT_ADMIN | CUSTOMER_USER
     */
    public function saveDeviceAttributes(array $payload, EnumTelemetryScope $scope, string $deviceId): bool
    {
        Thingsboard::validation(
            $scope->equals(EnumTelemetryScope::CLIENT_SCOPE()),
            'in',
            ['attribute' => 'scope', 'values' => implode(', ', array_diff(EnumTelemetryScope::cases(), [EnumTelemetryScope::CLIENT_SCOPE()]))]
        );

        Thingsboard::validation(empty($payload), 'required', ['attribute' => 'payload']);

        Thingsboard::validation(! Str::isUuid($deviceId), 'uuid', ['attribute' => 'deviceId']);

        return $this->api(handleException: config('thingsboard.rest.exception.throw_bool_methods'))->post("plugins/telemetry/{$deviceId}/{$scope}", $payload)->successful();
    }

    /**
     * Delete device attributes using provided Device Id, scope and a list of keys.
     * Referencing a non-existing Device Id will cause an error
     *
     * @param  EnumTelemetryScope  $scope
     * @param  array  $keys
     * @param  string  $deviceId
     * @return bool
     *
     * @throws \Throwable
     *
     * @author Sabiee
     *
     * @group TENANT_ADMIN | CUSTOMER_USER
     */
    public function deleteDeviceAttributes(EnumTelemetryScope $scope, array $keys, string $deviceId): bool
    {
        Thingsboard::validation(empty($keys), 'required', ['attribute' => 'keys']);

        Thingsboard::validation(! Str::isUuid($deviceId), 'uuid', ['attribute' => 'deviceId']);

        $keys = implode(',', $keys);

        return $this->api(handleException: config('thingsboard.rest.exception.throw_bool_methods'))->bodyFormat('query')
            ->delete("plugins/telemetry/{$deviceId}/{$scope}", ['keys' => $keys])->successful();
    }

    /**
     * Creates or updates the entity attributes based on Entity Id and the specified attribute scope.
     * List of possible attribute scopes depends on the entity type:
     * SERVER_SCOPE - supported for all entity types;
     * SHARED_SCOPE - supported for devices.
     * The request payload is a JSON object with key-value format of attributes to create or update.
     * For example:
     *
     * {
     *  "stringKey":"value1",
     *  "booleanKey":true,
     *  "doubleKey":42.0,
     *  "longKey":73,
     *  "jsonKey": {
     *      "someNumber": 42,
     *      "someArray": [1,2,3],
     *      "someNestedObject": {"key": "value"}
     *  }
     * }
     * Referencing a non-existing entity Id or invalid entity type will cause an error.
     *
     * @param  array  $payload
     * @param  EnumEntityType  $entityType
     * @param  EnumTelemetryScope  $scope
     * @param  string  $entityId
     * @return bool
     *
     * @throws \Throwable
     *
     * @author Sabiee
     *
     * @group TENANT_ADMIN | CUSTOMER_USER
     */
    public function saveEntityAttributesV1(array $payload, EnumEntityType $entityType, string $entityId, EnumTelemetryScope $scope): bool
    {
        Thingsboard::validation(empty($payload), 'required', ['attribute' => 'payload']);

        Thingsboard::validation(
            $scope->equals(EnumTelemetryScope::CLIENT_SCOPE()),
            'in',
            ['attribute' => 'scope', 'values' => implode(', ', array_diff(EnumTelemetryScope::cases(), [EnumTelemetryScope::CLIENT_SCOPE()]))]
        );

        Thingsboard::validation(! Str::isUuid($entityId), 'uuid', ['attribute' => 'entityId']);

        return $this->api(handleException: config('thingsboard.rest.exception.throw_bool_methods'))->post("plugins/telemetry/{$entityType}/{$entityId}/{$scope}", $payload)->successful();
    }

    /**
     * Delete entity attributes using provided Entity Id, scope and a list of keys.
     * Referencing a non-existing entity Id or invalid entity type will cause an error.
     *
     * @author Sabiee
     *
     * @group TENANT_ADMIN | CUSTOMER_USER
     */
    public function deleteEntityAttributes(EnumEntityType $entityType, string $entityId, EnumTelemetryScope $scope, array $keys): bool
    {
        Thingsboard::validation(empty($keys), 'required', ['attribute' => 'keys']);

        Thingsboard::validation(! Str::isUuid($entityId), 'uuid', ['attribute' => 'entityId']);

        $keys = implode(',', $keys);

        return $this->api(handleException: config('thingsboard.rest.exception.throw_bool_methods'))->bodyFormat('query')
            ->delete("plugins/telemetry/{$entityType}/{$entityId}/{$scope}", ['keys' => $keys])->successful();
    }

    /**
     * Creates or updates the entity attributes based on Entity Id and the specified attribute scope.
     * List of possible attribute scopes depends on the entity type:
     *
     * SERVER_SCOPE - supported for all entity types;
     * SHARED_SCOPE - supported for devices.
     * The request payload is a JSON object with key-value format of attributes to create or update. For example:
     *
     * {
     * "stringKey":"value1",
     * "booleanKey":true,
     * "doubleKey":42.0,
     * "longKey":73,
     * "jsonKey": {
     * "someNumber": 42,
     * "someArray": [1,2,3],
     * "someNestedObject": {"key": "value"}
     * }
     * }
     *
     * @param  array  $payload
     * @param  EnumEntityType  $entityType
     * @param  string  $entityId
     * @param  EnumTelemetryScope  $scope
     * @return bool
     *
     * @throws \Throwable
     *
     * @author Sabiee
     *
     * @group TENANT_ADMIN | CUSTOMER_USER
     */
    public function saveEntityAttributesV2(array $payload, EnumEntityType $entityType, string $entityId, EnumTelemetryScope $scope): bool
    {
        Thingsboard::validation(empty($payload), 'required', ['attribute' => 'payload']);

        Thingsboard::validation(! Str::isUuid($entityId), 'uuid', ['attribute' => 'entityId']);

        Thingsboard::validation(
            $scope->equals(EnumTelemetryScope::CLIENT_SCOPE()),
            'in',
            ['attribute' => 'scope', 'values' => implode(', ', array_diff(EnumTelemetryScope::cases(), [EnumTelemetryScope::CLIENT_SCOPE()]))],
        );

        return $this->api(handleException: config('thingsboard.rest.exception.throw_bool_methods'))->post("plugins/telemetry/{$entityType}/{$entityId}/attributes/{$scope}", $payload)->successful();

    }

    /**
     * Returns a set of unique attribute key names for the selected entity.
     * The response will include merged key names set for all attribute scopes:
     *
     * SERVER_SCOPE - supported for all entity types;
     * CLIENT_SCOPE - supported for devices;
     * SHARED_SCOPE - supported for devices.
     * Referencing a non-existing entity Id or invalid entity type will cause an error.
     *
     * @param  EnumEntityType  $entityType
     * @param  string  $entityId
     * @return array
     *
     * @throws \Throwable
     *
     * @author Sabiee
     *
     * @group TENANT_ADMIN | CUSTOMER_USER
     */
    public function getAttributeKeys(EnumEntityType $entityType, string $entityId): array
    {
        Thingsboard::validation(! Str::isUuid($entityId), 'uuid', ['attribute' => 'entityId']);

        return $this->api()->get("plugins/telemetry/{$entityType}/{$entityId}/keys/attributes")->json();
    }

    /**
     * Returns a set of unique attribute key names for the selected entity and attributes scope:
     * SERVER_SCOPE - supported for all entity types;
     * CLIENT_SCOPE - supported for devices;
     * SHARED_SCOPE - supported for devices.
     * Referencing a non-existing entity Id or invalid entity type will cause an error.
     *
     * @param  EnumEntityType  $entityType
     * @param  string  $entityId
     * @param  EnumTelemetryScope  $scope
     * @return array
     *
     * @throws \Throwable
     *
     * @author Sabiee
     *
     * @group TENANT_ADMIN | CUSTOMER
     */
    public function getAttributeKeysByScope(EnumEntityType $entityType, string $entityId, EnumTelemetryScope $scope): array
    {
        Thingsboard::validation(! Str::isUuid($entityId), 'uuid', ['attribute' => 'entityId']);

        return $this->api()->get("plugins/telemetry/{$entityType}/{$entityId}/keys/attributes/{$scope}")->json();
    }

    /**
     * Returns a set of unique time-series key names for the selected entity.
     * Referencing a non-existing entity Id or invalid entity type will cause an error.
     *
     * @param  EnumEntityType  $entityType
     * @param  string  $entityId
     * @return array
     *
     * @throws \Throwable
     *
     * @author Sabiee
     *
     * @group TENANT_ADMIN | CUSTOMER
     */
    public function getTimeseriesKeys(EnumEntityType $entityType, string $entityId): array
    {
        Thingsboard::validation(! Str::isUuid($entityId), 'uuid', ['attribute' => 'entityId']);

        return $this->api()->get("plugins/telemetry/{$entityType}/{$entityId}/keys/timeseries")->json();
    }

    /**
     * Creates or updates the entity time-series data based on the Entity Id and request payload.
     * The request payload is a JSON document with three possible formats:
     * Simple format without timestamp. In such a case, current server time will be used:
     * {"temperature": 26}
     * Single JSON object with timestamp:
     * {"ts":1634712287000,"values":{"temperature":26, "humidity":87}}
     * JSON array with timestamps:
     * [{"ts":1634712287000,"values":{"temperature":26, "humidity":87}}, {"ts":1634712588000,"values":{"temperature":25, "humidity":88}}]
     * The scope parameter is not used in the API call implementation but should be specified whatever value because it is used as a path variable.
     * Referencing a non-existing entity Id or invalid entity type will cause an error.
     *
     * @param  array  $payload
     * @param  EnumEntityType  $entityType
     * @param  string  $entityId
     * @return bool
     *
     * @throws \Throwable
     *
     * @author Sabiee
     *
     * @group TENANT_ADMIN | CUSTOMER_USER
     */
    public function saveEntityTelemetry(array $payload, EnumEntityType $entityType, string $entityId): bool
    {
        Thingsboard::validation(
            empty($payload),
            'array_of',
            ['attribute' => 'payload', 'struct' => '["ts" => in millisecond-timestamp, "values" => associative-array]']
        );

        foreach ($payload as $row) {
            Thingsboard::validation(
                ! array_key_exists('ts', $row) || strlen($row['ts']) != 13 || ! array_key_exists('values', $row) || ! isArrayAssoc($row['values']),
                'array_of',
                ['attribute' => 'payload', 'struct' => '["ts" => in millisecond-timestamp, "values" => associative-array]']
            );
        }

        Thingsboard::validation(! Str::isUuid($entityId), 'uuid', ['attribute' => 'entityId']);

        return $this->api(handleException: config('thingsboard.rest.exception.throw_bool_methods'))->post("plugins/telemetry/{$entityType}/{$entityId}/timeseries/ANY?scope=ANY", $payload)->successful();
    }

    /**
     * Creates or updates the entity time-series data based on the Entity Id and request payload.
     * The request payload is a JSON document with three possible formats:
     *
     * Simple format without timestamp. In such a case, current server time will be used:
     *
     * {"temperature": 26}
     * Single JSON object with timestamp:
     *
     * {"ts":1634712287000,"values":{"temperature":26, "humidity":87}}
     * JSON array with timestamps:
     *
     * [{"ts":1634712287000,"values":{"temperature":26, "humidity":87}}, {"ts":1634712588000,"values":{"temperature":25, "humidity":88}}]
     * The scope parameter is not used in the API call implementation but should be specified whatever value because it is used as a path variable.
     *
     * The ttl parameter takes affect only in case of Cassandra DB.Referencing a non-existing entity Id or invalid entity type will cause an error.
     *
     * @author Sabiee
     *
     * @group TENANT_ADMIN | CUSTOMER_USER
     */
    public function saveEntityTelemetryWithTTL(array $payload, EnumEntityType $entityType, string $entityId, int $ttl): bool
    {
        Thingsboard::validation(
            empty($payload),
            'array_of',
            ['attribute' => 'payload', 'struct' => '["ts" => in millisecond-timestamp, "values" => associative-array]']
        );

        foreach ($payload as $row) {
            Thingsboard::validation(
                ! array_key_exists('ts', $row) || strlen($row['ts']) != 13 || ! array_key_exists('values', $row) || ! isArrayAssoc($row['values']),
                'array_of',
                ['attribute' => 'payload', 'struct' => '["ts" => in millisecond-timestamp, "values" => associative-array]']
            );
        }

        Thingsboard::validation(! Str::isUuid($entityId), 'uuid', ['attribute' => 'entityId']);

        return $this->api(handleException: config('thingsboard.rest.exception.throw_bool_methods'))->post("plugins/telemetry/{$entityType}/{$entityId}/timeseries/ANY/{$ttl}?scope=ANY", $payload)->successful();
    }

    /**
     * Delete time-series for selected entity based on entity id, entity type and keys.
     * Use 'deleteAllDataForKeys' to delete all time-series data. Use 'startTs' and 'endTs' to specify time-range instead.
     * Use 'rewriteLatestIfDeleted' to rewrite latest value (stored in separate table for performance) after deletion of the time range.
     *
     * @param  EnumEntityType  $entityType
     * @param  string  $entityId
     * @param  array  $keys
     * @param  bool  $deleteAllDataForKeys
     * @param  int|null  $startTs
     * @param  int|null  $endTs
     * @param  bool|null  $rewriteLatestIfDeleted
     * @return bool
     *
     * @throws \Throwable
     *
     * @author Sabiee
     *
     * @group TENANT_ADMIN | CUSTOMER_USER
     */
    public function deleteEntityTimeseries(EnumEntityType $entityType, string $entityId, array $keys, bool $deleteAllDataForKeys = false, int $startTs = null, int $endTs = null, bool $rewriteLatestIfDeleted = null)
    {
        Thingsboard::validation(! Str::isUuid($entityId), 'uuid', ['attribute' => 'entityId']);

        Thingsboard::validation(empty($keys), 'required', ['attribute' => 'keys']);

        $keys = implode(',', $keys);
        if (! $deleteAllDataForKeys) {
            Thingsboard::validation(is_null($startTs), 'required_if', ['attribute' => 'startTs', 'other' => 'deleteAllDataForKeys', 'value' => 'false']);
            Thingsboard::validation(is_null($endTs), 'required_if', ['attribute' => 'endTs', 'other' => 'deleteAllDataForKeys', 'value' => 'false']);
        }

        $queryParams = [
            'deleteAllDataForKeys' => $deleteAllDataForKeys,
            'keys' => $keys,
        ];

        if (! is_null($startTs) && ! is_null($endTs)) {
            $queryParams = array_merge($queryParams, [
                'startTs' => $startTs,
                'endTs' => $endTs,
            ]);
        }

        if (! is_null($rewriteLatestIfDeleted)) {
            $queryParams = array_merge($queryParams, [
                'rewriteLatestIfDeleted' => $rewriteLatestIfDeleted,
            ]);
        }

        return $this->api(handleException: config('thingsboard.rest.exception.throw_bool_methods'))->bodyFormat('query')
            ->delete("plugins/telemetry/{$entityType}/{$entityId}/timeseries/delete", $queryParams)->successful();
    }

    /**
     * Returns all attributes of a specified scope that belong to specified entity.
     * List of possible attribute scopes depends on the entity type:
     *
     * SERVER_SCOPE - supported for all entity types;
     * SHARED_SCOPE - supported for devices;
     * CLIENT_SCOPE - supported for devices.
     * Use optional 'keys' parameter to return specific attributes. Example of the result:
     *
     * [
     * {"key": "stringAttributeKey", "value": "value", "lastUpdateTs": 1609459200000},
     * {"key": "booleanAttributeKey", "value": false, "lastUpdateTs": 1609459200001},
     * {"key": "doubleAttributeKey", "value": 42.2, "lastUpdateTs": 1609459200002},
     * {"key": "longKeyExample", "value": 73, "lastUpdateTs": 1609459200003},
     * {"key": "jsonKeyExample",
     * "value": {
     * "someNumber": 42,
     * "someArray": [1,2,3],
     * "someNestedObject": {"key": "value"}
     * },
     * "lastUpdateTs": 1609459200004
     * }
     * ]
     * Referencing a non-existing entity Id or invalid entity type will cause an error.
     *
     * @param  EnumEntityType  $entityType
     * @param  string  $entityId
     * @param  EnumTelemetryScope  $scope
     * @param  array  $keys
     * @return array|mixed
     *
     * @throws \Throwable
     *
     * @author Sabiee
     *
     * @group TENANT_ADMIN | CUSTOMER_USER
     */
    public function getAttributesByScope(EnumEntityType $entityType, string $entityId, EnumTelemetryScope $scope, array $keys)
    {
        Thingsboard::validation(! Str::isUuid($entityId), 'uuid', ['attribute' => 'entityId']);

        Thingsboard::validation(empty($keys), 'required', ['attribute' => 'keys']);

        $keys = implode(',', $keys);

        return $this->api()->get("plugins/telemetry/{$entityType}/{$entityId}/values/attributes/{$scope}", ['keys' => $keys])->json();
    }

    /**
     * Returns a range of time-series values for specified entity. Returns not aggregated data by default. Use aggregation function ('agg') and aggregation interval ('interval') to enable aggregation of the results on the database / server side. The aggregation is generally more efficient then fetching all records.
     *
     * {
     * "temperature": [
     * {
     * "value": 36.7,
     * "ts": 1609459200000
     * },
     * {
     * "value": 36.6,
     * "ts": 1609459201000
     * }
     * ]
     * }
     * Referencing a non-existing entity Id or invalid entity type will cause an error.
     *
     * @param  EnumEntityType  $entityType
     * @param  string  $entityId
     * @param  array  $keys
     * @param  int  $startTs
     * @param  int  $endTs
     * @param  int  $interval
     * @param  int  $limit
     * @param  EnumTelemetryAggregation  $agg
     * @param  EnumSortOrder  $orderBy
     * @param  bool  $useStrictDataTypes
     * @return array|mixed
     *
     * @author Sabiee
     *
     * @group TENANT_ADMIN | CUSTOMER_USER
     */
    public function getTimeseries(EnumEntityType $entityType, string $entityId, array $keys,
        int $startTs, int $endTs, int $interval, int $limit, EnumTelemetryAggregation $agg,
        EnumSortOrder $orderBy, bool $useStrictDataTypes)
    {
        Thingsboard::validation(! Str::isUuid($entityId), 'uuid', ['attribute' => 'entityId']);

        Thingsboard::validation(empty($keys), 'required', ['attribute' => 'keys']);

        $keys = implode(',', $keys);

        return $this->api()->get("plugins/telemetry/{$entityType}/{$entityId}/values/timeseries", [
            'keys' => $keys,
            'startTs' => $startTs,
            'endTs' => $endTs,
            'interval' => $interval,
            'limit' => $limit,
            'agg' => $agg,
            'orderBy' => $orderBy,
            'useStrictDataTypes' => $useStrictDataTypes,
        ])->json();
    }
}
