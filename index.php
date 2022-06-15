<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Yesterlinks Directory</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
  <script type="text/javascript" src="assets/js/jquery.tablesorter.min.js"></script>
  <link rel="stylesheet" href="assets/css/style.css?v=2022-04-30">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
<script data-goatcounter="https://yesterlinks.goatcounter.com/count"
        async src="//gc.zgo.at/count.js"></script>
</head>
<body>
  <?php 
  error_reporting(E_ERROR | E_WARNING | E_PARSE);
  if ($_SERVER['HTTP_HOST'] !== "links.yesterweb.org") { ?>
  <p class="banner">This is a copy of <a href="https://github.com/sadgrlonline/yesterlinks/">sadgrlonline's repository</a>. The most updated version of this project (with even more links!) lives at <a href="https://links.yesterweb.org">links.yesterweb.org</a>. Have fun!</p>
<?php } ?>
  <div class="container">
<?php
  include "config.php";
  include "submit.php";

  $sql = "SELECT COUNT(*) FROM websites WHERE pending = 0";
  $qry = mysqli_query($con, $sql);
  $totalCount = mysqli_fetch_assoc($qry)['COUNT(*)'];
  $voter_id = hash('sha3-256', $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . "yw-links");

  /* Remove expired votes */
  remove_expired_votes($con, "$decay ago");
  ?>

    <?php include 'navigation.php'; 
    ?>
      <section class="display-flex">
        <div>
          <h1>Yesterlinks</h1>
          <p>Remember when the internet felt exciting and mysterious?</p>
