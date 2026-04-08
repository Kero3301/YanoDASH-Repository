<?php
require_once '../components/head.php';
require_once '../components/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Document</title>

<style>
.button {
  font: bold 16px Arial;
  border: none;
  color: white;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: block;
  width: 250px;
  margin: 10px auto;
  cursor: pointer;
  border-radius: 25px;
  transition: all 0.2s ease;
}

.button:hover {
  opacity: 0.85;
  transform: scale(1.03);
}

.button:active {
  transform: scale(0.95);
}

.button1 {background-color: #2E7D32;} 
.button2 {background-color: #1976D2;}
.button3 {background-color: #ff7b00;}
</style>

</head>
<body>

<?php echo navbar(0); ?>

<h1 style="text-align:center;">Hey there! choose what you want to do</h1>

<a href="archive.php" class="button button1">Request to archive</a>
<a href="track.php" class="button button2">Track your request</a>
<a href="overview.php" class="button button3">Overview</a>

</body>
</html>