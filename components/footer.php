<?php
    function footer(): string {
        $html = <<< HTML
            <style>
                .footer-section {
                    color: white;
                }
                .footer-section ul {
                    list-style-type: none;
                    padding: 0;
                    margin: 0;
                }
            </style>

            <div id="footer" style="display: flex; gap: 64px; padding: 32px; left: 0; right: 0; background-color: black;">
                <div class="footer-section">
                    <h3 class="footer-section-label">Documents</h3>
                    <ul>
                        <li><a>Latest Releases</a></li>
                        <li><a>Browse Public Archive</a></li>
                        <li><a>Departmental Documents</a></li>
                        <li><a>Council Documents</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3 class="footer-section-label">Statistics</h3>
                    <ul>
                        <li><a>General Statistics</a></li>
                        <li><a>For Admins</a></li>
                        <li><a>For Editors</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3 class="footer-section-label">About</h3>
                    <ul>
                        <li><a>What is the OSC?</a></li>
                        <li><a>Meet the OSC Executives</a></li>
                        <li><a>YanoDASH's Story</a></li>
                    </ul>
                </div>
                <div class="footer-section" style="margin-left: auto;">
                    <div id="contacts" style="display: flex; gap: 16px;">
                        <div id="phone">
                            <img class="indicator" src="/yanodash-repository/images/navigation/phone.png" width="32px" draggable="false">
                            <p>***-****</p>
                        </div>
                        <div id="email">
                            <img class="indicator" src="/yanodash-repository/images/navigation/email.png" width="32px" draggable="false">
                            <p>sc_obrero@usep.edu.ph</p>
                        </div>
                    </div>
                    <div id="legal">
                        Copyright© 2026 PrismaCode Systems and<br>University of Southeastern Philippines (USEP)<br> Obrero Student Council (OSC).
                    </div>
                </div>
            </div>
        HTML;
        
        return $html;
    }
?>