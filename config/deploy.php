<?php
date_default_timezone_set("Europe/Berlin");
$time = date("Y-m-d_H-i-s");
if (is_dir("/release/")) {
    echo "Removing directory ./release/";
    system("rmdir ./release/");
}

echo "Creating directory ./release/\n";
system("mkdir ./release/");

echo "Unzipping $argv[1]\n";
system("unzip $argv[1] -d ./release/");

if (is_dir("./htdocs/")){
    echo "Renaming ./htdocs/ to ./htdocs_$time\n";
    system("mv ./htdocs/ ./htdocs_$time");
}

echo "Renaming ./release/ to ./htdocs/\n";
system("mv ./release/ ./htdocs/");

echo "Removing zipfile $argv[1]\n";
system("rm $argv[1] -rf");

if (!is_dir("./htdocs/tmp")) {
    echo "Creating /tmp directory";
    system("mkdir ./htdocs/tmp");
}

if (!is_dir("./htdocs/tmp/logs")) {
    echo "Creating /logs directory";
    system("mkdir ./htdocs/tmp/logs");
}

if (!is_dir("./htdocs/tmp/cache")) {
    echo "Creating /cache directory";
    system("mkdir ./htdocs/tmp/cache");
}

echo "Updating directory permissions to 775\n";
system("chmod -R 775 ./htdocs/tmp/");

echo "Updating permissions";
system("chmod 775 ./htdocs/vendor/bin/phinx && chmod -R 775 ./htdocs/vendor/robmorgan/");

echo "Migrating database";
system("cd htdocs/config/ && ../vendor/bin/phinx migrate");
system("cd ..");

echo "Deleting old Backups ...";
system("php clean-up.php 31536000");

echo "\n";
echo "--------------------------------------------------------\n";
echo "Server deployment done\n";
echo "--------------------------------------------------------\n";