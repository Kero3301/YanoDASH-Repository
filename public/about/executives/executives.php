<?php
    session_start();
    
    require_once '../../../bootstrap/app.php';
    load('navbar', 'footer');
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php initialize_page("Executives | About | YanoDASH");?>
        <link rel="stylesheet" href="../../css/pages/execs-style.css">
    </head>
    <body class="exec-body">
        <?php echo navbar()?>

        <div class="page-contents no-padding">
            <div class="pch">
                <h1>Meet The Executives</h1>
            </div>
            <div class="exec-container">
                <header class="exec-header">
                    <p class="quote">“Coming together is a beginning. Keeping together is progress. Working together is success.”</p>
                </header>

                <div class="exec-grid">
                    <div class="exec-card">
                        <div class="exec-image">
                            <img src="placeholder.png" alt="Founder">
                        </div>
                        <div class="exec-info">
                            <div class="line-divider"></div>
                            <h2>Alex Cruz</h2>
                            <p class="role">OSC President</p>
                            <p class="bio">Visionary behind YanoDASH, focusing on streamlining document management systems for student councils.</p>
                        </div>
                    </div>

                    <div class="exec-card">
                        <div class="exec-image">
                            <img src="placeholder.png" alt="Operations">
                        </div>
                        <div class="exec-info">
                            <div class="line-divider"></div>
                            <h2>Marcus Adler</h2>
                            <p class="role">OSC Internal Vice President</p>
                            <p class="bio">Overseeing the logistical flow and ensuring project milestones are met with absolute resolve.</p>
                        </div>
                    </div>

                    <div class="exec-card">
                        <div class="exec-image">
                            <img src="placeholder.png" alt="Creative Lead">
                        </div>
                        <div class="exec-info">
                            <div class="line-divider"></div>
                            <h2>Elena Santos</h2>
                            <p class="role">OSC External Vice President</p>
                            <p class="bio">Handles external communications and community outreach with strength and clarity.</p>
                        </div>
                    </div>

                    <div class="exec-card">
                        <div class="exec-image">
                            <img src="placeholder.png" alt="Secretary">
                        </div>
                        <div class="exec-info">
                            <div class="line-divider"></div>
                            <h2>John Elias</h2>
                            <p class="role">OSC General Secretary</p>
                            <p class="bio">Responsible for the systematic organization of all council documents and meeting minutes.</p>
                        </div>
                    </div>


                    <div class="exec-card">
                        <div class="exec-image">
                            <img src="placeholder.png" alt="Creative">
                        </div>
                        <div class="exec-info">
                            <div class="line-divider"></div>
                            <h2>Dominic Santos</h2>
                            <p class="role">OSC General Auditor</p>
                            <p class="bio">Ensuring transparency and fairness in all council dealings with a hero's passion.</p>
                        </div>
                    </div>

                    <div class="exec-card">
                        <div class="exec-image">
                            <img src="placeholder.png" alt="Public Relations">
                        </div>
                        <div class="exec-info">
                            <div class="line-divider"></div>
                            <h2>Sofia Reyes</h2>
                            <p class="role">OSC General Treasurer</p>
                            <p class="bio">Expert in financial management and resource allocation for various council projects.</p>
                        </div>
                    </div>

                    <div class="exec-card">
                        <div class="exec-image">
                            <img src="placeholder.png" alt="Public Relations">
                        </div>
                        <div class="exec-info">
                            <div class="line-divider"></div>
                            <h2>Sofia Reyes</h2>
                            <p class="role">OSC General Treasurer</p>
                            <p class="bio">Expert in financial management and resource allocation for various council projects.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php echo footer() ?>
    </body>
</html>