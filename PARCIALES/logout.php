<?php
session_start();
session_destroy();
header('Location: sesion.php');
exit();
