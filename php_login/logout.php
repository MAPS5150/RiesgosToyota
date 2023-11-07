<?php
    session_start();

    session_unset();

    session_destroy();

    header('Location: /crudtoyota/php_login/');
?>