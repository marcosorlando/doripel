<?php

    use App\Conn\Read;

    $AdminLevel = LEVEL_WC_COMMENTS;
    if (!APP_COMMENTS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();

    $Comment = \filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $Comment = $Comment ? 'WHERE id = ' . $Comment : 'WHERE status = 2';

    $SupportStatus = [2 => 'Em Aberto', 1 => 'Respondido', 3 => 'Concluído'];
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-bubbles2">Responder Comentário</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=comments/home">Comentário</a>
			<span class="crumb">/</span>
			Responder
		</p>
	</div>

	<div class="dashboard_header_search">
		<a title="Recarregar Comentários" href="dashboard.php?wc=comments/home"
		   class="btn btn_blue icon-spinner11 icon-notext"></a>
		<a title="Recarregar Comentários" href="dashboard.php?wc=comments/comment_response"
		   class="btn btn_green icon-play3">Play</a>
	</div>
</header>

<div class="dashboard_content">
	<section class="box box100">
		<div class="panel" style="padding: 30px;">
            <?php
                $Read->exeRead(DB_COMMENTS, $Comment . ' ORDER BY created ASC LIMIT 1');
                if (!$Read->getResult()) {
                echo \sprintf(
                    "<div class='trigger trigger_success trigger_none al_center icon-heart font_medium'>Não existem mais comentários em aberto %s</div><div class='clear'></div>",
                    $_SESSION['userLogin']['user_name']
                );
            } else {
                $Comm = $Read->getResult()[0];
                \extract($Read->getResult()[0]);

                // SOURCE COMMENT
                $Link = '#';
                $Title = 'Conteúdo removido';
                if ($Comm['post_id']) {
                    $Read->fullRead(
                        'SELECT post_name, post_title FROM ' . DB_POSTS . ' WHERE post_id = :id',
                        'id=' . $Comm['post_id']
                    );
                    if ($Read->getResult()) {
                        $Link = 'artigo/' . $Read->getResult()[0]['post_name'];
                        $Title = $Read->getResult()[0]['post_title'];
                    }
                } elseif ($Comm['pdt_id']) {
                    $Read->fullRead(
                        'SELECT pdt_name, pdt_title FROM ' . DB_PDT_DORIPEL . ' WHERE pdt_id = :id',
                        'id=' . $Comm['pdt_id']
                    );
                    if ($Read->getResult()) {
                        $Link = 'produto/' . $Read->getResult()[0]['pdt_name'];
                        $Title = $Read->getResult()[0]['pdt_title'];
                    }
                } elseif ($Comm['page_id']) {
                    $Read->fullRead(
                        'SELECT page_name, page_title FROM ' . DB_PAGES . ' WHERE page_id = :id',
                        'id=' . $Comm['page_id']
                    );
                    if ($Read->getResult()) {
                        $Link = $Read->getResult()[0]['page_name'];
                        $Title = $Read->getResult()[0]['page_title'];
                    }
                }

                // USER
                $Read->linkResult(
                    DB_USERS,
                    'user_id',
                    $user_id,
                    'user_id, user_name, user_lastname, user_email, user_thumb'
                );
                $user_task = $Read->getResult()[0] ?? [
                    'user_id' => 0,
                    'user_name' => 'Usuário',
                    'user_lastname' => 'removido',
                    'user_email' => '-',
                    'user_thumb' => null,
                ];
                $UserThumb = '../uploads/' . $user_task['user_thumb'];
                $user_task['user_thumb'] = (\file_exists($UserThumb) && !\is_dir(
                    $UserThumb
                ) ? 'uploads/' . $user_task['user_thumb'] : 'admin/_img/no_avatar.jpg');

                // STATUS
                $TicketStatus = (2 == $status ? "<span class='status bar_red radius'>Em Aberto</span>" : (1 == $status ? "<span class='status bar_blue radius'>Respondido</span>" : (3 == $status ? "<span class='status bar_green radius'>Concluído</span>" : '')));

                // LIKES
                $Read->fullRead(
                    'SELECT user_id, user_name, user_lastname FROM ' . DB_USERS . ' WHERE user_id IN(SELECT user_id FROM ' . DB_COMMENTS_LIKES . ' WHERE comm_id = :comm)',
                    'comm=' . $Comm['id']
                );
                if ($Read->getResult()) {
                    $getLikes = [];
                    foreach ($Read->getResult() as $UserLike) {
                        if (APP_USERS === 1) {
                            $getLikes[] = \sprintf(
                                "<a target='_blank' title='Ver Usuário' href='dashboard.php?wc=users/create&id=%s'>%s %s</a>",
                                $UserLike['user_id'],
                                $UserLike['user_name'],
                                $UserLike['user_lastname']
                            );
                        } else {
                            $getLikes[] = \sprintf(
                                "<a target='_blank' title='Ver Usuário' href='javascript:void(0)'>%s %s</a>",
                                $UserLike['user_name'],
                                $UserLike['user_lastname']
                            );
                        }
                    }
                    $Likes = \implode(', ', $getLikes);
                } else {
                    $Likes = '<span class="na">N/A</span>';
                }
            ?>
			<article class="ead_support_response" id="<?php
                echo $id; ?>" style="margin-bottom: 20px;">
				<div class="ead_support_response_avatar">
					<img class="rounded"
					     src="<?php
                             echo BASE; ?>/tim.php?src=<?php
                             echo $user_task['user_thumb']; ?>&w=<?php
                             echo \round(
                                 AVATAR_W / 2
                             ); ?>&h=<?php
                             echo \round(AVATAR_H / 2); ?>"
					     alt="<?php
                             echo $user_task['user_name']; ?>" title="<?php
                        echo $user_task['user_lastname']; ?>"/>
				</div>
				<div class="ead_support_response_content">
                    <?php
                        $ContentUrl = ($Link !== '#') ? BASE . '/' . $Link . '#' . $id : null; ?>
					<header class="ead_support_response_content_header">
						<h1>Comentário de <a target="_blank"
						                     href="dashboard.php?wc=users/create&id=<?php
                                                 echo $user_task['user_id']; ?>"
						                     title="<?php
                                                 echo \sprintf(
                                                     '%s %s',
                                                     $user_task['user_name'],
                                                     $user_task['user_lastname']
                                                 ); ?>"><?php
                                    echo \sprintf(
                                        '%s %s',
                                        $user_task['user_name'],
                                        $user_task['user_lastname']
                                    ); ?></a>dia <?php
                                echo \date(
                                    'd/m/Y H\hi',
                                    \strtotime((string)$created)
                                ); ?> <span class="j_comment_status"><?php
                                    echo $TicketStatus; ?></span>
						</h1>
						<p>Em:
                            <?php
                                if ($ContentUrl) { ?>
									<a target="_blank" title="Ver <?php
                                        echo $Title; ?>" href="<?php
                                        echo $ContentUrl; ?>"><?php
                                            echo $Title; ?></a>
                                    <?php
                                } else { ?>
									<span class="na"><?php
                                            echo $Title; ?></span>
                                    <?php
                                } ?>
						</p>
					</header>

					<div class="htmlchars response_chars"><?php
                            echo $comment; ?></div>

					<div class="ead_support_response_actions">
                        <span rel='ead_support_response' class='j_delete_action icon-cross btn btn_red'
                              id='<?php
                                  echo $id; ?>'>Apagar</span>
						<span rel='ead_support_response' callback='Comments' callback_action='remove_comment'
						      class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none;'
						      id='<?php
                                  echo $id; ?>'>Deletar Resposta?</span>
						<span class="btn btn_blue icon-pencil2 j_comment_action" data-action='comment_edit'
						      id="<?php
                                  echo $id; ?>">Editar</span>

                        <?php
                            if (2 == $status) {
                                ?>
								<span class="btn btn_green icon-checkmark j_comment_action ead_support_finish"
								      data-action='comment_completed' id="<?php
                                    echo $id; ?>">Concluir</span>
                                <?php
                            }

                            // ACTIONS COMMENTS
                            $Read->fullRead(
                                'SELECT id FROM ' . DB_COMMENTS_LIKES . ' WHERE user_id = :user AND comm_id = :comm',
                                \sprintf('user=%s&comm=%s', $_SESSION['userLogin']['user_id'], $Comm['id'])
                            );
                            if (!$Read->getResult()) {
                                ?>
								<a style="color: #fff; font-weight: bold;" href='#<?php
                                    echo $Comm['id']; ?>'
								   class='btn btn_blue wc_comment_action icon-heart' id="<?php
                                    echo $Comm['id']; ?>"
								   rel='<?php
                                       echo $Comm['id']; ?>' action='like' href='Gostei do Comentário'
								   title='Gostei do Comentário'>GOSTEI</a>
                                <?php
                            }
                        ?>

						<div class='comm_likes icon-heart' id='<?php
                            echo $Comm['id']; ?>'><span><?php
                                    echo $Likes; ?></span></div>
					</div>

					<div class="j_content">
                        <?php
                            // COUNT REPLIES
                            $Read->exeRead(DB_COMMENTS, 'WHERE alias_id = :id ORDER BY created ASC', 'id=' . $id);
                            $Reply = $Read->getResult();

                            if ($Reply) {
                                foreach ($Reply as $ResponseReply) {
                                    // VAR USER
                                    $Read->linkResult(
                                        DB_USERS,
                                        'user_id',
                                        $ResponseReply['user_id'],
                                        'user_id, user_name, user_lastname, user_email, user_thumb'
                                    );
                                    $user_reply = $Read->getResult()[0] ?? [
                                        'user_id' => 0,
                                        'user_name' => 'Usuário',
                                        'user_lastname' => 'removido',
                                        'user_email' => '-',
                                        'user_thumb' => null,
                                    ];
                                    $UserThumb = '../uploads/' . $user_reply['user_thumb'];
                                    $user_reply['user_thumb'] = (\file_exists($UserThumb) && !\is_dir(
                                        $UserThumb
                                    ) ? 'uploads/' . $user_reply['user_thumb'] : 'admin/_img/no_avatar.jpg');

                                    echo "<article class='ead_support_response ead_support_response_reply reply' id='{$ResponseReply['id']}'>
                                                <div class='ead_support_response_avatar'>
                                                    <img class='rounded' src='" . BASE . \sprintf(
                                            '/tim.php?src=%s&w=',
                                            $user_reply['user_thumb']
                                        ) . \round(AVATAR_W / 2) . '&h=' . \round(AVATAR_H / 2) . "' alt='{$user_reply['user_name']}' title='{$user_reply['user_lastname']}'/>
                                                </div><div class='ead_support_response_content'>
                                                    <header class='ead_support_response_content_header'>
                                                        <h1>Resposta de <a target='_blank' href='dashboard.php?wc=users/create&id= {$user_reply['user_id']}' title='{$user_reply['user_name']} {$user_reply['user_lastname']}'>{$user_reply['user_name']} {$user_reply['user_lastname']}</a> dia " . \date(
                                            'd/m/Y H\hi',
                                            \strtotime((string)$ResponseReply['created'])
                                        ) . "</h1>
                                                    </header>
                                                    <div class='htmlchars reply_chars'>{$ResponseReply['comment']}</div>
                                                    <div class='ead_support_response_actions'>
                                                        <span rel='ead_support_response_reply' class='j_delete_action icon-cross btn btn_red' id='{$ResponseReply['id']}'>Apagar</span>
                                                        <span rel='ead_support_response_reply' callback='Comments' callback_action='remove' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none;' id='{$ResponseReply['id']}'>Deletar Resposta?</span>
                                                        <span class='btn btn_blue icon-pencil2 center j_ead_support_action' data-action='ead_support_reply_edit' id='{$ResponseReply['id']}'>Editar</span>
                                                    </div>
                                                </div>       
                                                <div class='ead_support_response_edit_modal reply'>
                                                    <form name='class_adda' action='' method='post' enctype='multipart/form-data'>
                                                        <p class='title icon-pencil2'>Atualizar Resposta de {$user_reply['user_name']} {$user_reply['user_lastname']}</p>
                                                        <span class='btn btn_red icon-cross icon-notext ead_support_response_edit_modal_close j_comment_action_close'></span>
                                                        <input type='hidden' name='callback' value='Comments'/>
                                                        <input type='hidden' name='callback_action' value='edit_response'/>
                                                        <input type='hidden' name='id' value='{$ResponseReply['id']}'/>
                                                        <input type='hidden' name='user_id' value='{$ResponseReply['user_id']}'/>
                                                        <input type='hidden' name='type' value='reply'/>
                                                        <label class='label'>
                                                            <textarea class='work_mce_basic' style='font-size: 1em;' name='comment' rows='3'>" . \htmlspecialchars(
                                            (string)$ResponseReply['comment']
                                        ) . "</textarea>
                                                        </label>
                                                        <div class='wc_actions' style='margin-top: 15px;'>
                                                            <button class='btn btn_blue icon-pencil2'>ATUALIZAR RESPOSTA</button>
                                                            <img class='form_load none' style='margin-left: 10px;' alt='Enviando Requisição!' title='Enviando Requisição!' src='_img/load.gif'/>
                                                        </div>
                                                    </form>
                                                </div>
                                        </article>";
                                }
                            }
                        ?>
					</div>

                    <?php
                        if ($rank) {
                            $ReviewPositive = '<span class="icon-star-full icon-notext font_green"></span>';
                            $ReviewNegative = '<span class="icon-star-empty icon-notext font_red"></span>';
                            ?>
							<footer class="ead_support_response_review">
								<h1 class="icon-star-half">Avaliação: <?php
                                        echo $rank; ?></h1>
							</footer>
                            <?php
                        } ?>
				</div>

				<!--FORM EDIT RESPONSE-->
				<div class="ead_support_response_edit_modal response">
					<form name="class_edit" action="" method="post" enctype="multipart/form-data">
						<p class="title icon-pencil2">Atualizar Pergunta
							de <?php
                                echo \sprintf('%s %s', $user_task['user_name'], $user_task['user_lastname']); ?></p>
						<span class="btn btn_red icon-cross icon-notext ead_support_response_edit_modal_close j_ead_support_action_close"></span>

						<input type="hidden" name="callback" value="Comments"/>
						<input type="hidden" name="callback_action" value="edit_response"/>
						<input type='hidden' name='id' value='<?php
                            echo $Comm['id']; ?>'/>
						<input type='hidden' name='user_id' value='<?php
                            echo $Comm['user_id']; ?>'/>
						<input type='hidden' name='type' value='comment'/>

						<label class="label">
                            <textarea class="work_mce_basic" style="font-size: 1em;" name="comment"
                                      rows="3"><?php
                                    echo \htmlspecialchars((string)$comment); ?></textarea>
						</label>

						<div class="wc_actions" style="margin-top: 15px;">
							<button class="btn btn_blue icon-pencil2">ATUALIZAR PERGUNTA</button>
							<img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!"
							     title="Enviando Requisição!" src="_img/load.gif"/>
						</div>
					</form>
				</div>

				<!--FORM DELETE RESPONSE-->
				<div class="ead_support_response_edit_modal remove">
					<form name="class_add" action="" method="post" enctype="multipart/form-data">
						<p class="title icon-warning">Enviar notificação
							a <?php
                                echo \sprintf('%s %s', $user_task['user_name'], $user_task['user_lastname']); ?>:
							(Opcional)</p>
						<span class="btn btn_red icon-cross icon-notext ead_support_response_edit_modal_close j_ead_support_action_close"></span>

						<input type="hidden" name="callback" value="Comments"/>
						<input type="hidden" name="callback_action" value="remove"/>
						<input type="hidden" name="del_id" value="<?php
                            echo $id; ?>"/>

						<label class="label">
                            <textarea class="work_mce_basic" style="font-size: 1em;" name="mail_body"
                                      rows="3"></textarea>
						</label>

						<div class="wc_actions" style="margin-top: 15px;">
							<button class="btn btn_red icon-cross">NOTIFICAR E EXCLUIR</button>
							<img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!"
							     title="Enviando Requisição!" src="_img/load.gif"/>
						</div>
					</form>
				</div>
			</article>

			<form name="class_add" class="ead_support_response_form" action="" method="post"
			      enctype="multipart/form-data">
				<input type="hidden" name="callback" value="Comments"/>
				<input type="hidden" name="callback_action" value="response"/>
				<input type="hidden" name="alias_id" value="<?php
                    echo $id; ?>"/>
				<input type='hidden' name='user_id' value='<?php
                    echo $Comm['user_id']; ?>'/>

				<label class="label">
					<textarea class="work_mce_basic" style="font-size: 1em;" name="comment" rows="3"></textarea>
				</label>

				<div class="wc_actions" style="margin-top: 25px;">
					<button style="padding: 10px 25px; margin-right: 10px;" class="btn btn_green icon-bubble">
						RESPONDER
					</button>
					<a class="btn btn_yellow icon-arrow-right" style="padding: 10px 25px;"
					   href="dashboard.php?wc=comments/comment_response" title="Próximo Comentário Pendente">PRÓXIMO
						COMENTÁRIO</a>
					<img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!"
					     title="Enviando Requisição!" src="_img/load.gif"/>
				</div>
			</form>
		</div>
        <?php
            }
        ?>
	</section>
</div>
<script src="_js/wccomments.js"></script>
