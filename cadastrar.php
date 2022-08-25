<?php
require_once "config/conn.php";
//VERIFICAR SE A POSTAGEM EXISTE DE ACORDO COM OS CAMPO 
if (isset($_POST['nome_completo']) && isset($_POST['email']) &&  isset($_POST['senha']) && isset($_POST['repete_senha'])) {
  //verificar se todos os campos foram preenchido
  if (empty($_POST['nome_completo']) or empty($_POST['email']) or empty($_POST['senha']) or  empty($_POST['repete_senha']) or empty($_POST['termos'])) {
    $erro_geral = "todos os campos são obrigatorios";
  } else {
    //receber o valor do post e limpar
    $nome = limparPost($_POST['nome_completo']);
    $email = limparPost($_POST['email']);
    $senha = limparPost($_POST['senha']);
    $senha_cript = sha1($senha);
    $repete_senha = limparPost($_POST['repete_senha']);
    $checkbox = limparPost($_POST['termos']);

    //VALIDAÇÃO DE NOME 
    if (!preg_match("/^[a-zA-Z-' ]*$/", $nome)) {
      $erro_nome = "Somente letras e espaços em branco";
    }

    //VERIFICAR SE O EMAIL É VALIDO
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $erro_email = "Formato de email invalido!";
    }

    //verificar se senha tem mais de 6 digitos
    if (strlen($senha) < 6) {
      $erro_senha = "Senha deve ter 6 cacteres ou mais";
    }

    //verificar se as senha são iguais
    if ($senha !== $repete_senha) {
      $erro_repete_senha = "As senhas devem ser iguais";
    }
    //verificar se checkbox foi marcado 
    if ($checkbox !== "ok") {
      $erro_checkbox = "Desativado";
    }

    /**SE TODAS A INFORMAÇOES NO FORMULARIO ESTIVER CORRETA */
    if (!isset($erro_geral) && !isset($erro_nome) && !isset($erro_email) && !isset($erro_senha) && !isset($erro_repete_senha) && !isset($erro_checkbox)) {
      //VERRIFICAR SE O EMAIL JÁ ESTÁ CADASTRADO NO BANCO
      $sql = $pdo->prepare("SELECT * FROM usuarios WHERE email=? LIMIT 1");
      $sql->execute(array($email));
      $usuario = $sql->fetch();
      //SE NÃO EXISTIR UM USUARIO ADICIONAR NO BANCO
      if (!$usuario) {
        $recupera_senha = "";
        $token = "";
        $cod_confirmacao = uniqid();
        $status = "novo";
        $data_cadastro = date('d/m/Y');
        $sql = $pdo->prepare("INSERT INTO usuarios VALUES (null,?,?,?,?,?,?,?,?)");
        if ($sql->execute(array($nome, $email, $senha_cript, $recupera_senha, $token,  $cod_confirmacao, $status, $data_cadastro))) {

          //VERIFICADO QUAL MODO É 
          //SE O MODO FOR LOCAL 
          if ($modo == 'local') {
            header('location: index.php?result=ok');
          }
        }
      } else {
        //JA EXISTE USUARIO COM ESSE EMAIL CADASTRADO
        $erro_geral = "Email já está cadastrado";
      }
    }
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
    <h1>Cadastrar</h1>

    <?php if (isset($erro_geral)) { ?>
      <div class="erro-geral animate__animated animate__rubberBand">
        <?php print $erro_geral; ?>
      </div>
    <?php } ?>

    <!--INPUT DO FORMULARIO -->
    <div class="input-group">
      <img class="input-icon" src="img/card.png" />
      <input <?php if (isset($erro_geral) or isset($erro_nome)) {
                print 'class="erro-input"';
              } ?>name="nome_completo" type="text" placeholder="Nome Completo" <?php if (isset($_POST['nome_completo'])) {
                                                                                  print "value='" . $_POST['nome_completo'] . "'";
                                                                                } ?> required />
      <?php if (isset($erro_nome)) { ?>
        <div class="erro"><?php print $erro_nome; ?> </div>
      <?php } ?>
    </div>

    <div class="input-group">
      <img class="input-icon" src="img/user.png" />
      <input <?php if (isset($erro_geral) or isset($erro_email)) {
                print 'class="erro-input';
              } ?> name="email" type="email" placeholder="Seu melhor email" <?php if (isset($_POST['email'])) {
                                                                              print "value='" . $_POST['email'] . "'";
                                                                            } ?> required />
      <?php if (isset($erro_email)) { ?>
        <div class="erro"><?php print $erro_email; ?> </div>
      <?php } ?>
    </div>

    <div class="input-group">
      <img class="input-icon" src="img/lock.png" />
      <input <?php if (isset($erro_geral) or isset($erro_senha)) {
                print 'class="erro-input"';
              } ?>name="senha" type="password" placeholder="Senha mínimo 6 Dígitos" <?php if (isset($_POST['senha'])) {
                                                                                      print "value='" . $_POST['senha'] . "'";
                                                                                    } ?>required />
      <?php if (isset($erro_senha)) { ?>
        <div class="erro"><?php print $erro_senha; ?> </div>
      <?php } ?>
    </div>

    <div class="input-group">
      <img class="input-icon" src="img/lock-open.png" />
      <input <?php if (isset($erro_geral) or isset($erro_repete_senha)) {
                print 'class="erro-input"';
              } ?>name="repete_senha" type="password" placeholder="Repita a senha criada" <?php if (isset($_POST['repete_senha'])) {
                                                                                            print "value='" . $_POST['repete_senha'] . "'";
                                                                                          } ?>required />
      <?php if (isset($erro_repete_senha)) { ?>
        <div class="erro"><?php print $erro_repete_senha; ?> </div>
      <?php } ?>
    </div>

    <div <?php if (isset($erro_geral) or isset($erro_checkbox)) {
            print 'class="input-group erro-input"';
          } else {
            print 'class="input-group"';
          } ?>>
      <input type="checkbox" id="termos" name="termos" value="ok" required />
      <label for="termos">Ao se cadastrar você concorda com a nossa
        <a class="link" href="#">Política de Privacidade</a> e os
        <a class="link" href="#">Termos de uso</a></label>
    </div>

    <button class="btn-blue" type="submit">Cadastrar</button>
    <a href="index.php">Já tenho uma conta</a>
  </form>
</body>

</html>