<?php
    require_once '../components/navbar.php';
    require_once '../components/head.php';
    require_once '../components/footer.php';
    require_once '../components/password_input.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <script src="../script/control-actions.js" defer></script>
        <style>
            #uname {
                box-sizing: border-box;
                display: block;
                height: 44px;
                width: 240px;
                margin-left: auto;
                margin-right: auto;
                margin-bottom: 10px;
                padding: 0 4px;
                border: 2px solid #ddd;
            }

            #login-form {
                margin: auto;
                padding: 64px;
                background-color: #f2f2f2;
                width: 320px;
                border-radius: 10px;
            }

            #login-enter-password {
                margin: auto;
            }
        </style>
        <?php initializePage("Login | YanoDASH")?>
    </head>
    <body>
        <?php echo navbar(0)?>
        <div id="login-form" style="box-shadow: 0 8px 16px rgba(0,0,0,0.2);">
            <h1 style="text-align: center;">Login</h1>
            <input type="text" id="uname" name="username" placeholder="Username or Email Address">
            <?php echo passwordInput("login-enter-password", "login-password-input", height: 44, width: 240)?>
            <br>
            <button style="display: block; margin: auto; height: 32px; width: 64px; border-radius: 16px; border: none; background-color: #792A25; color: white; cursor: pointer">Login</button>
            <a style="text-align: center; cursor: pointer"><p style="margin: 0;">I forgot my password</p></a>
            <p style="text-align: center; margin-bottom: 0;">Don't have an account?</p>
            <a href="/yanodash-repository/request-account">
                <button style="display: block; margin: auto; width: 150px; height: 32px; color: black; background-color: white;">Request an account</button>
            </a>
        </div>
    </body>
</html>