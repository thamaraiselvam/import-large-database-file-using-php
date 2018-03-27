# import-large-database-file-using-php
This script will import large database file in multiple requests.

Tested : PHPMyAdmin Export, Adminer Export, InfiniteWP Database Backup file, ManageWP Database Backup file and WP Time Capsule Database Backup file

Steps to Run the secript.

1.Clone the repo 

`git clone https://github.com/thamaraiselvam/import-large-database-file-using-php.git`

OR

Download here - <a href="https://github.com/thamaraiselvam/import-database-file-using-php/archive/master.zip">import-database-file-using-php</a>

2.Open the `import.php` and replace following varialbles with your Database credentials

`$file = 'file.sql';`

`$host = 'localhost';`

`$username = 'USERNAME';`

`$password = 'PASSWORD';`

`$database = 'DB_NAME';'`

3.Run the `index.php` and hit the start button. 

That's it. your database is imported :)

Note: During this process multiple request sent from browser (Ajax) so do not close the browser until progres gets completed
