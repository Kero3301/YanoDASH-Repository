<?php
    session_start();

    require_once '../../../src/loader.php';
    load (
        'authentication',
        'authorization',
        'navbar'
    );

    if (!is_logged_in()) {
        header('location: '. $app_url. '/auth/login.php');
        exit;
    }

    if (!can_use_dms($permissions))
        die("You do not have permission to access this resource.");

    

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php initialize_page("Upload Document")?>
    <link rel="stylesheet" href="../../css/pages/adddocstyles.css">
</head>
<body>
    <?php echo navbar()?>

    <div class="container">
        <h2 style="font-family: 'Gupter'; font-weight: nomral">New Document</h2>

        <div class="section">
            <p style="color: white; font-family: 'RobotoFlex'">Document Name </p>
            <input type="text" name="docname" placeholder="Document Name" required><br><br>
            <p style="color: white;">Categories </p>
            <select name="categories" id="categories">
            <option value="Financial">Financial</option>
            <option value="Activity Design">Activity Design</option>
            <option value="Minutes">Minutes</option>
            <option value="Other">Other</option>
            </select>
            <br><br>

            <p style="color: white;">Initial File </p>
            <label for="fileUpload" class="custom-file-upload">
                Choose Document
            </label>
            <p class="subtitle">Select a file from your device to upload. <br>Max: 20 MB</p>
            <input type="file" id="fileUpload" accept=".pdf,.doc,.docx,.txt">
            
            <div id="fileInfo" class="file-info">No file chosen</div>

            <button class="upload-btn" onclick="startUpload()">Upload Document</button>
            <div id="statusMessage" class="status"></div>
        </div>
    </div>

    <script>
        const fileInput = document.getElementById('fileUpload');
        const fileInfo = document.getElementById('fileInfo');
        const statusMessage = document.getElementById('statusMessage');

        // Update the text when a file is selected
        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                fileInfo.textContent = "Selected: " + this.files[0].name;
                statusMessage.innerHTML = ""; 
            } else {
                fileInfo.textContent = "No file chosen";
            }
        });

        function startUpload() {
            if (fileInput.files.length === 0) {
                statusMessage.innerHTML = `<span class="error">Please select a file first.</span>`;
                return;
            }

            // Simulating an upload process
            statusMessage.innerHTML = `<span class="success">Uploading ${fileInput.files[0].name}...</span>`;
            
            setTimeout(() => {
                statusMessage.innerHTML = `<span class="success">✔ Upload Complete!</span>`;
            }, 1500);
        }
    </script>

</body>
</html>