<?php

namespace Hcode;

use Rain\Tpl;

class Page {

    private $tpl;
    private $options = [];

    /* As variáveis virão de acordo com determinada rota */
    private $defaults = [
        "header" => true,
        "footer" => true,
        "data" => []
    ];

    public function __construct($opts = array(), $tpl_dir = "/views/") {

        /* Mescla os arrays e guarda dentro da variável options, e esses dados serão passados para chave data */
        $this->options = array_merge($this->defaults, $opts);

        /* Configurar o template Importante */
        $config = array(
            "tpl_dir" => $_SERVER["DOCUMENT_ROOT"] . $tpl_dir,
            "cache_dir" => $_SERVER["DOCUMENT_ROOT"] . "/views-cache/",
            "debug" => true
        );
        Tpl::configure($config);

        /* Criar o objeto TPL */
        $this->tpl = new Tpl();

        $this->setData($this->options["data"]);

        /* Desenhar o template na tela */
        if ($this->options['header'] == true)
            $this->tpl->draw("header");
    }

    /* Conteúdo da página */

    public function setTpl($name, $data = array(), $returnHTML = false) {
        $this->setData($data);
        return $this->tpl->draw($name, $returnHTML);
    }

    private function setData($data = array()) {

        foreach ($data as $key => $value) {
            $this->tpl->assign($key, $value);
        }
    }

    public function __destruct() {
        if ($this->options['footer'] == true)
            $this->tpl->draw("footer");
    }

}

?>