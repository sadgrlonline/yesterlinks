<?php

include 'config.php';

// handles edits
if (isset($_POST['cat'])) {
  $id = $_POST['id'];
  $title = $_POST['title'];
  $url = $_POST['url'];
  $descr = $_POST['descr'];
  $cat = $_POST['cat'];
  $tags = array(
    'db'  => array(), // store the tags from the database
    'new' => json_decode($_POST['tags']), // store the tags from the $_POST
  );

  $stmt = $con->prepare("UPDATE websites SET title = ?, url = ?, descr = ?, category = ? WHERE id = ?");
  $stmt->bind_param("sssss", $title, $url, $descr, $cat, $id);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();

  /* update tags for the item */
  // get existing tags from database
  $sql_tags = "SELECT id, tag_id FROM taglist WHERE site_id =" . $id;
  $qry_tags = mysqli_query($con, $sql_tags);

  /* Fill our $tags['db'] array with the IDs of the site's existing tags */
  while ($tag = mysqli_fetch_assoc($qry_tags)) {
    array_push($tags['db'], $tag['tag_id']);
  }

  $db_length = count($tags['db']);
  $new_length = count($tags['new']);

  /* Compare the new ($tags['new']) and existing ($tags['db']) tag arrays, removing duplicates  */
  for ($n=0; $n < $new_length; $n++) {
    for ($d=0; $d < $db_length; $d++) :
      if ($tags['new'][$n] === $tags['db'][$d]) {
        unset($tags['new'][$n]);
        unset($tags['db'][$d]);
      }
    endfor;
  }

  /* fix array indices so we can use for loops below */
  $tags['new'] = array_values($tags['new']);
  $tags['db'] = array_values($tags['db']);

  /* add newly checked items */
  for ($i=0; $i < count($tags['new']); $i++) {
    $stmt = $con->prepare("INSERT INTO taglist (tag_id, site_id) VALUES (?,?)");
    $stmt->bind_param("ss", $tags['new'][$i], $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
  }

  /* remove exisitng tags that we unchecked */
  for ($i=0; $i < count($tags['db']); $i++) {
    $stmt = $con->prepare("DELETE FROM taglist WHERE tag_id = ? AND site_id = ?");
    $stmt->bind_param("ss", $tags['db'][$i], $id);
    $stmt->execute();
    $stmt->close();
  }

  /* this was for making sure the arrays updated properly; it's a bit clunky, but it may be useful in a pinch */
  // echo "=== DB\n";
  // var_dump($tags['db']);
  // echo "\n=== NEW\n";
  // var_dump($tags['new']);


  // this generates the JSON file
  $rows = array();
  $sql = ("SELECT id, title, url, descr, category FROM websites WHERE pending = 0");
  mysqli_set_charset($con, 'utf8');
  if ($result = mysqli_query($con, $sql)) {
    if (mysqli_num_rows($result) > 0) {
      while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
      }
      $f = fopen('yesterlinks.json', 'w');
      fwrite($f, json_encode($rows));
    }
  }
}

// handles deletions
if (isset($_POST['del'])) {
  $id = $_POST['id'];
  $stmt = $con->prepare("DELETE FROM websites WHERE id = ?");
  $stmt->bind_param("s", $id);
  $stmt->execute();
  $stmt->close();

  // this generates the JSON file
  $rows = array();
  $sql = ("SELECT id, title, url, descr, category FROM websites WHERE pending = 0");
  mysqli_set_charset($con, 'utf8');
  if ($result = mysqli_query($con, $sql)) {
    if (mysqli_num_rows($result) > 0) {
      while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
      }
      $f = fopen('yesterlinks.json', 'w');
      fwrite($f, json_encode($rows));
    }
  }
}

// handles submissions
if (isset($_POST['submit'])) {
  $honeypot = $_POST['honeypot'];
  if(!empty($honeypot)){
    return; //you may add code here to echo an error etc.
  }else{
    $title = $_POST['titleInput'];
    $url = $_POST['urlInput'];
    $descr = $_POST['descrInput'];
    $cat = $_POST['categories'];
    $tags = array(
      'db'  => array(), // store the tags from the database
      'new' => array(), // store the tags from the $_POST
    );

    echo $title;
    echo $url;
    echo $descr;
    echo $cat;
    $stmt = $con->prepare("INSERT INTO websites(title, url, descr, category) VALUES (?,?,?,?)");
    $stmt->bind_param("ssss", $title, $url, $descr, $cat);
    $stmt->execute();
    $stmt->close();

    /* `submit-a-link.php` sends each checkbox individually, so we need to capture those in a different way. We can make them the same later.` */
      $get_all_tags = mysqli_query($con, "SELECT * FROM tags ORDER BY tags.name ASC"); // query all tags for displaying the checkboxes
      while ($single_tag = mysqli_fetch_assoc($get_all_tags)) {
        $tag_name = $single_tag['name'];

        $f = fopen('tags.json', 'a+');
        fwrite($f, strval($_POST[$tag_name]));
        /* If the tag was checked, the name of the tag will be set in the POST request and we can add its value to our $tags['new'] array */
        if (isset($_POST[$tag_name])) {
          echo strval($_POST[$tag_name]);
          array_push($tags['new'], strval($_POST[$tag_name]));
        }
        unset($tag_name);
      }

      /* Get id of newly entered item */
      $stmt = $con->prepare("SELECT id FROM websites ORDER BY id DESC LIMIT 1");
      $stmt->execute();
      $result = $stmt->get_result();

      $stmt->close();

      $id = $result->fetch_assoc()['id'];

    /* add tags for new item */
    for ($i=0; $i < count($tags['new']); $i++) {
      $stmt = $con->prepare("INSERT INTO taglist (tag_id, site_id) VALUES (?,?)");
      $stmt->bind_param("is", $tags['new'][$i], $id);
      $stmt->execute();
      $result = $stmt->get_result();
      $stmt->close();
    }

    header('location: submit-a-link.php');
  }

}

// this block handles approvals logic
if (isset($_POST['approved'])) {
  $id = $_POST['id'];
  $stmt = $con->prepare("UPDATE websites SET pending = 0 WHERE id = ?");
  $stmt->bind_param("s", $id);
  $stmt->execute();
  $stmt->close();

  // this generates the JSON file
  $rows = array();
  $sql = ("SELECT id, title, url, descr, category FROM websites WHERE pending = 0");
  mysqli_set_charset($con, 'utf8');
  if ($result = mysqli_query($con, $sql)) {
    if (mysqli_num_rows($result) > 0) {
      while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
      }
      $f = fopen('yesterlinks.json', 'w');
      fwrite($f, json_encode($rows));
    }
  }
}
