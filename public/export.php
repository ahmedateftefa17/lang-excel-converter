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
          <h1 class="jumbotron-heading">Export</h1>
          <p class="lead text-muted">Convert Languages Files to Excel and Download</p>
          <p>Click to <b>generate</b> download link</p>
          <form action="export.php" method="post">
              <?php $langs = scandir('../lang');?>
              <?php if (count($langs) > 2) {?>
                  <?php foreach ($langs as $lang) {?>
                      <?php if ($lang != "." && $lang != ".." && $lang != ".gitignore") {?>
                          <button type="submit" name="lang" value="<?=$lang?>" class="btn btn-info"><?=$lang?></button>
                      <?php }?>
                  <?php }?>
              <?php } else {?>
                  <h3>No Langs Found!</h3>
              <?php }?>
          </form>
          <?php
if (isset($_POST['lang'])) {
    require 'WordCounter.php';
    $lang     = $_POST['lang'];
    $lang_dir = "../lang/$lang";
    if (is_dir($lang_dir)) {
        $files_name = scandir($lang_dir);
        if (count($files_name) > 2) {
            $global_lang = [];
            foreach ($files_name as $lang_file) {
                if ($lang_file != "." && $lang_file != ".." && $lang_file != ".gitignore") {
                    if (!defined('BASEPATH')) {
                        @define("BASEPATH", "BASEPATH");
                    }

                    $lang           = [];
                    $file_lang_list = include "./$lang_dir/$lang_file";
                    if (is_int($file_lang_list)) {
                        $file_lang_list = $lang;
                        unset($lang);
                    }
                    $global_lang[str_replace(".php", "", $lang_file)] = $file_lang_list;
                }
            }
            if (count($global_lang)) {
                $file_name   = date('Y-m-d H-i-s');
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $spreadsheet->getProperties()->setCreator("Lang-Excel-Converter")
                    ->setLastModifiedBy("Lang-Excel-Converter")
                    ->setTitle("Lang-Excel-Converter $file_name")
                    ->setSubject("Lang-Excel-Converter $file_name")
                    ->setDescription("Lang-Excel-Converter $file_name")
                    ->setKeywords("Lang-Excel-Converter")
                    ->setCategory("Lang-Excel-Converter");
                $create_sheet = false;
                $index        = 0;
                foreach ($global_lang as $lang_file_name => $lang_file_array) {
                    if ($create_sheet) {
                        $spreadsheet->createSheet();
                    } else {
                        $create_sheet = true;
                    }
                    $spreadsheet->setActiveSheetIndex($index++);
                    $sheet = $spreadsheet->getActiveSheet();
                    $sheet->setTitle($lang_file_name);
                    if (count($lang_file_array)) {
                        $line = 1;
                        $sheet_words_counter = 0;
                        foreach ($lang_file_array as $key => $value) {
                            if (is_string($value)) {
                                $line_words_counter = WordCounter::countLineWords($value);
                                $sheet_words_counter += $line_words_counter;
                                $sheet->setCellValue("A$line", $key);
                                $sheet->setCellValue("B$line", $value);
                                $sheet->setCellValue("C$line", $line_words_counter);
                                $line++;
                            } else {
                                $sheet->setCellValue("A$line", $key);
                                $sheet->getStyle("A$line")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                                $sheet->getStyle("A$line")->getFill()->getStartColor()->setARGB('FFFF0000');
                                $sheet->setCellValue("B$line", "ARRAY $key START");
                                $sheet->getStyle("B$line")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                                $sheet->getStyle("B$line")->getFill()->getStartColor()->setARGB('FFFF0000');
                                $line++;
                                if (count($value)) {
                                    foreach ($value as $k => $v) {
                                        if (is_string($v)) {
                                            $line_words_counter = WordCounter::countLineWords($v);
                                            $sheet_words_counter += $line_words_counter;
                                            $sheet->setCellValue("A$line", $k);
                                            $sheet->setCellValue("B$line", $v);
                                            $sheet->setCellValue("C$line", $line_words_counter);
                                            $line++;
                                        } else {
                                            $sheet->setCellValue("A$line", $k);
                                            $sheet->getStyle("A$line")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                                            $sheet->getStyle("A$line")->getFill()->getStartColor()->setARGB('FFFF0000');
                                            $sheet->setCellValue("B$line", "ARRAY $k START");
                                            $sheet->getStyle("B$line")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                                            $sheet->getStyle("B$line")->getFill()->getStartColor()->setARGB('FFFF0000');
                                            $line++;
                                            if (count($v)) {
                                                foreach ($v as $k2 => $v2) {
                                                    $line_words_counter = WordCounter::countLineWords($v2);
                                                    $sheet_words_counter += $line_words_counter;
                                                    $sheet->setCellValue("A$line", $k2);
                                                    $sheet->setCellValue("B$line", $v2);
                                                    $sheet->setCellValue("C$line", $line_words_counter);
                                                    $line++;
                                                }
                                            }
                                            $sheet->setCellValue("A$line", $k);
                                            $sheet->getStyle("A$line")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                                            $sheet->getStyle("A$line")->getFill()->getStartColor()->setARGB('FFFF0000');
                                            $sheet->setCellValue("B$line", "ARRAY $k END");
                                            $sheet->getStyle("B$line")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                                            $sheet->getStyle("B$line")->getFill()->getStartColor()->setARGB('FFFF0000');
                                            $line++;
                                        }
                                    }
                                }
                                $sheet->setCellValue("A$line", $key);
                                $sheet->getStyle("A$line")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                                $sheet->getStyle("A$line")->getFill()->getStartColor()->setARGB('FFFF0000');
                                $sheet->setCellValue("B$line", "ARRAY $key END");
                                $sheet->getStyle("B$line")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                                $sheet->getStyle("B$line")->getFill()->getStartColor()->setARGB('FFFF0000');
                                $line++;
                            }
                        }
                        $sheet->setCellValue("C$line", $sheet_words_counter);
                    }
                    $sheet->getColumnDimension('A')->setAutoSize(true);
                    $sheet->getColumnDimension('B')->setAutoSize(true);
                    $sheet->getColumnDimension('C')->setAutoSize(true);
                }
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save("../tmp/exports/$file_name.xlsx");
                echo "<a href='download.php?file=$file_name.xlsx' class='btn btn-success my-3'>Download ({$_POST['lang']})</a>";
            }
        } else {
            echo "No Files Found in Lang Folder!";
        }
    } else {
        echo "Lang Folder not Found!";
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
