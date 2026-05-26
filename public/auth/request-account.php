<?php
    error_reporting(0);

    session_start();
    require_once '../../bootstrap/app.php';
    load (
        'navbar',
        'vendor_autoload',
        'accordion',
        'footer',
        'mongodb_client',
        'mongodb_collections',
        'user_form'
    );

    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email_address = $_POST['email_address'];
    $student_id_number = $_POST['student_id_number'];
    $college = $_POST['college'];
    $organization = $_POST['organization'];
    $department = $_POST['department'];
    $position = $_POST['position'];

    $success = false;

    if (all_set(
        $first_name,
        $last_name,
        $email_address,
        $student_id_number,
        $college,
        $organization,
        $department,
        $position
    )) {
        $client = mongodb_client();
        $accountRequests = coll('account_requests', $client);
        $result = $accountRequests->insertOne([
            "requester_name" => [
                "first_name" => $first_name,
                "last_name" => $last_name,
            ],
            "requester_email" => $email_address,
            "student_id_number" => $student_id_number,
            "college" => $college,
            "organization" => $organization,
            "department" => $department,
            "position" => $position,
            "date_requested" => new MongoDB\BSON\UTCDateTime()
        ]);

        if ($result) {
            $success = true;

            $first_name = '';
            $last_name = '';
            $email_address = '';
            $student_id_number = '';
            $college = '';
            $organization = '';
            $department = '';
            $position = '';
        }
    }

    function all_set(...$vars) {
        foreach ($vars as $var) if (!isset($var)) {
            return false;
        }
        return true;
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initialize_page("Account Requesting | YanoDASH")?>
        <style>
            #title {
                font-family: 'Gupter';
                font-weight: normal;
            }

            p, b, i, a, button {
                font-family: 'RobotoFlex';
            }

            #continue-login, #begin {
                cursor: pointer;
                display: block;
                margin: auto;
                width: max-content;
            }

            #begin {
                padding: 8px 32px;
                background: black;
                color: white;
            }

            .form-panel {
            width: 90%;
            margin-inline: auto;

            background: #e9e9e9;
            color: black;

            border-radius: 12px;
            border: 3px solid #d5d5d5;

            padding: 0 64px;

            max-height: 0;
            opacity: 0;

            overflow: hidden;

            transition:
                max-height 0.35s ease 0.15s,
                opacity 0.15s ease;

            pointer-events: none;
            }

            .form-panel.open {
            max-height: 340px;
            opacity: 1;

            padding: 24px 64px;

            transition:
                max-height 0.35s ease,
                opacity 0.3s ease;

            pointer-events: auto;

            overflow-y: auto;
            }

           .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;

            /* padding: 12px 18px; */
            cursor: pointer;
            }

            /* Arrow wrapper */
            .arrow {
            display: flex;
            align-items: center;
            justify-content: center;

            transition: transform 0.3s ease;
            }

            /* Rotate upward when open */
            .btn.open .arrow {
            transform: rotate(180deg);
            }

            /* SVG styling */
            .chevron {
            width: 16px;
            height: 16px;

            stroke: currentColor;
            stroke-width: 2.5;

            fill: none;

            stroke-linecap: round;
            stroke-linejoin: round;
            }

            #request-form form p {
                font-weight: bold;
                margin-bottom: 0;
            }

            input.form-input {
                width: 100%;
            }

            .headr {
                color: white;
                padding: 44px 0;
                background: linear-gradient(to bottom, #ca0033, #63071e);
            }

            .container {
                display: flex;
                height: 100vh;
            }

            .left-section, .right-section {
                flex: 1;
            }

            .left-section { overflow-y: auto; min-height: 0; scrollbar-gutter: stable; }
            .right-section { 
                background: linear-gradient(to bottom, #dd0000, #6e071e); 
                display: flex;
                justify-content: center; /* horizontal center */
                align-items: center;     /* vertical center */
                flex-direction: column;   /* stack items vertically */
                text-align: center;       /* centers inline text inside children */
                }

            .nav-area {
                box-sizing: border-box;
                padding: 16px;
                grid-row: 1;
                display: flex;
                flex-direction: row;
                align-items: center;
            }

            .nav-area a img {
                width: 64px !important;
            }

            #yanodash-a {
                display: flex;
                flex-direction: row;
                align-items: center;
                margin-left: 128px;
            }

            #yanodash-a a {
                text-decoration: none;
            }

            #abc {
                width: 75%;
                padding: 16px;
                background: white;
                margin: auto;
                border-radius: 16px;
                border-top: 7px solid #63071e;
                font-size: 15px;
                padding-bottom: 36px;
            }

            #rsc {
                padding: 0 32px;
            }

            @media (max-width: 800px) {
    .container {
        flex-direction: column;
        height: auto;
    }

    .left-section,
    .right-section {
        width: 100%;
    }

    .left-section {
        overflow-y: visible;
    }

    .right-section {
        min-height: 100vh;
        padding: 48px 24px;
    }
}
        </style>
    </head>
    <body>
        <div class="container">
            <!-- LEFT SECTION -->
            <div class="left-section">
                <div class="nav-area">
                    <a class="btn green" id="ab" href="/yanodash-repository/public" style="display: block; width: 110px; margin-top: 16px; margin-bottom: 8px; margin-left: 12px; cursor: pointer; text-align: center; font-family: 'RobotoFlex'">← Home</a>                  
                    <div id="yanodash-a">
                        <a href="/yanodash-repository/public">
                            <img src="/yanodash-repository/public/images/navbar-logo.png" draggable="false">
                        </a>
                        <a href="/yanodash-repository/public"><h1 style="user-select: none; font-family: 'Gupter'">YanoDASH</h1></a>
                    </div>
                </div>

                <?php if ($success) :?>
                    <div style="display: block; width: 65%; margin: 2px auto; background-color: #d4edda; color: #155724; padding: 15px; border-radius: 8px; border: 1px solid #28a745; border-left: 4px solid #28a745; text-align: center;">
                        Request successfully sent! Please wait 1 to 7 business days for your request to be confirmed.
                    </div>
                    <?php unset($_POST['subject']); unset($_POST['content']);?>
                <?php endif; ?>

                <div id="abc">
                <h1 id="title" style="text-align: center;">Account Requesting</h1>
                <p style="text-align: center; display: block; margin: auto; width: 80%">
                    Thank you for taking interest in YanoDASH's features.<br>This form will guide you through the process of requesting your own account using your university email address.
                    <br><br>
                </p>
                <button id="begin" class="btn">
                <span class="btn-text">Begin</span>

                <span class="arrow">
                    <svg
                    class="chevron"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                    >
                    <path
                        d="M6 9l6 6 6-6"
                    />
                    </svg>
                </span>
                </button>

                <div id="request-form" class="form-panel">
                    <form method="POST">
                        <p>First Name</p> 
                        <input class="form-input" type="text" name="first_name" placeholder="e.g. Juan" required>
                        <p>Last Name</p> 
                        <input class="form-input" type="text" name="last_name" placeholder="e.g. dela Cruz" required>
                        <p>Email Address</p> 
                        <input class="form-input" type="email" name="email_address" placeholder="e.g. jdcruz01202600000@usep.edu.ph" required>
                        <p>Student ID Number</p>
                        <input class="form-input" type="text" name="student_id_number" placeholder="e.g. 2026-00000" required>
                        <p>College</p>
                        <select class="sct" name="college">
                            <option value="caec">College of Applied Economics</option>
                            <option value="cas">College of Arts and Sciences</option>
                            <option value="cba">College of Business Administration</option>
                            <option value="ced">College of Education</option>
                            <option value="cic">College of Information and Computing</option>
                            <option value="coe">College of Engineering</option>
                            <option value="ct">College of Technology</option>
                        </select>
                        <p>Organization</p>
                        <input class="form-input" type="text" name="organization" placeholder="e.g. DOST-ALAS" required>
                        <p>Department</p>
                        <input class="form-input" type="text" name="department" placeholder="e.g. Creatives Committee" required>
                        <p>Position</p>
                        <input class="form-input" type="text" name="position" placeholder="e.g. Creative Lead" required>
                        <button class="btn" type="submit" style="display: block; margin: auto">Request my Account</button>
                    </form>
                </div>
