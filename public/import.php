<?php require '../vendor/autoload.php'; ?>
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
          <a href="index.php" class="navbar-brand d-flex align-items-center">
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
          <hr>
          <h1 class="jumbotron-heading">Import</h1>
          <p class="lead text-muted">Upload and Convert Excel Files to Languages</p>
          <p>Fill form to upload file</p>
          <form action="import.php" method="post" enctype="multipart/form-data">
            <div class="form-group text-left">
              <label for="lang">Language Folder Name</label>
              <input type="text" id="lang" name="lang" placeholder="Language Folder Name" class="form-control" required="">
            </div>
            <div class="form-group text-left">
              <label for="langFile">Excel File</label>
              <input type="file" id="langFile" name="langFile" accept=".xlsx" class="form-control-file" required="">
            </div>
            <div class="text-left">
              <div class="form-check form-check-inline">
                <input type="radio" value="array" name="type" id="array" class="form-check-input" checked>
                <label class="form-check-label" for="array">
                  As Array (Laravel)
                </label>
              </div>
              <div class="form-check form-check-inline">
                <input type="radio" value="variable" name="type" id="variable" class="form-check-input">
                <label class="form-check-label" for="variable">
                  As Variable (Codeigniter)
                </label>
              </div>
            </div>
            <button class="btn btn-success" value="Import" type="submit" name="submit">Import</button>
          </form>
    <?php
if (isset($_POST['submit'])) {
    $target_dir  = "../tmp/imports/";
    $target_file = $target_dir . basename($_FILES["langFile"]["name"]);
    if (move_uploaded_file($_FILES["langFile"]["tmp_name"], $target_file)) {
        echo '<div class="alert alert-success my-3" role="alert">The file ' . basename($_FILES["langFile"]["name"]) . " has been uploaded.</div>";
    } else {
        die("Sorry, there was an error uploading your file.");
    }
    $lang     = $_POST['lang'];
    $lang_dir = "../lang/$lang";
    if (!is_dir($lang_dir)) {
        mkdir($lang_dir);
    }
    $reader       = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $spreadsheet  = $reader->load($target_file);
    $sheets_count = $spreadsheet->getSheetCount();
    for ($i = 0; $i < $sheets_count; $i++) {
        $sheet       = $spreadsheet->getSheet($i);
        $sheet_title = $sheet->getTitle();
        $lang_file   = fopen("$lang_dir/$sheet_title.php", "w");
        $sheet_rows  = $sheet->toArray(null, true, true, true);
        if (count($sheet_rows)) {
            fwrite($lang_file, "<?php\n");
            $type = $_POST['type'];
            if ($type == "array") {
                fwrite($lang_file, "return [\n");
            }
            $tabCount = 1;
            $tabStack = [];
            foreach ($sheet_rows as $row) {
                if ($type == "array") {
                    if ($row["B"] == "ARRAY {$row["A"]} START") {
                        for ($tabIndex = 0; $tabIndex < $tabCount; $tabIndex++) {
                            fwrite($lang_file, "\t");
                        }

                        fwrite($lang_file, "'{$row["A"]}' => [\n");
                        $tabCount++;
                    } else if ($row["B"] == "ARRAY {$row["A"]} END") {
                        $tabCount--;
                        for ($tabIndex = 0; $tabIndex < $tabCount; $tabIndex++) {
                            fwrite($lang_file, "\t");
                        }

                        fwrite($lang_file, "],\n");
                    } else {
                        for ($tabIndex = 0; $tabIndex < $tabCount; $tabIndex++) {
                            fwrite($lang_file, "\t");
                        }

                        $value = str_replace("'", "\'", $row["B"]);
                        fwrite($lang_file, "'{$row["A"]}' => '{$value}',\n");
                    }
                } else if ($type == "variable") {
                    $value = str_replace("'", "\'", $row["B"]);
                    fwrite($lang_file, "\$lang['{$row["A"]}'] = '{$value}';\n");
                }
            }
            if ($type == "array") {
                fwrite($lang_file, "];\n");
            }
        }
        fclose($lang_file);
    }
}
    ?>
        </div>
      </section>
    </main>

    <footer class="text-muted">
      <div class="container">
      </div>
    </footer>
  </body>
</html>
