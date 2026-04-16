<?php
  require_once '../components/head.php';
  require_once '../components/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php initializePage("Request Overview | YanoDASH")?>

<style>
  /* body {
    font: normal 1em 'Times New Roman', Times, serif, sans-serif;
    background: #fff;
     margin: 0;
    padding: 0;
    box-sizing: border-box;
  } */

  .header-container {
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    margin-top: 60px;
    width: 100%;
  }

  .btnarchive {
    position: absolute;
    right: 15%;
    background: #fff;
    border: 2px solid maroon;
    color: maroon;
    padding: 10px 20px;
    border-radius: 25px;
    text-decoration: none;
    font: bold 1em Arial, sans-serif;
    transition: 0.3s ease;
    margin-top: 80px;
    margin-left: 25px;
  }

  .btnarchive:hover {
    background: maroon;
    color: white;
    align-items: center;
    flex-direction: column;
    
  }

  .stats-container {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-top: 60px;
    flex-wrap: wrap;
  }

  .card {
    width: 180px;
    padding: 20px;
    border-radius: 15px;
    color: white;
    text-align: center;
    font: bold 18px Arial;
  }

  .activity-section {
    max-width: 800px;
    margin: 50px auto;
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    font: normal 1em Arial, sans-serif;
  }

  th {
    text-align: left;
    color: #888;
    font-size: 14px;
    border-bottom: 2px solid #eee;
    padding: 10px;
  }

  td {
    padding: 15px 10px;
    border-bottom: 1px solid #eee;
    font-size: 15px;
    color: #333;
  }

  .status {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
  }

  .status-approved { background: #e8f5e9; color: #2e7d32; }
  .status-pending { background: #e3f2fd; color: #1976d2; }
  .status-rejected { background: #ffebee; color: #c62828; }

  /* Colors */
  .total { background: #616161; }
  .approved { background: #2e7d32; }
  .pending { background: #1976d2; }
  .rejected { background: #c62828; }
</style>

</head>
<body>

<?php echo navbar(0); ?>

<div class="header-container">
    <h1 style="margin: -5px;">Request Overview</h1>
    <a href="archive.php" class="btnarchive">+ New Request</a>
</div>

<div class="stats-container">
  <div class="card total">Total Requests<br><strong>120</strong></div>
  <div class="card approved">Approved<br><strong>80</strong></div>
  <div class="card pending">Pending<br><strong>25</strong></div>
  <div class="card rejected">Rejected<br><strong>15</strong></div>
</div>
<div class="activity-section">
  <h2 style="margin-top: 0; font-family: 'Times New Roman'; border-bottom: 1px solid #eee; padding-bottom: 10px;">Recent Activity</h2>
  <table>
    <thead>
      <tr>
        <th>Requester Name</th>
        <th>Date</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Juan Dela Cruz</td>
        <td>Oct 24, 2024</td>
        <td><span class="status status-approved">Approved</span></td>
      </tr>
      <tr>
        <td>Petta Mellar</td>
        <td>Oct 23, 2024</td>
        <td><span class="status status-pending">Pending</span></td>
      </tr>
      <tr>
        <td>Catherene Everdeen</td>
        <td>Oct 22, 2024</td>
        <td><span class="status status-rejected">Rejected</span></td>
      </tr>
    </tbody>
  </table>
</div>

</body>
</html>