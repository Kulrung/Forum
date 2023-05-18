<?php

    include 'include/user.php';

    include 'include/header.php';

    $errors=[];
    if (!empty($_POST)){

        $description=trim($_POST['description']);
        if (empty($description)){
            $errors['description']='Musíte zadat platnou e-mailovou adresu.';
        }

        $name = trim($_POST['name']);
        if (empty($name)){
            $errors['name']='Musíte zadat název kategorie.';
        }
        else{
            $nameQuery=$db->prepare('SELECT * FROM categories WHERE name=:name LIMIT 1;');
            $nameQuery->execute([
                    ':name'=>$name
            ]);
            if ($nameQuery->rowCount()>0){
                $errors['name']='Kategorie s tímto názvem již existuje.';
            }
        }

        if (empty($errors)){
            $password=password_hash($_POST['password'],PASSWORD_DEFAULT);

            $categoriesQuery=$db->prepare('INSERT INTO categories (name, description) VALUES (:name, :description);');
            $categoriesQuery->execute([
                ':name'=>$name,
                ':description'=>$description
            ]);

            header('Location: index.php');
            exit();
        }
    }

?>

    <h2>Kategorie</h2>

    <form method="post">
        <div class="form-group">
            <label for="name">Název:</label>
            <input type="text" name="name" id="name" required class="form-control">
        </div>
        <div class="form-group">
            <label for="description">Popis:</label>
            <input type="text" name="description" id="description" required class="form-control">
        </div>
        <div class="mt-2">
            <button type="submit" class="btn btn-primary">Přidat</button>
            <a href="index.php" class="btn btn-light">Zrušit</a>
        </div>

    </form>

<?php

    include 'include/footer.php';



