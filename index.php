<?php

    require_once __DIR__.'/include/db.php';

    include __DIR__.'/include/header.php';

    $title = 'Fórum';





    $categories = $db->query('SELECT DISTINCT categories.name AS category_name, categories.description AS description, categories.categories_id AS categories_id, comments.updated AS updated, COUNT(comments.comments_id) AS comments
                                    FROM categories JOIN topics USING (categories_id) JOIN comments USING (topics_id) GROUP BY categories.name  
                                    ORDER BY `comments`.`updated` ASC;')->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($categories)){
        foreach ($categories as $category) {
            echo '<div class="container p-3 my-3 border border-3">
                    <h2>
                      <a href="topics.php?category='.$category['categories_id'].'">'.htmlspecialchars($category['category_name']).'</a>
                    </h2>
                    <p>'.htmlspecialchars($category['description']).'</p>
                    <p>Počet komentářů: '.htmlspecialchars($category['comments']).'</p>
                    <p class="text-muted">Poslední příspěvek: '.htmlspecialchars(date('d.m.Y H:i',strtotime($category['updated']))).'</p>
                  </div>';
        }
    }
    else{
        echo '<div class="container p-5 my-5 border border-3">
                <h2>Nebyly nalezeny žádné kategorie.</h2>
              </div>';
    }

    include __DIR__.'/include/footer.php';