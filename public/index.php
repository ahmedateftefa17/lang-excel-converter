<?php
    $folder = '../tmp/exports';
    $files = glob($folder . '/*');
    foreach($files as $file){
        if(is_file($file) && fileatime($file) < (time() - 1800)) {
            unlink($file);
        }
    }
    $folder = '../tmp/imports';
    $files = glob($folder . '/*');
    foreach($files as $file){
        if(is_file($file) && fileatime($file) < (time() - 1800)) {
            unlink($file);
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Lang Excel Converter</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>
    <header>
      <div class="navbar navbar-dark bg-dark shadow-sm">
        <div class="container d-flex justify-content-between">
          <a href="#" class="navbar-brand d-flex align-items-center">
            <strong>Lang Excel Converter</strong>
          </a>
        </div>
      </div>
    </header>

    <main role="main">
      <section class="jumbotron text-center">
        <div class="container">
          <h1 class="jumbotron-heading">Lang Excel Converter</h1>
          <p class="lead text-muted">Convert Languages Files to Excel and vice versa!</p>
          <p>
            <a href="export.php" class="btn btn-primary my-2">Export</a>
            <a href="import.php" class="btn btn-success my-2">Import</a>
          </p>
        </div>
      </section>
    </main>

    <footer class="text-muted">
      <div class="container">
      </div>
    </footer>
  </body>
</html>
