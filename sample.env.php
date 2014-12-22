<?php
/*Sample .env.php Please enter correct valid for production and change file name to .env.php*/

return array(

    //MySQL Database Info
    'mysql_host' => 'localhost',
    'mysql_database' => 'minestack',
    'mysql_username' => 'root',
    'mysql_password' => 'root',

    //Mongo Database Info
    'mongodb_hosts' => array('server1', 'server2'),
    'mongodb_username' => 'root',
    'mongodb_password' => 'root',
    'mongodb_database' => 'minestack',
    'mongodb_replicaSetName' => 'rs1',

    //Secure 32 character key for encryption
    'secure_key' => 'abcdefghijklmnopqrstuvwxyz012345',

    //URL that the application will be located at
    'url' => 'http://somewebsite.com',

);