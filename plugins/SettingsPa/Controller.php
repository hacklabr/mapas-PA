<?php

namespace SettingsPa;

use MapasCulturais\App;
use MapasCulturais\i;
use MapasCulturais\Connection;

class Controller extends \MapasCulturais\Controllers\EntityController
{
    use \MapasCulturais\Traits\ControllerAPI;

    function __construct()
    {
    }

    public function GET_registrationEmpty()
    {
        $this->requireAuthentication();

        $app = App::i();
        $em = $app->em;

        if ($app->user->is('saasAdmin')) {
            $app->pass();
        }

        /** @var Connection $conn */
        $conn = $em->getConnection();

        $delete = isset($_GET['deleteFile']) ? true : false;
        $count_file = isset($_GET['countFile']) ? true : false;

        $empty_files = [];
        if ($registration_numbers = $this->registrationFileEmpty()) {
            foreach ($registration_numbers as $number) {
                if ($registrations = $conn->fetchAll("SELECT * FROM registration where number = '{$number}'")) {

                    foreach ($registrations as $registration) {

                        $id = $registration['id'];
                        if ($files = $conn->fetchAll("SELECT * FROM file where object_type = 'MapasCulturais\Entities\Registration' AND object_id = {$id}")) {

                            foreach ($files as $file) {
                                $file_name = $file['name'];
                                $path_file = "/var/www/var/private-files/registration/{$id}/{$file_name}";
                                if (file_exists($path_file) && filesize($path_file) === 0) {
                                    $empty_files[$file['id']] = $path_file;
                                }
                            }
                        }
                    }
                }
            }
        }

        if($delete && $empty_files) {
            $delete_files = array_keys($empty_files);
            $delete_files = implode(",", $delete_files);
            $conn->executeQuery("DELETE FROM file where id in ({$delete_files})");

            foreach($empty_files as $file) {
                unlink($file);
            }
        } 

        dump($empty_files);

        if($count_file) {
            dump(count($empty_files)); 
        }

    }
    
