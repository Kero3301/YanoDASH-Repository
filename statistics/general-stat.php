<!-- General Statistics -->
<!-- Assigned Member: Carylle -->
<?php
    require_once '../components/head.php';
    require_once '../components/navbar.php';
?>

<!DOCTYPE html>
<html>
    <head>
        <?php initializePage("General Statistics | YanoDASH")?>
        <link rel="stylesheet" href="../css/stats.css"/>
    </head>
    <body>
        <?php echo navbar(2)?>
        <h2 class="title"> Statistics Dashboard </h2>
        <div class="container">
            <h6 class="box"> Total Documents </br> <p class="num">10,540</p> </h6>    
            <h6 class="box"> Storage Used </br><p class="num"> 1GB/5GB(20%)</p> </h6>
            <h6 class="box"> Total Collections </br><p class="num"> 45 </p></h6>
            <h6 class="box"> Pending Reviews </br><p class="num"> 15 </p></h6>
            <h6 class="boxx"> Average Uploads per month </br><p class="num"> 20 </p></h6>
        </div>
        <div class="t-container">
            <h4> Top Downloaded Documents </h4>
            <table>
                <thead>
                    <tr>
                        <th> Document Title </th>
                        <th> Category </th>
                        <th> Downloads </th>
                    </tr>
                </thead>
                <tbody> 
                    <tr> 
                        <td> Hudyaka Memo </td>
                        <td> Council </td>
                        <td> 235 </td>
                    </tr>
                    <tr> 
                        <td> Budget </td>
                        <td> Departmental </td>
                        <td> 60 </td>
                    </tr>
                    <tr> 
                        <td> CIC Fest Memo </td>
                        <td> Departmental </td>
                        <td> 45 </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </body>
</html>