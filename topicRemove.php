<?php

require_once 'include/user.php';

if (!empty($_GET['id'])){
    $query = $db->prepare('DELETE FROM topics WHERE topics_id=:id');
    $query->execute([
        ":id"=>$_GET['id']
    ]);
}

header('Location: index.php');