<?php

require_once 'include/user.php';

if (empty($_SESSION['users_id'])){
    exit('Pro úpravu kategorií musíte být přihlášen(a).');
}

$topicsId='';
$name='';

if (!empty($_REQUEST['id'])){
    $topicsQuery=$db->prepare('SELECT * FROM topics WHERE topics_id=:id LIMIT 1');
    $topicsQuery->execute([
        ':id'=>$_REQUEST['id']
    ]);
    if ($topics = $topicsQuery->fetch(PDO::FETCH_ASSOC)) {
        $topicsId=$topics['topics_id'];
        $name = $topics['name'];
    }
    else{
        exit('Téma neexistuje.');
    }
}

$errors=[];
if (!empty($_POST)){

    $name = trim($_POST['name']);
    if (empty($name)){
        $errors['name']='Musíte zadat téma.';
    }

    if (empty($errors)){

        if ($topicsId){
            $query = $db->prepare('UPDATE topics SET name=:name WHERE topics_id=:id');
            $query->execute([
                ':name'=>$name,
                ':id'=>$topicsId
            ]);
        }
        else{
            $query=$db->prepare('INSERT INTO topics (name) VALUES (:name);');
            $query->execute([
                ':name'=>$name,
            ]);
        }

        header('Location: index.php');
        exit();
    }
}

if ($topicsId){
    $title = 'Úprava témtatu';
}
else{
    $title='Nové téma';
}

include 'include/header.php';

?>

    <h2>Téma</h2>

    <form method="post">
        <input type="hidden" name="id" value="<?php echo $topicsId ?>">
        <div class="form-group">
            <label for="name">Název:</label>
            <input type="text" name="name" id="name" required class="form-control <?php echo (!empty($errors['name'])?'is-invalid':'');?>" value="<?php echo htmlspecialchars($name)?>">
            <?php
            if (!empty($errors['name'])){
                echo '<div class="invalid-feedback">'.$errors['name'].'</div>';
            }
            ?>
        </div>
        <div class="mt-2">
            <button type="submit" class="btn btn-primary">
                <?php
                if ($topicsId){
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


