<?php

$Read = new Read();
$Update = new Update();

$Read->fullRead('SELECT post_id, post_cover FROM '.DB_POSTS.' WHERE post_status = :st', 'st=0');
// $Read->exeRead(DB_POSTS);

if ($Read->getResult()) {
    foreach ($Read->getResult() as $PostData) {
        \extract($PostData);
        $pos = \strpos((string) $post_cover, '.');

        $PostData['post_cover'] = \substr_replace($post_cover, '.jpg', $pos, 4);

        echo $post_cover.' = '.$post_id.' <br>';

        $Update->exeUpdate(DB_POSTS, $PostData, 'WHERE post_id = :id', 'id='.$post_id);
    }
}
