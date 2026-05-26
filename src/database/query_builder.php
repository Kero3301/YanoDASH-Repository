<?php
require_once dirname(__DIR__, 2). '/vendor/autoload.php';
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Decimal128;

final class QueryBuilder {
    private const ALLOWED_OPERATIONS = [
        'find',
        'findOne',
        'insertOne',
        'updateOne',
        'deleteOne',
    ];

    private const ALLOWED_OPERATORS = [
        '$set',
        '$inc',
        '$gt',
        '$gte',
        '$lt',
        '$lte',
        '$ne',
        '$in',
        '$nin',
        '$or',
        '$and',
        '$oid',
        '$date'
    ];

    private const ALLOWED_COLLECTIONS = [
        'access_levels',
        'account_requests',
        'accounts',
        'archive_requests',
        'document_versions',
        'documents',
        'folders',
        'login_credentials',
        'organizations'
    ];

    private string $collection = '';
    private string $operation = 'find';

    private array $filter = [];
    private array $data = [];
    private array $update = [];
    private array $options = [];

    private string $endpoint;
    private string $apiKey;

    public function __construct() {
        $baseUrl = $_ENV['YANODASH_API_URL'] ?? '';
        $apiKey = $_ENV['YANODASH_API_KEY'] ?? '';

        if ($baseUrl === '') throw new RuntimeException('YANODASH_API_URL is missing');
        if ($apiKey === '') throw new RuntimeException('YANODASH_API_KEY is missing');

        $this->endpoint = rtrim($baseUrl, '/') . '/query';
        $this->apiKey = $apiKey;

        $this->validateEndpoint();
    }

    public function collection(string $collection): self {
        if (!in_array($collection, self::ALLOWED_COLLECTIONS, true)) {
            throw new InvalidArgumentException('Invalid collection');
        }

        $this->collection = $collection;
        return $this;
    }

    public function find(array $filter = []): self {
        $this->operation = 'find';
        $this->validateArray($filter);

        $this->filter = $filter;
        return $this;
    }

    public function findOne(array $filter = []): self {
        $this->operation = 'findOne';
        $this->validateArray($filter);

        $this->filter = $filter;
        return $this;
    }

    public function insertOne(array $data): self {
        if ($data === []) {
            throw new InvalidArgumentException('Insert data cannot be empty');
        }

        $this->validateArray($data);

        $this->operation = 'insertOne';
        $this->data = $data;
        return $this;
    }

    public function updateOne(array $filter, array $update, array $options = []): self {
        if ($filter === []) {
            throw new InvalidArgumentException('Empty update filters are not allowed');
        }

        if ($update === []) {
            throw new InvalidArgumentException('Update payload cannot be empty');
        }

        $this->validateArray($filter);
        $this->validateArray($update);

        $this->operation = 'updateOne';
        $this->filter = $filter;
        $this->update = $update;
        $this->options = $options;
        return $this;
    }

    public function deleteOne(array $filter): self {
        if ($filter === []) {
            throw new InvalidArgumentException('Empty delete filters are not allowed');
        }

        $this->validateArray($filter);

        $this->operation = 'deleteOne';
        $this->filter = $filter;
        return $this;
    }

    public function limit(int $limit): self {
        if ($limit < 1 || $limit > 1000) {
            throw new InvalidArgumentException('Limit must be between 1 and 1000');
        }

        $this->options['limit'] = $limit;
        return $this;
    }

    public function sort(array $sort): self {
        $this->options['sort'] = $sort;
        return $this;
    }

    public function project(array $projection): self {
        $this->options['projection'] = $projection;
        return $this;
    }

    public function execute(): array {
        if ($this->collection === '') {
            throw new RuntimeException('Collection is required');
        }

        if (!in_array($this->operation, self::ALLOWED_OPERATIONS, true)) throw new RuntimeException('Invalid operation');

        $payload = [
            'collection' => $this->collection,
            'operation' => $this->operation,
            'filter' => $this->encodeTypes($this->filter),
            'data' => $this->encodeTypes($this->data),
            'update' => $this->encodeTypes($this->update),
            'options' => $this->encodeTypes($this->options),
        ];

        $jsonPayload = json_encode($payload, JSON_THROW_ON_ERROR);

        $ch = curl_init($this->endpoint);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $jsonPayload,

            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'x-api-key: ' . $this->apiKey,
            ],

            CURLOPT_TIMEOUT => 60,
            CURLOPT_CONNECTTIMEOUT => 30,

            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);

            curl_close($ch);

            throw new RuntimeException(
                'Request failed: ' . $error
            );
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 400) {
            // Add this debug line to see exactly what the Node API says
            throw new RuntimeException('Database operation failed: ' . $response);
        }

        try { $decoded = json_decode($response, true, 512, JSON_THROW_ON_ERROR); } 
        catch (JsonException $e) { throw new RuntimeException('Invalid API response'); }

        if ($httpCode >= 400) throw new RuntimeException('Database operation failed');

        $this->reset();

        return $this->decodeTypes($decoded);
    }

    private function validateEndpoint(): void {
        $url = parse_url($this->endpoint);
        if (!$url || !isset($url['scheme'], $url['host'])) throw new RuntimeException('Invalid API endpoint');

        $isHttps = $url['scheme'] === 'https';
        $isLocalhost = in_array(
            $url['host'],
            ['localhost', '127.0.0.1', '::1'],
            true
        );

        if (!$isHttps && !$isLocalhost) throw new RuntimeException('HTTPS is required outside local development');
    }

    private function validateArray(array $input): void {
        foreach ($input as $key => $value) {
            if (
                is_string($key) &&
                str_starts_with($key, '$') &&
                !in_array(
                    $key,
                    self::ALLOWED_OPERATORS,
                    true
                )
            ) throw new InvalidArgumentException("Disallowed operator: {$key}");
            if (is_string($key) && str_contains($key, "\0")) throw new InvalidArgumentException('Invalid key detected');
            if (is_array($value)) $this->validateArray($value);
        }
    }

    private function reset(): void {
        $this->operation = 'find';
        $this->filter = [];
        $this->data = [];
        $this->update = [];
        $this->options = [];
    }

    private function encodeTypes(mixed $value): mixed {
        if (is_array($value)) {
            $out = [];
            foreach ($value as $k => $v) $out[$k] = $this->encodeTypes($v);
            return $out;
        }
        if (is_int($value)) return ['$int' => $value];
        if (is_float($value)) return ['$double' => $value];
        if ($value instanceof DateTimeInterface) return ['$date' => $value->format(DATE_ATOM)];
        if ($value instanceof MongoDB\BSON\ObjectId) return ['$oid' => (string) $value];
        if (is_string($value) && preg_match('/^[a-f\d]{24}$/i', $value)) return ['$oid' => $value];
        if ($value instanceof MongoDB\BSON\Decimal128) return ['$decimal' => (string)$value];

        return $value;
    }

    private function decodeTypes(mixed $value): mixed {
        if (!is_array($value)) return $value;
        if (isset($value['$oid'])) {
            if (!ObjectId::isValid($value['$oid'])) throw new InvalidArgumentException("Invalid ObjectId");
            return new ObjectId($value['$oid']);
        }
        if (isset($value['$date'])) return new DateTimeImmutable($value['$date']);
        if (isset($value['$decimal'])) return Decimal128::fromString($value['$decimal']);
        if (isset($value['$int'])) return (int)$value['$int'];
        if (isset($value['$double'])) return (float)$value['$double'];

        foreach ($value as $k => $v) $value[$k] = $this->decodeTypes($v);

        return $value;
    }
}