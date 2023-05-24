<?php

require_once 'include/user.php';

if (empty($_SESSION['users_id'])){
    exit('Pro úpravu kategorií musíte být přihlášen(a).');
}

$topicsId='';
$topicsName='';

if (!empty($_GET['category'])){
    $categories_id = $_GET['category'];
}


if (!empty($_REQUEST['id'])){
    $topicsQuery=$db->prepare('SELECT * FROM topics WHERE topics_id=:id LIMIT 1');
    $topicsQuery->execute([
        ':id'=>$_REQUEST['id']
    ]);
    if ($topics = $topicsQuery->fetch(PDO::FETCH_ASSOC)) {
        $topicsId=$topics['topics_id'];
        $topicsName = $topics['name'];
    }
    else{
        exit('Téma neexistuje.');
    }
}

$errors=[];
if (!empty($_POST)){

    $topicsName = trim($_POST['name']);
    if (empty($topicsName)){
        $errors['name']='Musíte zadat téma.';
    }

    // může se vynechat, dokud není search

    $topicsQuery=$db->prepare('SELECT name FROM topics');
    $topicsQuery->execute();

    if ($topics = $topicsQuery->fetchAll()){
        foreach ($topics as $topic){
            if ($topic['name'] == $topicsName && empty($_GET['id'])){
                $errors['name']='Toto téma již existuje již existuje.';
            }
        }
    }

    if (empty($errors)){

        if ($topicsId){
            $query = $db->prepare('UPDATE topics SET name=:name WHERE topics_id=:id');
            $query->execute([
                ':name'=>$topicsName,
                ':id'=>$topicsId
            ]);
        }
        else{
            $query=$db->prepare('INSERT INTO topics (name,categories_id,creator_id) VALUES (:name,:categories_id,:creator_id);');
            $query->execute([
                ':name'=>$topicsName,
                ':categories_id'=>$categories_id,
                ':creator_id'=>$_SESSION['users_id']
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
        <input type="hidden" name="destination" value="<?php echo $_SERVER['REQUEST_URI'];?>">
        <input type="hidden" name="id" value="<?php echo $topicsId ?>">
        <div class="form-group">
            <label for="name">Název:</label>
            <input type="text" name="name" id="name" required class="form-control <?php echo (!empty($errors['name'])?'is-invalid':'');?>" value="<?php echo htmlspecialchars($topicsName)?>">
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



