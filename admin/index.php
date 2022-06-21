<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['username'])) {
  header("location: ../login");
} ?>
<!DOCTYPE html>
<html>
<head>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
  <script type="text/javascript" src="../assets/js/jquery.tablesorter.min.js"></script>
  <link rel="stylesheet" href="../assets/css/style.css?v=2022-04-30">
  <link rel="stylesheet" href="../assets/css/admin-styles.css?v=2022-04-30">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
  <title>Yesterlinks: Admin</title>
</head>

<body class="admin">
  <?php
  /* Display this banner if this instance of the repo is not the main one */
  if ($_SERVER['HTTP_HOST'] !== "links.yesterweb.org") { ?>
  <p class="banner">You are editing a copy of <a href="https://github.com/sadgrlonline/yesterlinks/">sadgrlonline's repository</a>. The most updated version of this project lives at <a href="https://links.yesterweb.org">links.yesterweb.org</a>.</p>
  <?php } ?>
  <div class="container">
    <?php include '../navigation.php' ?>
    <h1>Yesterlinks: Admin</h1>
    <p>hi <?php if (isset($_SESSION['username'])) { echo $_SESSION['username']; } ?>!</p>
    <div id="search"><label>Search: </label><input type="text" id="searchInput">
          </div>
    <div class="table-wrapper">
    <table id="directory">
      <div class="row">
        <thead>
          <tr>
            <tr>
              <th class="title">Title <i class="fa fa-sort fa-1x"></i><i class="fa fa-sort-asc fa-1x"></i><i class="fa fa-sort-desc fa-1x"></i></th>
              <th class="urlAdmin title">URL <i class="fa fa-sort fa-1x"></i><i class="fa fa-sort-asc fa-1x"></i><i class="fa fa-sort-desc fa-1x"></i></th>
              <th class="descr">Description <i class="fa fa-sort fa-1x"></i><i class="fa fa-sort-asc fa-1x"></i><i class="fa fa-sort-desc fa-1x"></i></th>
              <th class="cat title">Tags <i class="fa fa-sort fa-1x"></i><i class="fa fa-sort-asc fa-1x"></i><i class="fa fa-sort-desc fa-1x"></i></th>
              <th class="cat title" colspan="3">Options</th>
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

        while ($row = $result->fetch_assoc()) {
          $idarray[] = $row['id'];
          $urlarray[] = $row['url'];
          $id = $row['id'];
          $title = $row['title'];
          $descr = $row['descr'];
          $url = $row['url'];
          $is_pending = $row['pending'];

          $tags_query = "SELECT tag_id, tags.name FROM taglist JOIN tags ON taglist.tag_id = tags.id WHERE site_id =" . $row['id'] . " ORDER BY tags.name ASC";
          $tags_result = mysqli_query($con, $tags_query);
          $site_tag_ids = array(); // array for storing this site's tag IDs

          $tag_html = "";

          while ($single_tag = mysqli_fetch_assoc($tags_result)) {
            array_push($site_tag_ids, $single_tag['tag_id']); // store each tag's ID; we'll use this to check the appropriate checkboxes below
            $tag_html .= "<li data-tag-id=" . $single_tag['tag_id'] . ">" . $single_tag['name'] . "</li>";
          } ?>

          <tr class="row" id="<?php echo $id; ?>">
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
              <td class="tags">
                <ul>
                  <?php if (empty($tag_html) === false) {
                    echo $tag_html;
                  } ?>
                </ul>
                <fieldset class="tags__edit hide">
                  <legend>Tag List</legend>
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
              <td class="text-align-center">
                <button type="button" class="edit" data-id="<?php echo $id; ?>" aria-pressed="false">edit</button>
                <button type="button" class="save" data-id="<?php echo $id; ?>">save</button>
              </td>
              <td class="text-align-center"><button type="button" class="del" data-id="<?php echo $id; ?>">remove</button></td>
              <?php
              /* If the website is pending */
              if ($is_pending === 1) {
                $status = "pending";
                $is_pressed = "false";
              }

              /* If the website is approved */
              if ($is_pending === 0) {
                $status = "approved";
                $is_pressed = "true";
              }
              ?>
              <td class="approve text-align-center">
                <label for="approval-<?php echo $id; ?>">Approved</label>
                <input id="approval-<?php echo $id; ?>" class="approve" type="checkbox" data-id="<?php echo $id; ?>" value="<?php
              if ($is_pending === 1) { echo 0; }
              if ($is_pending === 0) { echo 1; }
              ?>"
              <?php
              if ($is_pending === 0) { echo "checked"; }
              ?>>
            </td>
            </tr>
          <?php } // end while loop for sites ?>
      </tbody>
    </table>
  </div>
    </div>
    <script type="text/javascript">
    /* We will use these variables in the linked `scripts.js` file */
    var idArr = <?php echo json_encode($idarray); ?>;
    var urlArr = <?php echo json_encode($urlarray); ?>;
    </script>

    <script src="../assets/js/admin-scripts.js?v=2022-04-30" type="text/javascript" charset="utf-8">
    </script>

    <style>
      .container {
        max-width:1500px !important;
      }
    
      </style>

</body>
</html>
