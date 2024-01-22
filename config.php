<?php
// Common settings
$pageDir = 'pages'; // Folder path to store page files
$pageExtention = '.html'; // File extension
$list_excerpt_length = 100;

// Database configuration
define('DB_HOST', 'MySQL_Database_Host'); 
define('DB_USERNAME', 'MySQL_Database_Username'); 
define('DB_PASSWORD', 'MySQL_Database_Password'); 
define('DB_NAME', 'MySQL_Database_Name'); 

// Start session
if(!session_id()){
   session_start();
}