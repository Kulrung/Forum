<?php

    require_once 'include/user.php';

    include 'include/header.php';

    $comments = $db->prepare('SELECT creator.username AS creator, comments.comments_id, comments.text AS text, topics.name AS topic_name, categories.name AS category_name, users.username AS username, comments.created AS created, comments.updated AS updated
                                    FROM users JOIN likes ON users.users_id=likes.users_id JOIN comments ON likes.comments_id=comments.comments_id JOIN users AS creator ON creator.users_id=comments.creator_id JOIN topics ON topics.topics_id=comments.topics_id JOIN categories ON categories.categories_id=topics.categories_id
                                    WHERE users.users_id=:users_id;');
    $comments->execute([
        ':users_id'=> $_SESSION['users_id']
    ]);

    if (!empty($comments)){
        foreach ($comments as $comment) {
            echo '<div class="container p-3 my-3 border border-3">
                            <div class="row">
                                <div class="col-8">
                                    <h2>'.htmlspecialchars($comment['creator']).'</h2>
                                    <p>'.htmlspecialchars($comment['text']).'</p>
                                    <span class="badge bg-primary">'.$comment['category_name'].'</span>
                                    <span class="badge bg-secondary">'.$comment['topic_name'].'</span>
                                </div>
                                <div class="col-4">
                                    <p class="text-muted">Aktualizováno: '.htmlspecialchars(date('d.m.Y H:i',strtotime($comment['updated']))).'</p>
                                    <p class="text-muted">Vytvořeno: '.htmlspecialchars(date('d.m.Y H:i',strtotime($comment['created']))).'</p>
                                    <a href="comment.php='.$comment['comments_id'].'" class="btn btn-primary">Upravit</a>
                                    <a href="deleteComment.php?id='.$comment['comments_id'].'" class="btn btn-danger">Smazat</a>
                                </div>
                            </div>
                      </div>';
        }
    }
    else{
        echo '<div class="container p-5 my-5 border border-3">
                    <h2>Nebyly nalezeny žádné kategorie.</h2>
                  </div>';
    }

    include 'include/footer.php';

