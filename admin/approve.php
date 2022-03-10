<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
        <script type="text/javascript" src="scripts/jquery.tablesorter.min.js"></script>
        <link rel="stylesheet" href="../style.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
      
    </head>
    <body>
        <div class="container">
        <?php include 'navigation.php' ?>
            <div class="wrapper">
                <h1>Yesterlinks</h1>
                <table id="directory">
                    <thead> 
                            <th class="url title">Title <i class="fa fa-sort fa-1x"></i></th>
                            <th class="descr">Description <i class="fa fa-sort fa-1x"></i></th>
                            <th class="cat title">Category <i class="fa fa-sort fa-1x"></i></th>
                            <th class="cat title">Approve <i class="fa fa-sort fa-1x"></i></th>
                    </thead>
                
                    <tbody>
<?php
include '../config.php';
                $stmt = $con->prepare("SELECT * FROM websites WHERE pending = 1 ORDER BY id DESC");
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
                            echo '<td class="approveLink" data-attr="' . $id . '"><a href="#" class="approve">Apprv</a></td>';
                            echo '</tr>';
                        }  ?>
                    
                        <script>

                        $('.approve').on("click", function() {
                            var id = $(this).parent().attr('data-attr');
                            var approved = 1;
                            console.log(id);
                            $(this).parent().parent().hide();
                            $.ajax({
                                type: 'post',
                                data: {'approved':approved, 'id':id},
                                url: '../submit.php',
                                success: function(response) {
                                
                                    
                                }
                            });
                        })
                        </script>