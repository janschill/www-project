<?php

namespace Service;

class FileUploader
{
  public static function upload()
  {
    $valid = 1;
    
    if ($_FILES) {
      /* creates accordingly folders and moves image there */
      $path = __DIR__ . '/../../public/images/uploads/' . date("Y", time()) . "/". date("m", time()) . "/";
      if (!file_exists($path)) {
        mkdir($path, 0777, true);
      }
      $uploadfile = $path . basename($_FILES['file-upload']['name']);
      $valid = 1;
      
      if (!move_uploaded_file($_FILES['file-upload']['tmp_name'], $uploadfile)) {
        $valid = 0;
      }
    }
    
    return $valid;
  }
}