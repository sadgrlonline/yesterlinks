
<?php
include 'config.php';

$num_of_days = 30;
$decay = "$num_of_days days";

/* Remove expired votes */
function remove_expired_votes($con, $expiry_time) {
  $expiry_datetime = date_create($expiry_time);
  $expiry_string = date_format($expiry_datetime, "Y-m-d H:i:s");

  $stmt = $con->prepare("DELETE FROM `votelist` WHERE time_cast < '$expiry_string'");
  $stmt->execute();
  $stmt->close();
}

/* Calculate the weight of a vote */
function get_vote_weight($datetime) {
  $current_time = strtotime("now");
  $time_cast = strtotime($datetime);

  global $num_of_days;
  $num_segments = 12;
  $interval = ($num_of_days * 24) / $num_segments; // time (in hours) until vote fully decays

  /* Get how many hours have passed since the vote was cast */
  $sec = $current_time - $time_cast;
  $min = floor($sec / 60);
  $hours = floor($min / 60);

  /*
  * Find out the number of segments have past since the vote was cast
  * Subtract that number from the total number of segments and divide it by the total number of segments to get the fraction weight this vote should have
  */
  $segment = floor($hours / $interval);
  $weight = ($num_segments - $segment) / $num_segments;

  $vote = 1;

  return $vote * $weight;
}

/* Quanitfy all votes cast for a site */
function get_vote_count($con, $site_id) {
  /* Count upvotes */
  $votes_query = "SELECT * FROM votelist WHERE site_id =" .  $site_id . " AND vote = 1";
  $votes_result = mysqli_query($con, $votes_query);
  $votes['up'] = 0;

  while ($vote = mysqli_fetch_assoc($votes_result)) {
    $votes['up'] += get_vote_weight($vote['time_cast']);
  }

  /* Count downvotes */
  $votes_query = "SELECT * FROM votelist WHERE site_id =" .  $site_id . " AND vote = 0";
  $votes_result = mysqli_query($con, $votes_query);
  $votes['down'] = 0;

  while ($vote = mysqli_fetch_assoc($votes_result)) {
    $votes['down'] += get_vote_weight($vote['time_cast']);
  }

  $votes['total'] = $votes['up'] - $votes['down'];

  return round($votes['total'], 2);
}

/* Check if this voter has cast a vote for any given site before */
function has_existing_votes($con, $site_id, $voter_id, $vote_type) {
  $stmt = $con->prepare("SELECT COUNT(*) AS amount FROM votelist WHERE voter_id = ? AND site_id = ? AND vote = ?");
  $stmt->bind_param("sii", $voter_id, $site_id, $vote_type);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();

  while ($row = $result->fetch_assoc()) {
    $vote_count = $row['amount'];
  }


  if ($vote_count !== 0) { return true; }

  return false;
}

/* This function is for adding and removing votes */
function cast_vote($con, $site_id, $voter_id, $action) {
  $can_execute = false;
  $message = null;

  switch ($action) {
    case 'recommend':
    case 'upvote':
    $stmt = $con->prepare("INSERT INTO votelist (time_cast,voter_id, site_id, vote) VALUES (?,?,?,1)");
    $message = "Upvote cast successfully.";
    $can_execute = true;
    break;

    // case 'downvote':
    // $stmt = $con->prepare("INSERT INTO votelist (time_cast, voter_id, site_id, vote) VALUES (?,?,?,0)");
    // $message = "Downvote cast successfully.";
    // break;

    case 'remove':
    $stmt = $con->prepare("DELETE FROM votelist WHERE voter_id = ? AND site_id = ?");
    $message = "Vote removed successfully.";
    $can_execute = true;
    break;

    default:
    $message = "Invalid action chosen.";
    break;
  }

  if ($can_execute) :
    $time = date('Y-m-d H:i:s');
    if ($action !== "remove") {
      $stmt->bind_param("ssi", $time, $voter_id, $site_id);
    } else {
      $stmt->bind_param("si", $voter_id, $site_id);
    }

    $stmt->execute();
    $stmt->close();
  endif;

  return $message;
}

/* Voting logic */
if (isset($_POST['action'])) {
  $voter_id = hash('sha3-256', $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . "yw-links");
  $site_id = intval($_POST['id']);
  $action = $_POST['action'];

  $stmt = null;
  $has_voted = false;

  $response = array(
    'action' => $action,
    'count' => array(
      'new' => null,
      'old' => get_vote_count($con, $site_id),
    ),
    'message' => null,
    'site_id' => $site_id,
    'voter_id' => $voter_id,
  );


  if ($action === "upvote" || $action === "recommend") {
    $has_voted = has_existing_votes($con, $site_id, $voter_id, 1);
  }

  if ($action === "downvote") {
    $has_voted = has_existing_votes($con, $site_id, $voter_id, 0);
  }


  if ($action === "remove" || $has_voted === false) {
    cast_vote($con, $site_id, $voter_id, "remove"); // we want to remove existing votes of the other type so they don't just cancel out
    $response['message'] = cast_vote($con, $site_id, $voter_id, $action);
  }

  if ($has_voted) {
    $response['message'] = "You've already cast ";

    switch ($action) {
      case 'upvote':
      $response['message'] .= "an upvote ";
      break;

      case 'downvote':
      $response['message'] .= "a downvote ";
      break;

      default:
      $response['message'] = "You have already cast that type of vote ";
      break;
    }

    $response['message'] .= "for this site; no cheating! :p";
  }

  $response['count']['new'] = get_vote_count($con, $site_id);
  echo json_encode($response);
}

