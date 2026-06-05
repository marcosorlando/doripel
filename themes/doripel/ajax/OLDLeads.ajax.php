<?php
//DEFINE O CALLBACK E RECUPERA O POST
require_once '../../../_app/Config.inc.php';

$jSON = null;
$CallBack = 'Leads';
$PostData = filter_input_array(INPUT_POST, FILTER_DEFAULT);

//VALIDA AÇÃO
if ($PostData && $PostData['callback_action'] && $PostData['callback'] = $CallBack):
  //PREPARA OS DADOS
  $Case = $PostData['callback_action'];
  unset($PostData['callback'], $PostData['callback_action']);

  //ELIMINA CÓDIGOS
  $PostData = array_map('strip_tags', $PostData);
  
  //var_dump($PostData);

  //SELECIONA AÇÃO
  switch ($Case):
    //CAPTURA DE ACORDO COM CALLBACK-ACTION
    case 'news1':
      if (in_array('', $PostData)):
        $jSON['trigger'] = AjaxErro('<b>OPPSSS:</b> Informe seu e-mail por favor!', E_USER_NOTICE);
      else:
        if (!Check::Email($PostData['email']) || !filter_var($PostData['email'], FILTER_VALIDATE_EMAIL)):
          $jSON['trigger'] = AjaxErro('<b>OPPSSS:</b> E-mail informado não é válido!', E_USER_NOTICE);
        else:

          $LeadData = [
              'lead_name' => NULL,
              'lead_email' => $PostData['email'],
              'lead_conversion' => $Case
          ];

          $Create = new Create;
          $Create->ExeCreate(DB_LEADS, $LeadData);

          $jSON['trigger'] = AjaxErro("<b>Obrigado!</b> Seu e-mail foi registrado com Sucesso!");
        //$jSON['redirect'] = 'dashboard.php?wc=home';
        endif;
      endif;
      break;

    case 'news2':
      if (in_array('', $PostData)):
        $jSON['trigger'] = AjaxErro('<b>OPPSSS:</b> Informe seu e-mail por favor!', E_USER_NOTICE);
      else:
        if (!Check::Email($PostData['email']) || !filter_var($PostData['email'], FILTER_VALIDATE_EMAIL)):
          $jSON['trigger'] = AjaxErro('<b>OPPSSS:</b> E-mail informado não é válido!', E_USER_NOTICE);
        else:

          $LeadData = [
              'lead_name' => NULL,
              'lead_email' => $PostData['email'],
              'lead_conversion' => $Case
          ];

          $Create = new Create;
          $Create->ExeCreate(DB_LEADS, $LeadData);

          $jSON['trigger'] = AjaxErro("<b>Obrigado!</b> Seu e-mail foi registrado com Sucesso!");
        //$jSON['redirect'] = 'dashboard.php?wc=home';
        endif;
      endif;
      break;
  endswitch;

  //RETORNA O CALLBACK
  if ($jSON):
    echo json_encode($jSON);
  else:
    $jSON['trigger'] = AjaxErro('<b class="icon-warning">OPSS:</b> Desculpe. Mas uma ação do sistema não respondeu corretamente. Ao persistir, contate o desenvolvedor!', E_USER_ERROR);
    echo json_encode($jSON);
  endif;
else:
  //ACESSO DIRETO
  die('<br><br><br><center><h1>Acesso Restrito!</h1></center>');
endif;

