<?php
require_once 'config/conn.php';
//VERIFICAÇÃO DE AUTENTICAÇÃO 
$user = auth($_SESSION['TOKEN']);
if ($user) {
  print "<h1>Seja bem vindo <b style='color:red'>" . $user['nome'] . "!</b> </h1>";
  print "<br></br><a style='background:green; color:white; text-decoration:none; padding:15px; border-radius:5px;' href='logout.php'>Desconectar o Usuário<a>";
} else {
  //redirecionar para o login
  header('Location: index.php');
}

// //verificar se tem autorização 
// $sql = $pdo->prepare("SELECT * FROM usuarios WHERE token=? LIMIT 1");
// $sql->execute(array($_SESSION['TOKEN']));
// $usuario = $sql->fetch(PDO::FETCH_ASSOC);

// //SE NÃO ENCONTRAR O USUARIO 
// if (!$usuario) {
//   header('Location: index.php');
// } else {
//   print "<h1>Seja bem vindo <b style='color:red'>" . $usuario['nome'] . "!</b> </h1>";
//   print "<br></br><a style='background:green; color:white; text-decoration:none; padding:15px; border-radius:5px;' href='logout.php'>Desconectar o Usuário<a>";
// }
