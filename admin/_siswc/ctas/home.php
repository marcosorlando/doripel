<?php
$AdminLevel = LEVEL_WC_CTAS;
if (!APP_CTAS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
    die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
endif;

//AUTO DELETE POST TRASH
if (DB_AUTO_TRASH):
    $Delete = new Delete;
    $Delete->ExeDelete(DB_CTAS, "WHERE cta_image IS NULL and cta_text IS NULL","");

    //AUTO TRASH IMAGES
//    $Read->FullRead("SELECT image FROM " . DB_PRESENTIAL_IMAGE . " WHERE cta_id NOT IN(SELECT cta_id FROM " . DB_PRESENTIAL . ")");
//    if ($Read->getResult()):
//        $Delete->ExeDelete(DB_PRESENTIAL_IMAGE, "WHERE id >= :id AND cta_id NOT IN(SELECT cta_id FROM " . DB_PRESENTIAL . ")", "id=1");
//        foreach ($Read->getResult() as $ImageRemove):
//            if (file_exists("../uploads/{$ImageRemove['image']}") && !is_dir("../uploads/{$ImageRemove['image']}")):
//                unlink("../uploads/{$ImageRemove['image']}");
//            endif;
//        endforeach;
//    endif;
endif;

// AUTO INSTANCE OBJECT READ
if (empty($Read)):
    $Read = new Read;
endif;

$S = filter_input(INPUT_GET, "s", FILTER_DEFAULT);
$Search = filter_input_array(INPUT_POST);
if ($Search && (isset($Search['s']) || isset($Search['status']))):
    $S = (isset($Search['s']) ? urlencode($Search['s']) : $S);
    $SearchCat = (!empty($Search['searchcat']) ? $Search['searchcat'] : null);
    header("Location: dashboard.php?wc=ctas/home&s={$S}&cat={$SearchCat}&tag={$T}");
endif;
?>

<header class="dashboard_header">
    <div class="dashboard_header_title">
        <h1 class="icon-pen">Call to actions</h1>
        <p class="dashboard_header_breadcrumbs">
            &raquo; <?= ADMIN_NAME; ?>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
            <span class="crumb">/</span>
            <a title="Todos os Call to actions" href="dashboard.php?wc=ctas/home">Call to actions</a>
            <?= ($S ? "<span class='crumb'>/</span> <span class='icon-search'>{$S}</span>" : ''); ?>
        </p>      
    </div>

    <div class="dashboard_header_search">

        <form style="width: 100%; display: inline-block;" name="searchCategoriesPost" action="" method="post" enctype="multipart/form-data" class="ajax_off">
            <input type="search" value="<?= $S; ?>" name="s" placeholder="Pesquisar:" style="width: 38%; margin-right: 3px;">          
            <button class="btn btn_green icon icon-search icon-notext"></button>
        </form>
    </div>
</header>

<div class="dashboard_content">
    <?php
    $getPage = filter_input(INPUT_GET, 'pg', FILTER_VALIDATE_INT);
    $Page = ($getPage ? $getPage : 1);
    $Paginator = new Pager("dashboard.php?wc=ctas/home&s={$S}&pg=", '<<', '>>', 5);
    $Paginator->ExePager($Page, 100);
    
    if (!empty($S)):
        $WhereString[0] = "AND ( cta_title LIKE '%' :s '%' OR cta_text LIKE '%' :s '%')";
        $WhereString[1] = "&s={$S}";
    else:
        $WhereString[0] = "";
        $WhereString[1] = "";
    endif;
         
    $Read->FullRead("SELECT * FROM " . DB_CTAS . " WHERE 1=1 "
            . "{$WhereString[0]} "
            . "ORDER BY cta_title ASC "
            . "LIMIT :limit OFFSET :offset", "limit={$Paginator->getLimit()}&offset={$Paginator->getOffset()}{$WhereString[1]}"
    );
            
    if (!$Read->getResult()):
        $Paginator->ReturnPage();
        echo Erro("<span class='al_center icon-notification'>Ainda não existem CTAs cadastrados {$Admin['user_name']}. Comece agora mesmo criando seu primeiro call to action!</span>", E_USER_NOTICE);
    else:
        foreach ($Read->getResult() as $Cta):
            extract($Cta);

            $CtaCover = (file_exists("../uploads/{$cta_image}") && !is_dir("../uploads/{$cta_image}") ? "uploads/{$cta_image}" : 'admin/_img/no_image.jpg');
                        
            $cta_title = (!empty($cta_title) ? $cta_title : 'Edite esse rascunho para poder exibir como CTA em seu site!');
                        

  
            echo "<article class='box box25 post_single' id='{$cta_id}'>        
                <div class='post_single_cover box_content'>
                   <img alt='{$cta_title}' title='{$cta_title}' src='../tim.php?src={$CtaCover}&w=500&h=400'/></a>
                    
                <div class='post_single_content wc_normalize_height'>
                    <h1 class='title' style='font-size:1.25em;'>{$cta_title}</h1>                    
                </div>
                
                <div class='post_single_actions'>
                    <a title='Editar Call to action' href='dashboard.php?wc=ctas/create&id={$cta_id}' class='post_single_center icon-pencil btn btn_blue'>Editar</a>
                    <span rel='post_single' class='j_delete_action icon-cancel-circle btn btn_red' id='{$cta_id}'>Deletar</span>
                    <span rel='post_single' callback='Ctas' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$cta_id}'>Deletar Call to action?</span>
                      
                </div>
            </article>";
        endforeach;

        $Paginator->ExePaginator(DB_CTAS, "WHERE ( cta_title LIKE '%' :s '%' OR cta_text LIKE '%' :s '%')", "s={$S}"
        );
        echo $Paginator->getPaginator();
    endif;
    ?>
</div>