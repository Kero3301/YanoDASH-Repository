    <?php
    session_start();

    require_once '../components/head.php';
    require_once '../components/navbar.php';
    require_once '../components/footer.php';
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <?php initializePage("My Account Dashboard | YanoDASH")?>
    

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
    .avatar{
        width:75px;height:75px;
        border-radius:50%;
        background:var(--maroon);
        color:#fff;
        display:flex;
        align-items:center;
        justify-content:center;
        font-weight:bold;
        font-size:1.5rem;
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

    <!-- <div class="page-contents no-padding"> -->
    <div class="account-container">

    <!-- PROFILE -->
    <div class="card div1">
        <div class="title">User Profile</div>
        <div class="profile">
            <div class="avatar"><?= $_SESSION['initials']?></div>
            <div class="info">
                <h2><?= $_SESSION['fullname']?> <span class="badge"><?= $_SESSION['badge']?></span></h2>
                <p><b><?= strtoupper($_SESSION['role']) ?></b></p>
                <p>📧<?= $_SESSION['username']?></p>
                <p>Role: <?= $_SESSION['position'] ?? '(unknown)'?></p>
                <p>Joined: Sept 2023</p>
            </div>
        </div>
    </div>

    <!-- SECURITY -->
    <div class="card div2">
        <div class="title">Security</div>
        <input type="password" placeholder="Current">
        <input type="password" placeholder="New">
        <input type="password" placeholder="Confirm">
        <button>Update</button>
    </div>

    <!-- DOCUMENTS -->
    <div class="card div3">
        <div class="title">Documents</div>
        <div class="scroll">
            <?php for($i=1;$i<=12;$i++): ?>
            <div class="item">
                Resolution_<?php echo str_pad($i,2,'0',STR_PAD_LEFT); ?>.pdf
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- ACTIVITY -->
    <div class="card div4">
        <div class="title">Activity</div>
        <div class="scroll">
            <div class="item">Viewed document</div>
            <div class="item">Updated password</div>
            <div class="item">Logged in</div>
            <div class="item">Downloaded file</div>
            <div class="item">Edited profile</div>
        </div>
    </div>

    </div>
    <!-- </div> -->

        <?php echo footer(); ?>

    </body>
    </html>