    <?php
    require_once dirname(dirname(__DIR__)). '/bootstrap/app.php';
    load (
        'authentication',
        'identity_resolver',
        'user_profile_service',
        'navbar',
        'footer',
        'password_input',
        'mongodb'
    );

    if (!is_logged_in()) {
        header('location: '. $app_url. '/auth/login.php');
        exit;
    }

    $userID = $identity['user_id'] ?? null;
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <?php initialize_page("My Account Dashboard | YanoDASH")?>
    

    <style>
    @font-face {
        font-family: 'Gupter';
        src: url('../fonts/gupter/Gupter-Regular.ttf');
    }

    @font-face {
        font-family: 'RobotoFlex';
        src: url('../fonts/roboto-flex/RobotoFlex-Variable.ttf');
    }
    :root{
        --maroon:#800000;
        --yellow:#FFD700;
        --bg:#f4f7f9;
        --white:#fff;
        --text:#333;
        --border:#e1e4e8;
        --r:12px;
    }

    *{margin:0;padding:0;box-sizing:border-box; font-family:'RobotoFlex'}
    body{background:var(--bg); color:var(--text)}


    /* LAYOUT */
    .account-container{
        padding:20px 16px 24px;
        display:flex;
        flex-direction:column;
        gap:25px;
        max-width:1600px;
        margin:auto;
    }

    /* CARD */
    .card{
        background:#fff;
        border-radius:var(--r);
        border-top:4px solid var(--maroon);
        padding:18px;
        box-shadow:0 6px 14px rgba(0,0,0,.05);
    }

    .title{
        font-weight:700;
        color:var(--maroon);
        margin-bottom:12px;
        border-bottom:2px solid rgba(128,0,0,.2);
        padding-bottom:6px;
        text-transform:uppercase;
        font-size:.95rem;
        font-family: 'RobotoFlex', serif !important;
    }

    /* SCROLL */
    .scroll{
        max-height:220px;
        overflow:auto;
        scrollbar-width:none;
    }
    .scroll::-webkit-scrollbar{width:0}
    .scroll:hover{scrollbar-width:thin}
    .scroll:hover::-webkit-scrollbar{width:6px}

    /* ITEMS */
    .item{
        padding:10px;
        border-bottom:1px solid #eee;
        font-size:.85rem;
    }
    .item:hover{background:#fef5e8}

    /* PROFILE */
    .profile{
        display:flex;
        gap:16px;
        flex-wrap:wrap;
    }
    .avatar {
        width: 90px;
        height: 90px;
        border: 2px solid #63071e;
        border-radius: 50%;
        background: var(--maroon);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.5rem;
        overflow: hidden; 
    }
    .avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .info{flex:1;min-width:240px}
    .info h2 {
        font-family: 'Gupter', serif !important;
    }
    .badge{
        background:#e8f5e9;
        color:#2e7d32;
        padding:4px 10px;
        border-radius:20px;
        font-size:.75rem;
    }

    /* INPUT */
    input,button{
        width:100%;
        padding:10px;
        margin-top:8px;
        border:1px solid var(--border);
        border-radius:10px;
    }
button {
    background: var(--maroon);
    color: #fff;
    border: 2px solid transparent;
    cursor: pointer;
    transition: all 0.3s ease;
    padding: 12px 18px;
    border-radius: 50px;
    font-weight: 600;
}

/* hover state */
button:hover {
    background: #5f0000;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.card.div2 button {
    background-color: maroon;
    color: white;
}

    /* TABLET */
    @media(min-width:768px){
        .account-container{
            display:grid;
            grid-template-columns:repeat(2,1fr);
        }
    }

    /* DESKTOP */
    @media(min-width:1024px){
        .account-container{
            grid-template-columns:repeat(5,1fr);
            padding:12px 32px 32px;
        }

        .div1{grid-column:1/-1}
        .div2{grid-column:1/2}
        .div3{grid-column:2/4}
        .div4{grid-column:4/6}
    }
    </style>
    </head>
    <body>
    <?php echo navbar(0); ?>


    <div class="account-container">

    <!-- PROFILE -->
    <div class="card div1">
        <div class="title">User Profile</div>
        <div class="profile">
            <?php
                # Get and store properties of profile and identity
                $fullname = full_name($profile);
                $student_id = student_id_number($profile);
                $email = $identity['email'];
                $avatar = avatar($profile);
                $badge = match($identity['organization']) {
                    default => "Student",
                    'CICLC', 'CTLC' => "LC Officer",
                    'Obrero Student Council', 'OSC' => "OSC Officer"
                };
                $albadge = match ($permissions['access_level']) {
                    'admin' => "Administrator",
                    'editor' => "Editor",
                    'viewer' => "Viewer"
                };

                $avatarElement = <<< HTML
                    <img src="../images/ui-indicators/account.png" alt="Placeholder profile picture">
                HTML;
                $avatarType = $avatar['type'];
                $avatarValue = $avatar['value'];

                switch ($avatarType) {
                    case 'initials':
                        $avatarElement = $avatarValue;
                        break;
                    case 'url':
                        $sanitizedValue = htmlspecialchars($avatarValue);
                        $avatarElement = <<< HTML
                            <img src="$sanitizedValue" alt="Profile picture for $fullname">
                        HTML;
                        break;
                }
            ?>
            <div class="avatar">
                <?= $avatarElement ?>
            </div>
            <div class="info">
                <?php
                    echo <<< HTML
                        <h2>$fullname <span class="badge">$albadge</span></h2>
                    HTML;
                ?>
                <p>📧 <?= $email ?></p>
                <p>Organization: <?= $identity['organization'] ?></p>
                <p>Department: <?= $identity['department'] ?></p>
                <p>Position: <?= $profile['position'] ?></p>
                <p>Joined: <?= $profile['date_joined'] ?></p>
            </div>
        </div>
    </div>

    <!-- SECURITY -->
    <div class="card div2">
        <div class="title">Security</div>
        <p><b>Change Password</b></p>
        <form method="POST" action="../auth/change_password.php">
            <input type="password" name="current_password" placeholder="Current password" minlength="8" required>
            <input type="password" name="new_password" placeholder="New password" minlength="8" required>
            <input type="password" name="confirm_new_password" placeholder="Confirm new password" minlength="8" required>
            <input type="submit" class="btn action" value="Update">
        </form>
        <?php
            if (isset($_SESSION['msg']['passwordChangeMsg']) && !empty($_SESSION['msg']['passwordChangeMsg'])) {
                echo '<p style="font-size: 0.7rem; text-align: center">'. $_SESSION['msg']['passwordChangeMsg'] . '</p>';
                unset($_SESSION['msg']['passwordChangeMsg']);
            }
        ?>
        <p style="text-align: center"><a href="../auth/setup-mfa.php" class="inline-link" style="text-align: center; font-size: 13px">Set up two-factor authentication ↗</a></p>
    </div>

    <!-- DOCUMENTS -->
    <div class="card div3">
        <div class="title">Documents</div>
        <div class="scroll">
            <?php
                $documentTitles = [];
                try {
                    $documents = coll('documents')
                        ->find(["author" => new MongoDB\BSON\ObjectId($userID)])
                        ->sort(['dates.date_added' => -1])
                        ->execute();
                    if (!empty($documents)) {
                        foreach($documents as $document) {
                            $title = $document['doc_title'];
                            array_push($documentTitles, $title);
                        }
                    }
                } catch (Exception $e) { 
                    $documentTitles = [];
                }
                
                if (!empty($documentTitles)) {
                    foreach($documentTitles as $docTitle) echo <<< HTML
                        <div class="item">
                            $docTitle
                        </div>
                    HTML;
                } else echo <<< HTML
                    <div class="item">
                        (no documents)
                    </div>
                HTML;
            ?>
        </div>
        <p style="text-align: center"><a href="../dms/" class="inline-link" style="text-align: center; font-size: 13px">Visit DMS ↗</a></p>
    </div>

    <!-- ACTIVITY -->
    <div class="card div4">
        <div class="title">Activity</div>
        <div class="scroll">
            <div class="item">(no data)</div>
        </div>
    </div>

    </div>

        <?php echo footer(); ?>

    </body>
    </html>