<?php
require_once dirname(__DIR__, 2). '/vendor/autoload.php';
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Decimal128;

# List of recognized and allowed MongoDB collections from the database
const COLLECTIONS = [
    'access_levels',
    'account_requests',
    'accounts',
    'archive_requests',
    'document_versions',
    'documents',
    'folders',
    'login_credentials',
    'organizations',
];

# A helper function to instantiate a QueryBuilder object prepared to the specified collection name
function coll(string $collectionName): mixed {
    if (!in_array($collectionName, COLLECTIONS)) 
        throw new InvalidArgumentException("Collection $collectionName not found in database!");
    return (new QueryBuilder())->collection($collectionName);
}

# A helper function to abstract and simplify the process of creating an ObjectId from any type without worrying about manual exception handling
function oid(mixed $oid): ?ObjectId {
    if ($oid instanceof ObjectId) return $oid;                                                          # Return back the variable if it is already an ObjectId
    try { return new ObjectId($oid); }                                                                  # Try to create an ObjectId from the variable
    catch (Exception $e) { error_log('Failed to create ObjectId from value: '. $oid); return null; }    # If it fails, return null
}

# A helper function to strictly verify that a given ObjectId-ified variable is a legitimate ObjectId
function valid_oid(mixed $oid): bool { return $oid instanceof ObjectId; }

# A class for executing queries, which supports batch queries
final class QueryRunner {
    private array $results = [];
    private array $seen = [];

    private function __construct(array $results = [], array $seen = []) {
        $this->results = $results;
        $this->seen = $seen;
    }

    # A helper function to safely define and perform queries on a collection or set of collections
    public static function tryWithCollections(array $queries): self {
        # STEP 1: Initialize a new QueryRunner using the private constructor
        $queryRunner = new self();

        # STEP 2: Iterate on the specified queries, which are defined in a key-value pair format as follows: K (string) => V (callable)
        foreach ($queries as $collectionName => $operation) {
            if (!is_string($collectionName) || !is_callable($operation)) continue;  # If the format is not followed, skip the query
            # POSTCONDITION(S): $collectionName is a string, and $operation is a callable, thus the format is followed

            $collectionName = strtolower(trim($collectionName));
            if (isset($queryRunner->seen[$collectionName])) continue;    # If the collection name has already been seen (i.e. duplicated specification), skip iteration and continue to the next one to prevent result data overrides
            $queryRunner->seen[$collectionName] = true;                  # Assuming the above was not true, declare the collection name as seen
            # POSTCONDITION(S): Duplicates of $collectionName in subsequent iterations will be duly skipped

            try {$queryRunner->results[$collectionName] = $operation(coll($collectionName));}   # Attempt to perform the query operation and store its result, if successful, in the result set's entry that corresponds to the pertinent collection
            catch (Throwable $e) {continue;}                                                    # If the above query operation fails, gracefully skip it and continue processing the rest of the queries
            # POSTCONDITION(S): The result set may or may not contain the result for the given collection name depending on the above attempt's success
        }
        return $queryRunner;
    }

    # A helper function to fetch the stored results of a QueryRunner, typically called after tryWithCollections($queries)
    public function getResults(string ...$keys): array {
        if (empty($keys)) return $this->results;
        # POSTCONDITION(S): $keys >= 1

        $keys = array_map(fn ($k)=> strtolower(trim($k)), $keys);   # Normalize all keys once to lowercase and remove leading/trailing whitespaces

        $monoKey = count($keys) === 1;                              # Check if there is only one key in $keys
        $K0 = $keys[0];                                             # Store the first key in $keys in a variable

        # CASE 1: Parameter is any of the following: (1)'*', (2)'all', (3)'{key_name}'
        if ($monoKey) return match ($K0) {
            '*', 'all'  => $this->results,
            default     => $this->results[$K0] ?? []};
        # CASE 2: Parameters are '{key_1}', '{key_2}', ...
        else {
            $returnArray = [];
            foreach ($keys as $K_) {
                if (!array_key_exists($K_, $this->results)) continue;
                if (isset($returnArray[$K_])) continue;
                $returnArray[$K_] = $this->results[$K_];
            }    
            return $returnArray;
        }
    }
}

# A class for building and executing MongoDB queries over HTTPS
final class QueryBuilder {
    private const ALLOWED_OPERATIONS = [
        'find' => true,
        'findOne' => true,
        'insertOne' => true,
        'updateOne' => true,
        'deleteOne' => true,
        'deleteMany' => true,
        'countDocuments' => true
    ];

    private const ALLOWED_OPERATORS = [
        '$set' => true, '$inc' => true, '$gt' => true, '$gte' => true,
        '$lt' => true, '$lte' => true, '$ne' => true, '$in' => true,
        '$nin' => true, '$or' => true, '$and' => true, '$oid' => true, '$date' => true
    ];

