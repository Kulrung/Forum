<?php

    require_once 'include/user.php';

    include 'include/header.php';


    if (empty($_SESSION['users_id'])){
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

        if (empty($_POST['password']) && empty($_POST['password2']) && empty($_POST['password3'])){
            if (empty($errors)){
                $query=$db->prepare('UPDATE users SET username=:username, email=:email WHERE users_id=:users_id');
                $query->execute([
                        ':users_id'=>$_SESSION['users_id'],
                        ':username'=>$_POST['username'],
                        ':email'=>$_POST['email']
                ]);

                $_SESSION['username'] = $_POST['username'];
                $_SESSION['email'] = $_POST['email'];

                Header('Location: index.php');
                exit();
            }
        }
        else{
            if ($_POST['password2']==$_POST['password3']){
                if (!empty($_POST['password2'])){
                    if (strlen($_POST['password2']) >5){
                        $query=$db->prepare('SELECT password FROM users WHERE users_id=:users_id');
                        $query->execute([
                            ':users_id'=>$_SESSION['users_id']
                        ]);

                        if ($user = $query->fetch(PDO::FETCH_ASSOC)){
                            if (password_verify($_POST['password'], $user['password'])){
                                return;
                            }
                            else{
                                $errors['password'] = 'Pro změnu hesla musíte nejprve potvrdit staré heslo.';
                            }
                        }
                    }
                    else{
                        $errors['password2']= 'Nové heslo musí být delší než 5 znaků.';
                    }
                }
                else{
                    $errors['password2']='Nové heslo nesmí být prázdné.';
                }
            }
            else{
                $errors['password2'] = 'Hesla se musí rovnat.';
            }

            if (empty($errors)){

                $password=password_hash($_POST['password2'],PASSWORD_DEFAULT);

                $emailQuery=$db->prepare('UPDATE users SET name=:name, email=:email, password=:password WHERE users_id=:users_id LIMIT 1;');
                $emailQuery->execute([
                    ':users_id'=>$_SESSION['users_id'],
                    ':email'=>$_POST['email'],
                    ':name'=>$_POST['username'],
                    ':password'=>$password
                ]);

                $_SESSION['username'] = $_POST['username'];
                $_SESSION['email'] = $_POST['email'];

                Header('Location: index.php');
                exit();
            }
        }
    }

    $title = 'Možnosti';

    ?>

    <h2>Změna údajů</h2>

    <form method="post">
        <div class="form-group">
            <label for="username">Jméno či přezdívka:</label>
            <input type="text" name="username" id="username" required class="form-control <?php echo (!empty($errors['username'])?'is-invalid':'');?>" value="<?php echo !empty($_SESSION['username'])? htmlspecialchars($_SESSION['username']):''; ?>">
            <?php

            echo (!empty($errors['username'])? '<div class="invalid-feedback">'.$errors['username'].'</div>':'');

            ?>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" class="form-control <?php echo (!empty($errors['email'])?'is-invalid':'');?>" value="<?php echo !empty($_SESSION['email'])? htmlspecialchars($_SESSION['email']):''; ?>">
            <?php

            echo (!empty($errors['email'])? '<div class="invalid-feedback">'.$errors['email'].'</div>':'');

            ?>
        </div>
        <div class="form-group">
            <label for="password">Staré heslo:</label>
            <input type="password" name="password" id="password" class="form-control <?php echo (!empty($errors['password'])?'is-invalid':'');?>">
            <?php

            echo (!empty($errors['password'])? '<div class="invalid-feedback">'.$errors['password'].'</div>':'');

            ?>
        </div>
        <div class="form-group">
            <label for="password2">Nové heslo:</label>
            <input type="password" name="password2" id="password2" class="form-control <?php echo (!empty($errors['password2'])?'is-invalid':'');?>">
            <?php

            echo (!empty($errors['password2'])? '<div class="invalid-feedback">'.$errors['password2'].'</div>':'');

            ?>
        </div>
        <div class="form-group">
            <label for="password3">Potvrzení nového hesla:</label>
            <input type="password" name="password3" id="password3" class="form-control <?php echo (!empty($errors['password3'])?'is-invalid':'');?>">
            <?php

            echo (!empty($errors['password3'])? '<div class="invalid-feedback">'.$errors['password3'].'</div>':'');

            ?>
        </div>
        <div class="mt-2">
            <button type="submit" class="btn btn-primary">Upravit </button>
            <a href="index.php" class="btn btn-light">Zrušit</a>
        </div>
    </form>

<?php

    include 'include/footer.php';