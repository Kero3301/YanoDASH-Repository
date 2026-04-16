<?php
    require_once '../components/head.php';
    require_once '../components/navbar.php';
    require_once '../components/faq_section.php';
    require_once '../components/footer.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initializePage("Account Requesting | YanoDASH")?>
        <!-- <link rel="stylesheet" href="../css/base-layout.css"> -->
    </head>
    <body>
        <?php echo navbar(0)?>
        <h1 style="text-align: center;">Account Requesting</h1>
        <p style="text-align: center; width: 40%; display: block; margin: auto">
            Thank you for taking interest in YanoDASH's features. This form will guide you through the process of requesting your own account using your university email address.
        </p>
        <button style="display: block; margin: auto; height: 32px; cursor: pointer;">Begin</button>
        <p style="text-align: center; width: 40%; display: block; margin: auto">
            <br><i>Not a student of the University of Southeastern Philippines?<br> You can always continue <a href="/yanodash-repository/">browsing as a guest.</i></a> 
            <br><br>Already have an account?
        </p>
        <a href="/yanodash-repository/login">
            <button style="display: block; margin: auto; height: 32px;">Continue to Login Page →</button>
        </a>
        <?php 
            echo FAQSection(
                "account-requesting-faqs",
                [
                    "What is a guest user? / What can I do as a guest?"
                    => "Guest users are users without a registered account in YanoDASH. They can either be bona fide USeP students or external visitors. As a guest, you are free to browse the public archive and download public files.",

                    "What are the benefits of having an account?"
                    => "Logged in users get additional perks such as the ability to save documents to their profile and be notified of the latest releases. Keep in mind, however, that they have view-only access to documents, just like guests.",

                    "Can I request an account using a non-USeP email address?"
                    => "No, not currently. For verification and safety purposes, we only support USeP university email addresses."
                ]
            )
        ?>
        <?php echo footer()?>
    </body>
</html>