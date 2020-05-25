<?php
chdir(dirname(__FILE__));

/**
 * Скрипт для очистки онлайн-ссылок:
 */
require '../instance-config.php';
require '../app/classes/kvs.class.php';

$cache = KVS::getInstance();

$links   = $cache -> listGet('Blog_BlogOnlineModel', null, 'links');
if ($links) {
    foreach($links as $link)
        {
                        $cache -> listRemove('Blog_BlogOnlineModel', null, 'links', $link);
        }
}
 
