<?php

use MapasCulturais\App;
use MapasCulturais as M;

use function MapasCulturais\__exec;

return [
    'remove segmento Outro do segmento cultural' => function () {
        $app = App::i();
        $em = $app->em;
        $conn = $em->getConnection();
        $conn->executeQuery("delete from term where taxonomy = 'segmento' and term = 'Outros'");
    }
];