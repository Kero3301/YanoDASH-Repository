<?php
    require_once '../components/head.php';
    require_once '../components/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php initializePage("Upload Document")?>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php echo navbar()?>

    <div class="container">
        <h2>Document Portal</h2>
        <p class="subtitle">Select a file from your device to upload.</p>

        <div class="section">
            <label for="fileUpload" class="custom-file-upload">
                Choose Document
            </label>
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