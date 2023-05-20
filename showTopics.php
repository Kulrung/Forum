<?php

    require_once 'include/user.php';

    include 'include/header.php';

    if (!empty($_GET['category'])){

        $query = $db->prepare('SELECT topics.topics_id AS topics_id, topics.name AS topic_name, COUNT(*) AS comments, MAX(comments.updated) AS updated, categories.name AS category_name, categories.categories_id AS categories_id, users.users_id AS users_id
                                     FROM categories JOIN topics ON categories.categories_id=topics.categories_id LEFT JOIN comments ON topics.topics_id=comments.topics_id JOIN users ON comments.creator_id=users.users_id
                                     WHERE topics.categories_id=:categories_id
                                     ORDER BY comments.updated;');
        $query->execute([
            ':categories_id'=>$_GET['category']
        ]);

        $topics = $query->fetchAll();

        if (!empty($topics)){
            foreach ($topics as $topic){
                echo '<div class="container p-3 my-3 border border-3">
                        <div class="row">
                            <div class="col-8">
                                <h2>
                                  <a href="showComments.php?topic='.$topic['topics_id'].'">'.htmlspecialchars($topic['topic_name']).'</a>
                                </h2>
                                <a href="showTopics.php?category='.$topic['categories_id'].'" class="badge bg-primary">'.$topic['category_name'].'</a>
                            </div>
                            <div class="col-4">
                                <p>Počet příspěvků: '.htmlspecialchars($topic['comments']).'</p>
                                <p class="text-muted">Poslední příspěvek: ';
                if($topic['updated'] != NULL ){
                    echo htmlspecialchars(date('d.m.Y H:i',strtotime($topic['updated'])));
                }
                else{
                    echo 'Nikdy neaktualizováno.';
                }
                echo '</p>';

                if (isset($_SESSION['users_id'])){
                    if($topic['users_id'] == $_SESSION['users_id'] || $_SESSION['isAdmin']){
                        echo '<a href="topic.php?id='.$topic['topics_id'].'" class="btn btn-primary">Upravit</a>
                                <a href="topicRemove.php'.$topic['topics_id'].'" class="btn btn-danger">Smazat</a>';
                    }
                }

                echo'
                            </div>
                        </div>
                  </div>';
            }
        }
        else{
            echo '<div class="container p-5 my-5 border border-3">
                <h2>Nebyla nalezena žádná témata.</h2>
              </div>';
        }

    }
    else{
        echo '<div class="container p-5 my-5 border border-3">
                <h2>Taková kategorie neexistuje.</h2>
              </div>';
    }



    include 'include/footer.php';