    public function GET_querys()
    {

        $this->requireAuthentication();

        $app = App::i();

        if (!$app->user->is('admin')) {
            return;
        }

        $em = $app->em;
        $conn = $em->getConnection();

        // $total_oportunidades = $conn->fetchColumn("select count(*) as total from opportunity");
        $total_oportunidades_publicados = $conn->fetchColumn("select count(*) as total from opportunity where status > 0");
        $total_oportunidades_oficiais = $conn->fetchColumn("select count(*) as total from  opportunity o where id in (select sr.object_id from seal_relation sr where sr.object_type = 'MapasCulturais\Entities\Opportunity' and sr.status = 1 and sr.seal_id in ('1','2','3','4')) and o.parent_id is null");
        $total_oportunidades_rascunho = $conn->fetchColumn("select count(*) as total from opportunity where status = 0");
        $total_oportunidades_lixeira = $conn->fetchColumn("select count(*) as total from opportunity where status = -10");
        $total_oportunidades_arquivado = $conn->fetchColumn("select count(*) as total from opportunity where status = -2");
        // Oportunidades por area de atuação
        $results = $conn->fetchAll("
            select 
                t.term as area_atuacao,
                count(*) as total
            from 
                term_relation tr 
            join 
                term t on t.id = tr.term_id 
            join 
                opportunity o on o.id = tr.object_id and o.status > 0
            where 
                tr.object_type = 'MapasCulturais\Entities\Opportunity' and 
                t.taxonomy = 'area'
            group by area_atuacao
        ");
        foreach ($results as $_key => $_value) {
            $total_oportunidade_por_area_atuacao[$_value['area_atuacao']] = $_value['total'];
        }

        // $total_espacos = $conn->fetchColumn("select count(*) as total from space");
        $total_espacos_publicados = $conn->fetchColumn("select count(*) as total from space where status > 0");
        $total_espacos_oficiais = $conn->fetchColumn("select count(*) as total from  space e where e.id in (select sr.object_id from seal_relation sr where sr.object_type = 'MapasCulturais\Entities\Space' and sr.status = 1 and sr.seal_id in (1,2,3,4)) and e.parent_id is null");
        $total_espacos_rascunho = $conn->fetchColumn("select count(*) as total from space where status = 0");
        $total_espacos_lixeira = $conn->fetchColumn("select count(*) as total from space where status = -10");
        $total_espacos_arquivado = $conn->fetchColumn("select count(*) as total from space where status = -2");
        // Espaços por area de atuação
        $results = $conn->fetchAll("
            select 
                t.term as area_atuacao,
                count(*) as total
            from 
                term_relation tr 
            join 
                term t on t.id = tr.term_id 
            join 
                space e on e.id = tr.object_id and e.status > 0
            where 
                tr.object_type = 'MapasCulturais\Entities\Space' and 
                t.taxonomy = 'area'
            group by area_atuacao
        ");
        foreach ($results as $_key => $_value) {
            $total_espaco_por_area_atuacao[$_value['area_atuacao']] = $_value['total'];
        }

        // $total_projetos = $conn->fetchColumn("select count(*) as total from project");
        $total_projetos_publicados = $conn->fetchColumn("select count(*) as total from project where status > 0");
        $total_projetos_oficiais = $conn->fetchColumn("select count(*) as total from  project e where e.id in (select sr.object_id from seal_relation sr where sr.object_type = 'MapasCulturais\Entities\Project' and sr.status = 1 and sr.seal_id in (1,2,3,4)) and e.parent_id is null");
        $total_projetos_rascunho = $conn->fetchColumn("select count(*) as total from project where status = 0");
        $total_projetos_lixeira = $conn->fetchColumn("select count(*) as total from project where status = -10");
        $total_projetos_arquivado = $conn->fetchColumn("select count(*) as total from project where status = -2");

        $total_inscricoes = $conn->fetchColumn("select count(*) as total from registration");



        $total_agentes = $conn->fetchColumn("SELECT count(*) as Total from agent WHERE status > 0");
        $total_agentes_oficiais = $conn->fetchColumn("select count(*) as total from  agent e where e.id in (select sr.object_id from seal_relation sr where sr.object_type = 'MapasCulturais\Entities\Agent' and sr.status = 1 and sr.seal_id in (1,2,3,4)) and e.parent_id is null");
        $total_agentes_inscritos_em_editais = $conn->fetchColumn("select count(*) as total from agent a where a.id in (select r.agent_id from registration r where status > 0) AND a.status > 0");
        $total_agentes_nunca_inscritos_em_editais = $conn->fetchColumn("select count(*) as total from agent a where a.id not in (select r.agent_id from registration r) AND a.status > 0 and a.type = 1");
        $total_agentes_rascunhos = $conn->fetchColumn("SELECT count(*) as Total from agent where status = 0");
        $total_agentes_lixeira = $conn->fetchColumn("SELECT count(*) as Total from agent where status = -10");
        $total_agentes_arquivados = $conn->fetchColumn("SELECT count(*) as Total from agent where status = -2");
        $total_agentes_individual = $conn->fetchColumn("select count(*) from agent a where type = 1 and status > 0");
        $total_agentes_coletivo = $conn->fetchColumn("select count(*) from agent a where type = 2 and status > 0");
        $total_agentes_idoso = $conn->fetchColumn("select count(*) from agent_meta am  join agent a on a.id = am.object_id where am.key = 'idoso' and value <> '0' and a.status > 0");
        $outras_comunidades_tradicionais = $conn->fetchColumn("select count(*) as total from agent_meta am where am.key = 'comunidadesTradicionalOutros'");

        ####### INSCRICOES ########

        // Total de inscrições suplente
        $total_inscricoes_nao_suplente = $conn->fetchColumn("
            select 
                count(distinct number) 
            from 
                registration r 
            where 
            r.status = 8
        ");

        // Total de inscrições inválidas
        $total_inscricoes_nao_invalidas = $conn->fetchColumn("
            select 
                count(distinct number) 
            from 
                registration r 
            where 
            r.status = 2
        ");

        // Total de inscrições não selecionadas
        $total_inscricoes_nao_selecionadas = $conn->fetchColumn("
            select 
                count(distinct number) 
            from 
                registration r 
            join 
                seal_relation sr on sr.object_id = r.opportunity_id and sr.object_type = 'MapasCulturais\Entities\Opportunity'
            where 
            r.status = 3 and
            sr.seal_id in (1,2,3,4)
        ");

        // Total de inscrições enviadas
        $total_inscricoes_enviadas = $conn->fetchColumn("
            select 
                count(*) 
            from 
                registration r
            where 
                r.opportunity_id in (
                    select 
                        distinct o.id 
                    from 
                        opportunity o 
                    join 
                        seal_relation sr on sr.object_id = o.id and sr.object_type = 'MapasCulturais\Entities\Opportunity'
                    where 
                        o.parent_id is null and 
                        o.status > 0 and
                        sr.seal_id in (1,2,3,4)
                )
            and r.status > 0
        ");

        // Total de inscrições rascunho
        $total_inscricoes_pendentes = $conn->fetchColumn("
            select 
                count(*) 
            from 
                registration r
            where 
                r.opportunity_id in (
                    select 
                        o.id 
                    from 
                        opportunity o 
                    join 
                        seal_relation sr on sr.object_id = o.id and sr.object_type = 'MapasCulturais\Entities\Opportunity'
                    where 
                        o.parent_id is null and 
                        o.status > 0 and
                        sr.seal_id in (1,2,3,4)
                )
            and r.status = 1
        ");

        // Total de inscrições rascunho
        $total_inscricoes_rascunho = $conn->fetchColumn("
            select 
                count(*) 
            from 
                registration r
            where 
                r.opportunity_id in (
                    select 
                        o.id 
                    from 
                        opportunity o 
                    join 
                        seal_relation sr on sr.object_id = o.id and sr.object_type = 'MapasCulturais\Entities\Opportunity'
                    where 
                        o.parent_id is null and 
                        o.status > 0 and
                        sr.seal_id in (1,2,3,4)
                )
            and r.status = 0
        ");

        // Total de inscrições selecionadas
        $total_inscricoes_selecionadas = $conn->fetchColumn("
            select 
                count(*) 
            from 
                registration r 
            where 
                r.opportunity_id 
            in (
                select o.id 
            from 
                opportunity o
            join 
                opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
            where
                o.parent_id in (
                    select 
                        sr.object_id 
                    from 
                        seal_relation sr 
                    where 
                        sr.seal_id 
                    in 
                        (1,2,3,4) and sr.object_type = 'MapasCulturais\Entities\Opportunity'
                )
            )  and 
            r.status = 10
        ");

        // Total de inscrições pendentes
        $total_inscricoes_pendentes_na_ultima_fase = $conn->fetchColumn("
            select 
                count(*) 
            from 
                registration r 
            where 
                r.opportunity_id 
            in (
                select o.id 
            from 
                opportunity o
            join 
                opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
            where
                o.parent_id in (
                    select 
                        sr.object_id 
                    from 
                        seal_relation sr 
                    where 
                        sr.seal_id 
                    in 
                        (1,2,3,4) and sr.object_type = 'MapasCulturais\Entities\Opportunity'
                )
            )  and 
            r.status = 1
        ");

        // Total de inscrições pendentes
        $total_inscricoes_rascunhos_na_ultima_fase = $conn->fetchColumn("
            select 
                count(*) 
            from 
                registration r 
            where 
                r.opportunity_id 
            in (
                select o.id 
            from 
                opportunity o
            join 
                opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
            where
                o.parent_id in (
                    select 
                        sr.object_id 
                    from 
                        seal_relation sr 
                    where 
                        sr.seal_id 
                    in 
                        (1,2,3,4) and sr.object_type = 'MapasCulturais\Entities\Opportunity'
                )
            )  and 
            r.status = 0
        ");

        ####### AGENTES########

        // Total de agentes NÃO CONTEMPLADOS em editais
        $total_agentes_nao_contemplados_em_editais = $conn->fetchColumn("
            select 
                count(*)
            from 
                agent a
            where 
                a.id 
            in (
                select 
                    r.agent_id
                from 
                    registration r 
                where 
                    r.opportunity_id 
                in (
                    select 
                        o.id 
                    from 
                        opportunity o
                    join 
                        opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                )  and 
                r.status in (2,3,8)
            ) 
        ");

        // Total de agentes CONTEMPLADOS em algum edital
        $total_agentes_contemplados_em_editais = $conn->fetchColumn("
            select 
                count(*)
            from 
                agent a
            where 
                a.id 
            in (
                select 
                    r.agent_id
                from 
                    registration r 
                where 
                    r.opportunity_id 
                in (
                    select 
                        o.id 
                    from 
                        opportunity o
                    join 
                        opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                )  and 
                r.status = 10
            )
        ");

        // Total de agentes com CPF e CNPJ (MEI)
        $total_agente_MEI = $conn->fetchColumn("
            select 
                count(*) as total 
            from 
                agent_meta am 
            join agent a on a.id = am.object_id 
            where 
                am.key = 'cpf' and 
                am.object_id 
            in (
                select 
                    am2.object_id  
                from 
                    agent_meta am2 
                where 
                    am2.key = 'cnpj' and 
                    am2.value is not null AND trim(am2.value) <> ''
            ) and 
            a.status > 0
        ");

        //Total de agentes somente com CNPJ (Pessoa Jurídica)
        $total_agente_pessoa_juridica = $conn->fetchColumn("
            select 
                count(*) as total 
            from 
                agent_meta am 
            join 
                agent a on a.id = am.object_id 
            where 
                am.key = 'cnpj' and 
                am.object_id 
            not in (
                select 
                    am2.object_id 
                from 
                    agent_meta am2 
                where 
                    am2.key = 'cpf' and 
                    (am2.value is null or trim(am2.value) = '')
            ) and 
            A.status > 0
        ");

        // Total de agentes somente com CPF (Pessoa Física)'
        $total_agente_pessoa_fisica = $conn->fetchColumn("
            select 
                count(*) as total 
            from 
                agent_meta am 
            join 
                agent a on a.id = am.object_id 
            where 
                am.key = 'cpf' and 
                am.object_id 
            not in (
                select 
                    am2.object_id 
                from 
                    agent_meta am2 
                where 
                    (am2.key = 'cnpj') and 
                   ( am2.value is null or trim(am2.value) = '')
            ) and 
            A.status > 0
        ");


        // Agentes individuais por area de atuação
        $results = $conn->fetchAll("
            select 
                t.term as area_atuacao,
                count(*) as total
            from 
                term_relation tr 
            join 
                term t on t.id = tr.term_id 
            join 
                agent a on a.id = tr.object_id and a.status > 0 and a.type = 1
            where 
                tr.object_type = 'MapasCulturais\Entities\Agent' and 
                t.taxonomy = 'area'
            group by area_atuacao
        ");
        foreach ($results as $_key => $_value) {
            $total_agentes_individual_por_area_atuacao[$_value['area_atuacao']] = $_value['total'];
        }


        // Agentes coletivo por area de atuação
        $results = $conn->fetchAll("
            select 
                t.term as area_atuacao,
                count(*) as total
            from 
                term_relation tr 
            join 
                term t on t.id = tr.term_id 
            join 
                agent a on a.id = tr.object_id and a.status > 0 and a.type = 2
            where 
                tr.object_type = 'MapasCulturais\Entities\Agent' and 
                t.taxonomy = 'area'
            group by area_atuacao
        ");
        foreach ($results as $_key => $_value) {
            $total_agentes_coletivo_por_area_atuacao[$_value['area_atuacao']] = $_value['total'];
        }

        // Agentes individuais por segmento cultural
        $results = $conn->fetchAll("
            select 
                t.term as segmento,
                count(*) as total
            from 
                term_relation tr 
            join 
                term t on t.id = tr.term_id 
            join 
                agent a on a.id = tr.object_id and a.status > 0 and a.type = 1
            where 
                tr.object_type = 'MapasCulturais\Entities\Agent' and 
                t.taxonomy = 'segmento'
            group by segmento
        ");
        foreach ($results as $_key => $_value) {
            $total_agentes_individual_por_segmento_cultural[$_value['segmento']] = $_value['total'];
        }

        // Agentes coletivos por segmento cultural
        $results = $conn->fetchAll("
            select 
                t.term as segmento,
                count(*) as total
            from 
                term_relation tr 
            join 
                term t on t.id = tr.term_id 
            join 
                agent a on a.id = tr.object_id and a.status > 0 and a.type = 2
            where 
                tr.object_type = 'MapasCulturais\Entities\Agent' and 
                t.taxonomy = 'segmento'
            group by segmento
        ");
        foreach ($results as $_key => $_value) {
            $total_agentes_coletivos_por_segmento_cultural[$_value['segmento']] = $_value['total'];
        }

        // Individuais sem CNPJ
        $total_agentes_individuais_com_cpf_sem_cnpj = $conn->fetchColumn("
            select 
                count(*) as total 
            from 
                agent a 
            where 
                a.type = 1 and 
                a.status > 0 and
                a.id not in (select am.object_id from agent_meta am where am.key = 'cnpj' and (am.value is null or trim(am.value) = ''))
        ");

        // Individuais com CNPJ
        $total_agentes_individuais_com_cpf_com_cnpj = $conn->fetchColumn("
            select 
                count(*) as total 
            from 
                agent a 
            where 
                a.type = 1 and 
                a.status > 0 and
                a.id in (select am.object_id from agent_meta am where am.key = 'cnpj' and am.value is not null)	
        ");

        // Coletivos sem CNPJ
        $total_agentes_coletivos_com_cpf_sem_cnpj = $conn->fetchColumn("
          select 
              count(*) as total 
          from 
              agent a 
          where 
              a.type = 2 and 
              a.status > 0 and
              a.id not in (select am.object_id from agent_meta am where am.key = 'cnpj' and trim(am.value) <> '')
      ");

        // Coletivos com CNPJ
        $total_agentes_coletivos_com_cpf_com_cnpj = $conn->fetchColumn("
          select 
              count(*) as total 
          from 
              agent a 
          where 
              a.type = 2 and 
              a.status > 0 and
              a.id in (select am.object_id from agent_meta am where am.key = 'cnpj' and am.value is not null)
      ");

        // Comunidade tadicional
        $results = $conn->fetchAll("select 
                                    CASE 
                                        WHEN am.value IS NULL OR am.value = '' THEN 'Não Informado'
                                        else am.value
                                    END AS comunidade,
                                    count(*) as total
                                from 
                                    agent_meta am 
                                join 
                                    agent a on a.id = am.object_id and a.status > 0
                                where 
                                    am.key = 'comunidadesTradicional'
                                group by 
                                    comunidade");
        foreach ($results as $_key => $_value) {
            $comunidadeTradicional[$_value['comunidade']] = $_value['total'];
        }

        // Pessoa com deficiencia
        $results = $conn->fetchAll("
            select 
                CASE 
                    WHEN am.value IS NULL OR am.value = '' THEN 'Não Informado'
                    else am.value
                END AS pessoaDeficiente,
                count(*) as total
            from 
                agent_meta am 
            join agent a on a.id = am.object_id and a.status > 0
            where 
                am.key = 'pessoaDeficiente'
            group by 
                pessoaDeficiente
        ");

        foreach ($results as $_key => $_value) {
            $pessoaDeficiente[$_value['pessoadeficiente']] = $_value['total'];
        }

        // Faixa de idade
        $results = $conn->fetchAll("
                                SELECT 
                                CONCAT(FLOOR((idade::numeric / 10)) * 10, ' - ', FLOOR((idade::numeric / 10) + 1) * 10 - 1) AS faixa_idade,
                                COUNT(*) AS total
                            FROM (
                                SELECT 
                                    CASE 
                                        WHEN EXTRACT(YEAR FROM age(current_date, am.value::date)) < 1 THEN '0' 
                                        WHEN EXTRACT(YEAR FROM age(current_date, am.value::date)) > 120 THEN '121' 
                                        ELSE EXTRACT(YEAR FROM age(current_date, am.value::date))::text 
                                    END AS idade
                                FROM 
                                    agent_meta am 
                                    join agent a on am.object_id = a.id and a.status > 0
                                    
                                WHERE 
                                    am.key = 'dataDeNascimento' AND
                                    am.value <> '' and
                                    a.type = 1
                            ) AS subquery
                            GROUP BY 
                                CONCAT(FLOOR((idade::numeric / 10)) * 10, ' - ', FLOOR((idade::numeric / 10) + 1) * 10 - 1)
                            ORDER BY 
                                faixa_idade;
                            ");

        foreach ($results as $_key => $_value) {
            $faixas_de_idade[$_value['faixa_idade']] = $_value['total'];
        }

        // tempo de funcao
        $results = $conn->fetchAll("
                        SELECT 
                        CONCAT(FLOOR((idade::numeric / 10)) * 10, ' - ', FLOOR((idade::numeric / 10) + 1) * 10 - 1) AS faixa_idade,
                        COUNT(*) AS total
                    FROM (
                        SELECT 
                            CASE 
                                WHEN EXTRACT(YEAR FROM age(current_date, am.value::date)) < 1 THEN '0' 
                                WHEN EXTRACT(YEAR FROM age(current_date, am.value::date)) > 120 THEN '121' 
                                ELSE EXTRACT(YEAR FROM age(current_date, am.value::date))::text 
                            END AS idade
                        FROM 
                            agent_meta am 
                            join agent a on am.object_id = a.id and a.status > 0
                        WHERE 
                            am.key = 'dataDeNascimento' AND
                            am.value <> '' and
                            a.type = 2
                    ) AS subquery
                    GROUP BY 
                        CONCAT(FLOOR((idade::numeric / 10)) * 10, ' - ', FLOOR((idade::numeric / 10) + 1) * 10 - 1)
                    ORDER BY 
                        faixa_idade;
                    ");

        foreach ($results as $_key => $_value) {
            $tempo_funcao[$_value['faixa_idade']] = $_value['total'];
        }



        $results = $conn->fetchAll("
            select 
                o.id, o.name 
            from 
                opportunity o 
            join 
                seal_relation sr on sr.object_id = o.id and sr.object_type = 'MapasCulturais\Entities\Opportunity'
            where 
                o.status > 0 and 
                o.parent_id is null and
                sr.seal_id in (1,2,3,4)
            order by o.name
        ");

        $inscricoes_por_oportunidade = [];
        foreach ($results as $values) {
            $inscricoes_por_oportunidade["#{$values['id']} -- " . $values['name']] = [
                'Rascunho' =>  $conn->fetchOne("
                    select 
                        count(*) 
                    from 
                        registration r
                    
                    where 
                        r.opportunity_id = {$values['id']}
                    and r.status = 0
                "),
                'Enviadas' =>  $conn->fetchOne("
                    select 
                        count(*) 
                    from 
                        registration r
                    where 
                        r.opportunity_id = {$values['id']}
                    and r.status > 0
                "),
                'Selecionadas' =>  $conn->fetchOne("
                    select 
                        count(*) 
                    from 
                        registration r 
                    where 
                        r.opportunity_id 
                    in (
                        select 
                            o.id 
                        from 
                            opportunity o
                        join 
                            opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                        WHERE 
                            o.parent_id = {$values['id']}
                    )  and 
                    r.status = 10
                "),
                'Suplentes' =>  $conn->fetchOne("
                    select 
                        count(distinct number) 
                    from 
                        registration r 
                    where 
                        r.opportunity_id = {$values['id']} and 
                        r.status = 8
                "),
                'Não selecionadas' =>  $conn->fetchOne("
                    select 
                        count(distinct number) 
                    from 
                        registration r 
                    where 
                        r.opportunity_id = {$values['id']} and 
                        r.status = 3
                "),
                'Inválidas' =>  $conn->fetchOne("
                select 
                    count(distinct number) 
                from 
                    registration r 
                where 
                    r.opportunity_id = {$values['id']} and 
                    r.status = 2
                "),
                'Pendentes na última fase' =>  $conn->fetchOne("
                    select 
                        count(*) 
                    from 
                        registration r 
                    where 
                        r.opportunity_id 
                    in (
                        select 
                            o.id 
                        from 
                            opportunity o
                        join 
                            opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                        WHERE 
                            o.parent_id = {$values['id']}
                    )  and 
                    r.status = 1
                "),
                'Rascunhos na última fase' =>  $conn->fetchOne("
                select 
                    count(*) 
                from 
                    registration r 
                where 
                    r.opportunity_id 
                in (
                    select 
                        o.id 
                    from 
                        opportunity o
                    join 
                        opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                    WHERE 
                        o.parent_id = {$values['id']}
                )  and 
                r.status = 0
            "),
            ];
        }

        $results = $conn->fetchAll("
            select 
                id, name 
            from 
                opportunity o 
            where 
                o.status > 0 and 
                o.parent_id is null AND
                o.object_type = 'MapasCulturais\Entities\Project' AND
                o.object_id in (1274,1278)
            order by o.name
        ");


        $inscricoes_por_oportunidade_paulo_gustavo = [];
        foreach ($results as $values) {
            $inscricoes_por_oportunidade_paulo_gustavo["#{$values['id']} -- " . $values['name']] = [
                'Rascunho' =>  $conn->fetchOne("
                    select 
                        count(*) 
                    from 
                        registration r
                    where 
                        r.opportunity_id = {$values['id']}
                    and r.status = 0
                "),
                'Enviadas' =>  $conn->fetchOne("
                    select 
                        count(*) 
                    from 
                        registration r
                    where 
                        r.opportunity_id = {$values['id']}
                    and r.status > 0
                "),
                'Selecionadas' =>  $conn->fetchOne("
                    select 
                        count(*) 
                    from 
                        registration r 
                    where 
                        r.opportunity_id 
                    in (
                        select 
                            o.id 
                        from 
                            opportunity o
                        join 
                            opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                        WHERE 
                            o.parent_id = {$values['id']}
                    )  and 
                    r.status = 10
                "),
                'Suplentes' =>  $conn->fetchOne("
                    select 
                        count(*) 
                    from 
                        registration r 
                    where 
                        r.opportunity_id 
                    in (
                        select 
                            o.id 
                        from 
                            opportunity o
                        join 
                            opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                        WHERE 
                            o.parent_id = {$values['id']}
                    )  and 
                    r.status = 8
                "),
                'Não selecionadas' =>  $conn->fetchOne("
                    select 
                        count(*) 
                    from 
                        registration r 
                    where 
                        r.opportunity_id 
                    in (
                        select 
                            o.id 
                        from 
                            opportunity o
                        join 
                            opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                        WHERE 
                            o.parent_id = {$values['id']}
                    )  and 
                    r.status = 3
                "),
                'Inválidas' =>  $conn->fetchOne("
                    select 
                        count(*) 
                    from 
                        registration r 
                    where 
                        r.opportunity_id 
                    in (
                        select 
                            o.id 
                        from 
                            opportunity o
                        join 
                            opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                        WHERE 
                            o.parent_id = {$values['id']}
                    )  and 
                    r.status = 2
                "),
                'Pendente na última fase' =>  $conn->fetchOne("
                    select 
                        count(*) 
                    from 
                        registration r 
                    where 
                        r.opportunity_id 
                    in (
                        select 
                            o.id 
                        from 
                            opportunity o
                        join 
                            opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                        WHERE 
                            o.parent_id = {$values['id']}
                    )  and 
                    r.status = 1
                "),
                'Rascunho na última fase' =>  $conn->fetchOne("
                    select 
                        count(*) 
                    from 
                        registration r 
                    where 
                        r.opportunity_id 
                    in (
                        select 
                            o.id 
                        from 
                            opportunity o
                        join 
                            opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                        WHERE 
                            o.parent_id = {$values['id']}
                    )  and 
                    r.status = 0
                "),
            ];
        }

        $sem_inscricao_enviada = [];
        foreach ($results as $values) {
            $r = $conn->fetchOne("
                select 
                    count(*) 
                from 
                    registration r
                where 
                    r.opportunity_id = {$values['id']}
                and r.status > 0
            ");

            if ($r == 0) {
                $sem_inscricao_enviada["#{$values['id']} -- " . $values['name']] = $r;
            }
        }

        // Total de inscrições suplente
        $total_inscricoes_suplente_paulo_gustavo = $conn->fetchColumn("
            select 
                count(*) 
            from 
                registration r 
            where 
                r.opportunity_id 
            in (
                select 
                    o.id 
                from 
                    opportunity o
                join 
                    opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                where 
                    o.object_type = 'MapasCulturais\Entities\Project' AND
                    o.object_id in (1274,1278)
            )  and 
            r.status = 8
        ");


        // Total de inscrições inválidas
        $total_inscricoes_invalidas_paulo_gustavo = $conn->fetchColumn("
            select 
                count(*) 
            from 
                registration r 
            where 
                r.opportunity_id 
            in (
                select 
                    o.id 
                from 
                    opportunity o
                join 
                    opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                where 
                    o.object_type = 'MapasCulturais\Entities\Project' AND
                    o.object_id in (1274,1278)
            )  and 
            r.status = 2
        ");

        // Total de inscrições não selecionadas
        $total_inscricoes_nao_selecionadas_paulo_gustavo = $conn->fetchColumn("
            select 
                count(*) 
            from 
                registration r 
            where 
                r.opportunity_id 
            in (
                select 
                    o.id 
                from 
                    opportunity o
                join 
                    opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                where 
                    o.object_type = 'MapasCulturais\Entities\Project' AND
                    o.object_id in (1274,1278)
            )  and 
            r.status = 3
        ");

        // Total de inscrições pendentes
        $total_inscricoes_paulo_pendentes_paulo_gustavo = $conn->fetchColumn("
            select 
                count(*) 
            from 
                registration r
            where 
                r.opportunity_id in (
                    select 
                        o.id 
                    from 
                        opportunity o 
                    where 
                        o.parent_id is null and 
                        o.status > 0 AND
                        o.object_type = 'MapasCulturais\Entities\Project' AND
                        o.object_id in (1274,1278)
                )
            and r.status = 1
        ");

        // Total de inscrições enviadas
        $total_inscricoes_enviadas_paulo_gustavo = $conn->fetchColumn("
            select 
                count(*) 
            from 
                registration r
            where 
                r.opportunity_id in (
                    select 
                        o.id 
                    from 
                        opportunity o 
                    where 
                        o.parent_id is null and 
                        o.status > 0 AND
                        o.object_type = 'MapasCulturais\Entities\Project' AND
                        o.object_id in (1274,1278)
                )
            and r.status > 0
        ");

        // Total de inscrições rascunho
        $total_inscricoes_rascunho_paulo_gustavo = $conn->fetchColumn("
            select 
                count(*) 
            from 
                registration r
            where 
                r.opportunity_id in (
                    select 
                        o.id 
                    from 
                        opportunity o 
                    where 
                        o.parent_id is null and 
                        o.status > 0 AND
                        o.object_type = 'MapasCulturais\Entities\Project' AND
                        o.object_id in (1274,1278)
                )
            and r.status = 0
        ");

        // Total de inscrições selecionadas
        $total_inscricoes_selecionadas_paulo_gustavo = $conn->fetchColumn("
            select 
                count(*) 
            from 
                registration r 
            where 
                r.opportunity_id 
            in (
                select 
                    o.id 
                from 
                    opportunity o
                join 
                    opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                where 
                    o.object_type = 'MapasCulturais\Entities\Project' AND
                    o.object_id in (1274,1278)
            )  and 
            r.status = 10
        ");

        // Total de inscrições rascunhos na última fase
        $total_inscricoes_rascunhos_na_última_fase_paulo_gustavo = $conn->fetchColumn("
            select 
                count(*) 
            from 
                registration r 
            where 
                r.opportunity_id 
            in (
                select 
                    o.id 
                from 
                    opportunity o
                join 
                    opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                where 
                    o.object_type = 'MapasCulturais\Entities\Project' AND
                    o.object_id in (1274,1278)
            )  and 
            r.status = 0
        ");

        // Total de inscrições rascunhos na última fase
        $total_inscricoes_pendente_na_última_fase_paulo_gustavo = $conn->fetchColumn("
            select 
                count(*) 
            from 
                registration r 
            where 
                r.opportunity_id 
            in (
                select 
                    o.id 
                from 
                    opportunity o
                join 
                    opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                where 
                    o.object_type = 'MapasCulturais\Entities\Project' AND
                    o.object_id in (1274,1278)
            )  and 
            r.status = 1
        ");

        $_data = [
            'OPORTUNIDADES',
            // 'Total de oportunidades' => $total_oportunidades[0],
            'Total de oportunidades publicadas' => $total_oportunidades_publicados[0],
            'Total de oportunidades oficiais' => $total_oportunidades_oficiais[0],
            'Total de oportunidades rascunho' => $total_oportunidades_rascunho[0],
            'Total de oportunidades lixeira' => $total_oportunidades_lixeira[0],
            'Total de oportunidades arquivados' => $total_oportunidades_arquivado[0],
            'Total de oportunidades por área de interesse' => $total_oportunidade_por_area_atuacao,
            'Oportunidades sem inscrições enviadas' => $sem_inscricao_enviada,
            'ESPAÇOS',
            // 'Total de espacos' => $total_espacos[0],
            'Total de espacos publicados' => $total_espacos_publicados[0],
            'Total de espacos oficiais' => $total_espacos_oficiais[0],
            'Total de espacos rascunho' => $total_espacos_rascunho[0],
            'Total de espacos lixeira' => $total_espacos_lixeira[0],
            'Total de espacos arquivados' => $total_espacos_arquivado[0],
            'Total de espacos por área de atuação' => $total_espaco_por_area_atuacao,
            'PROJETOS',
            // 'Total de projetos' => $total_projetos[0],
            'Total de projetos publicados' => $total_projetos_publicados[0],
            'Total de projetos oficiais' => $total_projetos_oficiais[0],
            'Total de projetos rascunho' => $total_projetos_rascunho[0],
            'Total de projetos lixeira' => $total_projetos_lixeira[0],
            'Total de projetos arquivados' => $total_projetos_arquivado[0],
            'INSCRIÇÕES',
            'Total de inscrições enviadas' => $total_inscricoes_enviadas[0],
            'Total de inscrições rascunho' => $total_inscricoes_rascunho[0],
            'Total de inscrições pendentes' => $total_inscricoes_pendentes[0],
            'Total de inscrições selecionadas' => $total_inscricoes_selecionadas[0],
            'Total de inscrições não selecionadas' => $total_inscricoes_nao_selecionadas[0],
            'Total de inscrições inválidas' => $total_inscricoes_nao_invalidas[0],
            'Total de inscrições suplente' => $total_inscricoes_nao_suplente[0],
            'Total de inscricao pendentes na última fase' => $total_inscricoes_pendentes_na_ultima_fase[0],
            'Total de inscricao rascunhos na última fase' => $total_inscricoes_rascunhos_na_ultima_fase[0],
            'Inscrições por oportunidade' => $inscricoes_por_oportunidade,
            'INSCRIÇÕES - PAULO GUSTAVO',
            'Total de inscrições enviadas - PAULO GUSTAVO' => $total_inscricoes_enviadas_paulo_gustavo[0],
            'Total de inscrições rascunho - PAULO GUSTAVO' => $total_inscricoes_rascunho_paulo_gustavo[0],
            'Total de inscrições pendente - PAULO GUSTAVO' => $total_inscricoes_paulo_pendentes_paulo_gustavo[0],
            'Total de inscrições selecionadas - PAULO GUSTAVO' => $total_inscricoes_selecionadas_paulo_gustavo[0],

            'Total de inscrições rascunho na última fase - PAULO GUSTAVO' => $total_inscricoes_rascunhos_na_última_fase_paulo_gustavo[0],
            'Total de inscrições pendentes na última fase - PAULO GUSTAVO' => $total_inscricoes_pendente_na_última_fase_paulo_gustavo[0],

            'Total de inscrições não selecionadas - PAULO GUSTAVO' => $total_inscricoes_nao_selecionadas_paulo_gustavo[0],
            'Total de inscrições inválidas - PAULO GUSTAVO' => $total_inscricoes_invalidas_paulo_gustavo[0],
            'Total de inscrições suplente - PAULO GUSTAVO' => $total_inscricoes_suplente_paulo_gustavo[0],
            'Inscrições por oportunidade PAULO GUSTAVO' => $inscricoes_por_oportunidade_paulo_gustavo,
            'AGENTES',
            'Total de agentes publicados' => $total_agentes[0],
            'Total de agentes oficiais' => $total_agentes_oficiais[0],
            'Total de agentes COM inscrições' => $total_agentes_inscritos_em_editais[0],
            'Total de agentes SEM inscrições' => $total_agentes_nunca_inscritos_em_editais[0],
            'Total de agentes CONTEMPLADOS em algum edital' => $total_agentes_contemplados_em_editais[0],
            'Total de agentes NÃO CONTEMPLADOS em editais' => $total_agentes_nao_contemplados_em_editais[0],
            'Total de agentes rascunhos' => $total_agentes_rascunhos[0],
            'Total de agentes lixeira' => $total_agentes_lixeira[0],
            'Total de agentes arquivados' => $total_agentes_arquivados[0],

            'Total de agentes somente com CPF (Pessoa Física)' => $total_agente_pessoa_fisica[0],
            'Total de agentes somente com CNPJ (Pessoa Jurídica)' => $total_agente_pessoa_juridica[0],
            'Total de agentes com CPF e CNPJ (MEI)' => $total_agente_MEI[0],
            'Total de agentes coletivos SEM CNPJ' => $total_agentes_coletivos_com_cpf_sem_cnpj[0],

            'Total de agentes individual' => $total_agentes_individual[0],
            'Total de agentes coletivos' => $total_agentes_coletivo[0],
            // 'Total de agentes idoso' => $total_agentes_idoso[0],
            'Total de agentes comunidades tradicional' => $comunidadeTradicional,
            'Total de agentes outras comunidades tradicionais' => $outras_comunidades_tradicionais[0],
            // 'Total de agentes por pessoa com deficiência' => $pessoaDeficiente,
            'Total de agentes individual por faixa de idade' => $faixas_de_idade,
            // 'Total de agentes coletivo por tempo de fundação' => $tempo_funcao,
            // 'Total de agentes individuais SEM CNPJ' => $total_agentes_individuais_com_cpf_sem_cnpj[0],
            // 'Total de agentes individuais COM CNPJ' => $total_agentes_individuais_com_cpf_com_cnpj[0],
            // 'Total de agentes coletivos COM CNPJ' => $total_agentes_coletivos_com_cpf_com_cnpj[0],
            'Total de agentes individual por área de atuação' => $total_agentes_individual_por_area_atuacao,
            'Total de agentes coletivo por área de atuação' => $total_agentes_coletivo_por_area_atuacao,
            'Total de agentes individual por segmento cultural' => $total_agentes_individual_por_segmento_cultural,
            // 'Total de agentes coletivos por segmento cultural' => $total_agentes_coletivos_por_segmento_cultural,
        ];

        $multipleValues = [
            'raca' => (object) ['complement' => '', 'value' => 'raca'],
            'municipio' => (object) ['complement' => '', 'value' => 'En_Municipio'],
            'genero' => (object) ['complement' => '', 'value' => 'genero'],
            'escolaridade' => (object) ['complement' => '', 'value' => 'escolaridade'],
        ];

        foreach ($multipleValues as $key => $data) {
            $results = $conn->fetchAll("
                SELECT 
                    CASE 
                        WHEN normalized_value IS NULL OR normalized_value = '' THEN 'Não Informado' 
                        ELSE normalized_value 
                    END AS {$key},
                    COUNT(*) AS total
                FROM (
                    SELECT 
                        trim(unaccent(lower(CASE 
                            WHEN am.value IS NULL OR am.value = '' THEN 'Não Informado' 
                            ELSE am.value 
                        END))) AS normalized_value
                    FROM 
                        agent_meta am 
                    JOIN agent a on a.id = am.object_id and a.status > 0
                    WHERE 
                        am.key = '{$data->value}' AND
                        (am.value IS NOT NULL OR trim(am.value) = '') 
                ) AS subquery
                GROUP BY 
                    {$key}
                order by {$key} asc;
            ");

            $type = "Total de agentes por {$key}";
            foreach ($results as $_key => $_value) {
                $_data[$type][$_value[$key]] = $_value['total'];
            }
        }

        dump($_data);
    }

    public function buildQuery($values, $wheres = [], $joins = [])
    {
        $showPquery = $values['showQuery'] ?? null;
        $select = $values['select'] ?? "count(distinct(id))";
        $from = $values['from'];
        $group = $values['group'] ?: "";

        if (($join = $values['join'] ?? []) || $joins) {
            $where = array_merge($join, $joins);
            $join = implode("\n                    ", $join);
            $join = "{$join}";
        }

        if (($where = $values['where'] ?? []) || $wheres) {
            $where = array_merge($where, $wheres);
            $where = implode(") AND (", $where);
            $where = "({$where})";
        }

        $join = $join ?: "";
        $where = $where ?: "";


        $sql = "
                SELECT 
                    {$select}
                FROM
                    {$from}
                    {$join}
                WHERE
                    {$where}
                    {$group}
            ";

        if ($showPquery) {
            dump($sql);
            exit;
        }

        return $sql;
    }


    public function GET_querysv2()
    {
        $this->requireAuthentication();

        $app = App::i();

        if (!$app->user->is('admin')) {
            return;
        }

        $em = $app->em;
        $conn = $em->getConnection();

        $sessions = [
            "AGENTES" => [
                [
                    'label' => 'Total de usuários',
                    'select' => "count(distinct(u.id))",
                    'from' => "usr u",
                    'join' => [
                        'join agent a on a.id = u.profile_id'
                    ],
                    'where' => [
                        'u.status > 0',
                    ],
                ],
                [
                    // Existem agentes coletivos contemplados editais e agentes em rascunho contemplados em editais. Por isso o WHERE esta como esta
                    'label' => 'Total de agentes publicados',
                    'from' => "agent a",
                    'where' => [
                        'a.status > 0',
                    ],
                ],
                [
                    'label' => 'Total de agentes individuais',
                    'from' => "agent a",
                    'where' => [
                        'a.type = 1',
                        'a.status > 0'
                    ],
                ],
                [
                    'label' => 'Total de agentes coletivos',
                    'from' => "agent a",
                    'where' => [
                        'a.type = 2',
                        'a.status > 0'
                    ],
                ],
                [
                    'select' => 'count(distinct(a.id)) as total',
                    'label' => 'Total de agentes individuais com CPF',
                    'from' => "agent a",
                    'join' => [
                        "join agent_meta am on am.object_id = a.id and am.key = 'cpf' and trim(am.value) <> ''"
                    ],
                    'where' => [
                        'a.status > 0',
                        'a.type = 1'
                    ],
                ],
                [
                    'select' => 'count(distinct(a.id)) as total',
                    'label' => "Total de agentes individuais com CNPJ (MEI)",
                    'from' => "agent a",
                    'join' => [
                        "join agent_meta cnpj on cnpj.object_id = a.id and cnpj.key = 'cnpj' and trim(cnpj.value) <> ''"
                    ],
                    'where' => [
                        'a.status > 0',
                        'a.type = 1'
                    ],
                ],
                [
                    'label' => 'Total de agentes coletivos com CNPJ',
                    'select' => 'count(distinct(a.id)) as total',
                    'from' => "agent a",
                    'where' => [
                        "a.id in (select cnpj.object_id from agent_meta cnpj where cnpj.key = 'cnpj' and trim(cnpj.value) <> '')",
                        'a.status > 0',
                        'a.type = 2'
                    ],
                ],
                [
                    'label' => 'Total de agentes coletivos sem CNPJ',
                    'from' => "agent a",
                    'where' => [
                        "a.id not in (select cnpj.object_id from agent_meta cnpj where cnpj.key = 'cnpj' and trim(cnpj.value) <> '')",
                        'a.type = 2',
                        'a.status > 0'
                    ],
                ],
                [
                    'label' => 'Total de agentes COM inscrições enviadas',
                    'from' => "agent a",
                    'where' => [
                        'a.id in (select r.agent_id from registration r where status > 0)',
                        'a.type = 1'
                    ],
                ],
                [
                    'label' => 'Total de agentes CONTEMPLADOS em algum edital',
                    'from' => "agent a",
                    'where' => [
                        "a.id in (
                            select agent_id from registration where opportunity_id in (
                                select object_id from opportunity_meta where key = 'isLastPhase'
                            )  and status = 10
                        )",
                        'a.type = 1'
                    ],
                ],
                [
                    'label' => 'Total de agentes NÃO CONTEMPLADOS em editais',
                    'from' => "agent a",
                    'where' => [
                        "a.id not in (
                            select agent_id from registration where opportunity_id in (
                                select object_id from opportunity_meta where key = 'isLastPhase'
                            )  and status = 10
                        )",
                        'a.type = 1',
                        'a.status > 0'
                    ],
                ],
                [
                    'label' => 'Total de agentes com inscrições enviadas mas NÃO CONTEMPLADOS em editais',
                    'from' => "agent a",
                    'where' => [
                        "a.id not in (
                            select agent_id from registration where opportunity_id in (
                                select object_id from opportunity_meta where key = 'isLastPhase'
                            )  and status = 10
                        )",
                        'a.id in (select r.agent_id from registration r where status > 0)',
                        'type = 1'
                    ],
                ],
                [
                    'label' => 'Total de agentes por comunidade tradicional',
                    'select' => "
                        CASE 
                            WHEN am.value IS NULL OR am.value = '' THEN 'Não Informado'
                            else am.value
                        END AS alias,
                        count(*) as total
                    ",
                    'from' => "agent a",
                    'join' => [
                        "join agent_meta am on am.object_id = a.id and am.key = 'comunidadesTradicional'"
                    ],
                    'where' => [
                        'a.status > 0'
                    ],
                    'group' => "group by alias",
                    'fetch' => "fetchAll"
                ],
                [
                    'label' => 'Total de agentes inviduais por area de atuação',
                    'select' => "
                        t.term as alias,
                        count(*) as total
                    ",
                    'from' => "term_relation tr",
                    'join' => [
                        "join term t on t.id = tr.term_id",
                        "join agent a on a.id = tr.object_id and a.status > 0 and a.type = 1"
                    ],
                    'where' => [
                        "tr.object_type = 'MapasCulturais\Entities\Agent'",
                        "t.taxonomy = 'area'",
                    ],
                    'group' => "group by alias",
                    'fetch' => "fetchAll"
                ],
                [
                    'label' => 'Total de agentes coletivos por area de atuação',
                    'select' => "
                        t.term as alias,
                        count(*) as total
                    ",
                    'from' => "term_relation tr",
                    'join' => [
                        "join term t on t.id = tr.term_id",
                        "join agent a on a.id = tr.object_id and a.status > 0 and a.type = 2"
                    ],
                    'where' => [
                        "tr.object_type = 'MapasCulturais\Entities\Agent'",
                        "t.taxonomy = 'area'",
                    ],
                    'group' => "group by alias",
                    'fetch' => "fetchAll"
                ],
                [
                    'label' => 'Total de agentes individual por segmento cultural',
                    'select' => "
                        t.term as alias,
                        count(*) as total
                    ",
                    'from' => "term_relation tr",
                    'join' => [
                        "join term t on t.id = tr.term_id",
                        "join agent a on a.id = tr.object_id and a.status > 0 and a.type = 1"
                    ],
                    'where' => [
                        "tr.object_type = 'MapasCulturais\Entities\Agent'",
                        "t.taxonomy = 'segmento'",
                    ],
                    'group' => "group by alias",
                    'fetch' => "fetchAll"
                ],
                [
                    'label' => 'Total de agentes por raça',
                    'select' => "
                        CASE 
                            WHEN am.value IS NULL OR am.value = '' THEN 'Não Informado'
                            else am.value
                        END AS alias,
                        count(*) as total
                    ",
                    'from' => "agent a",
                    'join' => [
                        "join agent_meta am on am.object_id = a.id and am.key = 'raca'"
                    ],
                    'where' => [
                        'a.status > 0'
                    ],
                    'group' => "group by alias",
                    'fetch' => "fetchAll"
                ],
                [
                    'label' => 'Total de agentes por genero',
                    'select' => "
                        CASE 
                            WHEN am.value IS NULL OR am.value = '' THEN 'Não Informado'
                            else am.value
                        END AS alias,
                        count(*) as total
                    ",
                    'from' => "agent a",
                    'join' => [
                        "join agent_meta am on am.object_id = a.id and am.key = 'genero'"
                    ],
                    'where' => [
                        'a.status > 0'
                    ],
                    'group' => "group by alias",
                    'fetch' => "fetchAll"
                ],
                [
                    'label' => 'Total de agentes por escolaridade',
                    'select' => "
                        CASE 
                            WHEN am.value IS NULL OR am.value = '' THEN 'Não Informado'
                            else am.value
                        END AS alias,
                        count(*) as total
                    ",
                    'from' => "agent a",
                    'join' => [
                        "join agent_meta am on am.object_id = a.id and am.key = 'escolaridade'"
                    ],
                    'where' => [
                        'a.status > 0'
                    ],
                    'group' => "group by alias",
                    'fetch' => "fetchAll"
                ],
                [
                    'label' => 'Total de agentes individuais por RI',
                    'select' => "
                        CASE 
                            WHEN am.value IS NULL OR am.value = '' THEN 'Não Informado'
                            else am.value
                        END AS alias,
                        count(*) as total
                    ",
                    'from' => "agent a",
                    'join' => [
                        "join agent_meta am on am.object_id = a.id and am.key = 'geoRI'"
                    ],
                    'where' => [
                        'a.status > 0',
                        'a.type = 1'
                    ],
                    'group' => "group by alias",
                    'fetch' => "fetchAll"
                ],
                [
                    'label' => 'Total de agentes individuais por faixa de idade',
                    'result' =>  $this->agentByAgeGroup(),
                ]
            ],
            "INSCRIÇÕES" => [
                [
                    'label' => "Enviadas",
                    'select' => "count(distinct(r.id))",
                    'from' => "registration r",
                    'join' => [
                        'join agent a on a.id = r.agent_id'
                    ],
                    'where' => [
                        "r.opportunity_id in (
                            select 
                                distinct o.id 
                            from 
                                opportunity o 
                            join 
                                seal_relation sr on sr.object_id = o.id and sr.object_type = 'MapasCulturais\Entities\Opportunity'
                            where 
                                o.parent_id is null and 
                                o.status > 0 and
                                sr.seal_id in (1,2,3,4)
                        )",
                        "r.status > 0"
                    ],
                ],
                [
                    'label' => "Rascunho",
                    'select' => "count(distinct(r.id))",
                    'from' => "registration r",
                    'join' => [
                        'join agent a on a.id = r.agent_id'
                    ],
                    'where' => [
                        "r.opportunity_id in (
                            select 
                                distinct o.id 
                            from 
                                opportunity o 
                            join 
                                seal_relation sr on sr.object_id = o.id and sr.object_type = 'MapasCulturais\Entities\Opportunity'
                            where 
                                o.parent_id is null and 
                                o.status > 0 and
                                sr.seal_id in (1,2,3,4)
                        )",
                        "r.status = 0"
                    ],
                ],
                [
                    'label' => "Pendente",
                    'select' => "count(distinct(r.id))",
                    'from' => "registration r",
                    'join' => [
                        'join agent a on a.id = r.agent_id'
                    ],
                    'where' => [
                        "r.opportunity_id in (
                            select 
                                distinct o.id 
                            from 
                                opportunity o 
                            join 
                                seal_relation sr on sr.object_id = o.id and sr.object_type = 'MapasCulturais\Entities\Opportunity'
                            where 
                                o.parent_id is null and 
                                o.status > 0 and
                                sr.seal_id in (1,2,3,4)
                        )",
                        "r.status = 1"
                    ],
                ],
                [
                    'label' => "selecionadas",
                    'select' => "count(distinct(r.id))",
                    'from' => "registration r",
                    'join' => [
                        'join agent a on a.id = r.agent_id'
                    ],
                    'where' => [
                        "r.opportunity_id in 
                        (
                            select o.id 
                            from 
                                opportunity o
                            join 
                                opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                            where
                                o.parent_id in (
                                    select 
                                        sr.object_id 
                                    from 
                                        seal_relation sr 
                                    where 
                                        sr.seal_id in (1,2,3,4) and sr.object_type = 'MapasCulturais\Entities\Opportunity'
                                )
                        )",
                        "r.status = 10"
                    ],
                ],
                [
                    'label' => "Não selecionadas",
                    'select' => "count(distinct(r.id))",
                    'from' => "registration r",
                    'join' => [
                        'join agent a on a.id = r.agent_id'
                    ],
                    'where' => [
                        "r.opportunity_id in 
                        (
                            select o.id 
                            from 
                                opportunity o
                            join 
                                opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                            where
                                o.parent_id in (
                                    select 
                                        sr.object_id 
                                    from 
                                        seal_relation sr 
                                    where 
                                        sr.seal_id in (1,2,3,4) and sr.object_type = 'MapasCulturais\Entities\Opportunity'
                                )
                        )",
                        "r.status = 3"
                    ],
                ],
                [
                    'label' => "Inválidas",
                    'select' => "count(distinct(r.id))",
                    'from' => "registration r",
                    'join' => [
                        'join agent a on a.id = r.agent_id'
                    ],
                    'where' => [
                        "r.opportunity_id in 
                        (
                            select o.id 
                            from 
                                opportunity o
                            join 
                                opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                            where
                                o.parent_id in (
                                    select 
                                        sr.object_id 
                                    from 
                                        seal_relation sr 
                                    where 
                                        sr.seal_id in (1,2,3,4) and sr.object_type = 'MapasCulturais\Entities\Opportunity'
                                )
                        )",
                        "r.status = 2"
                    ],
                ],
                [
                    'label' => "Suplentes",
                    'select' => "count(distinct(r.id))",
                    'from' => "registration r",
                    'join' => [
                        'join agent a on a.id = r.agent_id'
                    ],
                    'where' => [
                        "r.opportunity_id in 
                        (
                            select o.id 
                            from 
                                opportunity o
                            join 
                                opportunity_meta om on om.object_id = o.id and om.key = 'isLastPhase'
                            where
                                o.parent_id in (
                                    select 
                                        sr.object_id 
                                    from 
                                        seal_relation sr 
                                    where 
                                        sr.seal_id in (1,2,3,4) and sr.object_type = 'MapasCulturais\Entities\Opportunity'
                                )
                        )",
                        "r.status = 8"
                    ],
                ],
            ],
            "INSCRIÇÕES PAULO GUSTAVO" => [
                [
                    'label' => "Enviadas",
                    'select' => "count(distinct(r.id))",
                    'from' => "registration r",
                    'join' => [
                        'join agent a on a.id = r.agent_id',
                        "join opportunity o on o.id = r.opportunity_id"
                    ],
                    'where' => [
                        "o.parent_id is null",
                        "o.status > 0",
                        "o.object_type = 'MapasCulturais\Entities\Project'",
                        "o.object_id in (1274,1278)",
                        "r.status > 0"
                    ],
                ],
                [
                    'label' => "Rascunho",
                    'select' => "count(distinct(r.id))",
                    'from' => "registration r",
                    'join' => [
                        'join agent a on a.id = r.agent_id',
                        "join opportunity o on o.id = r.opportunity_id"
                    ],
                    'where' => [
                        "o.parent_id is null",
                        "o.status > 0",
                        "o.object_type = 'MapasCulturais\Entities\Project'",
                        "o.object_id in (1274,1278)",
                        "r.status = 0"
                    ],
                ],
                [
                    'label' => "Pendentes",
                    'select' => "count(distinct(r.id))",
                    'from' => "registration r",
                    'join' => [
                        'join agent a on a.id = r.agent_id',
                        "join opportunity o on o.id = r.opportunity_id"
                    ],
                    'where' => [
                        "o.parent_id is null",
                        "o.status > 0",
                        "o.object_type = 'MapasCulturais\Entities\Project'",
                        "o.object_id in (1274,1278)",
                        "r.status = 1"
                    ],
                ],
                [
                    'label' => "Selecionadas",
                    'select' => "count(distinct(r.id))",
                    'from' => "registration r",
                    'join' => [
                        'join agent a on a.id = r.agent_id',
                        "join opportunity o on o.id = r.opportunity_id",
                        "join opportunity_meta om on om.object_id = o.id AND om.key = 'isLastPhase'"
                    ],
                    'where' => [
                        "o.status in (1,-1)",
                        "o.object_type = 'MapasCulturais\Entities\Project'",
                        "o.object_id in (1274,1278)",
                        "r.status = 10"
                    ],
                ],
                [
                    'label' => "Não selecionadas",
                    'select' => "count(distinct(r.id))",
                    'from' => "registration r",
                    'join' => [
                        'join agent a on a.id = r.agent_id',
                        "join opportunity o on o.id = r.opportunity_id",
                        "join opportunity_meta om on om.object_id = o.id AND om.key = 'isLastPhase'"
                    ],
                    'where' => [
                        "o.status in (1,-1)",
                        "o.object_type = 'MapasCulturais\Entities\Project'",
                        "o.object_id in (1274,1278)",
                        "r.status = 3"
                    ],
                ],
                [
                    'label' => "Não selecionadas",
                    'select' => "count(distinct(r.id))",
                    'from' => "registration r",
                    'join' => [
                        'join agent a on a.id = r.agent_id',
                        "join opportunity o on o.id = r.opportunity_id",
                        "join opportunity_meta om on om.object_id = o.id AND om.key = 'isLastPhase'"
                    ],
                    'where' => [
                        "o.status in (1,-1)",
                        "o.object_type = 'MapasCulturais\Entities\Project'",
                        "o.object_id in (1274,1278)",
                        "r.status = 3"
                    ],
                ],
                [
                    'label' => "Inválidas",
                    'select' => "count(distinct(r.id))",
                    'from' => "registration r",
                    'join' => [
                        'join agent a on a.id = r.agent_id',
                        "join opportunity o on o.id = r.opportunity_id",
                        "join opportunity_meta om on om.object_id = o.id AND om.key = 'isLastPhase'"
                    ],
                    'where' => [
                        "o.status in (1,-1)",
                        "o.object_type = 'MapasCulturais\Entities\Project'",
                        "o.object_id in (1274,1278)",
                        "r.status = 2"
                    ],
                ],
                [
                    'label' => "Supĺentes",
                    'select' => "count(distinct(r.id))",
                    'from' => "registration r",
                    'join' => [
                        'join agent a on a.id = r.agent_id',
                        "join opportunity o on o.id = r.opportunity_id",
                        "join opportunity_meta om on om.object_id = o.id AND om.key = 'isLastPhase'"
                    ],
                    'where' => [
                        "o.status in (1,-1)",
                        "o.object_type = 'MapasCulturais\Entities\Project'",
                        "o.object_id in (1274,1278)",
                        "r.status = 8"
                    ],
                ],
            ],
        ];

        $result = [];
        foreach ($sessions as $session => $queries) {
            if ($session === "INSCRIÇÕES PAULO GUSTAVO") {
                continue;
            }

            $result[] = $session;
            foreach ($queries as $values) {
                if (!isset($values['result'])) {
                    $fetch = $values['fetch'] ?? "fetchOne";
                    $sql = $this->buildQuery($values);

                    $label = $values['label'];

                    if ($fetch == "fetchAll") {
                        $results = $conn->$fetch($sql);

                        foreach ($results as $_key => $_value) {
                            $result[$label][$_value['alias']] = $_value['total'];
                        }
                    } else {
                        $result[$label] = $conn->$fetch($sql);
                    }
                } else {
                    $label = $values['label'];
                    $result[$label] = $values['result'];
                }
            }
        }

        // Print dos dados
        $this->output("Segmentação Global", $result);

        $this->output("Segmentação Paulo Gustavo por oportunidade", $this->segmentadoPauloGustavo($sessions));

        $this->output("Segmentação por RI", $this->segmentacaoRI($sessions));

        $this->output("Segmentação Paulo Gustavo por RI", $this->segmentadoPauloGustavoPorRI($sessions));
    }

    public function output($label, $data)
    {
        if (isset($_GET['csv'])) {
            echo "<div style='white-space: pre-line;'>";
            echo "{$label},\n";
            echo ",\n";
            $result = print_r($data, true);
            $result =  str_replace(["[", "]"], '"', $result);
            $result =  str_replace([" => ", ")"], ',', $result);
            $result =  str_replace(["Array", "("], '', $result);
            echo $result;
            echo ",\n";
            echo ",\n";
            echo "</div>";
        } else {
            echo "<h1>{$label}</h1>";
            dump($data);
        }
    }


    public function agentByAgeGroup()
    {
        $app = App::i();
        $em = $app->em;
        $conn = $em->getConnection();

        $results = $conn->fetchAll("
            SELECT 
                CONCAT(FLOOR((idade::numeric / 10)) * 10, ' - ', FLOOR((idade::numeric / 10) + 1) * 10 - 1) AS faixa_idade,
                COUNT(*) AS total
            FROM (
                SELECT 
                    CASE 
                        WHEN EXTRACT(YEAR FROM age(current_date, am.value::date)) < 1 THEN '0' 
                        WHEN EXTRACT(YEAR FROM age(current_date, am.value::date)) > 100 THEN '100' 
                        ELSE EXTRACT(YEAR FROM age(current_date, am.value::date))::text 
                    END AS idade
                FROM 
                    agent_meta am 
                    JOIN agent a ON am.object_id = a.id AND a.status > 0
                    
                WHERE 
                    am.key = 'dataDeNascimento' AND
                    am.value <> '' AND
                    a.type = 1 AND
                    EXTRACT(YEAR FROM age(current_date, am.value::date)) >= 18 AND
                    EXTRACT(YEAR FROM age(current_date, am.value::date)) <= 110
            ) AS subquery
            GROUP BY 
                CONCAT(FLOOR((idade::numeric / 10)) * 10, ' - ', FLOOR((idade::numeric / 10) + 1) * 10 - 1)
            ORDER BY
                CASE 
                    WHEN CONCAT(FLOOR((idade::numeric / 10)) * 10, ' - ', FLOOR((idade::numeric / 10) + 1) * 10 - 1) = '100 - 109' THEN 1
                    ELSE 0
                END,
                CONCAT(FLOOR((idade::numeric / 10)) * 10, ' - ', FLOOR((idade::numeric / 10) + 1) * 10 - 1);
    
        ");

        $result = [];
        foreach ($results as $_key => $_value) {
            $result[$_value['faixa_idade']] = $_value['total'];
        }

        return $result;
    }


    public function segmentacaoRI($sessions)
    {
        $app = App::i();
        $em = $app->em;
        $conn = $em->getConnection();

        $ris = $conn->fetchAll("SELECT cod,name FROM geo_division WHERE type = 'RI'");
        $ri_results = [];
        foreach ($ris as $ri) {
            $ri_result = [];
            foreach ($sessions as $session => $queries) {
                if ($session === "INSCRIÇÕES PAULO GUSTAVO") {
                    continue;
                }
                $ri_result[] = $session;
                foreach ($queries as $values) {
                    if (!isset($values['result'])) {
                        $fetch = $values['fetch'] ?? "fetchOne";
                        $sql = $this->buildQuery($values, wheres: ["a.id in(select object_id from agent_meta where key = 'geoRI' and value = '{$ri['cod']}')"]);

                        $label = $values['label'];

                        if ($fetch == "fetchAll") {
                            $results = $conn->$fetch($sql);

                            foreach ($results as $_key => $_value) {
                                $ri_result[$label][$_value['alias']] = $_value['total'];
                            }
                        } else {
                            $app->log->debug($sql);
                            $ri_result[$label] = $conn->$fetch($sql);
                        }
                    } else {
                        $label = $values['label'];
                        $result[$label] = $values['result'];
                    }
                }
            }
            $ri_results[$ri['name']] = $ri_result;
        }

        return $ri_results;
    }

    public function segmentadoPauloGustavo($sessions)
    {
        $app = App::i();
        $em = $app->em;
        $conn = $em->getConnection();
        $opps_paulo_gustavo = $conn->fetchAll("SELECT o.id,o.name FROM opportunity o WHERE o.parent_id is null AND o.object_type = 'MapasCulturais\Entities\Project' AND o.object_id in (1274,1278)");

        $app = App::i();
        $em = $app->em;
        $conn = $em->getConnection();

        $opp_results = [];
        foreach ($opps_paulo_gustavo as $opp) {
            $results = [];
            foreach ($sessions as $session => $queries) {
                if ($session !== "INSCRIÇÕES PAULO GUSTAVO") {
                    continue;
                }

                foreach ($queries as $values) {
                    if (!isset($values['result'])) {
                        $fetch = $values['fetch'] ?? "fetchOne";
                        $complement = "o.id = {$opp['id']}";
                        if (in_array("o.status in (1,-1)", array_values($values['where']))) {
                            $complement = "o.parent_id = {$opp['id']}";
                        }
                        $sql = $this->buildQuery($values, wheres: [$complement]);

                        $label = $values['label'];

                        if ($fetch == "fetchAll") {
                            $results = $conn->$fetch($sql);

                            foreach ($results as $_key => $_value) {
                                $results[$label][$_value['alias']] = $_value['total'];
                            }
                        } else {
                            $app->log->debug($sql);
                            $results[$label] = $conn->$fetch($sql);
                        }
                    } else {
                        $label = $values['label'];
                        $result[$label] = $values['result'];
                    }
                }
            }
            $opp_results["#{$opp['id']} - " . $opp['name']] = $results;
        }

        return $opp_results;
    }

    public function segmentadoPauloGustavoPorRI($sessions)
    {
        $app = App::i();
        $em = $app->em;
        $conn = $em->getConnection();
        $opps_paulo_gustavo = $conn->fetchAll("SELECT o.id,o.name FROM opportunity o WHERE o.parent_id is null AND o.object_type = 'MapasCulturais\Entities\Project' AND o.object_id in (1274,1278)");
        $ris = $conn->fetchAll("SELECT cod,name FROM geo_division WHERE type = 'RI'");

        $app = App::i();
        $em = $app->em;
        $conn = $em->getConnection();

        $opp_results = [];
        foreach ($ris as $ri) {
            foreach ($opps_paulo_gustavo as $opp) {
                $fetch = $values['fetch'] ?? "fetchOne";
                $ri_result = [];
                foreach ($sessions as $session => $queries) {
                    if ($session !== "INSCRIÇÕES PAULO GUSTAVO") {
                        continue;
                    }

                    foreach ($queries as $values) {
                        if (!isset($values['result'])) {
                            $complement = "o.id = {$opp['id']}";
                            if (in_array("o.status in (1,-1)", array_values($values['where']))) {
                                $complement = "o.parent_id = {$opp['id']}";
                            }
                            $sql = $this->buildQuery($values, wheres: ["a.id in(select object_id from agent_meta where key = 'geoRI' and value = '{$ri['cod']}')", $complement]);
                            $label = $values['label'];
                            $ri_result[$label] = $conn->$fetch($sql);
                        } else {
                            $label = $values['label'];
                            $ri_result[$label] = $values['result'];
                        }
                    }
                }
                $opp_results[$ri['name']]["#{$opp['id']} - " . $opp['name']] = $ri_result;
            }
        }

        return $opp_results;
    }

    public function GET_sendMailLpg()
    {

        $app = App::i();

        $this->requireAuthentication();

        $pass = $_GET['password'] ? md5($_GET['password']) : null;
        $sendMail = isset($_GET['sendMail']) ? true : false;

        if (!$app->user->is('admin') || $pass !== "d37216b2af706ef81740d9ed403c9d5a") {
            return;
        }

        $em = $app->em;
        $conn = $em->getConnection();
        $agent_ids = $conn->fetchAll("select r.agent_id  from registration r where r.opportunity_id in (SELECT o.id FROM opportunity o WHERE o.parent_id is null AND o.object_type = 'MapasCulturais\Entities\Project' AND o.object_id in (1274,1278))");
        $app->disableAccessControl();

        $file = __DIR__ . "/lpgmails.txt";
        $conteudo = file_get_contents($file);

        $total = count($agent_ids);
        $error = [];
        $success = [];
        foreach ($agent_ids as $key =>  $agent_id) {
            $pos = $key + 1;

            if ($agent = $app->repo('Agent')->find($agent_id['agent_id'])) {
                if ($agent->emailPublico ?: $agent->emailPrivado ?: $agent->user->email) {

                    $template_data = [
                        'siteName' => $app->siteName,
                        'baseUrl' => $app->getBaseUrl(),
                        'userName' => $agent->name,
                    ];

                    $message = $app->renderMustacheTemplate('send_mail_lpg.html', $template_data);

                    $email = $agent->emailPublico ?: $agent->emailPrivado ?: $agent->user->email;
                    if (strpos($conteudo, $email) === false) {
                        if (preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
                            $app->log->debug("{$pos} de {$total} - Email LPG enviado para " . $email);

                            $email_params = [
                                'from' => $app->config['mailer.from'],
                                'to' => $email,
                                'subject' => "[Pesquisa] colabore com melhorias no nosso mapa!",
                                'body' => $message,
                            ];
                            if ($sendMail) {
                                $app->createAndSendMailMessage($email_params);
                            }

                            $success[] = $email;

                            $conteudo .= "$email\n";
                            file_put_contents($file, $conteudo);
                        } else {
                            $error[] = $email;
                            $app->log->debug("{$pos} de {$total} - E-MAIL {$email} - INVÁLIDO");
                        }
                    } else {
                        $app->log->debug("{$pos} de {$total} - E-MAIL {$email} - Ja foi enviado");
                    }
                }
            }


            $app->em->clear();
        }
        $app->enableAccessControl();

        dump(count($success) . " E-mails disparados com sucesso");
        dump($success);

        dump(count($error) . " E-mails inválidos não disparados");
        dump($error);
    }

    public function registrationFileEmpty()
    {
        return [
            'pa-872215884',
            'pa-2076552382',
            'pa-1460060877',
            'pa-1184942327',
            'pa-1149341124',
            'pa-1750302760',
            'pa-549038548',
            'pa-1133740922',
            'pa-1584122642',
            'pa-1492777475',
            'pa-833258283',
            'pa-746162444',
            'pa-1313200959',
            'pa-1890050559',
            'pa-2082775778',
            'pa-1230951701',
            'pa-506234577',
            'pa-1764730197',
            'pa-320531467',
            'pa-272610538',
            'pa-1050662156',
            'pa-462589498',
            'pa-2023870469',
            'pa-1196078105',
            'pa-1157162792',
            'pa-2017847418',
            'pa-587831296',
            'pa-1557563670',
            'pa-1337965156',
            'pa-1238560012',
            'pa-118547398',
            'pa-1125627271',
            'pa-940090094',
            'pa-1840967656',
            'pa-240951233',
            'pa-1959778152',
            'pa-109615398',
            'pa-616215094',
            'pa-1580658610',
            'pa-1913320579',
            'pa-1393466094',
            'pa-1913644318',
            'pa-839496452',
            'pa-2101973441',
            'pa-1130813831',
            'pa-635146628',
            'pa-173114459',
            'pa-1582663483',
            'pa-2068942085',
            'pa-1743630921',
            'pa-939365240',
            'pa-367716926',
            'pa-1847785498',
            'pa-1163104289',
            'pa-1229112641',
            'pa-350478420',
            'pa-224897386',
            'pa-2097327283',
            'pa-1104333052',
            'pa-2089279456',
            'pa-2086652807',
            'pa-1293215593',
            'pa-466602839',
            'pa-1578235715',
            'pa-112957614',
            'pa-2017166501',
            'pa-254977673',
            'pa-1185470118',
            'pa-1424421769',
            'pa-379887795',
            'pa-321169097',
            'pa-1612040112',
            'pa-1054259949',
            'pa-590978762',
            'pa-143106548',
            'pa-1181805647',
            'pa-960029253',
            'pa-2093677519',
            'pa-222744790',
            'pa-170326940',
            'pa-697665735',
            'pa-1503372496',
            'pa-251101834',
            'pa-497193628',
            'pa-270786629',
            'pa-1143067427',
            'pa-35387586',
            'pa-262722063',
            'pa-659877857',
            'pa-614314404',
            'pa-117217683',
            'pa-1749941777',
            'pa-1165270583',
            'pa-1560577430',
            'pa-237890699',
            'pa-1909557457',
            'pa-1303311073',
            'pa-1778373543',
            'pa-933414627',
            'pa-1791525467',
            'pa-647286197',
            'pa-1302520837',
            'pa-1525270213',
            'pa-1163171819',
            'pa-1456638794',
            'pa-1270257880',
            'pa-653826045',
            'pa-641000435',
            'pa-1481561523',
            'pa-1004571950',
            'pa-877233839',
            'pa-1511777629',
            'pa-1696446276',
            'pa-1378212167',
            'pa-935315543',
            'pa-872910107',
            'pa-2060431540',
            'pa-289182913',
            'pa-2061494807',
            'pa-1709485295',
            'pa-1246531586',
            'pa-1918223654',
            'pa-1923712989',
            'pa-1039644173',
            'pa-1715580675',
            'pa-1335615272',
            'pa-928899346',
            'pa-1416611077',
            'pa-1831400470',
            'pa-170559088',
            'pa-193391207',
            'pa-1870433287',
            'pa-658895167',
            'pa-166125616',
            'pa-1276028973',
            'pa-1568602650',
            'pa-1307898308',
            'pa-352515305',
            'pa-1767618297',
            'pa-1934611490',
            'pa-1645013206',
            'pa-507021810',
            'pa-313371342',
            'pa-2136931539',
            'pa-1945673466',
            'pa-981324010',
            'pa-1481765129',
            'pa-1346626272',
            'pa-718456082',
            'pa-1292101434',
            'pa-1014340070',
            'pa-1597488679',
            'pa-1511309354',
            'pa-1350758676',
            'pa-12771845',
            'pa-951485294',
            'pa-591254846',
            'pa-1793508880',
            'pa-1366075914',
            'pa-254852866',
            'pa-382906135',
            'pa-551650168',
            'pa-1443345454',
            'pa-1500833562',
            'pa-1882888482',
            'pa-1639361336',
            'pa-566287155',
            'pa-1534127423',
            'pa-850960434',
            'pa-494522828',
            'pa-226882101',
            'pa-1545000674',
            'pa-1219341253',
            'pa-187150559',
            'pa-775338810',
            'pa-531924326',
            'pa-201384864',
            'pa-312646077',
            'pa-205038067',
            'pa-694593207',
            'pa-2027145386',
            'pa-476438709',
            'pa-1827520196',
            'pa-54709443',
            'pa-1674611144',
            'pa-1193779904',
            'pa-428704534',
            'pa-994416919',
            'pa-1625326120',
            'pa-1854860885',
            'pa-1918408776',
            'pa-657294056',
            'pa-1306430890',
            'pa-110926014',
            'pa-5737589',
            'pa-222859346',
            'pa-1104933748',
            'pa-705945505',
            'pa-934713276',
            'pa-1436657115',
            'pa-418618033',
            'pa-673691789',
            'pa-1825295509',
            'pa-900638889',
            'pa-1166247090',
            'pa-1282076894',
            'pa-290047685',
            'pa-847611376',
            'pa-429193780',
            'pa-941993743',
            'pa-873964834',
            'pa-1506908212',
            'pa-1422575429',
            'pa-1834155474',
            'pa-2087555566',
            'pa-2070061693',
            'pa-886800618',
            'pa-1786640006',
            'pa-1695808633',
            'pa-375447664',
            'pa-1756226112',
            'pa-742182672',
            'pa-756587002',
            'pa-344579050',
            'pa-2003694390',
            'pa-218078225',
            'pa-1833807875',
            'pa-1926990235',
            'pa-235902853',
            'pa-2070539685',
            'pa-1800831510',
            'pa-1238169594',
            'pa-1926686217',
            'pa-204182797',
            'pa-810585878',
            'pa-1677380417',
            'pa-261140764',
            'pa-190049806',
            'pa-707699831',
            'pa-578346401',
            'pa-1583591625',
            'pa-182971171',
            'pa-2084753204',
            'pa-842913867',
            'pa-679263307',
            'pa-1365607293',
            'pa-1093509724',
            'pa-931567460',
            'pa-815332191',
            'pa-912313941',
            'pa-207877374',
            'pa-1882502885',
            'pa-63108300',
            'pa-627103911',
            'pa-487254691',
            'pa-1038720403',
            'pa-1601617087',
            'pa-1559799924',
            'pa-741460583',
            'pa-1169670280',
            'pa-1467268827',
            'pa-2023940677',
            'pa-1421120338',
            'pa-439671756',
            'pa-1826983659',
            'pa-322013678',
            'pa-1962843878',
            'pa-2095575773',
            'pa-776441750',
            'pa-157929826',
            'pa-1880012205',
            'pa-829547405',
            'pa-2136467673',
            'pa-1983679172',
            'pa-45453307',
            'pa-1535360919',
            'pa-1464400100',
            'pa-1552003427',
            'pa-1995615241',
            'pa-776519470',
            'pa-146584637',
            'pa-1996130303',
            'pa-940430468',
            'pa-467062745',
            'pa-468827573',
            'pa-1093200820',
            'pa-626314873',
            'pa-1313782897',
            'pa-1032031037',
            'pa-1215869511',
            'pa-1613467671',
            'pa-208862352',
            'pa-900004308',
            'pa-924966230',
            'pa-578396146',
            'pa-1831837984',
            'pa-2093282071',
            'pa-1039845282',
            'pa-1512023701',
            'pa-16316305',
            'pa-1675615044',
            'pa-1460588814',
            'pa-1681765121',
            'pa-396474635',
            'pa-1984013097',
            'pa-682335203',
            'pa-1558100543',
            'pa-231054965',
            'pa-1300690139',
            'pa-272665488',
            'pa-1182975459',
            'pa-1092870768',
            'pa-1898106566',
            'pa-286491569',
            'pa-1375587191',
            'pa-1926290761',
            'pa-1925350261',
            'pa-1594356795',
            'pa-764541736',
            'pa-1712581487',
            'pa-242980065',
            'pa-1021424604',
            'pa-740858773',
            'pa-493872834',
            'pa-794737866',
            'pa-845319579',
            'pa-1807455994',
            'pa-651345083',
            'pa-2118157958',
            'pa-1731707839',
            'pa-615817710',
            'pa-1074104562',
            'pa-1432202542',
            'pa-889253895',
            'pa-1477892220',
            'pa-1607661870',
            'pa-675279583',
            'pa-1114822128',
            'pa-2132644802',
            'pa-470744977',
            'pa-1462736512',
            'pa-1727384503',
            'pa-489547899',
            'pa-187682873',
            'pa-1454291153',
            'pa-647489382',
            'pa-2030028358',
            'pa-312464726',
            'pa-450931343',
            'pa-737453204',
            'pa-183340458',
            'pa-20430946',
            'pa-1409459823',
            'pa-2135926559',
            'pa-1265281550',
            'pa-1040049006',
            'pa-1829734557',
            'pa-320782527',
            'pa-1092920676',
            'pa-1804113225',
            'pa-1370155062',
            'pa-1780022776',
            'pa-1392948903',
            'pa-810728605',
            'pa-1035503994',
            'pa-615548839',
            'pa-892568241',
            'pa-1067906869',
            'pa-805093947',
            'pa-1185984496',
            'pa-866816515',
            'pa-811969751',
            'pa-1482274719',
            'pa-617001144',
            'pa-919925448',
            'pa-393587620',
            'pa-976725831',
            'pa-1874045663',
            'pa-1661005386',
            'pa-358274576',
            'pa-1670032579',
            'pa-1278344163',
            'pa-781161604',
            'pa-181320420',
            'pa-144356361',
            'pa-963365371',
            'pa-90088611',
            'pa-274830688',
            'pa-357026943',
            'pa-1220660100',
            'pa-801816451',
            'pa-2103045624',
            'pa-158836446',
            'pa-483571478',
            'pa-954069461',
            'pa-1000655104',
            'pa-1256641605',
            'pa-1131909730',
            'pa-221746988',
            'pa-294311938',
            'pa-1150392256',
            'pa-472565552',
            'pa-237233688',
            'pa-799143450',
            'pa-310677107',
            'pa-1008921201',
            'pa-1555096624',
            'pa-598339006',
            'pa-1420936836',
            'pa-1101112696',
            'pa-1463022135',
            'pa-946980099',
            'pa-1016910812',
            'pa-267165870',
            'pa-1093445012',
            'pa-923777877',
            'pa-971765450',
            'pa-1967511996',
            'pa-295228284',
            'pa-757507274',
            'pa-77245617',
            'pa-2011817199',
            'pa-1686362506',
            'pa-1185805198',
            'pa-1232529130',
            'pa-448430094',
            'pa-273676229',
            'pa-1583274093',
            'pa-1336118397',
            'pa-294843421',
            'pa-1290391344',
            'pa-1991179913',
            'pa-2011148272',
            'pa-572754205',
            'pa-672657730',
            'pa-1601799092',
            'pa-1320805078',
            'pa-289858149',
            'pa-1780393672',
            'pa-1511519241',
            'pa-622585044',
            'pa-1508742693',
            'pa-89489965',
            'pa-84325689',
            'pa-2119211200',
            'pa-583318243',
            'pa-573482034',
            'pa-1175095572',
            'pa-1785854184',
            'pa-1753718662',
            'pa-1043191852',
            'pa-545976521',
            'pa-1569055635',
            'pa-1615310365',
            'pa-95219396',
            'pa-1008067817',
            'pa-1580399603',
            'pa-1961807771',
            'pa-1212725381',
            'pa-1869211851',
            'pa-943189399',
            'pa-1218827937',
            'pa-385350664',
            'pa-2079050071',
            'pa-1747701875',
            'pa-792975928',
            'pa-557239758',
            'pa-841780939',
            'pa-372045521',
            'pa-895854000',
            'pa-2096023084',
            'pa-800689992',
            'pa-1606847133',
            'pa-1901068184',
            'pa-2000997207',
            'pa-237157785',
            'pa-1403494916',
            'pa-435066625',
            'pa-1939464171',
            'pa-141084102',
            'pa-488491257',
            'pa-1605942489',
            'pa-2121958434',
            'pa-604758832',
            'pa-842786919',
            'pa-1904073144',
            'pa-1662173144',
            'pa-746565796',
            'pa-1111212040',
            'pa-54260084',
            'pa-14527165',
            'pa-551534955',
            'pa-463011533',
            'pa-7852593',
            'pa-1790624217',
            'pa-688454704',
            'pa-1593935444',
            'pa-750554443',
            'pa-1915194879',
            'pa-375836662',
            'pa-1670315671',
            'pa-833354672',
            'pa-1557770904',
            'pa-2029757102',
            'pa-1878099105',
            'pa-1391056550',
            'pa-1872604046',
            'pa-1020432311',
            'pa-1997963451',
            'pa-234805089',
            'pa-1465825436',
            'pa-830774105',
            'pa-1071569156',
            'pa-1814228781',
        ];
    }
    
}
