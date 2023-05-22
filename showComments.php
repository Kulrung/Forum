<?php

    require_once 'include/user.php';

    include 'include/header.php';

    function printComments($parent_id, $level): void
    {

        global $db;
        $query = $db->prepare('SELECT comments.comments_id AS comments_id, comments.parent_id AS parent_id, topics.name AS topic_name, users.username AS creator, comments.text AS text, topics.categories_id AS categories_id, categories.name AS category_name, topics.topics_id AS topics_id, comments.updated AS updated, comments.created AS created, comments.comments_id AS comments_id
                                     FROM comments JOIN topics ON comments.topics_id=topics.topics_id JOIN users ON comments.creator_id=users.users_id JOIN categories ON topics.categories_id=categories.categories_id
                                     WHERE comments.topics_id=:topics_id AND comments.parent_id=:parent_id
                                     ORDER BY updated DESC');
        $query->execute([
            ':topics_id'=>$_GET['topic'],
            ':parent_id'=>$parent_id
        ]);

        $comments = $query->fetchAll();

        $indent = 'ml-' . ($level * 3);;

        foreach ($comments as $comment) {
            echo '<div class="container p-3 my-3 '.$indent.' border border-3">
                        <div class="row">
                            <div class="col-8">
                                <h4>'.htmlspecialchars($comment['creator']).'</h4>
                                <p>'.htmlspecialchars($comment['text']).'</p>
                                <a href="showTopics.php?category='.$comment['categories_id'].'" class="badge bg-primary">'.$comment['category_name'].'</a>
                                <a href="showComments.php?topic='.$comment['topics_id'].'" class="badge bg-secondary">'.$comment['topic_name'].'</a>
                                <a href="">Odpovědět</a>
                            </div>
                            <div class="col-4">
                                <p class="text-muted">Aktualizováno: '.htmlspecialchars(date('d.m.Y H:i',strtotime($comment['updated']))).'</p>
                                <p class="text-muted">Vytvořeno: '.htmlspecialchars(date('d.m.Y H:i',strtotime($comment['created']))).'</p>';

            if (isset($_SESSION['users_id'])){
                if ($comment['creator'] == $_SESSION['users_id'] || $_SESSION['isAdmin']){
                    echo '<a href="topic.php?id='.$comment['comments_id'].'" class="btn btn-primary">Upravit</a>
                      <a href="topicRemove.php'.$comment['comments_id'].'" class="btn btn-danger">Smazat</a>';
                }
            }
            echo'
                            </div>
                        </div>
                </div>';

            $level++;
            printComments($comment['comments_id'],$level);
        }
    }


    if (!empty($_GET['topic'])){

        $query = $db->prepare('SELECT comments.comments_id AS comments_id, comments.parent_id AS parent_id, topics.name AS topic_name, users.username AS creator, comments.text AS text, topics.categories_id AS categories_id, categories.name AS category_name, topics.topics_id AS topics_id, comments.updated AS updated, comments.created AS created, comments.comments_id AS comments_id
                                     FROM comments JOIN topics ON comments.topics_id=topics.topics_id JOIN users ON comments.creator_id=users.users_id JOIN categories ON topics.categories_id=categories.categories_id
                                     WHERE comments.topics_id=:topics_id AND comments.parent_id IS NULL
                                     ORDER BY updated DESC');
        $query->execute([
            ':topics_id'=>$_GET['topic'],
        ]);

        $comments = $query->fetchAll();


        if (!empty($comments)){
            foreach ($comments as $comment){
                echo '<div class="container p-3 my-3 border border-3">
                        <div class="row">
                            <div class="col-8">
                                <h4>'.htmlspecialchars($comment['creator']).'</h4>
                                <p>'.htmlspecialchars($comment['text']).'</p>
                                <a href="showTopics.php?category='.$comment['categories_id'].'" class="badge bg-primary">'.$comment['category_name'].'</a>
                                <a href="showComments.php?topic='.$comment['topics_id'].'" class="badge bg-secondary">'.$comment['topic_name'].'</a>
                                <a href="">Odpovědět</a>
                            </div>
                            <div class="col-4">
                                <p class="text-muted">Aktualizováno: '.htmlspecialchars(date('d.m.Y H:i',strtotime($comment['updated']))).'</p>
                                <p class="text-muted">Vytvořeno: '.htmlspecialchars(date('d.m.Y H:i',strtotime($comment['created']))).'</p>';

                if (isset($_SESSION['users_id'])){
                    if ($comment['creator'] == $_SESSION['users_id'] || $_SESSION['isAdmin']){
                        echo '<a href="topic.php?id='.$comment['comments_id'].'" class="btn btn-primary">Upravit</a>
                      <a href="topicRemove.php'.$comment['comments_id'].'" class="btn btn-danger">Smazat</a>';
                    }
                }
                echo'
                            </div>
                        </div>
                </div>';
                printComments($comment['comments_id'], 1);


            }
        }
        else{
            echo '<div class="container p-5 my-5 border border-3">
                    <h2>Nebyly nalezeny žádné příspěvky.</h2>
                  </div>';
        }
    }
    else{
        echo '<div class="container p-5 my-5 border border-3">
                    <h2>Takové téma neexistuje..</h2>
                  </div>';
    }



include 'include/footer.php';