<p><img src="https://yesterweb.org/img/buttons/yesterlinks-button.png"></p>
          <p>Here are <strong><?php echo $totalCount; ?></strong> links!</p>
          <details>
            <summary>What is this?</summary>
            <p>This is a user-curated directory of interesting off-the-beaten path websites. Use the checkboxes to filter by category, or click the table headings to sort by certain columns. Every time this page reloads, the order of websites are shuffled.</p>
            <p>You can use the "Surf" button to load a webpage at random, or add/drag this <a href="surf.php">Surf</a> link to your bookmarks bar and you can click it to surf a random page.</p>
            <p>Want to contribute? Check out the <a href="https://github.com/sadgrlonline/yesterlinks" target="_blank">Github</a>!</p>
            <hr>
            <p>Recommendations will decay over time, and they disappear completely after <?php echo $decay; ?>.</p>
          </details>
          <details>
            <summary>Changelog</summary>
            <ul>
              <li><strong>6/15/22:</strong> Removed categories & category filtering, as they were too broad. In place of category filtering, there is now a dropdown to view all tags and choose one to sort.</li>
              <li><strong>5/23/22:</strong> Added a 'report' feature to report websites that are down or otherwise shouldn't be listed.</li>
              <li><strong>5/02/22:</strong> Added a recommendation system which decays votes over the course of 30 days.</li>
              <li><strong>3/16/22:</strong> Added a tagging system for the links.</li>
            </ul>
            <p>View the <a href="https://github.com/sadgrlonline/yesterlinks" target="_blank">GitHub repo</a> to contribute.</p>
          </details>
        </div>
        <div class="surf">
          <a href="surf.php" id="surf" target="_blank" aria-label="Random site (opens in a new tab)">Random</a>
          <p>This will open a site at random in a new tab.</p>
          <p>You can drag it to your bookmark bar for easy access!</p>
        </div>
      </section>
      <main>
      <div class="display-flex filter-search">
        <div id="filters">
          <?php 

          $sql = "SELECT COUNT(id) FROM tags";
          $qry = mysqli_query($con, $sql);
          $totalTags = mysqli_fetch_assoc($qry)['COUNT(id)'];

          ?>
        <details>
            <summary class="filter-button">
              <i class="fa fa-filter fa-1x"></i> <span class="intro">View Tags</a>
            </summary>
            <p>Click on a tag to filter the list.</p>
            <!-- <p>There are <?php echo $totalTags; ?> tags.</p> -->
            <div class="item-tags">
            <?php 
            $stmt = $con->prepare("SELECT * FROM tags ORDER BY name ASC");
            $stmt->execute();
              $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
              $name = $row["name"];
              $id = $row["id"];
              echo "<button data-tag-id=" . $id . ">" . $name . "</button>";
            }
            $stmt->close();
          
            ?>
            </div>
  </details>
            </div>
            <div id="search">
              <label for="search-input">Search: </label>
              <input type="text" id="search-input">
            </div>
          </div>
      <div class="display-flex justify-content-space-between">
      <div class="order-added-sorting">
        <button type="button" value="a">Sort by oldest</button>
        <button type="button" value="d">Sort by most recent</button>
      </div>
      <div class="rank-sorting">
        <button type="button" value="d">Show highest recommended</button>
      </div>
    </div>
      <div class="table-wrapper">
        <p class="tag-filter">You are currently filtering by the <b id="current-tag">[TAG_NAME]</b> tag. <button id="show-all">Show All</button></p>
      <table id="directory">
        <thead>
          <th scope="col" class="url">Title <i class="fa fa-sort fa-1x"></i><i class="fa fa-sort-asc fa-1x"></i><i class="fa fa-sort-desc fa-1x"></i></th>
          <th scope="col" class="order-added">Order Added <i class="fa fa-sort fa-1x"></i><i class="fa fa-sort-asc fa-1x"></i><i class="fa fa-sort-desc fa-1x"></i></th>
          <th scope="col" class="descr">Description <i class="fa fa-sort fa-1x"></i><i class="fa fa-sort-asc fa-1x"></i><i class="fa fa-sort-desc fa-1x"></i></th>
          <th scope="col" class="votes sorter-digit">Community <i class="fa fa-sort fa-1x"></i><i class="fa fa-sort-asc fa-1x"></i><i class="fa fa-sort-desc fa-1x"></i></th>
        </thead>
        <tbody>

          <?php
          /* Get sites */
          $stmt = $con->prepare("SELECT * FROM websites WHERE pending = 0 ORDER BY rand()");
          $stmt->execute();
          $result = $stmt->get_result();
          $stmt->close();


          $idarray = [];
          $urlarray = [];
          $catarray = [];

          while ($row = $result->fetch_assoc()) {
            $idarray[] = $row['id'];
            $urlarray[] = $row['url'];
            $id = $row['id'];
            $title = $row['title'];
            $descr = $row['descr'];
            $url = $row['url'];

            /* Get tags */
            $tags_query = "SELECT tag_id, tags.name FROM taglist JOIN tags ON taglist.tag_id = tags.id WHERE site_id =" . $row['id'] . " ORDER BY tags.name ASC";
            $tags_result = mysqli_query($con, $tags_query);


            $tag_html = "";

            while ($single_tag = mysqli_fetch_assoc($tags_result)) {
              $tag_html .= "<button data-tag-id=" . $single_tag['tag_id'] . ">" . $single_tag['name'] . "</button> ";
            }

            /* Get vote count */
            $votes = get_vote_count($con, $id);

            /* Get this visitor's' votes */
            $votes_query = "SELECT * FROM votelist WHERE voter_id = '$voter_id' AND site_id =" .  $row['id'];
            $votes_result = mysqli_query($con, $votes_query); // we'll use this later to set the state of the voting buttons
            ?>

            <tr id="<?php echo $id; ?>">
              <th scope="row" class="url"><a href="<?php echo $url; ?>" target="_blank">
                <?php if ($title === null) {
                  echo "Untitled";
                } else {
                  echo $title;
                } ?>
              </th>
              <td class="order-added"><?php echo $id; ?></td>
              <td class="desc">
                <div class="desc">
                  <?php if ($descr === '') {
                    echo "No description added.";
                  } else {
                    echo strtolower($descr);
                  } ?>
                </div>
                <div class="item-tags">
                  <?php
                  if (empty($tag_html) === false) {
                    echo "<strong>Tags:</strong> $tag_html";
                  } ?>
                </div>
               
              </td>
              <td class="voting text-align-center" data-value="<?php echo $votes; ?>" data-id="<?php echo $id; ?>">
                <p class="voting__amount hide"><?php echo $votes; ?></p>
                <div class="voting__buttons">
                  <?php
                  $pressed_upvote = "false";
                  $pressed_downvote = "false";

                  while ($vote_item = mysqli_fetch_assoc($votes_result)) {
                    if (intval($vote_item['site_id']) === $id) {
                      if ($vote_item['vote'] === '1') { $pressed_upvote = "true"; }
                      // if ($vote_item['vote'] === '0') { $pressed_downvote = "true"; }
                    }
                  }
                  ?>
                  <button type="button" value="recommend" aria-pressed="<?php echo $pressed_upvote; ?>">Recommend</button><br>
                  <a href="report.php?entry=<?php echo $id; ?>"><button id="reportSite" value="report">Report</button></a>
                </div>
              </td>
            </tr>
          <?php } // end while loop for sites ?>
        </tbody>
      </table>
    </div>
</main>
</div>
<style>
#reportSite {
  background-color:transparent;
  color:white;
  font-size:smaller;
  border:none;
  cursor:pointer;
}

</style>
  <script type="text/javascript">
  /* We will use these variables in the linked `scripts.js` file */
  var idArr = <?php echo json_encode($idarray); ?>;
  var urlArr = <?php echo json_encode($urlarray); ?>;
  var catArr = <?php echo json_encode($catarray); ?>;
  </script>
  <script src="assets/js/scripts.js?v=2022-05-11-am" type="text/javascript" charset="utf-8"></script>
</body>
</html>
