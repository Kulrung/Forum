<!doctype html>
<html lang="cz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php if (!empty($title)) {
            echo $title;
        } ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <header class="p-5 bg-primary text-white text-center">
        <h1>Fórum</h1>
        <p>Diskutujte kdykoliv a s kýmkoliv!</p>
    </header>

    <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
        <div class="container">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active" href="#">Domů</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Moje příspěvky</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Oblíbené příspěvky</a>
                </li>
            </ul>
            <ul class="navbar-nav justify-content-right">
                <li class="nav-item">
                    <a class="nav-link" href="#">Přihlásit se</a>
                </li>
            </ul>
        </div>
    </nav>

    <main class="container mt-4">