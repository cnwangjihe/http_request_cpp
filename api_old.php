<?php
$uploaddir = 'upload/';

if (is_uploaded_file(isset($_FILES['file']['tmp_name'])?($_FILES['file']['tmp_name']):0)) 
{
    $uploadfile = $uploaddir . basename($_FILES['file']['name']);
    echo "File ". $_FILES['file']['name'] ." uploaded successfully. ";

    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) 
    {
        echo "File was moved! ";
    }
    else
    {
        print_r($_FILES);
    }
}
else 
{
    print_r($_FILES);
}
?>