<?php

    require_once 'include/user.php';

    include 'include/header.php';

    function printComments($parent_id, $level): void
    {

        global $db;

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
            echo '<div class="container p-5 my-5 border border-3">
                    <h2>Nebyly nalezeny žádné příspěvky.</h2>
                  </div>';
            return;
        }

        $indent =  ($level * 50);

        foreach ($comments as $comment) {

            $likeQuery = $db->prepare('SELECT COUNT(*) AS likes 
                                             FROM comments JOIN likes ON comments.comments_id=likes.comments_id
                                             WHERE comments.comments_id=:comments_id AND likes.like_dislike=1 LIMIT 1
                                            ');

            $likeQuery->execute([
                ':comments_id'=>$comment['comments_id']
            ]);

            $data = $likeQuery->fetch(PDO::FETCH_ASSOC);

            $likes = $data['likes'];

            $dislikeQuery = $db->prepare('SELECT COUNT(*) AS dislikes 
                                             FROM comments JOIN likes ON comments.comments_id=likes.comments_id
                                             WHERE comments.comments_id=:comments_id AND likes.like_dislike=2 LIMIT 1
                                            ');

            $dislikeQuery->execute([
                ':comments_id'=>$comment['comments_id']
            ]);

            $data = $dislikeQuery->fetch(PDO::FETCH_ASSOC);

            $dislikes = $data['dislikes'];

            echo '<div class="p-3 border" style="margin-left: '.$indent.'px">
                        <div class="row">
                            <div class="col-8">
                                <h4>
                                    <a class="text-decoration-none" href="allComments.php?creator='.$comment['creator_id'].'">'.htmlspecialchars($comment['creator']).'</a>
                                </h4>
                                <p>'.htmlspecialchars($comment['text']).'</p>
                                <a href="showTopics.php?category='.$comment['categories_id'].'" class="badge bg-primary">'.$comment['category_name'].'</a>
                                <a href="showComments.php?topic='.$comment['topics_id'].'" class="badge bg-secondary">'.$comment['topic_name'].'</a>
                                <a href="comment.php?parent_id='.$comment['comments_id'].'&topic='.$_GET['topic'].'" class="badge bg-success">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-reply" viewBox="0 0 16 16">
                                      <path d="M6.598 5.013a.144.144 0 0 1 .202.134V6.3a.5.5 0 0 0 .5.5c.667 0 2.013.005 3.3.822.984.624 1.99 1.76 2.595 3.876-1.02-.983-2.185-1.516-3.205-1.799a8.74 8.74 0 0 0-1.921-.306 7.404 7.404 0 0 0-.798.008h-.013l-.005.001h-.001L7.3 9.9l-.05-.498a.5.5 0 0 0-.45.498v1.153c0 .108-.11.176-.202.134L2.614 8.254a.503.503 0 0 0-.042-.028.147.147 0 0 1 0-.252.499.499 0 0 0 .042-.028l3.984-2.933zM7.8 10.386c.068 0 .143.003.223.006.434.02 1.034.086 1.7.271 1.326.368 2.896 1.202 3.94 3.08a.5.5 0 0 0 .933-.305c-.464-3.71-1.886-5.662-3.46-6.66-1.245-.79-2.527-.942-3.336-.971v-.66a1.144 1.144 0 0 0-1.767-.96l-3.994 2.94a1.147 1.147 0 0 0 0 1.946l3.994 2.94a1.144 1.144 0 0 0 1.767-.96v-.667z"/>
                                    </svg>
                                    Odpovědět
                                </a>
                                <a>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-hand-thumbs-up-fill" viewBox="0 0 16 16">
                                        <path d="M6.956 1.745C7.021.81 7.908.087 8.864.325l.261.066c.463.116.874.456 1.012.965.22.816.533 2.511.062 4.51a9.84 9.84 0 0 1 .443-.051c.713-.065 1.669-.072 2.516.21.518.173.994.681 1.2 1.273.184.532.16 1.162-.234 1.733.058.119.103.242.138.363.077.27.113.567.113.856 0 .289-.036.586-.113.856-.039.135-.09.273-.16.404.169.387.107.819-.003 1.148a3.163 3.163 0 0 1-.488.901c.054.152.076.312.076.465 0 .305-.089.625-.253.912C13.1 15.522 12.437 16 11.5 16H8c-.605 0-1.07-.081-1.466-.218a4.82 4.82 0 0 1-.97-.484l-.048-.03c-.504-.307-.999-.609-2.068-.722C2.682 14.464 2 13.846 2 13V9c0-.85.685-1.432 1.357-1.615.849-.232 1.574-.787 2.132-1.41.56-.627.914-1.28 1.039-1.639.199-.575.356-1.539.428-2.59z"/>
                                    </svg>
                                </a>
                                <a>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-hand-thumbs-down-fill" viewBox="0 0 16 16">
                                      <path d="M6.956 14.534c.065.936.952 1.659 1.908 1.42l.261-.065a1.378 1.378 0 0 0 1.012-.965c.22-.816.533-2.512.062-4.51.136.02.285.037.443.051.713.065 1.669.071 2.516-.211.518-.173.994-.68 1.2-1.272a1.896 1.896 0 0 0-.234-1.734c.058-.118.103-.242.138-.362.077-.27.113-.568.113-.856 0-.29-.036-.586-.113-.857a2.094 2.094 0 0 0-.16-.403c.169-.387.107-.82-.003-1.149a3.162 3.162 0 0 0-.488-.9c.054-.153.076-.313.076-.465a1.86 1.86 0 0 0-.253-.912C13.1.757 12.437.28 11.5.28H8c-.605 0-1.07.08-1.466.217a4.823 4.823 0 0 0-.97.485l-.048.029c-.504.308-.999.61-2.068.723C2.682 1.815 2 2.434 2 3.279v4c0 .851.685 1.433 1.357 1.616.849.232 1.574.787 2.132 1.41.56.626.914 1.28 1.039 1.638.199.575.356 1.54.428 2.591z"/>
                                    </svg>   
                                </a>        
                            </div>
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
