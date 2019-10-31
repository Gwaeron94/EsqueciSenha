<?php
 require 'config.php';

 // Validar email para ver se existe no banco de dados.
 if(!empty($_POST['email'])) {
     $email = $_POST['email'];
     $sql = "SELECT * FROM usuarios WHERE email = :email";
     $sql = $pdo->prepare($sql);
     $sql->bindValue(":email", $email);
     $sql->execute();

     // Se existir, gera um token aleatório e insere na tabela
     if($sql->rowCount() > 0) {
         $sql = $sql->fetch();
         $id = $sql['id'];

         $token = md5(time().rand(0, 99999).rand(0, 99999));

         $sql = "INSERT INTO usuarios_token (id_usuario, hash, expirado_em) VALUES (:id_usuario, :hash, :expirado_em)";
         $sql = $pdo->prepare($sql);
         $sql->bindValue(":id_usuario", $id);
         $sql->bindValue(":hash", $token);
         $sql->bindValue("expirado_em", date('Y-m-d H:i', strtotime('+2 months')));
         $sql->execute();

         // Um link é criado com o token e mandado para o email do usuário.
         $link = "http://localhost/Projetos/EsqueciSenha/redefinir.php?token=".$token;

         $mensagem =  "Clique no link para redefinir sua senha:<br/>".$link;

         $assunto = "Redefinição de senha";
         $headers = 'From: seuemail@seusite.com.br'."\r\n".'X-Mailer: PHP/'.phpversion();

         //mail($email, $assunto, $mensagem, $headers);

         echo $mensagem;
         exit;
     }
 }
?>

<form method="POST">
    Qual o seu e-mail?<br/>
    <input type="email" name="email" /><br/>

    <input type="submit" value="Enviar" />
</form>