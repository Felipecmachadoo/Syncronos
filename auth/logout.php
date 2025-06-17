<?php
session_start();
session_destroy();
header("Location: ../auth/login.php"); // Ou onde você quiser direcionar depois de sair
exit;
