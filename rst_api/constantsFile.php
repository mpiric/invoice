<?php

    // get document root
    $dir_root = $_SERVER['REQUEST_URI'];
    $project_folder_name_array = explode("/",$dir_root);

    define('_PROJECT_FOLDER_NAME_',$project_folder_name_array[1]);

?>