<?php

    use App\Conn\Read;
    use App\Helpers\Check;
    use App\Models\Pager;

    $AdminLevel = LEVEL_WC_COMMENTS;
    if (!APP_COMMENTS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-bubbles2">Comentários</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			Comentários
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

		<div class="panel_header default">
			<h2 class="icon-bubbles3">#Responda seus comentários</h2>
		</div>

		<div class="panel">
            <?php
                // PAGINATOR
                $getPage = \filter_input(INPUT_GET, 'p', FILTER_VALIDATE_INT);
                $Page = ($getPage && $getPage > 0) ? $getPage : 1;
                $Pager = new Pager('dashboard.php?wc=comments/home&p=', '<<', '>>', 1);
                $Pager->exePager($Page, 12);

                // READ COMMENT
                $Read->exeRead(
                    DB_COMMENTS,
                    'WHERE alias_id IS NULL ORDER BY status DESC, created DESC LIMIT :limit OFFSET :offset',
                    \sprintf('limit=%d&offset=%d', $Pager->getLimit(), $Pager->getOffset())
                );
                if (!$Read->getResult()) {
                    $Pager->returnPage();
                    echo Check::erro(
                        \sprintf(
                            '<span>Ainda não existem comentários %s. Mas isso não deve demorar!</span>',
                            $_SESSION['userLogin']['user_name']
                        ),
                        E_USER_NOTICE
                    );
                } else {
                    // STATUS
                    $SupportStatus = [1 => 'Respondido', 2 => 'Em Aberto', 3 => 'Moderar'];
                    $SupportStatusClass = [
                        1 => 'btn_blue icon-bubbles3',
                        2 => 'btn_red icon-bubble2',
                        3 => 'btn_yellow icon-warning'
                    ];

                    foreach ($Read->getResult() as $Comm) {
                        // USERS
                        $Read->fullRead(
                            'SELECT user_id, user_name, user_lastname, user_thumb, user_email FROM ' . DB_USERS . ' WHERE user_id = :id',
                            'id=' . $Comm['user_id']
                        );
                        $UserData = $Read->getResult()[0] ?? null;
                        $UserId = $UserData['user_id'] ?? 0;
                        $User = \sprintf(
                            '%s %s',
                            $UserData['user_name'] ?? 'Usuário',
                            $UserData['user_lastname'] ?? 'removido'
                        );
                        $UserEmail = $UserData['user_email'] ?? '-';

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

                        // COUNT REPLIES
                        $Read->exeRead(DB_COMMENTS, 'WHERE alias_id = :id', 'id=' . $Comm['id']);
                        $Reply = $Read->getResult();
                        $ReplyCount = ($Reply ? $Read->getRowCount() : '0');

                        // RANK
                        $Stars = \str_repeat(
                            "<span class='icon-star-full icon-notext font_green review'></span>",
                            $Comm['rank']
                        );
                        ?>
						<article class="ead_support_single single_comment" id="<?php
                            echo $Comm['id']; ?>">
                            <?php
                                $ContentUrl = ($Link !== '#') ? BASE . '/' . $Link . '#' . $Comm['id'] : null; ?>
							<h1 class="row">
                                <?php
                                    if ($ContentUrl) { ?>
										<a class="a" target="_blank" href="<?php
                                            echo $ContentUrl; ?>" title="Ver comentário">
											#<?php
                                                echo \str_pad((string)$Comm['id'], 4, 0, 0); ?> - <?php
                                                echo $Title; ?>
										</a>
                                        <?php
                                    } else { ?>
										<span class="na">#<?php
                                                echo \str_pad((string)$Comm['id'], 4, 0, 0); ?> - <?php
                                                echo $Title; ?></span>
                                        <?php
                                    } ?>
							</h1>
							<p class="row icon-user-plus">
								Por <a class="a" target="_blank"
								       href="dashboard.php?wc=&id=<?php
                                           echo $Comm['user_id']; ?>"
								       title="<?php
                                           echo $User; ?>"><?php
                                        echo $User; ?></a>
								<span><?php
                                        echo $UserEmail; ?></span>
							</p>
							<p class="row icon-bubble2">
                                <?php
                                    echo $ReplyCount; ?> Resposta<?php
                                    echo ($ReplyCount >= 2) ? 's' : ''; ?> - <?php
                                    echo $Stars; ?>
							</p>
							<p class="row icon-hour-glass">
                                <?php
                                    echo \date(
                                        'd/m/Y H\hi',
                                        \strtotime($Comm['interact'] ? $Comm['interact'] : $Comm['created'])
                                    ); ?>
							</p>
							<p class="row btn_support">
								<a title="Responder Ticket"
								   href="dashboard.php?wc=comments/comment_response&id=<?php
                                       echo $Comm['id']; ?>"
								   class="btn btn_blue <?php
                                       echo $SupportStatusClass[$Comm['status']] ?? 'btn_blue icon-bubbles3'; ?>"><?php
                                        echo $SupportStatus[$Comm['status']] ?? 'Respondido'; ?></a>
							</p>
						</article>

                        <?php
                    }
                    $Pager->exePaginator(DB_COMMENTS, 'WHERE alias_id IS NULL');
                    echo $Pager->getPaginator();
                }
            ?>
			<div class="clear"></div>
		</div>
	</section>
</div>
