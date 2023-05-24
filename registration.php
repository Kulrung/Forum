<?php

    require_once 'include/user.php';

    if (!empty($_SESSION['users_id'])){
        header('Location: index.php');
        exit();
    }

    $errors=[];
    if (!empty($_POST)){

        $username = trim($_POST['username']);
        if (empty($username)){
            $errors['username']='Musíte zadat své jméno či přezdívku.';
        }

        $email=trim($_POST['email']);
        if (!filter_var($email,FILTER_VALIDATE_EMAIL)){
            $errors['email']='Musíte zadat platnou e-mailovou adresu.';
        }
        else{
            $emailQuery=$db->prepare('SELECT * FROM users WHERE email=:email LIMIT 1;');
            $emailQuery->execute([
                ':email'=>$email
            ]);
            if ($emailQuery->rowCount()>0){
                $errors['email']='Uživatelský účet s touto e-mailovou adresu již extustuje.';
            }
        }

        if (empty($errors)){
            $password=password_hash($_POST['password'],PASSWORD_DEFAULT);

            $passwordQuery=$db->prepare('INSERT INTO users (username, email, password, isAdmin) VALUES (:username, :email, :password, 0);');
            $passwordQuery->execute([
                ':username'=>$username,
                ':email'=>$email,
                ':password'=>$password
            ]);

            $_SESSION['users_id']=$db->lastInsertId();
            $_SESSION['email']=$email;
            $_SESSION['username']=$username;

            header('Location: index.php');
            exit();
        }
    }

    $title='Registrace';

    include 'include/header.php';

?>

    <h2>Registrace nového uživatele</h2>

    <form method="post">
        <div class="form-group">
            <label for="username">Jméno či přezdívka:</label>
            <input type="text" name="username" id="username" required class="form-control <?php echo (!empty($errors['username'])?'is-invalid':'');?>" value="<?php echo !empty($username)? htmlspecialchars($username):''; ?>">
            <?php

            echo (!empty($errors['username'])? '<div class="invalid-feedback">'.$errors['username'].'</div>':'');

            ?>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required class="form-control <?php echo (!empty($errors['email'])?'is-invalid':'');?>" value="<?php echo !empty($email)? htmlspecialchars($email):''; ?>">
            <?php

            echo (!empty($errors['email'])? '<div class="invalid-feedback">'.$errors['email'].'</div>':'');

            ?>
        </div>
        <div class="form-group">
            <label for="password">Heslo:</label>
            <input type="password" name="password" id="password" required class="form-control <?php echo (!empty($errors['password'])?'is-invalid':'');?>">
            <?php

            echo (!empty($errors['password'])? '<div class="invalid-feedback">'.$errors['password'].'</div>':'');

            ?>
        </div>
        <div class="form-group">
            <label for="password2">Potvrzení hesla:</label>
            <input type="password" name="password2" id="password2" required class="form-control <?php echo (!empty($errors['password2'])?'is-invalid':'');?>">
            <?php

            echo (!empty($errors['password2'])? '<div class="invalid-feedback">'.$errors['password2'].'</div>':'');

            ?>
        </div>
        <div class="mt-2">
            <button type="submit" class="btn btn-primary">Registrovat se</button>
            <a href="login.php" class="btn btn-light">Přihlásit se</a>
            <a href="index.php" class="btn btn-light">Zrušit</a>
        </div>
    </form>

<?php

    include 'include/footer.php';
