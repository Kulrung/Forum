<?php

require_once 'include/user.php';

include 'include/header.php';

if (!empty($_GET['creator'])){
    $title = 'Příspěvky uživatele '.$_GET['creator'];
}

?>

    <div class="row">
        <div class="col-4">

        </div>
        <div class="col-4">
            <!-- <p>Seřadit podle: </p> -->
        </div>
        <div class="col-4 mb-3">
            <p>Seřadit podle: </p>
            <form method="get" id="sortFilter">
                <input type="hidden" name="creator" value="<?php echo $_GET['creator']; ?>">
                <select name="sort" class="form-control" onchange="document.getElementById('sortFilter').submit();">
                    <option value="sort_by_updated" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'sort_by_updated'){ echo 'selected';} ?>>Poslední změny</option>
                    <option value="sort_by_created" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'sort_by_created'){ echo 'selected';} ?>>Vytvoření</option>
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
    elseif ($_GET['sort'] == 'sort_by_created'){
        $sort = 'created';
    }
}

if (!empty($_SESSION['users_id'])) {

    $query = $db->prepare('SELECT categories.categories_id AS categories_id, topics.topics_id AS topics_id, users.users_id AS users_id, comments.comments_id AS comments_id, comments.text AS text, topics.name AS topic_name, categories.name AS category_name, users.username AS username, comments.created AS created, comments.updated AS updated
                                    FROM users JOIN comments ON users.users_id=comments.creator_id JOIN topics ON topics.topics_id=comments.topics_id JOIN categories ON categories.categories_id=topics.categories_id
                                    WHERE users_id=:users_id
                                    ORDER BY ' . $sort . ' DESC;');
    $query->execute([
        ':users_id' => $_GET['creator']
    ]);

    $comments = $query->fetchAll(PDO::FETCH_ASSOC);

}

if (!empty($comments)){
    foreach ($comments as $comment) {

        echo '<div class="container p-3 border">
                            <div class="row">
                                <div class="col-8">
                                    <h4>
                                        <a class="text-decoration-none" href="allComments.php?creator='.$comment['users_id'].'">'.htmlspecialchars($comment['username']).'</a>
                                    </h4>
                                    <p>'.htmlspecialchars($comment['text']).'</p>
                                    <a href="showTopics.php?category='.$comment['categories_id'].'" class="badge bg-primary">'.$comment['category_name'].'</a>
                                    <a href="showComments.php?topic='.$comment['topics_id'].'" class="badge bg-secondary">'.$comment['topic_name'].'</a>
                                </div>
                                <div class="col-4">
                                    <p class="text-muted">Aktualizováno: '.htmlspecialchars(date('d.m.Y H:i',strtotime($comment['updated']))).'</p>
                                    <p class="text-muted">Vytvořeno: '.htmlspecialchars(date('d.m.Y H:i',strtotime($comment['created']))).'</p>';

        if (isset($_SESSION['users_id'])){
            if ($comment['users_id'] == $_SESSION['users_id'] || $_SESSION['isAdmin'] ){
                echo '<a href="comment.php?id='.$comment['comments_id'].'" class="btn btn-primary">Upravit</a>
                                    <a href="deleteComment.php?id='.$comment['comments_id'].'" class="btn btn-danger">Smazat</a>';
            }
        }
        echo '</div>
                            </div>
                      </div>';
    }
}
else{
    echo '<div class="container p-5 my-5 border border-3">
                    <h2>Nebyly nalezeny žádné příspěvky.</h2>
                  </div>';
}

include 'include/footer.php';