<?php
    session_start();

    require_once '../../bootstrap/app.php';
    load (
        'authenticator',
        'csrf_token',
        'password_input'
    );
 
    if (Authenticator::isLoggedIn()) {
        header("Location: ". $app_url. '/account/my-account.php');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = (string) trim($_POST['email'] ?? '');
        $password = (string) trim($_POST['password'] ?? '');

        $loginResult = Authenticator::loginUser($email, $password);
        if (!$loginResult->success) {
            header('Location: '. $_SERVER['REQUEST_URI']);
            exit;
        } else {
            $redir = trim($_GET['redirect'] ?? '');
            if ($redir !== '') {
                $parts = parse_url($redir);
                if (!isset($parts['scheme']) && !isset($parts['host'])) {
                    $path = '/'. ltrim($redir, '/');
                    header('Location: '. rtrim($app_url, '/'). $path);
                    exit;
                }
            }
            header('Location: '. rtrim($app_url, '/'). '/');
            exit;
        }
    }

    $error = $_SESSION['errorMsg'] ?? '';
    unset($_SESSION['errorMsg']);
?>

<!DOCTYPE html>
<html>
    <head>
        <script src="../script/control-actions.js" defer></script>
        <link rel="stylesheet" href="../css/fonts.css">
        <link rel="stylesheet" href="../css/components/user-form.css">
        
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
                /* box-sizing: border-box; */
                display: block;
                height: 44px;
                width: 240px;
                margin-left: auto;
                margin-right: auto;
                margin-bottom: 10px;
                /* padding: 0 4px;
                border: 2px solid #ddd;
                border-radius: 8px; */
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

            .locked {
                cursor: not-allowed;
                pointer-events: none;
            }

            input.locked {
                background: #DDD;
                color: gray;
            }

            #login-button.locked:hover {
                background: #DDD;
                color: gray;
                transition: none;
            }

            #message-container {
                min-height: 16px;
                min-width: 16px;
                padding: 1px;
                box-sizing: border-box;
                text-align: center;
            }

            #message-container #err-message {
                font-family: 'RobotoFlex';
                font-size: 13px;
                color: red;
            }
        </style>
        <?php initialize_page("Login | YanoDASH")?>
    </head>
    <body>
        <div class="container">
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
                <div class="login-area">
                    <form id="form-login" method="POST" style="padding: 60px 80px; border-radius: 16px; border-top: 6px solid maroon; background: #f4f4f4;">
                        <?= csrf_input_field() ?>
                        
                        <div>
                            <h1 style="font-family: 'Gupter', serif; margin-bottom: 8px;">Login</h1>
                        </div>
                        <input type="email" id="uname" name="email" placeholder="Email Address" style="font-family: 'RobotoFlex'" required minlength="3">
                        <?php echo password_input("login-enter-password", inputName: "password", height: 44, width: 240)?>
                        <div style="display: flex; flex-direction: row; margin-top: 8px;">
                            <input id="remember-me" type="checkbox" style="margin-right: 4px">
                            <p style="font-family: 'RobotoFlex'">Remember me</p>
                        </div>

                        <input id="login-button" class="btn" type="submit" name="login" value="Login" style="display: block; width: 128px; margin-top: 16px; margin-bottom: 8px; margin-left: auto; margin-right: auto; cursor: pointer; text-align: center">
                        <div id="message-container">
                            <?php 
                                if ($error) {
                                    $sanitizedError = htmlspecialchars($error);
                                    echo <<< HTML
                                        <p id="err-message">$sanitizedError</p>
                                    HTML;
                                }
                            ?>
                        </div>
                        <p style="text-align: center"><a href="mfa/forgot_password.php" class="inline-link" style="text-align: center;">I forgot my password</a></p>
                        <hr style="border: 1px solid rgba(0,0,0,0.1)">

                        <p style="text-align: center; margin-top: 16px; font-family: 'RobotoFlex'">Don't have an account?</p>
                        <a href="/yanodash-repository/public/auth/request-account.php" class="btn" style="display: block; margin: auto; width: 180px; margin-top: 8px; font-family: 'RobotoFlex'">Request an account</a>
                    </form>
                </div>
            </div>
            <div class="right-section">
                <div id="contacts">

                </div>
            </div>
        </div>
        <script>
            const inputs = document.querySelectorAll("#uname, #login-enter-password-inputfield");
            const errorMsg = document.getElementById("err-message");

            inputs.forEach(input => {
                input.addEventListener("input", () => {
                    if (errorMsg) {
                        errorMsg.textContent = "";
                    }
                });
            });

            const form = document.querySelector("#form-login");
            const loginButton = document.querySelector("#login-button");

            form.addEventListener("submit", (e) => {
                e.preventDefault();

                const isValid = validateForm(form);
                if (!isValid) return;

                hidePassword('login-enter-password-visibilitytoggle');
                setElementsLockedByIDs([
                    'uname',
                    'login-enter-password-inputfield',
                    'remember-me'
                ]);

                loginButton.disabled = true;
                loginButton.value = "Logging in...";
                loginButton.classList.add("locked");

                form.submit();
            });
        </script>
    </body>
</html>