    private const ALLOWED_COLLECTIONS = [
        'access_levels' => true,
        'account_requests' => true,
        'accounts' => true,
        'archive_requests' => true,
        'document_versions' => true,
        'documents' => true,
        'folders' => true,
        'login_credentials' => true,
        'organizations' => true
    ];

    private string $collection = '';
    private string $operation = 'find';

    private array $filter = [];
    private array $data = [];
    private array $update = [];
    private array $options = [];

    private string $endpoint;
    private string $apiKey;

    private $ch;

    private function getCurlHandle() {
        if ($this->ch === null) $this->ch = curl_init();
        curl_reset($this->ch);
        return $this->ch;
    }

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
        if (!isset(self::ALLOWED_COLLECTIONS[$collection])) 
            throw new InvalidArgumentException('Invalid collection');

        $this->collection = $collection;
        return $this;
    }

    public function countDocuments(array $filter = []): self {
        $this->operation = 'countDocuments';
        $this->validateArray($filter);
        $this->filter = $this->encodeTypes($filter);
        return $this;
    }

    public function find(array $filter = []): self {
        $this->operation = 'find';
        $this->validateArray($filter);
        $this->filter = $this->encodeTypes($filter);
        return $this;
    }

    public function findOne(array $filter = []): self {
        $this->operation = 'findOne';
        $this->validateArray($filter);
        $this->filter = $this->encodeTypes($filter);
        return $this;
    }

    public function insertOne(array $data): self {
        if ($data === []) 
            throw new InvalidArgumentException('Insert data cannot be empty');

        $this->validateArray($data);

        $this->operation = 'insertOne';
        $this->data = $this->encodeTypes($data);
        return $this;
    }

    public function updateOne(array $filter, array $update, array $options = []): self {
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

    public function deleteOne(array $filter): self {
        if ($filter === []) 
            throw new InvalidArgumentException('Empty delete filters are not allowed');

        $this->validateArray($filter);

        $this->operation = 'deleteOne';
        $this->filter = $this->encodeTypes($filter);
        return $this;
    }

    public function deleteMany(array $filter): self {
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

    public static function getInsertedId(array $result): ?ObjectId {
        if (!isset($result['insertedId'])) return null;

        $id = $result['insertedId'];
        if (!is_string($id)) throw new InvalidArgumentException('InsertedId must be a string');
        return new ObjectId($id);
    }

    public static function getDeletedCount(array $result): int {
        if (!isset($result['deletedCount'])) return 0;

        $count = $result['deletedCount'];
        if (!is_int($count)) 
            throw new InvalidArgumentException('DeletedCount must be an integer');

        if ($count < 0)
            throw new InvalidArgumentException('DeletedCount cannot be negative');

        return $count;
    }

    public function limit(int $limit): self {
        if ($limit < 1 || $limit > 1000) {
            throw new InvalidArgumentException('Limit must be between 1 and 1000');
        }

        $this->options['limit'] = $limit;
        return $this;
    }

    public function skip(int $skip): self {
        if ($this->operation === 'countDocuments') {
            throw new InvalidArgumentException('Skip is not supported for countDocuments');
        }

        if ($skip < 0) {
            throw new InvalidArgumentException('Skip cannot be negative');
        }

        $this->options['skip'] = $skip;
        return $this;
    }

    public function sort(array $sort): self {
        foreach ($sort as $field => $direction) {
            if (!is_string($field)) throw new InvalidArgumentException("Invalid sort field");
            if (!in_array($direction, [1, -1], true)) throw new InvalidArgumentException("Sort direction must be 1 or -1");
        }

        $this->options['sort'] = $sort;
        return $this;
    }

    public function project(array $projection): self {
        $this->options['projection'] = $projection;
        return $this;
    }

    public function execute(): mixed {
        try {
            # Safeguard from exceptions early
            if ($this->collection === '') throw new RuntimeException('Collection is required');
            if (!isset(self::ALLOWED_OPERATIONS[$this->operation])) throw new RuntimeException('Invalid operation');

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
        }
        catch (Throwable $e) {
            # Log the caught error backend-side to prevent the propagation of noisy errors to the frontend 
            error_log(sprintf(
                '[QueryBuilder] collection=%s operation=%s error=%s: %s', $this->collection, $this->operation,
                get_class($e), $e->getMessage()));
            # Return null upon failure 
            return null;
        } 
        finally { $this->reset(); } # In all cases, succeeding or failing, reset the query parameters
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
            if (is_string($key) && str_starts_with($key, '$') && !isset(self::ALLOWED_OPERATORS[$key])) 
                throw new InvalidArgumentException("Disallowed operator: {$key}");
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
        if ($value instanceof MongoDB\BSON\UTCDateTime) return ['$date' => $value->toDateTime()->format(DATE_ATOM)];
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