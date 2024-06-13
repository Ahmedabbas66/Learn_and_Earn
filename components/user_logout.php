<?php

   include 'connect.php';

   setcookie('user_id', '', time() - 1, '/');

   header('location:../start_page.php');

?>