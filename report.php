

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Yesterlinks | Report a Link</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script data-goatcounter="https://yesterweb.goatcounter.com/count" async src="//gc.zgo.at/count.js"></script>
</head>
<?php
    date_default_timezone_set("US/Eastern");
    $date = date("Y-m-d");
    include 'config.php';

    $sql = "SELECT DISTINCT category FROM websites";
    $catArray = [];
    $result = mysqli_query($con, $sql);
    while ($row = $result->fetch_assoc()) {
        $catArray[] = $row['category'];
    }
?>
    <body>
        <div class="container">
            <?php include 'navigation.php'; ?>
            <div class="wrapper">
                
            
                    <?php

                    if (isset($_GET['entry'])) {
                    //echo "isset";
                    $id = $_GET['entry'];
                                    
                 $stmt = $con->prepare("SELECT * FROM websites WHERE id = ?");
                 $stmt->bind_param("s", $id);
                 $stmt->execute();
                 $result = $stmt->get_result();
                 while ($row = $result->fetch_assoc()) {
                     $url = $row['url'];
                     $id = $row['id'];

                 }

                 $stmt->close();
                 ?>
                <div class="contain">
                    <h1>Report a Link</h1>
                    <p class="intro">
                    <p>Why you are reporting <a href="<?php echo $url ?>" target="_blank"><?php echo $url; ?></a>?</p>
                    <div class="report">
<form method="POST" action="report.php">
<label>Please select a reason for your report:</label>
<select name="reportReason" required>
  <option></option>
  <option value="hateful">Link contains hateful and discriminatory content</option>
  <option value="commercial">Link contains commercial content</option>
  <option value="false">Link contains false or misleading information</option>
  <option value="broken">Link is broken</option>
  <option value="miscategorized">Link is miscategorized</option>
  <option value="ads">Link has too many ads</option>
  <option value="other">Other</option>
</select><br><br>
<label>Additional information you'd like to include:</label>
<textarea name="extraNotes" class="reportField"></textarea>
<br><br>
<input type="hidden" name="id" value="<?php echo $id ?>">
<label>Type the word <u>website</u> to prove you're not a bot:</label>
<input type="text" id="botField" name="botField"><br><br>
<input name="submit" type="submit" id="submit" value="submit">
<?php } ?>
</div>
                    </form>
<?php

if (isset($_POST['submit'])) {
    //echo "isset";
    $botField = $_POST['botField'];
    $reportReason = $_POST['reportReason'];
    $extraNotes = $_POST['extraNotes'];
    $id = $_POST['id'];
    //echo $id;
    //echo $reportReason;
    //echo $extraNotes;
    if ($botField !== "website") {
        echo "did not pass bot field";
        return;
    } else {
?>
    <h1>Report a Link</h1>
    <p>Thank you for your report and for helping Yesterlinks be even better!</p>
    <p><a href="index.php">Return to Yesterlinks</a></p>
    
    <?php
    $stmt = $con->prepare("INSERT INTO reports(date, websites_id, reportReason, extraNotes) VALUES (?,?,?,?)");
    $stmt->bind_param("ssss", $date, $id, $reportReason, $extraNotes);
    $stmt->execute();
    $stmt->close();
    // submit logic here
}
}
 ?>


<style>
    .reportField {
        width:400px;
        height:100px;
    }
    </style>


<script>

    var botField = document.getElementById('botField');
    var submitBtn = document.getElementById('submit');

    submitBtn.disabled = true;
    botField.addEventListener("keyup", checkIfBot);


function checkIfBot() {
  var value = botField.value;
     if (value == "website") {
      submitBtn.disabled = false;
    }
}

    </script>
</html>
