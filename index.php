<!DOCTYPE html>
<html>
    <head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="scripts/jquery.tablesorter.min.js"></script>
    <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div id="container">
            <h1>Yesterlinks</h1>
        <div id="filters">
            <strong>
                <span class="rainbow-text">Filter</span> your results to a certain type of
                site!</strong><br><br>
                <div class="filters" style="display:flex; justify-content:space-between; flex-wrap:wrap;">
                            <label for="fun">Fun</label>
                            <input type="checkbox" id="fun" name="fun" rel="fun" value="fun" checked>
                            <label for="healing">Healing</label>
                            <input type="checkbox" id="healing" name="healing" rel="healing" value="healing" checked>
                            <label for="serious">Serious</label>
                            <input type="checkbox" id="serious" name="serious" rel="serious" value="serious" checked>
                            <label for="useful">Useful</label>
                            <input type="checkbox" id="useful" name="useful" rel="useful" value="useful" checked>
                            <label for="social">Social</label>
                            <input type="checkbox" id="social" name="social" rel="social" value="social" checked>

                    </div>
                    <br> Or just click <strong class="rainbow-text">surf</strong>.<br><br>
        <a href="#" id="surf" target="_blank">Surf</a>

        <table id="directory">
        <div class="row">
            <thead>
                
                <tr>
                    <th class="url title">Title</th>
                    <th class="descr">Description</th>
                    <th class="cat title">Category</th>
                </tr>

            </thead>
            </div>
            <tbody>

<?php
include "config.php";

$stmt = $con->prepare("SELECT * FROM websites ORDER BY id DESC");
$stmt->execute();

$stmt->close();

$sql = "SELECT COUNT(*) FROM websites";
$qry = mysqli_query($con, $sql);
$totalCount = mysqli_fetch_assoc($qry)['COUNT(*)'];

$idarray = [];
$urlarray = [];
$catarray = [];

while ($row = $result->fetch_assoc()) {
    $idarray[] = $row['id'];
    $urlarray[] = $row['url'];
    $catarray[] = $row['category'];
    $id = $row['id'];
    $title = $row['title'];
    $descr = $row['descr'];
    $url = $row['url'];
    $cat = $row['category'];

    echo '';
    echo '<tr class="' . $cat . '" id="' . $id . '">';
    if ($title === null) {
    echo '<td class="url"><a href="' . $url . '" target="_blank">Untitled</a></td>';
    } else {
    echo '<td class="url"><a href="' . $url . '" target="_blank">' . $title . '</a></td>';
    }
    if ($descr === '') {
        echo '<td class="desc">No description added.</td>';
    } else {
    echo '<td class="descr">' . $descr . '</a></td>';
    }
    echo '<td class="cat" data-attr="' . $cat . '">' . $cat . '</td>';
    echo '</tr>';
}
?>
</tbody>
</div>
</body>
</html>
<script>
var idArr = <?php echo json_encode($idarray); ?>;
var urlArr = <?php echo json_encode($urlarray); ?>;
var catArr = <?php echo json_encode($catarray); ?>;

var random;

$(function() {
  $("#directory").tablesorter();
});


$('#surf').on("click", function(e) {
    e.preventDefault();
    random = Math.floor(Math.random() * urlArr.length);
    console.log(shuffle(urlArr));
    window.open(shuffle(urlArr)[0]);
    shuffle(urlArr[0].pop());
});

// this puts all of the entries in a random order
function shuffle(urlArr) {
    let currentIndex = urlArr.length, randomIndex;

    // while there are items left to shuffle...
    while (currentIndex != 0) {
        // pick a remaining element...
        randomIndex = Math.floor(Math.random() * currentIndex);
        // decrease
        currentIndex--;

        // swap with current element
        [urlArr[currentIndex], urlArr[randomIndex]] = [urlArr[randomIndex], urlArr[currentIndex]];
    }
    return urlArr;
}
var firstClick = 0;
$('input[type=checkbox]').on("change", function() {
    if (firstClick !== 1) {
    //$('tbody').css('display', 'none');
    firstClick = 1;
    

    if ($(this).prop("checked") == true) {
        console.log('checked');
        var cat = $(this).val();
        console.log($(this).val());
        $('.' + cat).css("display", "table-row");
    } else if ($(this).prop("checked") == false) {
        console.log('unchecked');
        var removeCat = $(this).val();
        $('.' + removeCat).css("display", "none");
    }
    // if not first click...
} else {
    //$(this).css("display", "none");
    if ($(this).prop("checked") == true) {
        console.log('checked');
        var cat = $(this).val();
        console.log($(this).val());
        $('.' + cat).css("display", "table-row");
    } else if ($(this).prop("checked") == false) {
        console.log('unchecked');
        var removeCat = $(this).val();
        $('.' + removeCat).css("display", "none");
    }
}

});

</script>

