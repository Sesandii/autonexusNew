<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My MVC App</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header>
  <nav>
    <a href="/">Home</a>
  </nav>
</header>
<main class="container">
  <?= $content ?? '' ?>
</main>
<script src="/assets/js/app.js"></script>
</body>
</html>
