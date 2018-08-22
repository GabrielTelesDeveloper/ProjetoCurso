<?php

session_start();

/* Página de Rotas */
require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use Hcode\PageAdmin;
use Hcode\Model\Usuario;

$app = new Slim();

$app->config('debug', true);

/* Rota do home */
$app->get('/', function() {

    $page = new Page();
    /* Carrega o conteúdo */
    $page->setTpl("index");
});

/* Rota do admin */
$app->get('/admin', function() {

    Usuario::verificarLogin();

    $page = new PageAdmin();

    /* Carrega o template da página */
    $page->setTpl("index");
});

/* Rota de login */
$app->get('/admin/login', function() {

    /* Desabilitando o header e footer padrão do template */
    $page = new PageAdmin([
        "header" => false,
        "footer" => false
    ]);

    /* Carrega o template da página */
    $page->setTpl("login");
});


/* Recebe os dados do formulário e faz a validação do login */
$app->post("/admin/login", function () {
    Usuario::login($_POST['login'], $_POST['senha']);

    header("Location: /admin");
    exit;
});

/* Fazer logout */
$app->get("/admin/logout", function() {
    Usuario::logout();

    /* Redireciona para a tela de login */
    header("Location: /admin/login");
    exit();
});

/* Tela de listagem de usuários */
$app->get("/admin/users", function() {

    Usuario::verificarLogin();

    $usuarios = Usuario::listAll();

    $page = new PageAdmin();

    /* Após fazer o método que faz a consulta e armazenar os valores nele, devemos passar isso para o template */
    $page->setTpl("users", array(
        "users" => $usuarios
    ));
});

/* Criar usuario via html */
$app->get("/admin/users/create", function() {

    Usuario::verificarLogin();

    $page = new PageAdmin();

    $page->setTpl("users-create");
});

$app->get("admin/users/:idusuario/delete", function ($idusuario) {

    Usuario::verificarLogin();
});

/* Atualizar usuário, passando o id */
$app->get("/admin/users/:idusuario", function($idusuario) {

    Usuario::verificarLogin();

    $page = new PageAdmin();

    $page->setTpl("users-update");
});

/* Pega os dados do get e salva no banco de dados */
$app->post("/admin/users/create", function () {

    Usuario::verificarLogin();

    $user = new Usuario();
    
    #$_POST['tipo_usuario'] = (isset($_POST['tipo_usuario'])) ? 1 : 0;

    /* Pega todos os dados da url, e para salvar esses dados é necessário passar para o método GET */
    $user->setData($_POST);
    
    //var_dump($user);

    /* Executa o insert dentro do banco de dados */
    $user->save();

    /* header("Location: /admin/users");
      exit(); */
}); 

$app->post("/admin/users/:idusuario", function ($idusuario) {

    Usuario::verificarLogin();
});

/* Faz o funcionamento */
$app->run();
?>