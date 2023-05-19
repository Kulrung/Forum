<!doctype html>
<html lang="cz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo (!empty($title)?$title.' - ':'')?>Fórum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <header class="p-5 bg-primary text-white text-center">
        <h1>Fórum</h1>
        <p>Diskutujte kdykoliv a s kýmkoliv!</p>
    </header>

    <nav class="navbar navbar-expand-sm bg-dark navbar-dark">

        <?php $page= substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'],"/")+1) ?>


        <div class="container">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link <?= $page == 'index.php'? 'active': '' ?>" href="index.php">Domů</a>
                </li>
                <?php

                if (!empty($_SESSION['email'])){
                    echo '<li class="nav-item">
                            <a class="nav-link ';
                    if ($page == 'userComments.php'){
                        echo 'active';
                    }
                    echo '" href="userComments.php">Moje příspěvky</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link ';
                    if ($page =='userLikes.php'){
                        echo 'active';
                    }
                    echo '" href="userLikes.php">Oblíbené příspěvky</a>
                          </li>';
                }
                ?>
            </ul>
            <ul class="navbar-nav justify-content-right">
                <?php
                if (!empty($_SESSION['email'])){
                    echo '<li class="nav-item">
                            <a href="logout.php" class="nav-link">Odhlásit se</a>
                          </li>';
                }
                else {
                    echo '<li class="nav-item">
                            <a href="login.php" class="nav-link">Přihlásit se</a>
                          </li>
                          <li class="nav-item">
                            <a href="registration.php" class="nav-link">Registrovat se</a>
                          </li>';
                }
                ?>
            </ul>
        </div>
    </nav>

    <main class="container mt-4">