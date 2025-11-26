<?php
// header.php - site header/navigation
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>CnEduc Tutorial Site</title>
  <link rel="stylesheet" href="<?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) ? '../assets/style.css' : 'assets/style.css'; ?>" type="text/css">
  <style>
    * { box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; }
    html { scroll-behavior: smooth; -ms-overflow-style: scrollbar; }
    body { margin: 0; padding: 0; }
  </style>
</head>
<body>
  <header class="site-header">
    <div class="container">
      <a class="site-title" href="index.php">CnEduc</a>
      <nav class="site-nav">
        <a href="index.php">Home</a>
        <a href="explore.php">Explore</a>
        <a href="search.php">Search</a>
        <a href="how_to_use.php">Guide</a>
        <a href="about.php">About</a>
        <a href="admin/login.php">Admin</a>
      </nav>
    </div>
  </header>
  <div class="container">
