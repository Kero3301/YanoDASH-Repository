<?php

require_once dirname(__DIR__, 3) . '/vendor/autoload.php';
require_once 'MongoPolicies.php';
require_once 'MongoDataTypes.php';

# A helper function to instantiate a QueryBuilder object prepared to the specified collection name
function coll(string $collectionName): mixed {
    if (!isset(MongoPolicies::VALID_COLLECTIONS[$collectionName]))
        throw new InvalidArgumentException("Collection $collectionName not found in Database!");
    return (new QueryBuilder())->collection($collectionName);
}

# A class for building and executing MongoDB queries over HTTPS
final class MongoHTTPClient
{
    private string $endpoint;
    private string $apiKey;
    private $ch;

    private string $collection = '';
    private string $operation = 'find';
    private array $filter = [];
    private array $data = [];
    private array $update = [];
    private array $options = [];

    private function getCurlHandle()
    {
        if ($this->ch === null) $this->ch = curl_init();
        curl_reset($this->ch);
        return $this->ch;
    }

    # Parameterless
    public function __construct($collName = null)
    {
        $baseUrl = $_ENV['YANODASH_API_URL'] ?? '';
        $apiKey = $_ENV['YANODASH_API_KEY'] ?? '';

        if ($baseUrl === '') throw new RuntimeException('YANODASH_API_URL is missing');
        if ($apiKey === '') throw new RuntimeException('YANODASH_API_KEY is missing');

        $this->endpoint = rtrim($baseUrl, '/') . '/query';
        $this->apiKey = $apiKey;

        $this->validateEndpoint();

        if (is_string($collName)) $this->collection($collName);
    }

    public function collection(string $collection): self
    {
        if (!isset(MongoPolicies::VALID_COLLECTIONS[$collection]))
            throw new InvalidArgumentException('Invalid collection');

        $this->collection = $collection;
        return $this;
    }

    public function countDocuments(array $filter = []): self
    {
        $this->operation = 'countDocuments';
        $this->validateArray($filter);
        $this->filter = $this->encodeTypes($filter);
        return $this;
    }

    public function find(array $filter = []): self
    {
        $this->operation = 'find';
        $this->validateArray($filter);
        $this->filter = $this->encodeTypes($filter);
        return $this;
    }

    public function findOne(array $filter = []): self
    {
        $this->operation = 'findOne';
        $this->validateArray($filter);
        $this->filter = $this->encodeTypes($filter);
        return $this;
    }

    public function insertOne(array $data): self
    {
        if ($data === [])
            throw new InvalidArgumentException('Insert data cannot be empty');

        $this->validateArray($data);

        $this->operation = 'insertOne';
        $this->data = $this->encodeTypes($data);
        return $this;
    }

    public function updateOne(array $filter, array $update, array $options = []): self
    {
        if ($filter === [])
            throw new InvalidArgumentException('Empty update filters are not allowed');

        if ($update === [])
            throw new InvalidArgumentException('Update payload cannot be empty');

        $this->validateArray($filter);
        $this->validateArray($update);

        $this->operation = 'updateOne';
        $this->filter = $this->encodeTypes($filter);
        $this->update = $this->encodeTypes($update);
        $this->options = $this->encodeTypes($options);
        return $this;
    }

    public function deleteOne(array $filter): self
    {
        if ($filter === [])
            throw new InvalidArgumentException('Empty delete filters are not allowed');

        $this->validateArray($filter);

        $this->operation = 'deleteOne';
        $this->filter = $this->encodeTypes($filter);
        return $this;
    }

    public function deleteMany(array $filter): self
    {
        if ($filter === []) {
            throw new InvalidArgumentException(
                'Empty delete filters are not allowed'
            );
        }

        $this->validateArray($filter);

        $this->operation = 'deleteMany';
        $this->filter = $this->encodeTypes($filter);

        return $this;
    }

    public static function getInsertedId(array $result): ?ObjectId
    {
        if (!isset($result['insertedId'])) return null;

        $id = $result['insertedId'];
        if (!is_string($id)) throw new InvalidArgumentException('InsertedId must be a string');
        return new ObjectId($id);
    }

    public static function getDeletedCount(array $result): int
    {
        if (!isset($result['deletedCount'])) return 0;

        $count = $result['deletedCount'];
        if (!is_int($count))
            throw new InvalidArgumentException('DeletedCount must be an integer');

        if ($count < 0)
            throw new InvalidArgumentException('DeletedCount cannot be negative');

        return $count;
    }

