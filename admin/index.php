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
    <h1>Yesterlinks Admin</h1>
    <div id="search"><label>Search: </label><input type="text" id="searchInput">
          </div>
    <div class="table-wrapper">
    <table id="directory">
      <div class="row">
        <thead>
          <tr>
            <tr>
              <th class="title">Title</th>
              <th class="urlAdmin title">URL</th>
              <th class="descr">Description</th>
              <th class="cat title">Category</th>
              <th class="cat title">Tags</th>
              <th class="cat title" colspan="2">Options</th>
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
          $descr = $row['descr'];
          $url = $row['url'];
          $cat = $row['category'];

          $tags_query = "SELECT tag_id, tags.name FROM taglist JOIN tags ON taglist.tag_id = tags.id WHERE site_id =" . $row['id'] . " ORDER BY tags.name ASC";
          $tags_result = mysqli_query($con, $tags_query);
          $site_tag_ids = array(); // array for storing this site's tag IDs

          $tag_html = "";

          while ($single_tag = mysqli_fetch_assoc($tags_result)) {
            array_push($site_tag_ids, $single_tag['tag_id']); // store each tag's ID; we'll use this to check the appropriate checkboxes below
            $tag_html .= "<li data-tag-id=" . $single_tag['tag_id'] . ">" . $single_tag['name'] . "</li>";
          } ?>

          <tr class="<?php echo $cat; ?> row" id="<?php echo $id; ?>">
            <td class="title"><?php if ($title === null) {
                echo "Untitled";
              } else {
                echo $title;
              } ?></td>
            <td class="urlAdmin"><a href="<?php echo $url; ?>" target="_blank"><?php echo $url; ?></td>
            <td class="descr"><?php if ($descr === '') {
                echo "No description added.";
              } else {
                echo strtolower($descr);
              } ?></td>
              <td class="cat" data-attr="<?php echo $cat; ?>"><?php echo $cat; ?></td>
              <td class="tags">
                <ul>
                  <?php if (empty($tag_html) === false) {
                    echo $tag_html;
                  } ?>
                </ul>
                <fieldset class="tags__edit hide">
                  <legend>Tag List (Toggle checkbox to add/remove)</legend>
                  <?php
                  /* loop for displaying the checkboxes */
                  $get_all_tags = mysqli_query($con, "SELECT * FROM tags ORDER BY tags.name ASC"); // query all tags for displaying the checkboxes
                  while ($single_tag = mysqli_fetch_assoc($get_all_tags)) { ?>
                    <div>
                      <input type="checkbox" id="<?php echo $id . "-" . $single_tag['name']; ?>" name="<?php echo $single_tag['name']; ?>" value="<?php echo $single_tag['id']; ?>"
                      <?php
                      /* here, we loop through all of the site's existing tags */
                        for ($i=0; $i < count($site_tag_ids); $i++) {
                          /* if any of the site's existing tags match the one currently stored in $single_tag, we echo "checked" so our checkboxes match the database  */
                          if ($single_tag['id'] === $site_tag_ids[$i]) { echo "checked"; }
                        } ?>>
                      <label for="<?php echo $id . "-" . $single_tag['name']; ?>"><?php echo $single_tag['name']; ?></label>
                    </div>
                  <?php } ?>
                </fieldset>
              </td>
              <td><a href="#" class="edit" id="<?php echo $id; ?>">edit</a></td>
              <td><a href="#" class="del" id="<?php echo $id; ?>">x</a></td>
            </tr>
          <?php } // end while loop for sites ?>
      </tbody>
    </table>
  </div>
    </div>
    <style>
    .container {
      max-width:1200px;
    }

    /* Now we can horizontally scroll the table, but the nav will stay in place! */
    .table-wrapper {
      overflow: auto;
    }

    .descr {
      width:200px !important;
      max-width:200px !important;
    }
    .tags {
      min-width:120px;
    }
    .title {
      width:150px;
      max-width:150px;
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
  
  var url = $(this).parent('td').siblings('.urlAdmin').text();
  var cat = $(this).parent('td').parent('.row').children('.cat').text();
  var title = $(this).parent('td').parent('.row').children('.title').text();
  var descr = $(this).parent('td').parent('.row').children('.descr').text();
  var id = $(this).attr("id");


  $(this).parent('td').parent('.row').children('.urlAdmin').append('<input type="text" class="titleInput"></input>');

  console.log(id);
  $(this).parent('td').parent('.row').children('.title').html('<input type="text" class="titleInput" value="' + title + '"></input>');
  $(this).parent('td').parent('.row').children('.urlAdmin').html('<input type="text" class="urlInput" value="' + url + '"></input>');
  $(this).parent('td').parent('.row').children('.descr').html('<textarea rows="15" class="descrInput">' + descr + '</textarea>');
  $(this).parent('td').parent('.row').children('.cat').html('<input type="text" class="catInput" value="' + cat + '"></input>');
  $(this).parent('td').siblings('.tags').children('ul').addClass('hide');
  $(this).parent('td').parent('.row').children('.tags').children('.tags__edit').removeClass('hide');
  $(this).replaceWith('<a class="save" id="' + id + '" href="#">save</a>');



  $('td').on("click", ".save", function(e) {
    e.preventDefault();
    var id = $(this).attr('id');
    var url = $(this).parent('td').siblings('.urlAdmin').children('.urlInput').val();
    var cat = $(this).parent('td').parent('.row').children('.cat').children('.catInput').val();
    var title = $(this).parent('td').parent('.row').children('.title').children('.titleInput').val();
    var descr = $(this).parent('td').parent('.row').children('.descr').children('.descrInput').val();
    var tags = $(this).parent('td').parent('.row').children('.tags').find('input[type="checkbox"]:checked');
    var tagList = $(this).parent('td').parent('.row').children('.tags').children('ul');

    var chosenTagIDs = [];
    tagList.empty(); // clear the list of tags on the page

    /* Loop through the chosen tags and make an array of their IDs to send to `submit.php` */
    for (var i = 0; i < tags.length; i++) {
      chosenTagIDs.push(tags[i].value);

      tagList.append(`<li>${$(tags[i]).siblings('label').text()}</li>`); // Add this tag value to the list in the tags column
    }

    $(this).parent('td').siblings('.urlAdmin').html('<a href="' + url + '">' + url + '</a>');
    $(this).parent('td').siblings('.cat').text(cat);
    $(this).parent('td').siblings('.title').text(title);
    $(this).parent('td').siblings('.descr').text(descr);
    $(this).parent('td').siblings('.tags').children('ul').removeClass('hide');
    $(this).parent('td').siblings('.tags').children('.tags__edit').addClass('hide');
    $(this).replaceWith('<a class="edit" id="' + id + '"href="#">edit</a>')
    console.log(id, title, url, descr, cat, tags);
    $.ajax({
      type: 'post',
      data: {'id':id,
      'title':title,
      'url':url,
      'descr':descr,
      'cat':cat,
      'tags': JSON.stringify(chosenTagIDs)
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

$('#searchInput').on('keyup', function(e) {
    // value of text field
    var value = $(this).val();
    console.log(e.keyCode);

    // assigns the pattern we're searching for
    var patt = new RegExp(value, "i");
    // in the #directory table, find each tr
    $('#directory').find('tr').each(function() {
      if(value.length === 0) {
        // if search box is empty
        // loop through rows and make highlight 'invisible'.
        console.log('remove Mark');
        //$(this).children('td.descr').children(mark))
        $('mark').css('background-color', 'transparent');
        $('mark').css('padding', '0');

      }

      var $table = $(this);

      if (!($table.find('td').text().search(patt) >= 0)) {
        $table.not('.tablesorter-headerRow').hide();
      }

      if (($table.find('td').text().search(patt) >= 0)) {
        $(this).show();
        var td = $(this).children('td.desc').children('div.desc');
        var matchedRow = td.text();
        var newMarkup = matchedRow.replace(value, '<mark>' + value + '</mark>');
        td.html(newMarkup);

      }
    });
  });
</script>
