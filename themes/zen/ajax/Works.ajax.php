<?php

//DEFINE O CALLBACK E RECUPERA O POST
    $jSON = null;
    $CallBack = 'Works';
    $PostData = filter_input_array(INPUT_POST, FILTER_DEFAULT) ?? [];

//VALIDA AÇÃO
    if (isset($PostData['callback_action'], $PostData['callback']) && $PostData['callback'] === $CallBack) {
        //PREPARA OS DADOS
        $Case = $PostData['callback_action'];
        unset($PostData['callback'], $PostData['callback_action']);

        require_once __DIR__ . '/../../../vendor/autoload.php';
        $Read = new Read;
        $Update = new Update;


        //SELECIONA AÇÃO
        switch ($Case) {
            //NEXT
            case 'next':
                $Next_id = intval($PostData['controll_id']);
                $Read->exeRead(DB_PORTFOLIO, "WHERE id > :id LIMIT 1", "id={$Next_id}");

                if ($Read->getResult()) {
                    $Next = $Read->getResult()[0]['name'];

                    $UpdateView = [
                        'views' => $Read->getResult()[0]['views'] + 1,
                        'lastview' => date('Y-m-d H:i:s')
                    ];

                    $Update->exeUpdate(
                        DB_PORTFOLIO,
                        $UpdateView,
                        "WHERE id = :id",
                        "id={$Read->getResult()[0]['id']}"
                    );
                } else {
                    $Next = $PostData['link_atual'];
                }

                $jSON['redirect'] = BASE . "/trampo/" . $Next;

                break;

            case 'previews':
                $Next_id = intval($PostData['controll_id']);
                $Read->exeRead(DB_PORTFOLIO, "WHERE id < :id LIMIT 1", "id={$Next_id}");

                if ($Read->getResult()) {
                    $Next = $Read->getResult()[0]['name'];

                    $UpdateView = [
                        'views' => $Read->getResult()[0]['views'] + 1,
                        'lastview' => date('Y-m-d H:i:s')
                    ];

                    $Update->exeUpdate(
                        DB_PORTFOLIO,
                        $UpdateView,
                        "WHERE id = :id",
                        "id={$Read->getResult()[0]['id']}"
                    );
                } else {
                    $Next = $PostData['link_atual'];
                }
                $jSON['redirect'] = BASE . "/trampo/" . $Next;
                break;
//			case 'next-ajax':
//			$Next_id = intval($PostData['controll_id']);
//
//			//$Read->exeRead(DB_PORTFOLIO, "WHERE id > :id LIMIT 1", "id={$Next_id}");
//			$Read->fullRead("SELECT ws_works.*, ws_works_categories.title, ws_works_categories.id FROM " . DB_PORTFOLIO . " INNER JOIN " . DB_PORTFOLIO_CATEGORIES . " ON (`ws_works`.`category` = `ws_works_categories`.`id`) WHERE id > :id LIMIT 1", "id={$Next_id}");
//
//			if ($Read->getResult()) {
//				extract($Read->getResult()[0]);
//
//				$Next = $Read->getResult()[0]['name'];
//				$Next_id = $Read->getResult()[0]['id'];
//
//				$Read->exeRead(DB_PORTFOLIO, "WHERE id > :id LIMIT 1", "id={$Next_id}");
//
//				$UpdateView = [
//						'views' => $Read->getResult()[0]['views'] + 1,
//						'lastview' => date('Y-m-d H:i:s')
//				];
//
//				$Update->exeUpdate(DB_PORTFOLIO, $UpdateView, "WHERE id = :id", "id={$Read->getResult()[0]['id']}");
//
//			} else {
//				$Next = $PostData['link_atual'];
//			}
//
//			//$jSON['redirect'] = BASE . "/trampo/" . $Next;
//
//			$img_970x500 = BASE."/uploads/portfolio/". $img_970x500;
//
//			$jSON ['next'] = [
//				'id' => $Next_id,
//				'title' => $title,
//				'description' => $description,
//				'client' => $client,
//				'link_project' => $link_project ,
//				'skills' => $skills,
//				'creator' => $creator ,
//				'delivery' => $delivery,
//				'category' => $title,
//				'author' => $author,
//				'img_970x500' => $img_970x500 ,
//				'views' => $UpdateView['views'],
//				'lastview' => $lastview
//			];
//
//			break;

        }

        //RETORNA O CALLBACK
        if ($jSON) {
            echo json_encode($jSON);
        } else {
            $jSON['trigger'] = AjaxErro(
                '<b class="icon-warning">OPSS:</b> Desculpe. Mas uma ação do sistema não respondeu corretamente. Ao persistir, contate o desenvolvedor!',
                E_USER_ERROR
            );
            echo json_encode($jSON);
        }
    } else {
        //ACESSO DIRETO
        die('<br><br><br><center><h1>Acesso Restrito!</h1></center>');
    }