    public function limit(int $limit): self
    {
        if ($limit < 1 || $limit > 1000) {
            throw new InvalidArgumentException('Limit must be between 1 and 1000');
        }

        $this->options['limit'] = $limit;
        return $this;
    }

    public function skip(int $skip): self
    {
        if ($this->operation === 'countDocuments') {
            throw new InvalidArgumentException('Skip is not supported for countDocuments');
        }

        if ($skip < 0) {
            throw new InvalidArgumentException('Skip cannot be negative');
        }

        $this->options['skip'] = $skip;
        return $this;
    }

    public function sort(array $sort): self
    {
        foreach ($sort as $field => $direction) {
            if (!is_string($field)) throw new InvalidArgumentException("Invalid sort field");
            if (!in_array($direction, [1, -1], true)) throw new InvalidArgumentException("Sort direction must be 1 or -1");
        }

        $this->options['sort'] = $sort;
        return $this;
    }

    public function project(array $projection): self
    {
        $this->options['projection'] = $projection;
        return $this;
    }

    public function execute(): mixed
    {
        try {
            # Safeguard from exceptions early
            if ($this->collection === '') throw new RuntimeException('Collection is required');
            if (!isset(MongoPolicies::VALID_OPERATIONS[$this->operation])) throw new RuntimeException('Invalid operation');

            # Prepare the query payload
            $payload = [
                'collection' => $this->collection,
                'operation' => $this->operation,
                'filter' => $this->filter,
                'data' => $this->data,
                'update' => $this->update,
                'options' => $this->options,
            ];
            $jsonPayload = json_encode($payload, JSON_THROW_ON_ERROR);

            # Prepare the cURL HTTP request
            $ch = $this->getCurlHandle();
            curl_setopt_array($ch, [
                CURLOPT_URL => $this->endpoint,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
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

            # Perform the HTTP request
            $response = curl_exec($ch);
            if ($response === false) throw new RuntimeException('cURL error: ' . curl_error($ch));

            # Check the returned HTTP status code
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode < 200 || $httpCode >= 300) throw new RuntimeException("Unexpected HTTP status {$httpCode}");

            # Attempt to decode the response from JSON data
            $decoded = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
            $decoded = $this->decodeTypes($decoded);

            # Return the decoded data
            return $decoded;
        } catch (Throwable $e) {
            # Log the caught error backend-side to prevent the propagation of noisy errors to the frontend 
            error_log(sprintf(
                '[QueryBuilder] collection=%s operation=%s error=%s: %s', $this->collection, $this->operation,
                get_class($e), $e->getMessage()));
            # Return null upon failure 
            return null;
        } finally {
            $this->reset();
        } # In all cases, succeeding or failing, reset the query parameters
    }

    private function validateEndpoint(): void
    {
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

    private function validateArray(array $input): void
    {
        foreach ($input as $key => $value) {
            if (is_string($key) && str_starts_with($key, '$') && !isset(MongoPolicies::VALID_OPERATORS[$key]))
                throw new InvalidArgumentException("Disallowed operator: {$key}");
            if (is_string($key) && str_contains($key, "\0")) throw new InvalidArgumentException('Invalid key detected');
            if (is_array($value)) $this->validateArray($value);
        }
    }

    private function reset(): void
    {
        $this->operation = 'find';
        $this->filter = [];
        $this->data = [];
        $this->update = [];
        $this->options = [];
    }

    private function encodeTypes(mixed $value): mixed
    {
        if (is_array($value)) {
            $out = [];
            foreach ($value as $k => $v) $out[$k] = $this->encodeTypes($v);
            return $out;
        }
        if (is_int($value)) return ['$int' => $value];
        if (is_float($value)) return ['$double' => $value];
        if ($value instanceof MongoDB\BSON\UTCDateTime) return ['$date' => $value->toDateTime()->format(DATE_ATOM)];
        if ($value instanceof DateTimeInterface) return ['$date' => $value->format(DATE_ATOM)];
        if ($value instanceof MongoDB\BSON\ObjectId) return ['$oid' => (string)$value];
        if (is_string($value) && preg_match('/^[a-f\d]{24}$/i', $value)) return ['$oid' => $value];
        if ($value instanceof MongoDB\BSON\Decimal128) return ['$decimal' => (string)$value];

        return $value;
    }

    private function decodeTypes(mixed $value): mixed
    {
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