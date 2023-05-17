<?php

session_start();

require_once 'db.php';

if (!empty($_SESSION['users_id'])){

    $userQuery=$db->prepare('SELECT users_id FROM users WHERE users_id=:id LIMIT 1;');
    $userQuery->execute([
        ':id'=>$_SESSION['users_id']
    ]);

    if ($userQuery->rowCount() != 1){
        unset($_SESSION['users_id']);
        unset($_SESSION['email']);
        header('Location: index.php');
        exit();
    }
}