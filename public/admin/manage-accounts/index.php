<?php
    require_once '../../../bootstrap/app.php';
    load (
        'authentication',
        'authorization',
        'navbar',
        'footer',
        'filter_chips',
        'mongodb'
    );

    if (!is_logged_in()) {
        header('location: '. $app_url. '/auth/login.php');
        exit;
    }

    if (!can_access_admin_pages($permissions)) {
        die("You do not have permission to access this resource.");
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initialize_page('Manage Accounts | YanoDASH'); ?>
        <style>
            div.sec {
                padding: 8px 16px;
                margin: 16px;
                border-radius: 8px;
                background: #e5e5e5;
            }

            table.acclist {
                width: max-content;
                min-width: 100%;
                margin: 8px 0;
                background: white;
                border: 2px solid lightgray;
                border-radius: 8px;
                padding: 6px;
                overflow: auto;
            }

            table.acclist .headingrow th {
                padding: 8px;
            }

            table.acclist td {
                padding: 4px 8px;
                border-radius: 8px;
                border: 1px solid #CCC;
                box-sizing: border-box;
            }

            table.acclist tr:not(.headingrow):hover {
                background: rgba(255,0,0,0.12);
            }

            table.acclist tr:not(.headingrow) td {
                transition: transform 0.2s ease;
            }

            table.acclist tr:not(.headingrow) td:hover {
                background: rgba(255,0,0,0.17);
                outline: 1px solid #800000;
                outline-offset: -3px;
                transform: translateY(-3px);
            }

            .table-scroll {
                overflow: auto;
                width: 100%;
                height: 400px;
            }
        </style>
    </head>
    <body>
        <?php echo navbar(); ?>
        <div class="page-contents no-padding">
            <div class="pch">
                <h1>
                    Manage Accounts
                </h1>
            </div>
            <div style="padding: 32px">
            <h2>Pending Account Requests</h2>
            <div class="sec">
                <h3>Sort by:</h3>
                <?php echo filter_chips(["Newest", "Oldest", "Alphabetical"], "Newest")?>
                <table class="acclist">
                    <tr class="headingrow">
                        <th>Date</th>
                        <th>Requester</th>
                        <th>Requester's Email</th>
                        <th>Organization</th>
                        <th>Position</th>
                        <th>Actions</th>
                    </tr>
                    <tr>
                        <td>2026-05-08</td>
                        <td>Finn</td>
                        <td>finn@usep.edu.ph</td>
                        <td>-</td>
                        <td>Hero</td>
                        <td>
                            <button>Approve</button>
                            <button>Reject</button>
                        </td>
                    </tr>
                    <tr>
                        <td>2026-05-08</td>
                        <td>Jake</td>
                        <td>jake@usep.edu.ph</td>
                        <td>-</td>
                        <td>Sidekick</td>
                        <td>
                            <button>Approve</button>
                            <button>Reject</button>
                        </td>
                    </tr>
                    <tr>
                        <td>2026-05-04</td>
                        <td>Princess Bubblegum</td>
                        <td>pb@usep.edu.ph</td>
                        <td>Candy Kingdom</td>
                        <td>Princess</td>
                        <td>
                            <button>Approve</button>
                            <button>Reject</button>
                        </td>
                    </tr>
                    <tr>
                        <td>2026-05-04</td>
                        <td>Marceline</td>
                        <td>marcy@usep.edu.ph</td>
                        <td>Nightosphere</td>
                        <td>Vampire Queen</td>
                        <td>
                            <button>Approve</button>
                            <button>Reject</button>
                        </td>
                    </tr>
                    <tr>
                        <td>2026-05-01</td>
                        <td>Simon Petrikov</td>
                        <td>iceking@usep.edu.ph</td>
                        <td>Ice Kingdom </td>
                        <td>King</td>
                        <td>
                            <button>Approve</button>
                            <button>Reject</button>
                        </td>
                    </tr>
                </table>
            </div>
            <h2>Registered Accounts</h2>
            <div class="sec">
                <h3>Sort by:</h3>
                <?php echo filter_chips(["Newest", "Oldest", "Alphabetical"], "Newest")?>
                <div class="table-scroll">
                <table class="acclist">
                    <tr class="headingrow">
                        <th>Name</th>
                        <th>Email Address</th>
                        <th>Student ID Number</th>
                        <th>College</th>
                        <th>Organization</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Date Joined</th>
                        <th>Access Level</th>
                        <th>Access Domains</th>
                    </tr>
                    <?php
                        $accounts = QueryRunner::tryWithCollections([
                            ($C1='accounts') 
                                => fn ($C1)=> $C1->find(['_id' => ['$nin' => [null]]])->execute()
                        ])->getResults($C1);
                        // var_dump($accounts);
                    ?>
                    <?php if (!empty($accounts)):?>
                        <?php 
                            foreach($accounts as $account) {
                                $name = $account['name']['first_name']. ' '. $account['name']['last_name'];
                                $email = $account['email_address'];
                                $idnum = $account['student_id_number'] ?? '(not provided)';
                                $college = $account['college'] ?? '(unknown)';
                                $org = isset($account['organization']) 
                                    ? QueryRunner::tryWithCollections([
                                        ($C2='organizations')
                                            => fn ($C2)=> $C2->findOne(['_id' => $account['organization'] ?? null])->execute()
                                    ])->getResults($C2)['organization_name'] ?? '(none)'
                                    : "(none)";
                                $department = $account['department'] ?? '(unknown)';
                                $position = $account['position'];
                                $datejoined = isset($account['date_joined']) 
                                    ? (new DateTime($account['date_joined']))->setTimezone(new DateTimeZone('Asia/Manila'))->format('M d Y, g:i A') ?? null
                                    : "(unknown)";
                                $acclevel = QueryRunner::tryWithCollections([
                                    ($C3='access_levels')
                                        => fn ($C3)=> $C3->findOne(['_id' => $account['access_level']])->execute()
                                ])->getResults($C3)['level'] ?? 'Viewer';

                                $name = htmlspecialchars($name);
                                $email = htmlspecialchars($email);
                                $idnum = htmlspecialchars($idnum);
                                $college = htmlspecialchars($college);
                                $org = htmlspecialchars($org);
                                $department = htmlspecialchars($department);
                                $position = htmlspecialchars($position);
                                $datejoined = htmlspecialchars($datejoined);
                                $acclevel = htmlspecialchars($acclevel);

                                echo <<< HTML
                                    <tr>
                                        <td>$name</td>
                                        <td>$email</td>
                                        <td>$idnum</td>
                                        <td>$college</td>
                                        <td>$org</td>
                                        <td>$department</td>
                                        <td>$position</td>
                                        <td>$datejoined</td>
                                        <td>$acclevel</td>
                                    </tr>
                                HTML;
                            }
                        ?>
                    <?php else: ?>
                    <?php endif; ?>
                </table>
                </div>
                <a class="btn action" href="create-new.php">Create New Account</a>
            </div>
            </div>
        </div>
        <?php echo footer(); ?>
    </body>
</html>