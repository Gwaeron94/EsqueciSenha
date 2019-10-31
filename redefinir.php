<?php
    require 'config.php';

    // Verificar se o token está correto e se é válido.
    if(!empty($_GET['token'])) {
        $token = $_GET['token'];

        $sql = "SELECT * FROM usuarios_token WHERE hash = :hash AND used = 0 AND expirado_em > NOW()";
        $sql = $pdo->prepare($sql);
        $sql->bindValue(":hash", $token);
        $sql->execute();

        if($sql->rowCount() > 0) {
            $sql = $sql->fetch();
            $id = $sql['id_usuario'];

            if(!empty($_POST['senha'])) {
                $senha = $_POST['senha'];

                // Processo de trocar a senha.
                $sql = "UPDATE usuarios SET senha = :senha WHERE id = :id";
                $sql = $pdo->prepare($sql);
                $sql->bindValue(":senha", md5($senha));
                $sql->bindValue(":id", $id);
                $sql->execute();

                // Processo de invalidar o token, deixar ele usado.
                $sql = "UPDATE usuarios_token SET used = 1 WHERE hash = :hash";
                $sql = $pdo->prepare($sql);
                $sql->bindValue(":hash", $token);
                $sql->execute();

                echo "Senha alterado com sucesso!";
                exit;
            }
            // Se o token estiver validado, o formulário é mostrado.
            ?>
            <form method="POST">
                Digite a nova senha:<br/>
                <input type="password" name="senha" /><br/>

                <input type="submit" value="Mudar senha" />
            </form>
            <?php
        } else {
            echo "Token inválido ou usado!";
            exit;
        }
    }
?>