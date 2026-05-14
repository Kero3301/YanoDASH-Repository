<?php
    session_start();
    require_once '../../src/loader.php';
    
    load (
        'navbar',
        'footer'
    );

    

    $success = false;

    if ($success) {
        $success = false;
        unset($_POST['subject']);
        unset($_POST['content']);
    }

    if (!empty($_POST['subject']) && !empty($_POST['content'])) {
        $success = true;
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php initialize_page("Contact Us | YanoDASH");?>
        <link rel="stylesheet" href="../css/pages/contactstyle.css">
        <style>
            input {
                width: 100%;
            }

            .upload-btn {
                background: transparent;
            }

            .upload-btn:hover {
                background: rgba(255,0,0,0.5);
            }

            .typed-content {
                border-radius: 8px;
                width: 100%;
                resize: none;
                font-size: 16px;
                height: 160px;
                padding: 4px;
                border: 2px solid #DDDDDD;
            }

            .msg1 {

            }
        </style>
    </head>
    <body>
        <?php echo navbar()?>
            <?php if ($success) :?>
                <div style="display: block; width: 65%; margin: 2px auto; background-color: #d4edda; color: #155724; padding: 15px; border-radius: 8px; border: 1px solid #28a745; border-left: 4px solid #28a745; text-align: center;">
                    Message received! You will hear from us shortly.
                </div>
                <?php unset($_POST['subject']); unset($_POST['content']);?>
            <?php endif; ?>
            <div class="about-wrapper">    
                <div class="about-card" style="border-top: 8px solid #63071e">
                    <div class="about-left">
                            <img src="../images/osc-logo-temporary.png" alt="Large Logo" class="large-about-logo">
                    </div>
                    
                    <div class="about-right">
                        <h1>Contact Us</h1>
                        <p>
                            Have questions about YanoDASH, or want to submit feedback or recommendations? We got you, Ka-Yano! 
                        </p>
                        <p>
                            Feel free to use our contact form to voice out your concerns.
                        </p>
                        <form method="POST">
                            <input type="text" name="subject" placeholder="Subject" required>
                            <textarea placeholder="Content" name="content" class="typed-content" required></textarea><br>
                            <input class="btn" type="submit" value="Submit" style="display: block; margin: auto; width: auto;">
                        </form>
                        <p>Or, you may reach us directly through our other channels.</p>
                        <div style="margin: auto; display: block">
                            <a href="https://www.facebook.com/obrerosc" class="upload-btn contact-btn" target="_blank" style="display: inline-block; padding: 10px;">
                                <img src="Facebook_logo_(square).png" alt="Facebook" style="width: 30px; vertical-align: middle;">
                            </a>
                            <a href="mailto:sc_obrero@usep.edu.ph" class="upload-btn contact-btn" target="_blank" style="display: inline-block; padding: 10px;">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/7/7e/Gmail_icon_%282020%29.svg?utm_source=commons.wikimedia.org&utm_campaign=index&utm_content=original" alt="Facebook" style="width: 30px; vertical-align: middle;">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php echo footer() ?>
    </body>
</html>