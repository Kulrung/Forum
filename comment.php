<?php

require_once 'include/user.php';

    if (empty($_SESSION['users_id'])){
        exit('Pro komentování musíte být přihlášen(a).');
    }

    $commentsId='';
    $commentsText='';

    if (!empty($_GET['topic'])){
        $topicsId = $_GET['topic'];
    }

    if (!empty($_GET['parent_id'])){
        $parentId = $_GET['parent_id'];
    }


    if (!empty($_REQUEST['id'])){
        $commentsQuery=$db->prepare('SELECT * FROM comments WHERE comments_id=:id LIMIT 1');
        $commentsQuery->execute([
            ':id'=>$_REQUEST['id']
        ]);
        if ($comment = $commentsQuery->fetch(PDO::FETCH_ASSOC)) {
            $commentsId=$comment['comments_id'];
            $commentsText = $comment['text'];
        }
        else{
            exit('Komentář neexistuje.');
        }
    }

    $errors=[];
    if (!empty($_POST)){

        $commentsText = trim($_POST['text']);
        if (empty($commentsText)){
            $errors['text']='Musíte zadat text komentáře.';
        }

        if (empty($errors)){

            if ($commentsId){
                $query = $db->prepare('UPDATE comments SET text=:text WHERE comments_id=:id');
                $query->execute([
                    ':text'=>$commentsText,
                    ':id'=>$commentsId
                ]);
            }
            elseif ($parentId){
                $query=$db->prepare('INSERT INTO comments (text, parent_id,topics_id,creator_id) VALUES (:text,:parent_id,:topics_id,:creator_id);');
                $query->execute([
                    ':text'=>$commentsText,
                    ':parent_id'=>$parentId,
                    ':topics_id'=>$topicsId,
                    ':creator_id'=>$_SESSION['users_id']
                ]);
            }
            else{
                $query=$db->prepare('INSERT INTO comments (text,topics_id,creator_id) VALUES (:text,:topics_id,:creator_id);');
                $query->execute([
                    ':text'=>$commentsText,
                    ':topics_id'=>$topicsId,
                    ':creator_id'=>$_SESSION['users_id']
                ]);
            }

            header('Location: index.php');
            exit();
        }
    }

    if ($commentsId){
        $title = 'Úprava komentáře';
    }
    else{
        $title='Nový komentář';
    }

    include 'include/header.php';

    ?>

        <h2>Komentář</h2>

        <form method="post">
            <input type="hidden" name="id" value="<?php echo $commentsId ?>">
            <div class="form-group">
                <label for="text">Text:</label>
                <input type="text" name="text" id="text" required class="form-control <?php echo (!empty($errors['text'])?'is-invalid':'');?>" value="<?php echo htmlspecialchars($commentsText)?>">
                <?php
                if (!empty($errors['text'])){
                    echo '<div class="invalid-feedback">'.$errors['text'].'</div>';
                }
                ?>
            </div>
            <div class="mt-2">
                <button type="submit" class="btn btn-primary">
                    <?php
                    if ($commentsId){
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



