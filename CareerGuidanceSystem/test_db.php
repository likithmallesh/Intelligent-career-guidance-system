<?php
// test_db.php
require_once 'config/config.php'; // Include your config file

if ($conn) {
    echo "<h1>Database connection successful!</h1>";

    // Optional: Fetch and display some skills to confirm data retrieval
    $sql = "SELECT * FROM skills";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        echo "<h2>Skills:</h2><ul>";
        while($row = mysqli_fetch_assoc($result)) {
            echo "<li>" . $row["name"] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No skills found.</p>";
    }

    mysqli_close($conn); // Close the connection
} else {
    echo "<h1>Database connection failed!</h1>";
    echo "<p>Error: " . mysqli_connect_error() . "</p>";
}
?>
