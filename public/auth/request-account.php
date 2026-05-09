<?php
    session_start();
    require_once '../../src/loader.php';
    load (
        'navbar',
        'accordion',
        'footer',
        'user_form'
    );
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
        </style>
    </head>
    <body>
        <?php echo navbar(0)?>
        <h1 id="title" style="text-align: center;">Account Requesting</h1>
        <p style="text-align: center; width: 40%; display: block; margin: auto">
            Thank you for taking interest in YanoDASH's features. This form will guide you through the process of requesting your own account using your university email address.
            <br><br>
        </p>
        <a id="begin" class="btn">Begin</a>
        <p style="text-align: center; width: 40%; display: block; margin: auto">
            <br><i>Not a student of the University of Southeastern Philippines?<br> You can always continue <a href="<?= $app_url ?>">browsing as a guest.</i></a> 
            <br><br>Already have an account?<br>
        </p>
        <a id="continue-login" class="btn" href="<?= $app_url ?>/auth/login.php">
            Continue to Login Page →
        </a>
        <div style="width: 75%; margin: auto;">
            <h2 style="font-family: 'Gupter';">Frequently Asked Questions</h2>
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
                    ]
                );
            ?>
        </div>
        
        <?php echo footer()?>
    </body>
</html>