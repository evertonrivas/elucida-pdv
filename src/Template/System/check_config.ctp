<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\Error\Debugger;
use Cake\Http\Exception\NotFoundException;

$this->layout = false;

$passouPHP = false;
$passouMbstring = false;
$passouOpenssl = false;
$passouIntl = false;
$passouTmp = false;
$passouLog = false;
$passouDb = false;

$cakeDescription = 'Elucida: verifica&ccedil;&atilde;o de necessidades';
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $cakeDescription ?>
    </title>

    <?= $this->Html->meta('icon') ?>
    <?= $this->Html->css('base.css') ?>
    <?= $this->Html->css('style.css') ?>
    <?= $this->Html->css('home.css') ?>
    <link href="https://fonts.googleapis.com/css?family=Raleway:500i|Roboto:300,400,700|Roboto+Mono" rel="stylesheet">
</head>
<body class="home">

<header class="row">
    <div class="header-image"><?= $this->Html->image('logo.png') ?></div>
    <div class="header-title">
        <h1>Bem vindo ao Elucida PDV</h1>
    </div>
</header>

<div class="row">
    <div class="columns large-12">
        <div class="ctp-warning alert text-center">
            <p>Essa p&aacute;gina realiza verifica&ccedil;&otilde;es importantes para o perfeito funcionamento do seu sistema.</p>
        </div>
        <div id="url-rewriting-warning" class="alert url-rewriting">
            <ul>
                <li class="bullet problem">
                    URL rewriting is not properly configured on your server.<br />
                    1) <a target="_blank" href="https://book.cakephp.org/3.0/en/installation.html#url-rewriting">Help me configure it</a><br />
                    2) <a target="_blank" href="https://book.cakephp.org/3.0/en/development/configuration.html#general-configuration">I don't / can't use URL rewriting</a>
                </li>
            </ul>
        </div>
        <?php Debugger::checkSecurityKeys(); ?>
    </div>
</div>

<div class="row">
    <div class="columns large-6">
        <h4>Ambiente</h4>
        <ul>
        <?php if (version_compare(PHP_VERSION, '5.6.0', '>=')) : $passouPHP = true; ?>
            <li class="bullet success">Sua vers&atilde;o do PHP &eacute; 5.6.0 sou superior (detectado <?= PHP_VERSION ?>).</li>
        <?php else : ?>
            <li class="bullet problem">Sua vers&atilde;o do PHP &eacute; muito baixa. Voc&ecirc; precisa da vers&atilde;o  5.6.0 ou superior para usar o sistema (detectado <?= PHP_VERSION ?>).</li>
        <?php endif; ?>

        <?php if (extension_loaded('mbstring')) : $passouMbstring = true; ?>
            <li class="bullet success">Sua vers&atilde;o do PHP carregou corretamente a extens&atilde;o mbstring.</li>
        <?php else : ?>
            <li class="bullet problem">Sua vers&atilde;o do PHP <strong>n&atilde;o</strong> possui a extens&atilde;o mbstring habilitada, por favor habilite.</li>
        <?php endif; ?>

        <?php if (extension_loaded('openssl')) : $passouOpenssl = true; ?>
            <li class="bullet success">Sua vers&atilde;o do PHP carregou corretamente a extens&atilde;o openssl.</li>
        <?php elseif (extension_loaded('mcrypt')) : ?>
            <li class="bullet success">Sua ves&atilde;o do PHP carregou corretamente a extens&atilde;o mcrypt.</li>
        <?php else : ?>
            <li class="bullet problem">Sua vers&atilde;o do PHP <strong>n&atilde;o</strong> carregou corretamente a extens&atilde;o openssl ou mcrypt.</li>
        <?php endif; ?>

        <?php if (extension_loaded('intl')) : $passouIntl = true; ?>
            <li class="bullet success">Sua vers&atilde;o do PHP carregou corretamente a extens&atilde;o intl.</li>
        <?php else : ?>
            <li class="bullet problem">Sua vers&atilde;o do PHP <strong>n&atilde;o</strong> carregou corretamente a extens&atilde;o intl.</li>
        <?php endif; ?>
        </ul>
    </div>
    <div class="columns large-6">
        <h4>Sistema de Arquivos</h4>
        <ul>
        <?php if (is_writable(TMP)) : $passouTmp = true; ?>
            <li class="bullet success">Seu diret&oacute;rio tmp(<?=TMP;?>) possui permiss&atilde;o de escrita.</li>
        <?php else : ?>
            <li class="bullet problem">Seu diret&oacute;rio tmp(<?=TMP;?>) <strong>n&atilde;o</strong> possui permiss&atilde;o de descrita.</li>
        <?php endif; ?>

        <?php if (is_writable(LOGS)) : $passouLog = true; ?>
            <li class="bullet success">Seu diret&oacute;rio de logs (<?=LOGS?>) possui permiss&atilde;o de escrita.</li>
        <?php else : ?>
            <li class="bullet problem">Seu diret&oacute;rio de logs (<?=LOGS?>) <strong>n&atilde;o</strong> possui permiss&atilde;o de escrita.</li>
        <?php endif; ?>

        </ul>
    </div>
    <hr />
</div>

<div class="row">
    <div class="columns large-12">
        <h4>Banco de Dados</h4>
        <?php
        try {
            $connection = ConnectionManager::get('default');
            $connected = $connection->connect();
        } catch (Exception $connectionError) {
            $connected = false;
            $errorMsg = $connectionError->getMessage();
            if (method_exists($connectionError, 'getAttributes')) :
                $attributes = $connectionError->getAttributes();
                if (isset($errorMsg['message'])) :
                    $errorMsg .= '<br />' . $attributes['message'];
                endif;
            endif;
        }
        ?>
        <ul>
        <?php if ($connected) : $passouDb = true; ?>
            <li class="bullet success">O sistema est&aacute; configurado para conectar-se ao banco de dados.</li>
        <?php else : ?>
            <li class="bullet problem">o sistema <strong>n&atilde;o</strong> est&aacute; configurado para conectar-se ao banco de dados.<br /><?= $errorMsg ?></li>
        <?php endif; ?>
        </ul>
    </div>
    <hr />
</div>
<?php if($passouPHP && $passouMbstring && $passouOpenssl && $passouIntl && $passouTmp && $passouLog && $passouDb): ?>
<div class="row">
    <div class="columns large-12 text-center">
        <h3 class="more">Parab&eacute;ns!!!</h3>
        <p>Seu sistema est&aacute; pronto para ser configurado antes de iniciar seu uso. Clique <strong><a href="<?=$this->Url->build("/install/")?>">aqui</a></strong> para continuar com a instala&ccedil;&atilde;o dos dados b&aacute;sicos do sistema.
        </p>
    </div>
    <hr/>
</div>
<?php endif; ?>

</body>
</html>
