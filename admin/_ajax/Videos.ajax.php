<?php

use App\Conn\Create;
use App\Conn\Delete;
use App\Conn\Read;
use App\Conn\Update;
use App\Helpers\Check;
use App\Models\Upload;

\session_start();

require __DIR__.'/../../vendor/autoload.php';
$NivelAcess = 6;

if (!APP_VIDEOS || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess) {
    $jSON['trigger'] = Check::ajaxErro(
        '<b>OPPSSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!',
        E_USER_ERROR
    );
    echo \json_encode($jSON);

    exit;
}

\usleep(50000);

// DEFINE O CALLBACK E RECUPERA O VÍDEO
$jSON = null;
$CallBack = 'Videos';
$PostData = \filter_input_array(INPUT_POST, FILTER_DEFAULT) ?? [];

// VALIDA AÇÃO
if (isset($PostData['callback_action'], $PostData['callback']) && $PostData['callback'] === $CallBack) {
    // PREPARA OS DADOS
    $Case = $PostData['callback_action'];
    unset($PostData['callback'], $PostData['callback_action']);

    $Read = new Read();
    $Create = new Create();
    $Update = new Update();
    $Delete = new Delete();

    // SELECIONA AÇÃO
    switch ($Case) {
        // GERENCIA
        case 'manager':
            $VideoId = $PostData['video_id'];
            $VideoLink = $PostData['video_link'];
            $VideoEnd = (empty($PostData['video_end']) ? null : $PostData['video_end']);
            $Image = (empty($_FILES['video_image']) ? null : $_FILES['video_image']);
            unset($PostData['video_id'], $PostData['video_end'], $PostData['video_image']);

            $Read->fullRead('SELECT video_image FROM '.DB_YOUTUBE.' WHERE video_id = :id', "id={$VideoId}");
            $CurrentImage = $Read->getResult()[0]['video_image'] ?? null;

            //          if (empty($Image) && (!$Read->getResult() || !$Read->getResult()[0]['video_image'])):
            //              $jSON['trigger'] = Check::ajaxErro('<b>ERRO AO CADASTRAR:</b> Favor envie uma capa de vídeo nas medidas de ' . SLIDE_W . 'x' . SLIDE_H . 'px!', E_USER_ERROR);
            //          else
            $PostData = \array_map(
                static fn ($value) => \is_string($value) ? \trim($value) : $value,
                $PostData
            );

            $YouTubeId = Check::youtubeVideoId((string) $VideoLink);
            if (null === $YouTubeId) {
                $jSON['trigger'] = Check::ajaxErro(
                    '<b>ERRO AO CADASTRAR:</b> Informe um link válido do YouTube!',
                    E_USER_ERROR
                );
                $jSON['error'] = true;

                break;
            }

            $PostData['video_link'] = $YouTubeId;

            if (\in_array('', $PostData)) {
                $jSON['trigger'] = Check::ajaxErro(
                    '<b>ERRO AO CADASTRAR:</b> Para atualizar o vídeo, favor preencha todos os campos!',
                    E_USER_ERROR
                );
                $jSON['error'] = true;
            } else {
                $PostData['video_date'] = \date('Y-m-d H:i:s');
                $PostData['video_start'] = Check::Data($PostData['video_start']);
                $PostData['video_end'] = (empty($VideoEnd) ? null : Check::Data($VideoEnd));

                $uploadsBaseDir = \realpath(__DIR__.'/../../uploads') ?: (__DIR__.'/../../uploads');
                $currentImagePath = $CurrentImage ? $uploadsBaseDir.'/'.$CurrentImage : null;

                if (!empty($Image)) {
                    if (
                        $CurrentImage
                        && $currentImagePath
                        && \file_exists($currentImagePath)
                        && !\is_dir($currentImagePath)
                    ) {
                        \unlink($currentImagePath);
                    }

                    $Upload = new Upload('../../uploads/');
                    $Upload->image($Image, Check::name($PostData['video_title']), SLIDE_W, 'videos');
                    $PostData['video_image'] = $Upload->getResult();
                } elseif (!$CurrentImage || !$currentImagePath || !\file_exists($currentImagePath)) {
                    $videosDir = \rtrim($uploadsBaseDir, DIRECTORY_SEPARATOR).'/videos';
                    $downloadedThumb = null;
                    $safeBaseName = Check::name($PostData['video_title'].'-'.$YouTubeId);
                    if ('' === $safeBaseName) {
                        $safeBaseName = $YouTubeId;
                    }

                    $qualities = ['maxresdefault', 'sddefault', 'hqdefault', 'mqdefault', 'default'];
                    foreach ($qualities as $quality) {
                        $thumbUrl = Check::youtubeThumbnailUrl($YouTubeId, $quality);
                        if (null === $thumbUrl || '' === $thumbUrl || '0' === $thumbUrl) {
                            continue;
                        }

                        $context = \stream_context_create([
                            'http' => ['timeout' => 5],
                            'https' => ['timeout' => 5],
                        ]);
                        $imageData = @\file_get_contents($thumbUrl, false, $context);
                        if (false === $imageData) {
                            continue;
                        }

                        if (!\is_dir($videosDir) && !\mkdir($videosDir, 0755, true) && !\is_dir($videosDir)) {
                            break;
                        }

                        $fileName = \sprintf('%s-%s.jpg', $safeBaseName, $quality);
                        $fullPath = $videosDir.'/'.$fileName;
                        if (false !== \file_put_contents($fullPath, $imageData)) {
                            $downloadedThumb = 'videos/'.$fileName;

                            break;
                        }
                    }

                    if (null !== $downloadedThumb) {
                        $PostData['video_image'] = $downloadedThumb;
                    }
                }

                $Update->exeUpdate(DB_YOUTUBE, $PostData, 'WHERE video_id = :id', 'id='.$VideoId);
                $jSON['trigger'] = Check::ajaxErro(
                    \sprintf(
                        '<b>Tudo certo %s</b>: O vídeo foi atualizado com sucesso. E sera exibido nas datas cadastradas!',
                        $_SESSION['userLogin']['user_name']
                    )
                );
            }

            break;

            // DELETA
        case 'delete':
            $VideoId = $PostData['del_id'];
            $Read->fullRead('SELECT video_image FROM '.DB_YOUTUBE.' WHERE video_id = :id', 'id='.$VideoId);
            if ($Read->getResult()) {
                $VideoImage = (empty($Read->getResult()[0]['video_image']) ? null : $Read->getResult(
                )[0]['video_image']);
                if (
                    $VideoImage && \file_exists('../../uploads/'.$VideoImage) && !\is_dir(
                        '../../uploads/'.$VideoImage
                    )
                ) {
                    \unlink('../../uploads/'.$VideoImage);
                }
            }

            $Delete->exeDelete(DB_YOUTUBE, 'WHERE video_id = :id', 'id='.$VideoId);
            $jSON['success'] = true;

            break;
    }

    // RETORNA O CALLBACK
    if ($jSON) {
        echo \json_encode($jSON);
    } else {
        $jSON['trigger'] = Check::ajaxErro(
            '<b>OPSS:</b> Desculpe. Mas uma ação do sistema não respondeu corretamente. Ao persistir, contate o desenvolvedor!',
            E_USER_ERROR
        );
        echo \json_encode($jSON);
    }
} else {
    // ACESSO DIRETO
    exit('<br><br><br><center><h1>Acesso Restrito!</h1></center>');
}
