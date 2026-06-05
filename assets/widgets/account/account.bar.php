<?php

if (!empty($URL[0]) && !empty($URL[1]) && 'conta' == $URL[0] && 'sair' == $URL[1]) {
    unset($_SESSION['userLogin']);
}

if (!empty($_SESSION['userLogin'])) {
    $AccSaudation = (ACC_TAG === '' ? \sprintf('Olá %s!', $_SESSION['userLogin']['user_name']) : ACC_TAG);
    echo "<a title='Minha Conta' href='".BASE.\sprintf("/conta/home'>%s</a>", $AccSaudation);
} else {
    echo "<a class='icon-user' title='Minha Conta' href='".BASE."/conta/login'></a>";
}
