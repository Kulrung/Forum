<?php

    require_once 'include/user.php';

    include 'include/header.php';

    ?>


    <div class="row">
        <div class="col-4">
            <?php
            if (!empty($_SESSION['users_id'])) {
                echo '<a href="topic.php?" class="btn btn-primary">Vytvořit nové téma</a>';
            }
            ?>
        </div>
        <div class="col-4">
            <!-- <p>Seřadit podle: </p> -->
        </div>
        <div class="col-4 mb-3">
            <p>Seřadit podle: </p>
            <form method="get" id="sortFilter">
                <select name="sort" class="form-control" onchange="document.getElementById('sortFilter').submit();">
                    <option value="sort_by_updated" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'sort_by_updated'){ echo 'selected';} ?> >Poslední změny</option>
                    <option value="sort_by_comments" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'sort_by_comments'){ echo 'selected';} ?>>Počtu příspěvků</option>
                </select>
            </form>
        </div>
    </div>


    <?php

    $sort = 'updated';

    if (isset($_GET['sort'])){
        if ($_GET['sort'] == 'sort_by_updated'){
            $sort = 'updated';
        }
        elseif ($_GET['sort'] == 'sort_by_comments'){
            $sort = 'comments';
        }
    }

    if (!empty($_GET['category'])){


        $query = $db->prepare('SELECT topics.topics_id AS topics_id,  topics.name AS topic_name, topics.categories_id AS categories_id, categories.name AS category_name, MAX(comments.updated) AS updated, users.users_id AS creator_id, COUNT(comments.comments_id) as comments
                                     FROM topics JOIN categories ON topics.categories_id=categories.categories_id LEFT JOIN comments ON comments.topics_id=topics.topics_id LEFT JOIN users ON topics.creator_id=users.users_id
                                     GROUP BY topics.name
                                     HAVING topics.categories_id=:categories_id
                                     ORDER BY '.$sort.' DESC ;');
        $query->execute([
            ':categories_id'=>$_GET['category']
        ]);

        $topics = $query->fetchAll();

        if (!empty($topics)){
            foreach ($topics as $topic){
                echo '<div class="container p-3 border">
                        <div class="row">
                            <div class="col-8">
                                <h3>
                                  <a class="text-decoration-none" href="showComments.php?topic='.$topic['topics_id'].'">'.htmlspecialchars($topic['topic_name']).'</a>
                                </h3>
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
                    if($topic['creator_id'] == $_SESSION['users_id'] || $_SESSION['isAdmin']){
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
