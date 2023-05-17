<?php

    require_once __DIR__.'/include/db.php';

    include __DIR__.'/include/header.php';

    $title = 'Fórum';


    $categories = $db->query('SELECT DISTINCT categories.name AS category_name, categories.description AS description, comments.updated AS updated, COUNT(comments.comments_id) AS comments FROM categories JOIN topics USING (categories_id) JOIN comments USING (topics_id) GROUP BY categories.name ORDER BY comments.updated DESC;')->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($categories)){
        foreach ($categories as $category) {
            echo '<div class="container p-5 my-5 border">
                    <h2>'.htmlspecialchars($category['category_name']).'</h2>
                    <p>'.htmlspecialchars($category['description']).'</p>
                    <p>Počet komentářů: '.htmlspecialchars(date('d,m,Y H:i',strtotime($category['comments']))).'</p>
                    <p class="text-muted">Poslední příspěvek: '.htmlspecialchars($category['updated']).'</p>
                  </div>';
        }
    }
    else{
        echo '<div class="container p-5 my-5 border">
                <h2>Nebyly nalezeny žádné kategorie.</h2>
              </div>';
    }



    include __DIR__.'/include/footer.php';