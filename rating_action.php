<?php

global $db;
require_once("include/user.php");
include("include/functions.php");


if(isset($_SESSION['users_id'])){
    $users_id=$_SESSION['users_id'];

    if(isset($_POST['action'])) {
        $comments_id = $_POST['comments_id'];
        $action = $_POST['action'];
        switch ($action) {
            case 'like':
                $vote_action= 1;
                insert_vote($users_id,$comments_id,$vote_action,$db);
                break;
            case 'dislike':
                $vote_action= 2;
                insert_vote($users_id,$comments_id,$vote_action,$db);
                break;
            case 'undislike':
            case 'unlike':
                delete_vote($users_id,$comments_id,$db);
                break;
            default:
        }
        // execute query to effect changes in the database ...
        echo getRating($comments_id,$db);
        exit(0);
    }
}
else{
    exit('Pro likování a dislikování musíte být přihlášeni');
}




