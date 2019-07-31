<?php
    if(isset($_GET["file"])) {
        $file = $_GET["file"];
        $filepath = "../tmp/exports/" . $file;
        
        if(file_exists($filepath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            flush();
            readfile($filepath);
            exit;
        } else {
            header('location: index.php');
        }
    } else {
        header('location: index.php');
    }