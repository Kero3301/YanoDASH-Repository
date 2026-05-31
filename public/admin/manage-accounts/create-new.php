<?php
require_once '../../../bootstrap/app.php';
load (
    'authentication',
    'authorization',
    'navbar',
    'footer',
    'mongodb',
    'user_form',
    'password_input',
    'multiselect'
);

if (!is_logged_in()) {
    header('location: '. $app_url. '/auth/login.php');
    exit;
}

if (!can_access_admin_pages($permissions)) {
    die("You do not have permission to access this resource.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

?>
<!DOCTYPE html>
<html>
    <head>
        <?php initialize_page('Create New Account | YanoDASH')?>
    </head>
    <body>
            <?php
                echo user_form(
                    "create-account-form",
                    "Create Account",
                    [
                        form_group("First Name", "first_name", input("first_name", placeholder: "e.g. Juan")),
                        form_group("Last Name", "last_name", input("last_name", placeholder: "e.g. dela Cruz")),
                        form_group("Email Address", "email_address", input("email_address", "email", placeholder: "e.g. jdcruz01202600001@usep.edu.ph")),
                        form_group("Student ID Number", "id_number", input("id_number", placeholder: "e.g. 2026-00001", pattern: "2[0-9]{3}-[0-9]{5}")),
                        form_group("College", "college", options("college", 
                            [
                                '(unknown)' => '(unknown)',
                                'College of Arts and Sciences' => 'CAS',
                                'College of Applied Economics' => 'CAEC',
                                'College of Business Administration' => 'CBA',
                                'College of Education' => 'CED',
                                'College of Engineering' => 'COE',
                                'College of Information and Computing' => 'CIC',
                                'College of Technology' => 'CT',
                                
                            ]
                        )),
                        form_group("Organization", "org", options("org", 
                            [
                                '(none)' => '(none)',
                                'Obrero Student Council' => 'OSC',
                                'CAS Local Council' => 'CASLC',
                                'CAEc Local Council' => 'CAECLC',
                                'CBA Local Council' => 'CBALC',
                                'CEd Local Council' => 'CEDLC',
                                'COE Local Council' => 'COELC',
                                'CIC Local Council' => 'CICLC',
                                'CT Local Council' => 'CTLC',
                            ]
                        )),
                        form_group("Department/Office", "dept", input("dept", placeholder: "e.g. Office of the Treasurer")),
                        form_group("Position", "position", input("position", placeholder: "e.g. Treasurer")),
                        form_group("Access Level", "access_level", options("access_level", 
                            [
                                'Admin-level User' => 'admin',
                                'Editor-level User' => 'editor',
                                'Viewer-level User' => 'viewer',
                            ]
                        )),
                        form_group("Access Domains", "access_domains", multiselect(
    id: "access_domains",
    name: "access_domains",
    label: "Access Domains",
    options: [
        ["label" => "Office of the President", "value" => "osc_president_office"],
        ["label" => "Office of the Internal Vice President", "value" => "osc_ivp_office"],
        ["label" => "Office of the External Vice President", "value" => "osc_evp_office"],
        ["label" => "CAEC Local Council", "value" => "caeclc"],
        ["label" => "CAS Local Council", "value" => "caslc"],
        ["label" => "Public Area", "value" => "public"]
    ]
)),

                        form_group("Password", "passwd", password_input("passwd", "passwd", "Password (8 characters minimum)", percentWidth: '100%')),
                    ],
                    description: "Use this form to manually register an account.",
                    submitButtonText: "Create",
                    precontent: <<< HTML
                        <a class="btn action" href="./">← Back</a>
                    HTML
                );
            ?>
    </body>
    <script>
        document.getElementById('access_level').addEventListener('change', function () {
            if (this.value === 'admin') {
                alert(
                    '⚠️ Admin-Level Users have full access to the system and can manage accounts and documents completely. Make sure you understand the security implications of this choice.'
                );
            }
        });
    </script>
</html>