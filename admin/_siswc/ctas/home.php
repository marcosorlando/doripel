<?php
use App\Conn\Delete;
use App\Conn\Read;
use App\Helpers\Check;
use App\Models\Pager;

$AdminLevel = LEVEL_WC_CTAS;
if (!APP_CTAS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
    Check::accessBlocked();
}

//AUTO DELETE POST TRASH
if (DB_AUTO_TRASH) {
    $Delete = new Delete();
    $Delete->exeDelete(DB_CTAS, "WHERE cta_image IS NULL and cta_text IS NULL", "");
}

// AUTO INSTANCE OBJECT READ
if (empty($Read)) {
    $Read = new Read();
}

$S = filter_input(INPUT_GET, "s", FILTER_DEFAULT);
$Search = filter_input_array(INPUT_POST) ?: [];
if ($Search && isset($Search['s'])) {
    $S = (isset($Search['s']) ? urlencode($Search['s']) : $S);
    header("Location: dashboard.php?wc=ctas/home&s={$S}");
    exit;
}

$SearchTerm = (string) ($S ?? '');
$SearchParam = urlencode($SearchTerm);
$SearchHtml = htmlspecialchars($SearchTerm, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
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
            <?= ($SearchTerm !== '' ? "<span class='crumb'>/</span> <span class='icon-search'>{$SearchHtml}</span>" : ''); ?>
        </p>      
    </div>

    <div class="dashboard_header_search">

        <form style="width: 100%; display: inline-block;" name="searchCategoriesPost" action="" method="post" enctype="multipart/form-data" class="ajax_off">
            <input type="search" value="<?= $SearchHtml; ?>" name="s" placeholder="Pesquisar:" style="width: 38%; margin-right: 3px;">          
            <button class="btn btn_green icon icon-search icon-notext"></button>
        </form>
    </div>
</header>

<div class="dashboard_content">
    <?php
    $getPage = filter_input(INPUT_GET, 'pg', FILTER_VALIDATE_INT);
    $Page = ($getPage ? $getPage : 1);
    $Paginator = new Pager("dashboard.php?wc=ctas/home&s={$SearchParam}&pg=", '<<', '>>', 5);
    $Paginator->exePager($Page, 100);
    
    if ($SearchTerm !== '') {
        $WhereString[0] = "AND (cta_title LIKE :s OR cta_text LIKE :s)";
        $WhereString[1] = "&s=" . urlencode("%{$SearchTerm}%");
    } else {
        $WhereString[0] = "";
        $WhereString[1] = "";
    }
         
    $Read->fullRead("SELECT * FROM " . DB_CTAS . " WHERE 1=1 "
            . "{$WhereString[0]} "
            . "ORDER BY cta_title ASC "
            . "LIMIT :limit OFFSET :offset", "limit={$Paginator->getLimit()}&offset={$Paginator->getOffset()}{$WhereString[1]}"
    );
            
    if (!$Read->getResult()) {
        $Paginator->returnPage();
        echo Check::erro("<span class='al_center icon-notification'>Ainda não existem CTAs cadastrados {$Admin['user_name']}. Comece agora mesmo criando seu primeiro call to action!</span>", E_USER_NOTICE);
    } else {
        foreach ($Read->getResult() as $Cta) {
            extract($Cta);

            $CtaCover = (file_exists("../uploads/{$cta_image}") && !is_dir("../uploads/{$cta_image}") ? "uploads/{$cta_image}" : 'admin/_img/no_image.jpg');
                        
            $cta_title = (!empty($cta_title) ? $cta_title : 'Edite esse rascunho para poder exibir como CTA em seu site!');
            $CtaTitle = htmlspecialchars((string) $cta_title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                        

  
            echo "<article class='box box25 post_single' id='{$cta_id}'>        
                <div class='post_single_cover box_content'>
                   <img alt='{$CtaTitle}' title='{$CtaTitle}' src='../tim.php?src={$CtaCover}&w=500&h=400'/>
                    
                <div class='post_single_content wc_normalize_height'>
                    <h1 class='title' style='font-size:1.25em;'>{$CtaTitle}</h1>                    
                </div>
                
                <div class='post_single_actions'>
                    <a title='Editar Call to action' href='dashboard.php?wc=ctas/create&id={$cta_id}' class='post_single_center icon-pencil btn btn_blue'>Editar</a>
                    <span rel='post_single' class='j_delete_action icon-cancel-circle btn btn_red' id='{$cta_id}'>Deletar</span>
                    <span rel='post_single' callback='Ctas' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$cta_id}'>Deletar Call to action?</span>
                      
                </div>
            </article>";
        }

        $Paginator->exePaginator(DB_CTAS, ($SearchTerm !== '' ? "WHERE (cta_title LIKE :s OR cta_text LIKE :s)" : ""), ($SearchTerm !== '' ? "s=" . urlencode("%{$SearchTerm}%") : ""));
        echo $Paginator->getPaginator();
    }
    ?>
</div>
