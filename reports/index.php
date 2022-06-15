<?php include '../auth.php' ?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Yesterlinks | Report a Link</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script data-goatcounter="https://yesterweb.goatcounter.com/count" async src="//gc.zgo.at/count.js"></script>
</head>
<?php
    include '../config.php';
?>
    <body>
        <div class="container">
            <?php include '../navigation.php'; ?>
            <div class="wrapper">
                <h1>Reported Links</h1>
                <br><br>
                <?php
                $stmt = $con->prepare("SELECT reports.id as reports_id, reports.date, reports.reportReason, reports.extraNotes, websites.id as websites_id, websites.title, websites.url, websites.descr FROM reports JOIN websites ON reports.websites_id = websites.id");
                $stmt->execute();
                $result = $stmt->get_result();
                $number = mysqli_num_rows($result);
               
                echo "<div class='report'>";
                if ($number !== 0) {
                while ($row = $result->fetch_assoc()) {
                    $title = $row['title'];
                    $url = $row['url'];
                    $website_id = $row['websites_id'];
                    $report_id = $row['reports_id'];
                    $descrip = $row['descr'];
                    $reportReason = $row['reportReason'];
                    $extraNotes = $row['extraNotes'];
                    $date = $row['date'];
                    $formattedDate = date("m/d/Y", strtotime($date));
                    echo "On <strong>" . $formattedDate . "</strong>, the link <a href='" . $url . "'>" . $title . "</a> was reported </strong> because <strong>" . $reportReason . "</strong>";
                    
                    echo "<p><strong>Additional information: </strong>";

                    if (!empty($extraNotes)) {
                        echo $extraNotes . "</p>";
                    } else {
                        echo "<p>No further information provided.</p>";
                    }
                    echo "<p><strong>Website Description: </strong>";
                    if (!empty($descrip)) {
                        echo $descrip . "</p>";
                    } else {
                        echo "<p>No description added.</p>";
                    }
                    echo "<form method='POST' action='index.php'>";
                    echo "<input type='hidden' name='id' value='" . $website_id . "'>";
                    echo "<input type='hidden' name='reportId' value='" . $report_id . "'>";
                    echo "<input type='submit' id='delete' value='Delete Link' name='del'> ";
                    echo "|";
                    echo "<input type='submit' id='deleteReport' value=' Delete Report' name='delReport'><br><br>";
                    echo "</form>";
                }
            } else {
                echo "<p>There is nothing that has been reported.</p>";
            }
                $stmt->close();
/* This generates the JSON file */
function generateJSON($con) {
    $rows = array();
    $sql = ("SELECT id, title, url, descr, category FROM websites WHERE pending = 0");
    mysqli_set_charset($con, 'utf8');
    if ($result = mysqli_query($con, $sql)) {
      if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
          $rows[] = $row;
        }
        $f = fopen('../yesterlinks.json', 'w');
        fwrite($f, json_encode($rows));
      }
    }
  }

                if (isset($_POST['del'])) {
                    $id = $_POST['id'];
                    $stmt = $con->prepare("DELETE FROM websites WHERE id = ?");
                    $stmt->bind_param("s", $id);
                    $stmt->execute();
                    $stmt->close();
                    generateJSON($con);
                    header('location: ../reports/');
                  }

                if (isset($_POST['delReport'])) {
                $id = $_POST['reportId'];
                $stmt = $con->prepare("DELETE FROM reports WHERE id = ?");
                $stmt->bind_param("s", $id);
                $stmt->execute();
                $stmt->close();
                generateJSON($con);
                header('location: ../reports/');
                }
                ?>
          <style>
              .report {
                  border:1px solid var(--accent);
                  padding:10px;
              }
              #delete, #deleteReport {
                  background-color:transparent;
                  color:red;
                  border:none;
                  font-weight:bold;
                  margin:0;
                  padding:0;
                  cursor:pointer;
                  font-size:1em;
              }
              form {
                  margin-left:0;
                
              }
          </style>  