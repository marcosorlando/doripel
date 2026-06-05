<?php

echo "<aside class='workcontrol_account_sidebar'>";

echo '<header>';
$Avatar = (empty($user_thumb) ? 'admin/_img/no_avatar.jpg' : 'uploads/'.$user_thumb);
echo "<img class='account_user_avatar' src='".BASE.\sprintf('/tim.php?src=%s&w=', $Avatar).AVATAR_W.'&h='.AVATAR_H."' default='".BASE.\sprintf('/tim.php?src=%s&w=', $Avatar).AVATAR_W.'&h='.AVATAR_H.\sprintf("' title='%s' alt='%s'/>", $user_name, $user_name);
echo \sprintf('<h1>%s %s</h1>', $user_name, $user_lastname);
echo \sprintf('<p>%s</p>', $user_email);
echo '</header>';

echo "<nav class='workcontrol_account_sidebar_nav'>";
echo "<ul class='workcontrol_account_sidebar_nav'>";
echo '<li><a '.('home' == $AccountAction ? 'class="active"' : '').\sprintf(" href='%s/home#acc' title='Minha Conta'>Minha Conta</a></li>", $AccountBaseUI);
echo '<li><a '.('dados' == $AccountAction ? 'class="active"' : '').\sprintf(" href='%s/dados#acc' title='Atualizar Dados'>Atualizar Dados</a></li>", $AccountBaseUI);
echo \sprintf("<li><a class='logoff' href='%s/sair' title='Desconectar'>Desconectar!</a></li>", $AccountBaseUI);
echo '</ul>';
echo '</nav>';
echo '</aside>';
