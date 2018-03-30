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

        $term = mysqli_real_escape_string($this->conn,$data->term);

        $sql =
        "
        SELECT
        tb3.endereco_codigo as id,
        concat('Cep: ', ifnull(tb3.endereco_cep,tb1.cidade_cep) ,' | Cidade: ', tb1.cidade_descricao, ' | Bairro: ', ifnull(tb2.bairro_descricao,'Centro'), ' | Logradouro: ', ifnull(tb3.endereco_logradouro,'')) as label,
        tb1.cidade_descricao as cidade,
        ifnull(tb2.bairro_descricao,'Centro') as bairro,
        ifnull(tb3.endereco_logradouro,'') as logradouro,
        ifnull(tb3.endereco_cep,tb1.cidade_cep) as value
        from
        cep.cidade tb1
        left join
        cep.bairro tb2
        on tb1.cidade_codigo = tb2.cidade_codigo
        left JOIN
        cep.endereco tb3
        on tb2.bairro_codigo = tb3.bairro_codigo
        where (ifnull(tb3.endereco_cep,tb1.cidade_cep) like '%$term%') or (tb1.cidade_descricao like '%$term%')
        order by
        tb1.cidade_descricao,
        tb2.bairro_descricao,
        tb3.endereco_logradouro
        limit 10
        ";

        $result = $this->_select_fetch_all($sql);

        // Retorna os dados em formato JSON
        echo json_encode($result);

    }

}

$endereco = new endereco($_GET['_action']);