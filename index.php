<?php

    require_once 'include/user.php';

    include 'include/header.php';

    ?>


      <div class="row">
        <div class="col-4 mb-3">
            <?php
            if (isset($_SESSION['users_id'])){
                if ($_SESSION['isAdmin']){
                    {
                        echo '<a href="category.php" class="btn btn-primary">Vytvořit novou kategorii</a>';
                    }
                }
            }
            ?>
        </div>
        <div class="col-4">
            <!-- <p>Seřadit podle: </p> -->
        </div>
        <div class="col-4 mb-3" >
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

    $categoriesQuery = $db->prepare('SELECT DISTINCT categories.name AS category_name, categories.description AS description, categories.categories_id AS categories_id, MAX(comments.updated) AS updated, COUNT(comments.comments_id) AS comments
                                           FROM categories LEFT JOIN topics ON topics.categories_id=categories.categories_id LEFT JOIN comments ON comments.topics_id=topics.topics_id 
                                           GROUP BY categories.name  
                                           ORDER BY '.$sort.' DESC;');
    $categoriesQuery->execute();

    $categories = $categoriesQuery->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($categories)){
        foreach ($categories as $category) {
            echo '<div class="container p-3 border">
                        <div class="row">
                            <div class="col-8">
                                <h3>
                                  <a class="text-decoration-none" href="showTopics.php?category='.$category['categories_id'].'">'.htmlspecialchars($category['category_name']).'</a>
                                </h3>
                                <p>'.htmlspecialchars($category['description']).'</p>
                                
                            </div>
                            <div class="col-4">
                                <p>Počet příspěvků: '.htmlspecialchars($category['comments']).'</p>
                                <p class="text-muted">Poslední příspěvek: ';
                                if($category['updated'] != NULL ){
                                    echo htmlspecialchars(date('d.m.Y H:i',strtotime($category['updated'])));
                                }
                                else{
                                    echo 'Nikdy neaktualizováno.';
                                }
                                echo '</p>';

                                if(!empty($_SESSION['isAdmin'])){
                                    echo '<a href="category.php?id='.$category['categories_id'].'" class="btn btn-primary">Upravit</a>
                                <a href="categoryRemove.php?id='.$category['categories_id'].'" class="btn btn-danger">Smazat</a>';
                                }
                                echo'
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