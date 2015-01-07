<?php

// Make sure we're running on the command line. Exit if not.
if(!defined('STDIN') ) {
  echo "<h2>This PHP script can only be run from the command line.</h2>";
  exit(0);
}


//
// prompt
//
// Provides a command line prompt to get user input
// @param $_default The default parameter. Returned if the user doesn't override
//
function prompt($_default = false) {
    $_handle = fopen ("php://stdin","r");
    $_line = fgets($_handle);
    $_line = trim($_line);
    //if the user hits enter, return the default value
    if ($_line == "") {
        return $_default;
    } else {
        return $_line;
    }
}


/////////////////////////////////
// GO
//
echo "This script will download the given mysql database, then do a find/replace with the local and then destination domain\n";

date_default_timezone_set("America/New_York");
$timestamp = date('Ymd-His');

// db name
echo "What is the name of the mysql database?\n";
$mysql_db = prompt();
while(!$mysql_db){
    echo "We need a database.\n";
    $mysql_db = prompt();
}

// db user
echo "We also need the username...\n";
$mysql_username = prompt();
while(!$mysql_username){
    echo "We need a username.\n";
    $mysql_username = prompt();
}

// db password
echo "and that users password...\n";
$mysql_password = prompt();
while(!$mysql_password){
    echo "We need a password.\n";
    $mysql_password = prompt();
}

$mysql_dump_name = "$mysql_db-$timestamp.sql";

exec("mysqldump -u $mysql_username -p$mysql_password $mysql_db > $mysql_dump_name", $output, $success);

if($success != 0){
    unlink($mysql_dump_name);
    die("Something went wrong.");
} else {
    echo "Dumped\n";
}

echo "Now we're going to do a search and replace on that database, so the site works when you import it to another server.\n";
echo "Make sure that this stuff is correct, since there's no good way to error check this.\n";

// local domain
echo "What is the local site domain?\n";
$local_domain = prompt();
while(!$local_domain){
    echo "We need a local domain.\n";
    $local_domain = prompt();
}

// remote domain
echo "What is the domain of the site to which you're importing this?\n";
$remote_domain = prompt();
while(!$remote_domain){
    echo "We need a remote domain.\n";
    $remote_domain = prompt();
}

$file_contents = file_get_contents($mysql_dump_name);

$file_contents = str_replace($local_domain, $remote_domain, $file_contents);

file_put_contents($mysql_dump_name, $file_contents);

exit("Domains updated\n");



