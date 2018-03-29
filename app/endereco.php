<?php

require_once('baseClass.php');

class endereco extends baseClass {

    // Lista de métodos disponíveis nesta classe
    protected $actions = array(
        'get_endereco'
    );

    // Recebe o CEP como parâmetro
    // Retorna dados de endereço
    public function get_endereco(){


        $data = (object) $_GET;

        $term = mysql_real_escape_string($data->term);

        $sql =
        "
        SELECT
        tb3.endereco_codigo as id,
        concat('Cep: ', tb3.endereco_cep ,' | Cidade: ', tb1.cidade_descricao, ' | Bairro: ', tb2.bairro_descricao, ' | Logradouro: ', tb3.endereco_logradouro) as label,
        tb1.cidade_descricao as cidade,
        tb2.bairro_descricao as bairro,
        tb3.endereco_logradouro as logradouro,
        tb3.endereco_cep as value
        from
        cep.cidade tb1
        join
        cep.bairro tb2
        on tb1.cidade_codigo = tb2.cidade_codigo
        JOIN
        cep.endereco tb3
        on tb2.bairro_codigo = tb3.bairro_codigo
        where (tb3.endereco_cep like '%$term%')
        order by
        tb1.cidade_descricao,
        tb2.bairro_descricao,
        tb3.endereco_logradouro
        ";

        $result = $this->_select_fetch_all($sql);

        // Retorna os dados em formato JSON
        echo json_encode($result);

    }

}

$endereco = new endereco($_GET['_action']);