/* Edits from admin page */
if (isset($_POST['title'])) {
  $id = $_POST['id'];
  $title = $_POST['title'];
  $url = $_POST['url'];
  $descr = $_POST['descr'];
  //$cat = $_POST['cat'];
  $tags = array(
    'db'  => array(), // store the tags from the database
    'new' => json_decode($_POST['tags']), // store the tags from the POST request
  );


  /* Update everything but the tags */
  $stmt = $con->prepare("UPDATE websites SET title = ?, url = ?, descr = ? WHERE id = ?");
  $stmt->bind_param("ssss", $title, $url, $descr, $id);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();

  /* Update tags for the item */
  $tags_query = mysqli_query($con, "SELECT id, tag_id FROM taglist WHERE site_id =" . $id);

  /* Fill our $tags['db'] array with the IDs of the site's existing tags */
  while ($tag = mysqli_fetch_assoc($tags_query)) {
    array_push($tags['db'], $tag['tag_id']);
  }

  /*
  * Compare the $tags['new'] and $tags['db'] arrays, removing duplicates
  * Because we'll be altering the arrays, we store the original length in a variable so our loop won't throw errors
  */
  $db_length = count($tags['db']);
  $new_length = count($tags['new']);

  for ($n=0; $n < $new_length; $n++) {
    for ($d=0; $d < $db_length; $d++) :
      if (intval($tags['new'][$n]) === intval($tags['db'][$d])) {
        unset($tags['new'][$n]);
        unset($tags['db'][$d]);
      }
    endfor;
  }

  /* Fix array indices so we can use for loops below */
  $tags['new'] = array_values($tags['new']);
  $tags['db'] = array_values($tags['db']);

  /* Add newly checked tags */
  for ($i=0; $i < count($tags['new']); $i++) {
    $stmt = $con->prepare("INSERT INTO taglist (tag_id, site_id) VALUES (?,?)");
    $stmt->bind_param("ss", $tags['new'][$i], $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
  }

  /* Remove exisitng tags that were unchecked */
  for ($i=0; $i < count($tags['db']); $i++) {
    $stmt = $con->prepare("DELETE FROM taglist WHERE tag_id = ? AND site_id = ?");
    $stmt->bind_param("ss", $tags['db'][$i], $id);
    $stmt->execute();
    $stmt->close();
  }

  /* This will show up in the browser console for debugging purposes */
  echo json_encode(array(
    'db' =>$tags['db'],
    'new' =>$tags['new'],
  ));

  generateJSON($con);
}

/* Deletions */
if (isset($_POST['del'])) {
  $id = $_POST['id'];
  $stmt = $con->prepare("DELETE FROM websites WHERE id = ?");
  $stmt->bind_param("s", $id);
  $stmt->execute();
  $stmt->close();

  generateJSON($con);
}

/* This generates the JSON file */
function generateJSON($con) {
  $rows = array();
  $sql = ("SELECT id, title, url, descr FROM websites WHERE pending = 0");
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

/* Link Submissions */
if (isset($_POST['submit'])) {
  $honeypot = $_POST['honeypot'];
  $botField = $_POST['botField'];

  if(!empty($honeypot)) {
    echo "honeypot filled";
    return;
  }

  if ($botField !== "website") {
    echo "did not pass bot field";
    return;
  }

  echo "success";
  $title = $_POST['titleInput'];
  $url = $_POST['urlInput'];
  $descr = $_POST['descrInput'];
  //$cat = $_POST['categories'];
  $tags = array(
    'db'  => array(), // store the tags from the database
    'new' => array(), // store the tags from the POST request
  );

  /* Insert the item into the database; because tags are stored in a separate table, adding them will happen later */
  $stmt = $con->prepare("INSERT INTO websites(title, url, descr) VALUES (?,?,?)");
  $stmt->bind_param("sss", $title, $url, $descr);
  $stmt->execute();
  $stmt->close();

  /*
  * `submit-a-link.php` sends each checkbox individually, so we need to capture those in a different way.
  * We can make them the same later.
  */
  $get_all_tags = mysqli_query($con, "SELECT * FROM tags ORDER BY tags.name ASC");
  while ($single_tag = mysqli_fetch_assoc($get_all_tags)) :
    $tag_name = $single_tag['name'];

    /* If the tag was checked, the name of the tag will be set in the POST request and we can add its value to our $tags['new'] array */
    if (isset($_POST[$tag_name])) {
      array_push($tags['new'], strval($_POST[$tag_name]));
    }
  endwhile;
  unset($tag_name);

/* Get ID of newly entered item */
$stmt = $con->prepare("SELECT id FROM websites ORDER BY id DESC LIMIT 1");
$stmt->execute();
$result = $stmt->get_result();

$stmt->close();

$id = $result->fetch_assoc()['id'];

/* Add tags for new item */
for ($i=0; $i < count($tags['new']); $i++) {
  $stmt = $con->prepare("INSERT INTO taglist (tag_id, site_id) VALUES (?,?)");
  $stmt->bind_param("is", $tags['new'][$i], $id);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
}

header('location: submit-a-link.php');
}

/* Link Approvals */
if (isset($_POST['approved'])) {
  $id = $_POST['id'];
  $stmt = $con->prepare("UPDATE websites SET pending = 0 WHERE id = ?");
  $stmt->bind_param("s", $id);
  $stmt->execute();
  $stmt->close();

  generateJSON($con);
}

