<?php

use MapasCulturais\App;
use MapasCulturais as M;

use function MapasCulturais\__exec;

return [
    'remove segmento Outro do segmento cultural' => function () {
        $app = App::i();
        $em = $app->em;
        $conn = $em->getConnection();
        if($app->config['plugins']['SettingsPa']['config']['remove_other_segment']) {
            $conn->executeQuery("delete from term where taxonomy = 'segmento' and term = 'Outros'");
            $app->log->info("Todos os segmentos foram removidos");
        } else {
            $app->log->info("Segmento Outros não removido, configuração REMOVE_OTHER_SEGMENT está desativada (Roda apenas no Pará) Olhar plugins SettingsPa para ver implementação");
            return false;
        }
    }
];