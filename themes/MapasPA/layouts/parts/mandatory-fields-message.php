<?php

/**
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV2\Theme $this
 */

use MapasCulturais\i;

$this->import('
    mc-alert
');
?>


<mc-alert type="warning" class="mc-mandatory-fields-message">
    <?= i::__("Por favor, informe pelo menos um campo de seguimento cultural.") ?>
</mc-alert>