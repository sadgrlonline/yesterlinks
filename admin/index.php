<!DOCTYPE html>
<html>
    <head>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
        <script type="text/javascript" src="../scripts/jquery.tablesorter.min.js"></script>
        <link rel="stylesheet" href="../style.css">
    </head>

    <body>
        <div class="container">
        <?php include 'navigation.php' ?>
            <h1>Edit Links</h1>
            <div id="filters">
                <strong>
                    <span class="rainbow-text">Filter</span> your results to a certain type of
                    site!
                </strong><br><br>
                <div style="display:flex; justify-content:space-between; flex-wrap:wrap;">
                        <div class="filter">
                            <label for="personal">Personal</label>
                            <input type="checkbox" id="personal" name="personal" value="personal">
                        </div>
                        <div class="filter">
                            <label for="fun">Fun</label>
                            <input type="checkbox" id="fun" name="fun" value="fun">
                        </div>
                        <div class="filter">
                            <label for="healing">Healing</label>
                            <input type="checkbox" id="healing" name="healing" value="healing">
                        </div>
                        <div class="filter">
                            <label for="serious">Serious</label>
                            <input type="checkbox" id="serious" name="serious" value="serious">
                        </div>
                        <div class="filter">
                            <label for="useful">Useful</label>
                            <input type="checkbox" id="useful" name="useful" value="useful">
                        </div>
                        <div class="filter">
                            <label for="social">Social</label>
                            <input type="checkbox" id="social" name="social" value="social">
                        </div>

                    </div>
</div>
        <table id="directory">
        <div class="row">
            <thead>
                
                <tr>
                <tr>
                    <th class="title">Title</th>
                    <th class="url title">URL</th>
                    <th class="descr">Description</th>
                    <th class="cat title">Category</th>
                    <th class="cat title"></th>
                    <th class="cat title"></th>
                </tr>
                </tr>

            </thead>
            </div>
            <tbody>

<?php
include "../config.php";

$stmt = $con->prepare("SELECT * FROM websites ORDER BY id DESC");
$stmt->execute();

$result = $stmt->get_result();

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
    $url = $row['url'];
    $descr = $row['descr'];
    $cat = $row['category'];

    echo '';
    echo '<tr data-attr="' . $cat . '" id="' . $id . '" class="row">';
    if ($title === null || '') {
        echo '<td class="title"><a href="' . $title . '" target="_blank">Untitled</a></td>';
    } else {
    echo '<td class="title">'. $title . '</td>';
    }
    echo '<td class="url"><a href="' . $url . '" target="_blank">' . $url . '</td>';
    if ($descr === '') {
        echo '<td class="descr">No description added.</td>';
    } else {
    echo '<td class="descr">' . $descr . '</a></td>';
    }
    echo '<td class="cat" data-attr="' . $cat . '">' . $cat . '</td>';
    echo '<td><a href="#" class="edit" id="' . $id . '">edit</a></td>';
    echo '<td><a href="#" class="del" id="' . $id . '">x</a></td>';
    echo '</tr>';
}
?>
</tbody>
</div>
<style>
    .container {
        max-width:70%;
    }
</style>
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





$('td').on("click", ".edit", function(e) {
    e.preventDefault();
    var url = $(this).parent('td').siblings('.url').text();
    var cat = $(this).parent('td').parent('.row').children('.cat').text();
    var title = $(this).parent('td').parent('.row').children('.title').text();
    var descr = $(this).parent('td').parent('.row').children('.descr').text();
    var id = $(this).attr("id");

    
    $(this).parent('td').parent('.row').children('.url').append('<input type="text" class="titleInput"></input>');

    console.log(id);
    $(this).parent('td').parent('.row').children('.title').html('<input type="text" class="titleInput" value="' + title + '"></input>');
    $(this).parent('td').parent('.row').children('.url').html('<input type="text" class="urlInput" value="' + url + '"></input>');
    $(this).parent('td').parent('.row').children('.descr').html('<input type="text" class="descrInput" value="' + descr + '"></input>');
    $(this).parent('td').parent('.row').children('.cat').html('<input type="text" class="catInput" value="' + cat + '"></input>');
    $(this).replaceWith('<a class="save" id="' + id + '" href="#">save</a>');



    $('td').on("click", ".save", function(e) {
    e.preventDefault();
    var url = $(this).parent('td').siblings('.url').children('.urlInput').val();
    var cat = $(this).parent('td').parent('.row').children('.cat').children('.catInput').val();
    var title = $(this).parent('td').parent('.row').children('.title').children('.titleInput').val();
    var descr = $(this).parent('td').parent('.row').children('.descr').children('.descrInput').val();
    var id = $(this).attr('id');
       // console.log('test');
        console.log(id);
        $(this).parent('td').siblings('.url').html('<a href="' + url + '">' + url + '</a>');
        $(this).parent('td').siblings('.cat').text(cat);
        $(this).parent('td').siblings('.title').text(title);
        $(this).parent('td').siblings('.descr').text(descr);
   $(this).replaceWith('<a class="edit" id="' + id + '"href="#">edit</a>')
        console.log(id, title, url, descr, cat);
   $.ajax({
			type: 'post',
			data: {'id':id,
                   'title':title,
                   'url':url,
                   'descr':descr,
                   'cat':cat
			},
			url: '../submit.php',
			success: function(response) {
             //location.reload();
			}
		});
});
});

$('.del').on("click", function(e) {
    e.preventDefault();
    var id = $(this).attr('id');
    var del = "del";
    if (confirm("Are you sure you want to delete this entry?") == true) {
    $(this).parent('td').parent('.row').remove();
    $.ajax({
			type: 'post',
			data: {'id':id,
                   'del':del
			},
			url: '../submit.php',
			success: function(response) {
             //location.reload();
			}
		});
    }
})
</script>