</div>

                
            <p style="text-align: center; display: block; margin: auto">
                        <br><i>Not a student of the University of Southeastern Philippines?<br> You can always continue <a href="<?= $app_url ?>">browsing as a guest.</i></a> 
                        <br><br>Already have an account?<br>
                    </p>
                    <a id="continue-login" class="btn" href="<?= $app_url ?>/auth/login.php">
            Continue to Login Page →
        </a>

            </div>
            <!-- RIGHT SECTION -->
            <div class="right-section">
                <div id="rsc">
            <h2 style="font-family: 'Gupter'; text-align: center; color: white">Frequently Asked Questions</h2>
            <?php 
                echo accordion(
                    "account-requesting-faqs",
                    [
                        "What is a guest user? / What can I do as a guest?"
                        => "Guest users are users without a registered account in YanoDASH. They can either be bona fide USeP students or external visitors. As a guest, you are free to browse the public archive and download public files.",

                        "What are the benefits of having an account?"
                        => "Logged in users get additional perks such as the ability to save documents to their profile and be notified of the latest releases. Keep in mind, however, that they have view-only access to documents, just like guests.",

                        "Can I request an account using a non-USeP email address?"
                        => "No, not currently. For verification and safety purposes, we only support USeP university email addresses."
                    ], false
                );
            ?>
            </div>
            </div>
        </div>
        

        <script>
            const button = document.getElementById("begin");
            const panel = document.getElementById("request-form");
            const text = button.querySelector(".btn-text");

            button.addEventListener("click", () => {
            const isOpen = panel.classList.toggle("open");

            button.classList.toggle("open", isOpen);

            text.textContent = isOpen
                ? "Close"
                : "Begin";
            });
        </script>
    </body>
</html>