<?php
final class DocInfo {
    public const VALID_STATUSES = [
        'EDITING' => true,                      # Fresh DMS status
        'PENDING_ARCHIVAL_PROCESS' => true,     # Submitted via Request Archiving, now in waitlist
        'PENDING_AUDIT' => true,                # Seen and accepted by General Auditor, now awaiting audit completion
        'PENDING_ACKNOWLEDGEMENT' => true,      # Seen and accepted by Adviser, now awaiting acknowledgement
        'PENDING_INTERNAL_APPROVAL' => true,    # Seen and accepted by President, now awaiting internal approval/finalization
        'PENDING_EXTERNAL_APPROVAL' => true,    # Seen and accepted by external stakeholder, now awaiting external approval/finalization
        'ARCHIVED' => true,                     # Once approved/finalized and effectively archived
        'PUBLICIZED' => true                    # Once set to show up in Private Archive
    ];

    public const VALID_CATEGORIES = [
        'ACCOMPLISHMENT_REPORT' => true,
        'ACTIVITY_DESIGN' => true,
        'ATTENDANCE' => true,
        'FINANCIAL_STATEMENT' => true,
        'MEMORANDUM' => true,
        'MERCH_DOC' => true,
        'MEETING_MINUTES' => true,
        'MEETING_NOTICE' => true,
        'PROJECT_PROPOSAL' => true,
        'GENERAL' => true
    ];
}
?>