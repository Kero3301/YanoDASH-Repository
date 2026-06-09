<?php

class QueryRunner
{
    private array $results = [];
    private array $seen = [];

    private function __construct($results = [], $seen = [])
    {
        $this->results = is_array($results)? $results : [];
        $this->seen = is_array($seen) ? $seen : [];
    }

    # A helper function to safely define and perform queries on a collection or set of collections
    public static function tryWithCollections(array $queries): self
    {
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

            try {
                $queryRunner->results[$collectionName] = $operation(new MongoHTTPClient($collectionName));
            }   # Attempt to perform the query operation and store its result, if successful, in the result set's entry that corresponds to the pertinent collection
            catch (Throwable $e) {
                continue;
            }                                                    # If the above query operation fails, gracefully skip it and continue processing the rest of the queries
            # POSTCONDITION(S): The result set may or may not contain the result for the given collection name depending on the above attempt's success
        }
        return $queryRunner;
    }

    # A helper function to fetch the stored results of a QueryRunner, typically called after tryWithCollections($queries)
    public function getResults(string ...$keys): array
    {
        if (empty($keys)) return $this->results;
        # POSTCONDITION(S): $keys >= 1

        $keys = array_map(fn($k) => strtolower(trim($k)), $keys);   # Normalize all keys once to lowercase and remove leading/trailing whitespaces

        $monoKey = count($keys) === 1;                              # Check if there is only one key in $keys
        $K0 = $keys[0];                                             # Store the first key in $keys in a variable

        # CASE 1: Parameter is any of the following: (1)'*', (2)'all', (3)'{key_name}'
        if ($monoKey) return match ($K0) {
            '*', 'all' => $this->results,
            default => $this->results[$K0] ?? []
        };
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