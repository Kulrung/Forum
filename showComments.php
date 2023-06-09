<?php

    require_once 'include/user.php';

    include 'include/header.php';
    include 'include/functions.php';
    function printComments($parent_id, $level): void
    {

        global $db;

        if (!isset($_SESSION['users_id'])){
            $_SESSION['users_id'] = null;
        }


        if ($parent_id == null){
            $query = $db->prepare('SELECT comments.comments_id AS comments_id, comments.parent_id AS parent_id, topics.name AS topic_name, users.username AS creator, comments.text AS text, topics.categories_id AS categories_id, categories.name AS category_name, topics.topics_id AS topics_id, comments.updated AS updated, comments.created AS created, comments.comments_id AS comments_id, users.users_id AS creator_id
                                     FROM comments JOIN topics ON comments.topics_id=topics.topics_id JOIN users ON comments.creator_id=users.users_id JOIN categories ON topics.categories_id=categories.categories_id
                                     WHERE comments.topics_id=:topics_id AND comments.parent_id is null
                                     ORDER BY updated ASC');

            $query->execute([
                ':topics_id'=>$_GET['topic']
            ]);
        }else{
            $query = $db->prepare('SELECT comments.comments_id AS comments_id, comments.parent_id AS parent_id, topics.name AS topic_name, users.username AS creator, comments.text AS text, topics.categories_id AS categories_id, categories.name AS category_name, topics.topics_id AS topics_id, comments.updated AS updated, comments.created AS created, comments.comments_id AS comments_id, users.users_id AS creator_id
                                     FROM comments JOIN topics ON comments.topics_id=topics.topics_id JOIN users ON comments.creator_id=users.users_id JOIN categories ON topics.categories_id=categories.categories_id
                                     WHERE comments.topics_id=:topics_id AND comments.parent_id=:parent_id
                                     ORDER BY updated ASC');

            $query->execute([
                ':topics_id'=>$_GET['topic'],
                ':parent_id'=>$parent_id
            ]);
        }


        $comments = $query->fetchAll();

        if (empty($comments) && $level == 0){

            echo '<div class="row">
                    <div class="col-4 mb-3">
                        <a href="comment.php?topic='.$_GET['topic'].'" class="btn btn-primary">Vytvořit nový komentář</a>
                    </div>
                   </div>
                   <div class="container p-5 my-5 border border-3">
                    <h2>Nebyly nalezeny žádné příspěvky.</h2>
                  </div>';
            return;
        }

        $indent =  ($level * 50);

        foreach ($comments as $comment) {
            echo '<div class="p-3 border" style="margin-left: '.$indent.'px">
                        <div class="row">
                            <div class="col-8">
                                <h4>
                                    <a class="text-decoration-none" href="allComments.php?creator='.$comment['creator_id'].'">'.htmlspecialchars($comment['creator']).'</a>
                                </h4>
                                <p>'.htmlspecialchars($comment['text']).'</p>
                                <a href="showTopics.php?category='.$comment['categories_id'].'" class="badge bg-primary">'.htmlspecialchars($comment['category_name']).'</a>
                                <a href="showComments.php?topic='.$comment['topics_id'].'" class="badge bg-secondary">'.htmlspecialchars($comment['topic_name']).'</a>
                                <a href="comment.php?parent_id='.$comment['comments_id'].'&topic='.$_GET['topic'].'" class="badge bg-success">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-reply" viewBox="0 0 16 16">
                                      <path d="M6.598 5.013a.144.144 0 0 1 .202.134V6.3a.5.5 0 0 0 .5.5c.667 0 2.013.005 3.3.822.984.624 1.99 1.76 2.595 3.876-1.02-.983-2.185-1.516-3.205-1.799a8.74 8.74 0 0 0-1.921-.306 7.404 7.404 0 0 0-.798.008h-.013l-.005.001h-.001L7.3 9.9l-.05-.498a.5.5 0 0 0-.45.498v1.153c0 .108-.11.176-.202.134L2.614 8.254a.503.503 0 0 0-.042-.028.147.147 0 0 1 0-.252.499.499 0 0 0 .042-.028l3.984-2.933zM7.8 10.386c.068 0 .143.003.223.006.434.02 1.034.086 1.7.271 1.326.368 2.896 1.202 3.94 3.08a.5.5 0 0 0 .933-.305c-.464-3.71-1.886-5.662-3.46-6.66-1.245-.79-2.527-.942-3.336-.971v-.66a1.144 1.144 0 0 0-1.767-.96l-3.994 2.94a1.147 1.147 0 0 0 0 1.946l3.994 2.94a1.144 1.144 0 0 0 1.767-.96v-.667z"/>
                                    </svg>
                                    Odpovědět
                                </a>';

?>
                                <span>
                                    <i <?php
                                    if(userLikesDislikes($comment['comments_id'],$_SESSION['users_id'],1,$db)): ?>
                                        class="fa fa-thumbs-up like-btn"
                                    <?php else: ?>
                                        class="fa fa-thumbs-o-up like-btn"
                                    <?php endif ?>
                                        data-id="<?php echo $comment['comments_id'] ?>">
                                    </i>
                                    <span class="likes"><?php echo getLikesDislikes($comment['comments_id'],1,$db); ?></span>

                                    <i <?php if (userLikesDislikes($comment['comments_id'],$_SESSION['users_id'],2,$db)): ?>
                                          class="fa fa-thumbs-down dislike-btn"
                                      <?php else: ?>
                                          class="fa fa-thumbs-o-down dislike-btn"
                                      <?php endif ?>
                                      data-id="<?php echo $comment['comments_id'] ?>">
                                    </i>
                                    <span class="dislikes"><?php echo getLikesDislikes($comment['comments_id'],2,$db); ?></span>
                                </span>
<?php
            echo '            </div>
                            <div class="col-4">
                                <p class="text-muted">Aktualizováno: '.htmlspecialchars(date('d.m.Y H:i',strtotime($comment['updated']))).'</p>
                                <p class="text-muted">Vytvořeno: '.htmlspecialchars(date('d.m.Y H:i',strtotime($comment['created']))).'</p>';

            if (isset($_SESSION['users_id'])){
                if ($comment['creator_id'] == $_SESSION['users_id']){
                    echo '<a href="comment.php?id='.$comment['comments_id'].'" class="btn btn-primary mr-2">Upravit</a> ';
                }
                if ($comment['creator_id'] == $_SESSION['users_id'] || $_SESSION['isAdmin']){
                    echo '<a href="commentRemove.php?id='.$comment['comments_id'].'" class="btn btn-danger">Smazat</a>';
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
        printComments(null, 0);
    }
    else{
        echo '<div class="container p-5 my-5 border border-3">
                    <h2>Takové téma neexistuje..</h2>
                  </div>';
    }


include 'include/footer.php';
