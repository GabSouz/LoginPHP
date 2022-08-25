<?php

/**FORMA PARA ENCERRAR A SESSÃO */
session_start();
session_unset();
session_destroy();
header('location: index.php');
