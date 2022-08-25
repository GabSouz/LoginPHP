<?php
require_once('config/conn.php');

if (isset($_POST['email']) && isset($_POST['senha']) && !empty($_POST['email']) && !empty($_POST['email']) && !empty($_POST['senha'])) {
  //receber os dados do post e limpar 
  $email = limparPost($_POST['email']);
  $senha = limparPost($_POST['senha']);
  $senha_cript = sha1($senha);

  //VERIFICAR SE EXISTE ESTE USUARIO 
  $sql = $pdo->prepare("SELECT * FROM usuarios WHERE email=? AND senha=? LIMIT 1");
  $sql->execute(array($email, $senha_cript));
  $usuario = $sql->fetch(PDO::FETCH_ASSOC);
  if ($usuario) {
    //existe o usuario 
    //VERIFICAR SE O CADASTRO FOI CONFIRMADO
    if ($usuario['status'] == "novo") {
      //criar um token 
      $token = sha1(uniqid() . date('d-m-Y-H-i-s'));

      //ATUALIZAR O TOKEN DO USUARIO NO BANCO
      $sql = $pdo->prepare("UPDATE usuarios SET token=? WHERE email=? AND senha=?");
      if ($sql->execute(array($token, $email, $senha_cript))) {
        //ARMAZENAR ESTE TOKEN NA SESSÃO (SESSION)
        $_SESSION['TOKEN'] = $token;
        header('location: restrita.php');
      }
    } else {
      $erro_login = "Confirme o E-mail do cadastro!";
    }
  } else {
    $erro_login = "Usuario e/ou senha incorretos!!";
  }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="css/estilo.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
  <title>Login</title>
</head>

<body>
  <form method="post">
    <h1>Login</h1>

    <?php if (isset($_GET['result']) && ($_GET['result'] == 'ok')) { ?>

      <div class="sucesso class= animate__animated animate__pulse">Cadastrado com Sucesso!</div>

    <?php } ?>


    <?php if (isset($erro_login)) { ?>
      <div style="text-align:center" class="erro-geral animate__animated animate__rubberBand">
        <?php print $erro_login; ?>
      </div>
    <?php } ?>

    <div class="input-group">
      <img class="input-icon" src="img/user.png" />
      <input type="email" name="email" placeholder="Digite seu email" required />
    </div>

    <div class="input-group">
      <img class="input-icon" src="img/lock.png" />
      <input type="password" name="senha" placeholder="Digite sua senha" required />
    </div>

    <button class="btn-blue" type="submit">Fazer Login</button>
    <a href="cadastrar.php">Ainda não tenho cadastro</a>
  </form>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"> </script>
  <?php if (isset($_GET['result']) && ($_GET['result'] == 'ok')) { ?>
    <script>
      setTimeout(() => {
        $('.sucesso').hide();
      }, 2500);
    </script>
  <?php } ?>

</body>

</html>