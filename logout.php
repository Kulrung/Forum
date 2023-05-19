<?php

require_once 'include/user.php';

if (!empty($_SESSION['users_id'])){
    unset($_SESSION['users_id']);
    unset($_SESSION['email']);
    unset($_SESSION['username']);
    unset($_SESSION['isAdmin']);
}

header('Location: index.php');