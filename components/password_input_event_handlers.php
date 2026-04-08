<?php
    # Use this function to create a toggle password visibility event handler for a password input component so that when the "show/hide password" button is clicked, it automatically shows/hides the password
    function togglePasswordVisibilityFor(string $passwordInputID, string $inputID) {
        $script = <<< HTML
            <script>
                (function() {
                    const btn = document.querySelector('#$passwordInputID button');
                    btn.onclick = function() {
                        const input = document.getElementById("$inputID");
                        if (input.type === "password") {
                            input.type = "text";
                            this.textContent = "⊚";
                        } else {
                            input.type = "password";
                            this.textContent = "⊘";
                        }
                    };
                })();
            </script>
        HTML;
        return $script;
    }
?>