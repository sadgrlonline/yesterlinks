<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
</head>
<?php
    include 'config.php';

    $sql = "SELECT DISTINCT category FROM websites";
    $catArray = [];
    $result = mysqli_query($con, $sql);
    while ($row = $result->fetch_assoc()) {
        $catArray[] = $row['category'];
    }
?>
<body>
    <div id="container">
        <h1>Submit a Link</h1>
        <form method="POST" action="submit.php">
            <label>Webpage Title:</label> <input type="text" name="titleInput" id="titleInput"><br>
            <label>Webpage URL:</label> <input type="url" name="urlInput" id="urlInput"><br>
            <label>Webpage Description:</label><textarea name="descrInput" id="descrInput"></textarea><br>
            <label>Webpage Category:</label> <select name="categories" id="categories"></select>
            <br><br>

            <input type="text" id="honeypot" name="honeypot">
            <input type="submit" name="submit" value="Submit">
        </form>
    </div>
</body>
<script>
    // this part grabs the array of unique categories from the PHP and passes it to JS
    var catArr = <?php echo json_encode($catArray); ?>;

    // this removes null values from the array
    const results = catArr.filter(element => {
    return element !== null;
    });

    // this dynamically creates a selectbox for all available categories
    for (let i = 0; i < results.length; i++) {
        $('#categories').append('<option value="' + results[i] + '" name="categories">' + results[i] + '</option>');
    }
    
    </script>
</html>