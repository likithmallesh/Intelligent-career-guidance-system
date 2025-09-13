<?php
// config/config.php

// Database configuration for MySQL on port 3307
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // Default XAMPP username
define('DB_PASSWORD', '');     // Default XAMPP password (leave empty if no password is set)
define('DB_NAME', 'career_guidance_db');
define('DB_PORT', 3307);       // Crucial: Specify port 3307

// Attempt to establish a database connection
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);

// Check connection
if($conn === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>
