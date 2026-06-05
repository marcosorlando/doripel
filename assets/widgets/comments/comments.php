<?php

    use App\Conn\Delete;
    use App\Conn\Read;
    use App\Helpers\Check;

    if (empty($CommentKey) || empty($CommentType)) {
        echo Check::erro(
            '<b>&#9888; COMMENT ERROR:</b> Para iniciar os comentários é preciso definir as variáveis de identificação! Você pode criar comentários para qualquer APP do Work Control, bastando definir as seguintes variáveis:<p><b>$CommentType:</b> Id do post, página, produto ou curso que receberá os comentários!</p><p><b>$CommentKey:</b> post, page, product ou course (Destino do comentário!)</p>',
            E_USER_WARNING
        );

        return;
    }

    $_SESSION['comm'] = [];

    $Read ??= new Read();

    echo '<link rel="stylesheet" href="' . BASE . '/assets/widgets/comments/comments.min.css"/>';
    echo '<script src="' . BASE . '/assets/widgets/comments/comments.min.js"></script>';

// USUÁRIO
    $UserName = empty($_SESSION['userLogin']) ? null : ' ' . $_SESSION['userLogin']['user_name'];

// COMMENT TYPE
    $CommentModerate = (COMMENT_MODERATE !== 0 ? ' AND (status = 1 OR status = 3)' : '');
    if ('post' == $CommentType) {
        // TOTAL COMMENTS
        $Read->fullRead(
            'SELECT count(id) AS total FROM ' . DB_COMMENTS . ' WHERE post_id = :post OR alias_id IN(SELECT id FROM ' . DB_COMMENTS . ' WHERE post_id = :post)',
            'post=' . $CommentKey
        );
        $CommentCount = $Read->getResult()[0]['total'];

        // COMMENTS
        $Read->exeRead(
            DB_COMMENTS,
            \sprintf(
                'WHERE post_id = :post%s AND alias_id IS NULL ORDER BY created ',
                $CommentModerate
            ) . COMMENT_ORDER,
            'post=' . $CommentKey
        );
        $CommentTitle = ($post_title ?? '');
        $_SESSION['comm']['post_id'] = $CommentKey;

        echo "<section class='comments' id='comments'>";
        echo \sprintf(
            "<header><h5>Olá%s, <strong>deixe seu comentário</strong> para artigo: <br> <b class='text-small text-outside-line-full alt-font font-weight-600 text-uppercase text-extra-dark-gray'>%s</b></h5></header>",
            $UserName,
            $CommentTitle
        );
    } elseif ('page' == $CommentType) {
        // TOTAL COMMENTS
        $Read->fullRead(
            'SELECT count(id) AS total FROM ' . DB_COMMENTS . ' WHERE page_id = :post OR alias_id IN(SELECT id FROM ' . DB_COMMENTS . ' WHERE page_id = :post)',
            'post=' . $CommentKey
        );
        $CommentCount = $Read->getResult()[0]['total'];

        // COMMENTS
        $Read->exeRead(
            DB_COMMENTS,
            \sprintf(
                'WHERE page_id = :post%s AND alias_id IS NULL ORDER BY created ',
                $CommentModerate
            ) . COMMENT_ORDER,
            'post=' . $CommentKey
        );
        $CommentTitle = ($page_title ?? '');
        $_SESSION['comm']['page_id'] = $CommentKey;

        echo "<section class='comments' id='comments'>";
        echo '<header><h1>Avalie o conteúdo desta página!</h1></header>';
    } elseif ('product' == $CommentType) {
        // TOTAL COMMENTS
        $Read->fullRead(
            'SELECT count(id) AS total FROM ' . DB_COMMENTS . ' WHERE pdt_id = :post OR alias_id IN(SELECT id FROM ' . DB_COMMENTS . ' WHERE pdt_id = :post)',
            'post=' . $CommentKey
        );
        $CommentCount = $Read->getResult()[0]['total'];

        // COMMENTS
        $Read->exeRead(
            DB_COMMENTS,
            \sprintf('WHERE pdt_id = :post%s AND alias_id IS NULL ORDER BY created ', $CommentModerate) . COMMENT_ORDER,
            'post=' . $CommentKey
        );
        $CommentTitle = ($pdt_title ?? '');
        $_SESSION['comm']['pdt_id'] = $CommentKey;

        echo "<section class='comments' id='comments'>";
        echo \sprintf('<header><h5>%s</h5><h6>Confira opiniões e avaliações de clientes!</h6></header>', $CommentTitle);
    }

    if ($Read->getResult()) {
        echo \sprintf(
            "<div class='comments_count'>Já temos %s comentário(s). <b>DEIXE O SEU </b><i class='fa fa-heart text-red'></i></div>",
            $CommentCount
        );
        foreach ($Read->getResult() as $Comment) {
            $Read->fullRead(
                'SELECT user_id, user_thumb, user_name, user_lastname FROM ' . DB_USERS . ' WHERE user_id = :id',
                'id=' . $Comment['user_id']
            );
            if (!$Read->getResult()) {
                $Delete = new Delete();
                $Delete->exeDelete(DB_COMMENTS, 'WHERE id = :id OR alias_id = :id', 'id=' . $Comment['id']);
                \header('Location: ' . BASE . ('/' . $getURL));

                exit;
            }

            $UserComment = $Read->getResult()[0];
            $UserAvatar = ($UserComment['user_thumb'] ? BASE . \sprintf(
                    '/tim.php?src=uploads/%s&w=',
                    $UserComment['user_thumb']
                ) . AVATAR_W . '&h=' . AVATAR_H : BASE . '/tim.php?src=admin/_img/no_avatar.jpg&w=' . AVATAR_W . '&h=' . AVATAR_H);

            $CommentStars = \str_repeat('&starf;', $Comment['rank']) . \str_repeat('&star;', 5 - $Comment['rank']);

            echo \sprintf("<article class='comments_single' id='comment%s'>", $Comment['id']);
            echo \sprintf(
                "<div class='comments_single_avatar'><img alt='%s %s' title='%s %s' src='%s'/></div>",
                $UserComment['user_name'],
                $UserComment['user_lastname'],
                $UserComment['user_name'],
                $UserComment['user_lastname'],
                $UserAvatar
            );
            echo "<div class='comments_single_content'>";
            echo \sprintf('<header><h1>%s %s</h1></header>', $UserComment['user_name'], $UserComment['user_lastname']);
            echo "<div class='comments_single_comment'>" . \nl2br((string)$Comment['comment']) . '</div>';

            // LIKE COUNT
            $Read->fullRead(
                'SELECT count(id) as total FROM ' . DB_COMMENTS_LIKES . ' WHERE comm_id = :comm',
                'comm=' . $Comment['id']
            );
            $LikeCount = $Read->getResult()[0]['total'];

            // LIKES
            $Read->fullRead(
                'SELECT user_id, user_name, user_lastname FROM ' . DB_USERS . ' WHERE user_id IN(SELECT user_id FROM ' . DB_COMMENTS_LIKES . ' WHERE comm_id = :comm)',
                'comm=' . $Comment['id']
            );
            if ($Read->getResult()) {
                $getLikes = [];
                foreach ($Read->getResult() as $UserLike) {
                    if (!empty($_SESSION['userLogin']) && $_SESSION['userLogin']['user_id'] == $UserLike['user_id']) {
                        $getLikes[] = '<span><b>EU</b></span>';
                        $LikeThisPost = true;
                    } else {
                        $getLikes[] = \sprintf(
                            '<span>%s %s</span>',
                            $UserLike['user_name'],
                            $UserLike['user_lastname']
                        );
                    }
                }

                $Likes = \implode(', ', (array)$getLikes);
            } else {
                $Likes = '<span class="na">N/A</span>';
            }

            echo "<div class='comments_single_ui'>";
            echo \sprintf("<span class='stars'>%s</span>", $CommentStars);
            echo "<span class='date'>DIA " . \date('d.m.y H\hi', \strtotime((string)$Comment['created'])) . '</span>';
            if (empty($LikeThisPost)) {
                echo \sprintf(
                    "<span class='like wc_like' id='%s'><b>%s</b> <i class='text-blue fa fa-thumbs-up animated pulse infinite'></i> GOSTEI</span>",
                    $Comment['id'],
                    $LikeCount
                );
            } else {
                $LikeThisPost = null;
                echo \sprintf(
                    "<span class='liked'><b>%s</b> <i class='fa fa-thumbs-up text-blue'></i> VOCÊ JÁ CURTIU ISSO!</span>",
                    $LikeCount
                );
            }

            echo \sprintf(
                "<span class='response wc_response' id='%s' rel='%s'> <i class='fa fa-comments'></i> RESPONDER</span>",
                $Comment['id'],
                $UserComment['user_id']
            );
            echo '</div>'; // Ui
            echo \sprintf(
                "<div class='comments_single_likes' id='%s'><span><i class='text-red fa fa-heart animated pulse infinite'></i> %s</span></div>",
                $Comment['id'],
                $Likes
            );

            // FORM RESPONSE
            require \dirname(__DIR__) . '/comments/comment.form.php';

            // #############################
            // ################# SUBCOMMENTS
            // #############################
            $Read->exeRead(
                DB_COMMENTS,
                \sprintf('WHERE alias_id = :id%s ORDER BY created ', $CommentModerate) . COMMENT_RESPONSE_ORDER,
                'id=' . $Comment['id']
            );
            if ($Read->getResult()) {
                foreach ($Read->getResult() as $Response) {
                    $Read->fullRead(
                        'SELECT user_id, user_thumb, user_name, user_lastname FROM ' . DB_USERS . ' WHERE user_id = :id',
                        'id=' . $Response['user_id']
                    );
                    if (!$Read->getResult()) {
                        $Delete = new Delete();
                        $Delete->exeDelete(DB_COMMENTS, 'WHERE id = :id OR alias_id = :id', 'id=' . $Response['id']);
                        \header('Location: ' . BASE . ('/' . $getURL));

                        exit;
                    }

                    $UserComment = $Read->getResult()[0];
                    $UserAvatar = ($UserComment['user_thumb'] ? BASE . \sprintf(
                            '/tim.php?src=uploads/%s&w=',
                            $UserComment['user_thumb']
                        ) . AVATAR_W . '&h=' . AVATAR_H : BASE . '/tim.php?src=admin/_img/no_avatar.jpg&w=' . AVATAR_W . '&h=' . AVATAR_H);

                    $CommentStars = \str_repeat('&starf;', $Response['rank']) . \str_repeat(
                            '&star;',
                            5 - $Response['rank']
                        );

                    echo \sprintf("<article class='comments_single comment_response' id='comment%s'>", $Response['id']);
                    echo \sprintf(
                        "<div class='comments_single_avatar'><img alt='%s %s' title='%s %s' src='%s'/></div>",
                        $UserComment['user_name'],
                        $UserComment['user_lastname'],
                        $UserComment['user_name'],
                        $UserComment['user_lastname'],
                        $UserAvatar
                    );
                    echo "<div class='comments_single_content'>";
                    echo \sprintf(
                        '<header><h1>%s %s</h1></header>',
                        $UserComment['user_name'],
                        $UserComment['user_lastname']
                    );
                    echo "<div class='comments_single_comment'>" . \nl2br((string)$Response['comment']) . '</div>';

                    // LIKE COUNT
                    $Read->fullRead(
                        'SELECT count(id) as total FROM ' . DB_COMMENTS_LIKES . ' WHERE comm_id = :comm',
                        'comm=' . $Response['id']
                    );
                    $LikeCount = $Read->getResult()[0]['total'];

                    // LIKES
                    $Read->fullRead(
                        'SELECT user_id, user_name, user_lastname FROM ' . DB_USERS . ' WHERE user_id IN(SELECT user_id FROM ' . DB_COMMENTS_LIKES . ' WHERE comm_id = :comm)',
                        'comm=' . $Response['id']
                    );
                    if ($Read->getResult()) {
                        $getLikes = [];
                        foreach ($Read->getResult() as $UserLike) {
                            if (!empty($_SESSION['userLogin']) && $_SESSION['userLogin']['user_id'] == $UserLike['user_id']) {
                                $getLikes[] = '<span><b>EU</b></span>';
                                $LikeThisPost = true;
                            } else {
                                $getLikes[] = \sprintf(
                                    '<span>%s %s</span>',
                                    $UserLike['user_name'],
                                    $UserLike['user_lastname']
                                );
                            }
                        }

                        $Likes = \implode(', ', (array)$getLikes);
                    } else {
                        $Likes = '<span class="na">N/A</span>';
                    }

                    echo "<div class='comments_single_ui'>";
                    echo \sprintf("<span class='stars'>%s</span>", $CommentStars);
                    echo "<span class='date'>DIA " . \date(
                            'd.m.y H\hi',
                            \strtotime((string)$Response['created'])
                        ) . '</span>';
                    if (empty($LikeThisPost)) {
                        echo \sprintf(
                            "<span class='like wc_like' id='%s'><b>%s</b> <i class='text-blue fa fa-thumbs-up animated pulse infinite'></i> GOSTEI</span>",
                            $Response['id'],
                            $LikeCount
                        );
                    } else {
                        $LikeThisPost = null;
                        echo \sprintf(
                            "<span class='liked'><b>%s</b> <i class='text-blue fa fa-thumbs-up'></i> VOCÊ JÁ CURTIU ISSO!</span>",
                            $LikeCount
                        );
                    }

                    echo \sprintf(
                        "<span class='response wc_response' id='%s' rel='%s'><i class='fa fa-comments'></i> RESPONDER</span></span>",
                        $Response['id'],
                        $UserComment['user_id']
                    );
                    echo '</div>'; // Ui
                    echo \sprintf(
                        "<div class='comments_single_likes' id='%s'><span><i class='text-red fa fa-heart animated pulse infinite'></i> %s</span></div>",
                        $Response['id'],
                        $Likes
                    );

                    // FORM RESPONSE
                    require \dirname(__DIR__) . '/comments/comment.form.php';

                    echo '</div>'; // Content
                    echo '</article>';
                }
            }

            // END SUBCOMMENTS

            echo '</div>'; // Content
            echo '</article>';
        }
    }

    require \dirname(__DIR__) . '/comments/comment.form.php';
    echo '</section>';

// FORM LOGIN
    require \dirname(__DIR__) . '/comments/comment.modal.php';
