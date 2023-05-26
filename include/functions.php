<?php

function userLikesDislikes($commentsId,$creatorId,$action,$db)
{
    $query = $db->prepare('SELECT COUNT(*) FROM likes WHERE users_id=:users_id AND comments_id=:comments_id AND like_dislike=:action;');
    $query->execute([
        ':users_id'=>$creatorId,
        ':comments_id'=>$commentsId,
        ':action'=>$action
    ]);
    $count = $query->fetchColumn();
    if ($count > 0) {
        return true;
    }else{
        return false;
    }
}
function getLikesDislikes($commentsId,$action,$db)
{
    $query = $db->prepare('SELECT COUNT(*) FROM likes WHERE comments_id=:comments_id AND like_dislike=:action;');
    $query->execute([
        ':comments_id'=>$commentsId,
        ':action'=>$action
    ]);
    $count = $query->fetchColumn();
    return $count;
}
function insert_vote($creatorId,$commentsId,$action,$db){

    $query = $db->prepare('INSERT INTO likes(users_id, comments_id, like_dislike) 
             VALUES (:users_id, :comments_id, :action) 
             ON DUPLICATE KEY UPDATE like_dislike=:action;');
    $query->execute([
        ':users_id'=>$creatorId,
        ':comments_id'=>$commentsId,
        ':action'=>$action
    ]);

}
function delete_vote($creatorId,$commentsId,$db){

    $query = $db->prepare('DELETE FROM likes WHERE users_id=:users_id AND comments_id=:comments_id;');
    $query->execute([
        ':users_id'=>$creatorId,
        ':comments_id'=>$commentsId,
    ]);
}
function getRating($commentsId,$db)
{
    $likes=getLikesDislikes($commentsId,1,$db);
    $dislikes=getLikesDislikes($commentsId,2,$db);
    $rating = [
        'likes' => $likes,
        'dislikes' => $dislikes
    ];
    return json_encode($rating);
}