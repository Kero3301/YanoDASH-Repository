<?php
require_once 'LogDomain.php';
require_once 'LogSeverity.php';
require_once dirname(__DIR__, 2). '/Common/ReadableList.php';

final class Logs {
    public static function write($domain, $severity, $message): void {
        try {
            # Parameter validation
            $domainValid = $domain instanceof LogDomain;
            $severityValid = $severity instanceof LogSeverity;
            $messageValid = is_string($message) && trim($message) !== '';
            if (!$domainValid || !$severityValid || !$messageValid) {
                $invalidArguments = [];
                if (!$domainValid) $invalidArguments[] = "domain";
                if (!$severityValid) $invalidArguments[] = "severity";
                if (!$messageValid) $invalidArguments[] = "message";
                $fmtdList = ReadableList::format($invalidArguments);
                $err = count($invalidArguments) === 1
                    ? "$fmtdList is invalid"
                    : "$fmtdList are invalid";
                throw new InvalidArgumentException($err);
            }

            # Log writing
            $d = $domain->value;
            $s = $severity->value;
            error_log("[$d:$s] $message");
        } catch (\Throwable $t) {
            $d = LogDomain::Runtime->value;
            $s = LogSeverity::Error->value;
            $m = $t->getMessage();

            error_log("[$d:$s] Failed to write log: $m");
        }
    }
}
?>