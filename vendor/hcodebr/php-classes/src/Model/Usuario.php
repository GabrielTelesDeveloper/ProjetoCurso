<?php

/* localização do arquivo */

namespace Hcode\Model;

use Hcode\DB\Sql;
use Hcode\Model;

class Usuario extends Model {

    const SESSION = "Usuario";

    public static function login($login, $senha) {
        /* Buscar no banco de dados */
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM usuario WHERE login = :LOGIN AND senha = :SENHA", array(
            "LOGIN" => $login,
            "SENHA" => $senha,
        ));

        /* Validar o resultado */
        if (count($results) === 0) {
            echo 'Personalizar Erro!';
        } else {
            /* Encontrou resultado */
            $data = $results[0];

            /* Verificar a senha */
            $usuario = new Usuario;
            $usuario->setData($data);

            /* Criar um sessão para o usuário */
            $_SESSION[Usuario::SESSION] = $usuario->getValues();

            return $usuario;
        }
    }

    public static function verificarLogin($tipoUsuario = true) {

        /* Se a sessão não estiver definida */
        if (
                !isset($_SESSION[Usuario::SESSION]) ||
                !$_SESSION[Usuario::SESSION] ||
                !(int) $_SESSION[Usuario::SESSION]["idusuario"] > 0 ||
                (bool) $_SESSION[Usuario::SESSION]["tipo_usuario"] !== $tipoUsuario
        ) {
            header("Location: /admin/login");
            exit();
        }
    }

    public static function logout() {
        $_SESSION[Usuario::SESSION] = null;
    }

    public static function listAll() {
        $sql = new Sql();
        return $sql->select("SELECT * FROM usuario");
    }

    public function save(): array {
        $sql = new Sql();

        /* A inserção de dados será pelo conceito de procedures, para que não haja várias requisições desnecessárias */
        $result = $sql->select("CALL sp_users_save(:nome, :login, :senha)", array(
            ":nome" => $this->getnome(),
            ":login" => $this->getlogin(),
            ":senha" => $this->getsenha()
        ));

        $this->setData($result[0]);
    }

}

?>