<?php

    require_once 'include/user.php';

    $title = 'Přihlášení';

    if (!empty($_SESSION['users_id'])){
        header('Location: index.php');
        exit();
    }

    $errors=false;
    if (!empty($_POST)){
        $userQuery=$db->prepare('SELECT * FROM users WHERE email=:email LIMIT 1;');
        $userQuery->execute([
            ':email'=>trim($_POST['email'])
        ]);

        if ($user = $userQuery->fetch(PDO::FETCH_ASSOC)){
            if (password_verify($_POST['password'],$user['password'])){
                $_SESSION['users_id']=$user['users_id'];
                $_SESSION['email']=$user['email'];
                $_SESSION['username']=$user['username'];
                Header('Location: index.php');
                exit();
            }
        }
        else{
            $errors=true;
        }
    }

    $Title='Přihlášení uživatele';

    include 'include/header.php';

    ?>

    <h2>Přihlášení uživatele</h2>

    <form method="post">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required class="form-control <?php echo (!empty($errors)?'is-invalid':'');?>" value="<?php echo !empty($_POST['email']) ? htmlspecialchars($_POST['email']):''; ?>">
        </div>
        <div class="form-group">
            <label for="password">Heslo:</label>
            <input type="password" name="password" id="password" required class="form-control <?php echo (!empty($errors)?'is-invalid':'');?>">
            <?php

            echo (!empty($errors)? '<div class="invalid-feedback">Neplatná kombinace přihlašovacího e-mailu a hesla.</div>':'');

            ?>
        </div>
        <div class="mt-2">
            <button type="submit" class="btn btn-primary">Přihlásit se</button>
            <a href="registration.php" class="btn btn-light">Registrovat se</a>
            <a href="index.php" class="btn btn-light">Zrušit</a>
        </div>
    </form>

<?php

    include 'include/footer.php';
