<?php 
    require_once '../components/head.php';
    require_once '../components/navbar.php';
    require_once '../components/password_input.php';
?>

<!DOCTYPE html>
<html>
    <head>
        
        <script src="../script/control-actions.js" defer></script>
        <link rel="stylesheet" href="../css/base-layout.css">
        <link rel="stylesheet" href="../css/fonts.css">
        <link rel="stylesheet" href="../css/components/user-form.css">
         <link rel="stylesheet" text="text/css" href="../style.css">
        
        <style>
            * {
                font-family: 'RobotoFlex', sans-serif
            }

            h1, h2, h3, h4, h5, h6 {
                font-weight: normal;
            }

            p, button, input, .dropdown-contents {
                font-size: 14px;
            }

            #navbar h3 {
                font-weight: bold;
                font-size: 17px;
            }

            #yanodash-home h1, #yanodash-home span {
                font-family: 'Gupter', serif;
                font-size: 38px;
            }

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
                width: 340px;
                border-radius: 10px;
            }

            #login-enter-password-inputfield {
                font-family: 'RobotoFlex';
            }

            #background {
                background: url(../images/backgrounds/login.jpg) center/cover no-repeat;
                height: 640px;
            }

            .container {
                display: flex;
                height: 100vh;
            }

            .left-section, .right-section {
                flex: 1;
            }

            .left-section {
                display: grid;
                grid-template-rows: 1fr 8fr;
            }

            .nav-area {
                box-sizing: border-box;
                padding: 16px;
                grid-row: 1;
                display: flex;
                flex-direction: row;
                align-items: center;
            }

            .login-area {
                grid-row: 2;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }

            .right-section {
                background-image: 
                    linear-gradient(to top left, rgba(86, 0, 0, 0.75), rgba(255, 96, 79, 0.75)),
                    url('../images/backgrounds/eagle-background.jpg');
                background-repeat: no-repeat;
                background-position: right;
                background-size: auto;
                display: grid;
                grid-template-rows: 4fr 2fr;
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

            #contacts {
                grid-row: 2;
                display: flex;
                flex-direction: row;
            }

            #ab:hover {
                background-color: #226D2C;
            }
        </style>
        <?php initializePage("Login | YanoDASH")?>
    </head>
    <body>
        <div class="container">
            <div class="left-section">
                <div class="nav-area">
                    <a class="btn-back" id="ab" href="/yanodash-repository/" style="display: block; width: 110px; margin-top: 16px; margin-bottom: 8px; margin-left: 12px; cursor: pointer; text-align: center; font-family: 'RobotoFlex'">← Home</a>                  
                    <div id="yanodash-a">
                        <a href="/yanodash-repository/">
                            <img src="/yanodash-repository/images/navbar-logo.png" draggable="false">
                        </a>
                        <a href="/yanodash-repository"><h1 style="user-select: none; font-family: 'Gupter'">YanoDASH</h1></a>
                    </div>
                </div>
                <div class="login-area">
                    <form style="padding: 60px 80px; border-radius: 16px; border-top: 6px solid maroon; background: #f4f4f4;">
                        <div>
                            <h1 style="font-family: 'Gupter', serif; margin-bottom: 8px;">Login</h1>
                        </div>
                        <input type="text" id="uname" name="username" placeholder="Username or Email Address" style="font-family: 'RobotoFlex'">
                        <?php echo passwordInput("login-enter-password", "login-password-input", height: 44, width: 240)?>
                        <div style="display: flex; flex-direction: row; margin-top: 8px;">
                            <input type="checkbox" style="margin-right: 4px">
                            <p style="font-family: 'RobotoFlex'">Remember me</p>
                        </div>
                        <a class="btn-back" href="/yanodash-repository/" style="display: block; width: 100px; margin-top: 16px; margin-bottom: 8px; margin-left: auto; margin-right: auto; cursor: pointer; text-align: center; font-family: 'RobotoFlex'">Login</a>                  
                        <a style="text-align: center; cursor: pointer;"><p style="margin-top: 16px; margin-bottom: 16px; font-family: 'RobotoFlex'">I forgot my password</p></a>
                        <hr style="border: 1px solid rgba(0,0,0,0.1)">

                        <p style="text-align: center; margin-top: 16px; font-family: 'RobotoFlex'">Don't have an account?</p>
                        <a href="/yanodash-repository/request-account" class="btn-back" style="display: block; margin: auto; width: 180px; margin-top: 8px; font-family: 'RobotoFlex'">Request an account</a>
                        
                    </form>
                </div>
            </div>
            <div class="right-section">
                <div id="contacts">

                </div>
            </div>
        </div>

        
        <!-- <div id="background">
            <div class="form-container" style="width: 320px; z-index: 10;">
                <h1 style="text-align: center;">Login</h1>
                <input type="text" id="uname" name="username" placeholder="Username or Email Address">
                <br>
                <a class="btn-back" style="display: block; margin: auto; width: 64px; cursor: pointer; text-align: center;">Login</a>
                <a style="text-align: center; cursor: pointer"><p style="margin: 0;">I forgot my password</p></a>
                <p style="text-align: center; margin-bottom: 0;">Don't have an account?</p>
                <a href="/yanodash-repository/request-account">
                    <button style="display: block; margin: auto; width: 150px; height: 32px; color: black; background-color: white;">Request an account</button>
                </a>
                </div>
        </div> -->
    </body>
</html>