<?php

require_once 'include/user.php';

if (empty($_SESSION['users_id'])){
    exit('Pro úpravu kategorií musíte být přihlášen(a).');
}

$categoriesId='';
$categoryName='';
$categoryDescription='';

if (!empty($_REQUEST['id'])){
    $categoriesQuery=$db->prepare('SELECT * FROM categories WHERE categories_id=:id LIMIT 1');
    $categoriesQuery->execute([
        ':id'=>$_REQUEST['id']
    ]);
    if ($category = $categoriesQuery->fetch(PDO::FETCH_ASSOC)) {
        $categoriesId=$category['categories_id'];
        $categoryName = $category['name'];
        $categoryDescription = $category['description'];
    }
    else{
        exit('Kategorie neexistuje.');
    }
}

$errors=[];
if (!empty($_POST)){

    $categoryDescription=trim($_POST['description']);
    if (empty($categoryDescription)){
        $errors['description']='Musíte zadat platnou e-mailovou adresu.';
    }

    $categoryName = trim($_POST['name']);
    if (empty($categoryName)){
        $errors['name']='Musíte zadat název kategorie.';
    }

    $categoriesQuery=$db->prepare('SELECT name FROM categories');
    $categoriesQuery->execute();

    if ($categories = $categoriesQuery->fetchAll()){
        foreach ($categories as $category){
            if ($category['name'] == $categoryName && empty($_GET['id'])){
                $errors['name']='Tento název kategorie již existuje.';
            }
        }
    }

    if (empty($errors)){

        if ($categoriesId){
            $query = $db->prepare('UPDATE categories SET name=:name, description=:description WHERE categories_id=:id');
            $query->execute([
                ':name'=>$categoryName,
                ':description'=>$categoryDescription,
                ':id'=>$categoriesId
            ]);
        }
        else{
            $query=$db->prepare('INSERT INTO categories (name, description) VALUES (:name, :description);');
            $query->execute([
                ':name'=>$categoryName,
                ':description'=>$categoryDescription
            ]);
        }

        header('Location: index.php');
        exit();
    }
}

if ($categoriesId){
    $title = 'Úprava kategorie';
}
else{
    $title='Nová kategorie';
}

include 'include/header.php';

?>

    <h2>Kategorie</h2>

    <form method="post">
        <input type="hidden" name="id" value="<?php echo $categoriesId ?>">
        <div class="form-group">
            <label for="name">Název:</label>
            <input type="text" name="name" id="name" required class="form-control <?php echo (!empty($errors['name'])?'is-invalid':'');?>" value="<?php echo htmlspecialchars($categoryName)?>">
            <?php
            if (!empty($errors['name'])){
                echo '<div class="invalid-feedback">'.$errors['name'].'</div>';
            }
            ?>
        </div>
        <div class="form-group">
            <label for="description">Popis:</label>
            <textarea type="text" name="description" id="description" required class="form-control"><?php echo htmlspecialchars($categoryDescription)?></textarea>
            <?php
            if (!empty($errors['description'])){
                echo '<div class="invalid-feedback">'.$errors['description'].'</div>';
            }
            ?>
        </div>
        <div class="mt-2">
            <button type="submit" class="btn btn-primary">
                <?php
                if ($categoriesId){
                    echo 'Upravit';
                }
                else{
                    echo 'Přidat';
                }
                ?></button>
            <a href="index.php" class="btn btn-light">Zrušit</a>
        </div>

    </form>

<?php

include 'include/footer.php';



