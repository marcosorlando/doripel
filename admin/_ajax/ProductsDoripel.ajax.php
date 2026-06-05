<?php

use App\Conn\Create;
use App\Conn\Delete;
use App\Conn\Read;
use App\Conn\Update;
use App\Helpers\Check;
use App\Models\Upload;

session_start();
require __DIR__ . '/../../vendor/autoload.php';
$NivelAcess = LEVEL_WC_PRODUCTS_DORIPEL;

if (!APP_PRODUCTS_DORIPEL || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess):
    $jSON['trigger'] = Check::ajaxErro('<b class="icon-warning">OPSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!',
      E_USER_ERROR);
    echo json_encode($jSON);
    die;
endif;

usleep(50000);

//DEFINE O CALLBACK E RECUPERA O POST
$jSON = null;
$CallBack = 'ProductsDoripel';
$PostData = filter_input_array(INPUT_POST, FILTER_DEFAULT);

//VALIDA AÇÃO
if ($PostData && isset($PostData['callback_action'], $PostData['callback']) && $PostData['callback'] == $CallBack):
//PREPARA OS DADOS
    $Case = $PostData['callback_action'];
    unset($PostData['callback'], $PostData['callback_action']);

// AUTO INSTANCE OBJECT READ
    if (empty($Read)):
        $Read = new Read;
    endif;

// AUTO INSTANCE OBJECT CREATE
    if (empty($Create)):
        $Create = new Create;
    endif;

// AUTO INSTANCE OBJECT UPDATE
    if (empty($Update)):
        $Update = new Update;
    endif;

// AUTO INSTANCE OBJECT DELETE
    if (empty($Delete)):
        $Delete = new Delete;
    endif;
    $Upload = new Upload('../../uploads/');

//SELECIONA AÇÃO
    switch ($Case):
        case 'manager':

            $PdtId = $PostData['pdt_id'];
            $PostData['pdt_status'] = (!empty($PostData['pdt_status']) ? '1' : '0');
            $Ref = substr($PostData['pdt_ref'], strpos($PostData['pdt_ref'], '-'));

            $Read->exeRead(DB_PDT_DORIPEL, "WHERE pdt_id = :id", "id={$PdtId}");

            if (!$Read->getResult()):
                $jSON['trigger'] = Check::ajaxErro("<b class='icon-warning'>Erro ao atualizar:</b> Desculpe {$_SESSION['userLogin']['user_name']}, mas não foi possível consultar o produto. Experimente atualizar a página!",
                  E_USER_WARNING);
            else:
                $Product = $Read->getResult()[0];

                unset($PostData['pdt_id'], $PostData['pdt_cover'], $PostData['pdt_scene'], $PostData['image'], $PostData['pdt_instrutions']);

                $PostData['pdt_name'] = (!empty($PostData['pdt_name']) ? Check::name($PostData['pdt_name']) : Check::name($PostData['pdt_title'])) . $Ref;

                //UPLOAD PDF
                if (!empty($_FILES['pdt_instrutions'])):
                    $File = $_FILES['pdt_instrutions'];

                    if ($Product['pdt_instrutions'] && file_exists("../../uploads/{$Product['pdt_instrutions']}") && !is_dir("../../uploads/{$Product['pdt_instrutions']}")):
                        unlink("../../uploads/{$Product['pdt_instrutions']}");
                    endif;

                    $Upload->file($File);
                    if ($Upload->getResult()):
                        $PostData['pdt_instrutions'] = $Upload->getResult();
                    else:
                        $jSON['trigger'] = Check::ajaxErro("<b class='icon-file-pdf'>ERRO AO ENVIAR MANUAL DE MONTAGEM:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione um arquivo em PDF para inserir no produto!",
                          E_USER_WARNING);
                        echo json_encode($jSON);
                        return;
                    endif;
                endif;

                //COVER UPLOAD
                if (!empty($_FILES['pdt_cover'])):
                    $File = $_FILES['pdt_cover'];

                    if ($Product['pdt_cover'] && file_exists("../../uploads/{$Product['pdt_cover']}") && !is_dir("../../uploads/{$Product['pdt_cover']}")):
                        unlink("../../uploads/{$Product['pdt_cover']}");
                    endif;

                    $Upload->image($File, "{$PostData['pdt_name']}", 900);
                    if ($Upload->getResult()):
                        $PostData['pdt_cover'] = $Upload->getResult();
                    else:
                        $jSON['trigger'] = Check::ajaxErro("<b class='icon-image'>ERRO AO ENVIAR CAPA:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem JPG de 900x900px para a capa!",
                          E_USER_WARNING);
                        echo json_encode($jSON);
                        return;
                    endif;
                endif;

                //SCENE UPLOAD
                if (!empty($_FILES['pdt_scene'])):
                    $File = $_FILES['pdt_scene'];

                    if ($Product['pdt_scene'] && file_exists("../../uploads/{$Product['pdt_scene']}") && !is_dir("../../uploads/{$Product['pdt_scene']}")):
                        unlink("../../uploads/{$Product['pdt_scene']}");
                    endif;

                    $Upload->image($File, "{$PostData['pdt_name']}-scene", 1920);
                    if ($Upload->getResult()):
                        $PostData['pdt_scene'] = $Upload->getResult();
                    else:
                        $jSON['trigger'] = Check::ajaxErro("<b class='icon-image'>ERRO AO ENVIAR CENA:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem JPG de 1920x1152px para a cena!",
                          E_USER_WARNING);
                        echo json_encode($jSON);
                        return;
                    endif;
                endif;

                if (!empty($_FILES['image'])):
                    $File = $_FILES['image'];
                    $gbFile = array();
                    $gbCount = count($File['type']);
                    $gbKeys = array_keys($File);
                    $gbLoop = 0;

                    for ($gb = 0; $gb < $gbCount; $gb++):
                        foreach ($gbKeys as $Keys):
                            $gbFiles[$gb][$Keys] = $File[$Keys][$gb];
                        endforeach;
                    endfor;

                    $jSON['gallery'] = null;
                    foreach ($gbFiles as $UploadFile):
                        $gbLoop++;
                        $Upload->image($UploadFile,
                          "{$PostData['pdt_name']}-{$gbLoop}-" . time() . base64_encode(time()), 1000);
                        if ($Upload->getResult()):
                            $gbCreate = ['product_id' => $PdtId, "image" => $Upload->getResult()];
                            $Create->exeCreate(DB_PDT_GALLERY_DORIPEL, $gbCreate);
                            $jSON['gallery'] .= "<img rel='Products' id='{$Create->getResult()}' alt='Imagem em {$PostData['pdt_title']}' title='Imagem em {$PostData['pdt_title']}' src='../uploads/{$Upload->getResult()}'/>";
                        endif;
                    endforeach;
                endif;

                if (isset($PostData['pdt_subcategory'])):
                    $Read->fullRead("SELECT cat_parent FROM " . DB_PDT_CATS_DORIPEL . " WHERE cat_id = :id",
                      "id={$PostData['pdt_subcategory']}");
                    $PostData['pdt_category'] = ($Read->getResult() ? $Read->getResult()[0]['cat_parent'] : null);
                endif;

                $Read->fullRead("SELECT pdt_id FROM " . DB_PDT_DORIPEL . " WHERE pdt_name = :nm AND pdt_id != :id",
                  "nm={$PostData['pdt_name']}&id={$PdtId}");
                if ($Read->getResult()):
                    $PostData['pdt_name'] = "{$PostData['pdt_name']}{$Ref}";
                endif;

                $jSON['name'] = $PostData['pdt_name'];
                $jSON['trigger'] = Check::ajaxErro("<span class='icon-checkmark'><b>PRODUTO ATUALIZADO:</b> Olá {$_SESSION['userLogin']['user_name']}. O produto {$PostData['pdt_title']} foi atualizado com sucesso!<span>");

                $Read->fullRead("SELECT pdt_id FROM " . DB_PDT_DORIPEL . " WHERE pdt_code = :code AND pdt_id != :id",
                  "code={$PostData['pdt_code']}&id={$PdtId}");
                if ($Read->getResult()):
                    $jSON['trigger'] = Check::ajaxErro("<span class='icon-warning'><b>OPPSSS:</b> Já existe um produto cadastrado com o código {$PostData['pdt_code']}, favor altere o código deste produto!</span>",
                      E_USER_WARNING);
                    $PostData['pdt_code'] = str_pad($PdtId, 7, 0, STR_PAD_LEFT);
                    $PostData['pdt_status'] = '0';
                endif;

                //STOCK TABLE
                if (!empty($PostData['pdt_inventory'])):
                    $Delete->exeDelete(DB_PDT_STOCK_DORIPEL, "WHERE pdt_id = :id AND stock_code != :cd",
                      "id={$PdtId}&cd=default");
                    $Read->exeRead(DB_PDT_STOCK_DORIPEL, "WHERE pdt_id = :id AND stock_code = :cd",
                      "id={$PdtId}&cd=default");
                    if ($Read->getResult()):
                        $UpdateStock = ['stock_inventory' => $PostData['pdt_inventory']];
                        $Update->exeUpdate(DB_PDT_STOCK_DORIPEL, $UpdateStock,
                          "WHERE pdt_id = :id AND stock_code = :cd", "id={$PdtId}&cd=default");
                    else:
                        $CreateStock = [
                          'pdt_id' => $PdtId,
                          'stock_code' => 'default',
                          'stock_inventory' => $PostData['pdt_inventory'],
                          'stock_sold' => 0
                        ];
                        $Create->exeCreate(DB_PDT_STOCK_DORIPEL, $CreateStock);
                    endif;
                endif;

                //NORMALIZE STOCK AND DELIVERED
                $Read->fullRead("SELECT sum(stock_inventory) AS amount, sum(stock_sold) AS vendor FROM " . DB_PDT_STOCK_DORIPEL . " WHERE pdt_id = :id",
                  "id={$PdtId}");

                $PostData['pdt_inventory'] = (!empty($Read->getResult()[0]['amount']) ? $Read->getResult()[0]['amount'] : 0);

                $Update->exeUpdate(DB_PDT_DORIPEL, $PostData, "WHERE pdt_id = :id", "id={$PdtId}");
                $jSON['view'] = BASE . '/movel/' . $PostData['pdt_name'];
            endif;
            break;

        case 'sendimage':
            $NewImage = $_FILES['image'];
            $Read->fullRead("SELECT pdt_title, pdt_name FROM " . DB_PDT_DORIPEL . " WHERE pdt_id = :id",
              "id={$PostData['pdt_id']}");
            if (!$Read->getResult()):
                $jSON['trigger'] = Check::ajaxErro("<b class='icon-image'>ERRO AO ENVIAR IMAGEM:</b> Desculpe {$_SESSION['userLogin']['user_name']}, mas não foi possível identificar o produto vinculado!",
                  E_USER_WARNING);
            else:
                $Upload = new Upload('../../uploads/');
                $Upload->image($NewImage, $Read->getResult()[0]['pdt_title'] . '-' . time(), IMAGE_W);
                if ($Upload->getResult()):
                    $PostData['product_id'] = $PostData['pdt_id'];
                    $PostData['image'] = $Upload->getResult();
                    unset($PostData['pdt_id']);

                    $Create->exeCreate(DB_PDT_IMAGE_DORIPEL, $PostData);
                    $jSON['tinyMCE'] = "<img title='{$Read->getResult()[0]['pdt_title']}' alt='{$Read->getResult()[0]['pdt_title']}' src='../uploads/{$PostData['image']}'/>";
                else:
                    $jSON['trigger'] = Check::ajaxErro("<b class='icon-image'>ERRO AO ENVIAR IMAGEM:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem JPG ou PNG para inserir no produto!",
                      E_USER_WARNING);
                endif;
            endif;
            break;

        case 'delete':
            $PdtId = $PostData['del_id'];
            $Read->fullRead("SELECT pdt_id FROM " . DB_ORDERS_ITEMS . " WHERE pdt_id = :id", "id={$PdtId}");
            $PdtOrder = $Read->getResult();

            $Read->exeRead(DB_PDT_DORIPEL, "WHERE pdt_id = :id", "id={$PdtId}");
            if ($PdtOrder):
                $jSON['trigger'] = Check::ajaxErro("<b class='icon-warning'>OPSS:</b> Desculpe {$_SESSION['userLogin']['user_name']}. Não foi possível deletar pois existem pedidos para esse produto!",
                  E_USER_WARNING);
            elseif (!$Read->getResult()):
                $jSON['trigger'] = Check::ajaxErro("<b class='icon-warning'>OPSS:</b> Desculpe {$_SESSION['userLogin']['user_name']}. Não foi possível deletar pois o produto não existe ou foi removido recentemente!",
                  E_USER_WARNING);
            else:
                $Product = $Read->getResult()[0];
                $PdtCover = "../../uploads/{$Product['pdt_cover']}";

                if (file_exists($PdtCover) && !is_dir($PdtCover)):
                    unlink($PdtCover);
                endif;

                $Read->exeRead(DB_PDT_IMAGE_DORIPEL, "WHERE product_id = :id", "id={$Product['pdt_id']}");
                if ($Read->getResult()):
                    foreach ($Read->getResult() as $PdtImage):
                        $PdtImageIs = "../../uploads/{$PdtImage['image']}";
                        if (file_exists($PdtImageIs) && !is_dir($PdtImageIs)):
                            unlink($PdtImageIs);
                        endif;
                    endforeach;
                    $Delete->exeDelete(DB_PDT_IMAGE_DORIPEL, "WHERE product_id = :id", "id={$Product['pdt_id']}");
                endif;

                $Read->exeRead(DB_PDT_GALLERY_DORIPEL, "WHERE product_id = :id", "id={$Product['pdt_id']}");
                if ($Read->getResult()):
                    foreach ($Read->getResult() as $PdtGB):
                        $PdtGBImage = "../../uploads/{$PdtGB['image']}";
                        if (file_exists($PdtGBImage) && !is_dir($PdtGBImage)):
                            unlink($PdtGBImage);
                        endif;
                    endforeach;
                    $Delete->exeDelete(DB_PDT_GALLERY_DORIPEL, "WHERE product_id = :id", "id={$Product['pdt_id']}");
                endif;

                $Delete->exeDelete(DB_PDT_DORIPEL, "WHERE pdt_id = :id", "id={$Product['pdt_id']}");
                $Delete->exeDelete(DB_COMMENTS, "WHERE pdt_id = :id", "id={$Product['pdt_id']}");
                $Delete->exeDelete(DB_PDT_STOCK_DORIPEL, "WHERE pdt_id = :id", "id={$Product['pdt_id']}");
                $jSON['success'] = true;
            endif;
            break;

        case 'gbremove':
            $Read->fullRead("SELECT image FROM " . DB_PDT_GALLERY_DORIPEL . " WHERE id = :id", "id={$PostData['img']}");
            if ($Read->getResult()):
                $ImageRemove = "../../uploads/{$Read->getResult()[0]['image']}";
                if (file_exists($ImageRemove) && !is_dir($ImageRemove)):
                    unlink($ImageRemove);
                endif;
                $Delete->exeDelete(DB_PDT_GALLERY_DORIPEL, "WHERE id = :id", "id={$PostData['img']}");
                $jSON['success'] = true;
            endif;
            break;

        case 'cat_manager':
            $PostData = array_map('strip_tags', $PostData);
            $CatId = $PostData['cat_id'];
            unset($PostData['cat_id']);

            $PostData['cat_name'] = Check::name($PostData['cat_title']);
            $PostData['cat_parent'] = ($PostData['cat_parent'] ? $PostData['cat_parent'] : null);
            $PostData['cat_sizes'] = (!empty($PostData['cat_sizes']) && $PostData['cat_sizes'] != 'default' ? mb_strtoupper($PostData['cat_sizes']) : null);

            $Read->fullRead("SELECT cat_id FROM " . DB_PDT_CATS_DORIPEL . " WHERE cat_name = :cn AND cat_id != :ci",
              "cn={$PostData['cat_name']}&ci={$CatId}");

            if ($Read->getResult()):
                $PostData['cat_name'] = $PostData['cat_name'] . '-' . $CatId;
            endif;

            $Read->fullRead("SELECT cat_id FROM " . DB_PDT_CATS_DORIPEL . " WHERE cat_parent = :ci", "ci={$CatId}");

            if ($Read->getResult() && !empty($PostData['cat_parent'])):
                $jSON['trigger'] = Check::ajaxErro("<b class='icon-warning'>OPPSSS: </b> {$_SESSION['userLogin']['user_name']}, uma categoria PAI (que possui subcategorias) não pode ser atribuida como subcategoria",
                  E_USER_WARNING);
            else:
                $Read->fullRead("SELECT cat_parent FROM " . DB_PDT_CATS_DORIPEL . " WHERE cat_id = :id AND cat_parent != :parent",
                  "id={$CatId}&parent={$PostData['cat_parent']}");
                if ($Read->getResult()):
                    //Contriuição do André Dorneles #1856

                    $PdtUpdate['pdt_category'] = $PostData['cat_parent'];
                    $Update->exeUpdate(DB_PDT_DORIPEL, $PdtUpdate,
                      "WHERE pdt_category != :catpai AND pdt_subcategory = :catfilha",
                      "catpai={$PostData['cat_parent']}&catfilha={$CatId}");
                endif;
                $Update->exeUpdate(DB_PDT_CATS_DORIPEL, $PostData, "WHERE cat_id = :id", "id={$CatId}");
                $jSON['trigger'] = Check::ajaxErro("<b class='icon-checkmark'>TUDO CERTO: </b> A categoria <b>{$PostData['cat_title']}</b> foi atualizada com sucesso!");
            endif;
            break;

        case 'cat_delete':
            $CatId = $PostData['del_id'];
            $Read->fullRead(
              "SELECT pdt_id FROM " . DB_PDT_DORIPEL . " WHERE pdt_category = :cat_category OR pdt_subcategory = :cat_subcategory",
              http_build_query(['cat_category' => $CatId, 'cat_subcategory' => $CatId])
            );
            if ($Read->getResult()):
                $jSON['trigger'] = Check::ajaxErro("<b class='icon-info'>OPSS: </b>Desculpe {$_SESSION['userLogin']['user_name']}, mas não é possível remover categorias com produtos cadastrados nela!",
                  E_USER_WARNING);
            else:
                $Read->fullRead("SELECT cat_id FROM " . DB_PDT_CATS_DORIPEL . " WHERE cat_parent = :cat",
                  "cat={$CatId}");
                if ($Read->getResult()):
                    $jSON['trigger'] = Check::ajaxErro("<b class='icon-info'>OPSS: </b>Desculpe {$_SESSION['userLogin']['user_name']}, mas não é possível remover categorias com subcategorias ligadas a ela!",
                      E_USER_WARNING);
                else:
                    $Delete->exeDelete(DB_PDT_CATS_DORIPEL, "WHERE cat_id = :cat", "cat={$CatId}");
                    $jSON['success'] = true;
                endif;
            endif;
            break;

        case 'cat_sizes':
            $CatId = $PostData['catId'];
            $CatSizes = E_PDT_SIZE;
            $Read->fullRead("SELECT cat_sizes FROM " . DB_PDT_CATS_DORIPEL . " WHERE cat_id = :id", "id={$CatId}");
            if ($Read->getResult() && !empty($Read->getResult()[0]['cat_sizes'])):
                $CatSizes = $Read->getResult()[0]['cat_sizes'];
            endif;

            $EachCatSizes = explode(',', $CatSizes);
            $jSON['cat_sizes'] = null;
            foreach ($EachCatSizes as $Size):
                $PdtId = $PostData['pdtId'];
                $Size = trim(rtrim($Size));
                $Read->fullRead("SELECT stock_inventory, stock_sold FROM " . DB_PDT_STOCK_DORIPEL . " WHERE pdt_id = :pdt AND stock_code = :key",
                  "pdt={$PdtId}&key={$Size}");
                if ($Read->getResult()):
                    $jSON['cat_sizes'] .= "<label><span class='size'>{$Size}:</span><input name='{$Size}' type='number' min='0' value='{$Read->getResult()[0]['stock_inventory']}'><span class='cart'><b class='icon-cart'>" . str_pad($Read->getResult()[0]['stock_sold'],
                        2, 0, 0) . "</b></span></label>";
                else:
                    $jSON['cat_sizes'] .= "<label><span class='size'>{$Size}:</span><input name='{$Size}' type='number' min='0' value='0'><span class='cart'><b class='icon-cart'>00</b></span></label>";
                endif;
            endforeach;
            break;

        case 'brand_manager':
            $BrandId = $PostData['brand_id'];
            $PostData['brand_name'] = Check::name($PostData['brand_title']);


            $Read->fullRead("SELECT brand_id FROM " . DB_PDT_BRANDS_DORIPEL . " WHERE brand_name = :nm AND brand_id != :id",
              "nm={$PostData['brand_name']}&id={$BrandId}");
            if ($Read->getResult()):
                $PostData['brand_name'] = "{$PostData['brand_name']}-{$BrandId}";
            endif;

            unset($PostData['brand_id']);
            $Update->exeUpdate(DB_PDT_BRANDS_DORIPEL, $PostData, "WHERE brand_id = :id", "id={$BrandId}");
            $jSON['trigger'] = Check::ajaxErro("<b class='icon-checkmark'>TUDO CERTO: </b> A marca ou fabricante <b>{$PostData['brand_title']}</b> foi atualizada com sucesso!");
            break;

        case 'brand_remove':
            $BrandId = $PostData['del_id'];
            $Read->fullRead("SELECT pdt_id FROM " . DB_PDT_DORIPEL . " WHERE pdt_brand = :brand", "brand={$BrandId}");
            if ($Read->getResult()):
                $jSON['trigger'] = Check::ajaxErro("<b class='icon-info'>OPSS: </b>Desculpe {$_SESSION['userLogin']['user_name']}, mas não é possível remover uma marca quando existem produtos cadastrados com ela!",
                  E_USER_WARNING);
            else:
                $Delete->exeDelete(DB_PDT_BRANDS_DORIPEL, "WHERE brand_id = :brand", "brand={$BrandId}");
                $jSON['success'] = true;
            endif;
            break;

        case 'color_manager':
            $ColorId = $PostData['color_id'];
            $PostData['color_name'] = Check::name($PostData['color_title']);


            $Read->fullRead("SELECT color_id FROM " . DB_PDT_COLORS_DORIPEL . " WHERE color_name = :nm AND color_id != :id",
              "nm={$PostData['color_name']}&id={$ColorId}");
            if ($Read->getResult()):
                $PostData['color_name'] = "{$PostData['color_name']}-{$ColorId}";
            endif;

            unset($PostData['color_id']);
            $Update->exeUpdate(DB_PDT_COLORS_DORIPEL, $PostData, "WHERE color_id = :id", "id={$ColorId}");
            $jSON['trigger'] = Check::ajaxErro("<b class='icon-checkmark'>TUDO CERTO: </b> O Padrão/Cor  <b>{$PostData['color_title']}</b> foi atualizado com sucesso!");
            break;

        case 'color_remove':
            $ColorId = $PostData['del_id'];
            $Read->fullRead("SELECT pdt_id FROM " . DB_PDT_DORIPEL . " WHERE pdt_color = :color", "color={$ColorId}");
            if ($Read->getResult()):
                $jSON['trigger'] = Check::ajaxErro("<b class='icon-info'>OPSS: </b>Desculpe {$_SESSION['userLogin']['user_name']}, mas não é possível remover uma <b>padrão ou cor</b> com produtos cadastrados com ela!",
                  E_USER_WARNING);
            else:
                $Delete->exeDelete(DB_PDT_COLORS_DORIPEL, "WHERE color_id = :color", "color={$ColorId}");
                $jSON['success'] = true;
            endif;
            break;

        case 'pdt_stock':
            $PdtId = $PostData['pdt_id'];
            unset($PostData['pdt_id']);

            $SockTotal = 0;
            $jSON['res'] = null;
            foreach ($PostData as $SizeKey => $SizeValue):
                $SockTotal += $SizeValue;
                $SizeKey = str_replace("_", " ", $SizeKey);

                $Read->fullRead("SELECT stock_inventory FROM " . DB_PDT_STOCK_DORIPEL . " WHERE pdt_id = :pd AND stock_code = :cd",
                  "pd={$PdtId}&cd={$SizeKey}");
                if (!$Read->getResult() && $SizeValue >= 1):
                    $CreateStock = [
                      'pdt_id' => $PdtId,
                      'stock_code' => "{$SizeKey}",
                      'stock_inventory' => $SizeValue,
                      'stock_sold' => 0
                    ];
                    $Create->exeCreate(DB_PDT_STOCK_DORIPEL, $CreateStock);
                else:
                    $UpdateStock = ['stock_inventory' => $SizeValue];
                    $Update->exeUpdate(DB_PDT_STOCK_DORIPEL, $UpdateStock, "WHERE pdt_id = :pd AND stock_code = :cd",
                      "pd={$PdtId}&cd={$SizeKey}");
                endif;
            endforeach;

            //REMOVE NOT RELATED STOCK
            $StockParams = ['id' => $PdtId];
            $StockPlaceholders = [];
            foreach (array_keys($PostData) as $Index => $StockKey):
                $Param = "stock_{$Index}";
                $StockPlaceholders[] = ":{$Param}";
                $StockParams[$Param] = str_replace('_', ' ', (string)$StockKey);
            endforeach;

            if ($StockPlaceholders):
                $Delete->exeDelete(
                  DB_PDT_STOCK_DORIPEL,
                  "WHERE pdt_id = :id AND stock_code NOT IN(" . implode(', ', $StockPlaceholders) . ")",
                  http_build_query($StockParams)
                );
            endif;

            //CLEAR ZERO STOCK
            $Delete->exeDelete(DB_PDT_STOCK_DORIPEL,
              "WHERE pdt_id = :id AND stock_inventory = '0' AND stock_sold = '0'", "id={$PdtId}");

            //UPDATE GENERAL STOCK
            $UpdateGeneralStock = ['pdt_inventory' => $SockTotal];
            $Update->exeUpdate(DB_PDT_DORIPEL, $UpdateGeneralStock, "WHERE pdt_id = :id", "id={$PdtId}");

            $jSON['content'] = $SockTotal;
            $jSON['trigger'] = "<div class='trigger trigger_success trigger_ajax'><b class='icon icon-checkmark'>Estoque atualizado com sucesso!</b></div>";
            break;
    endswitch;

    //RETORNA O CALLBACK
    if ($jSON):
        echo json_encode($jSON);
    else:
        $jSON['trigger'] = Check::ajaxErro('<b class="icon-warning">OPSS:</b> Desculpe. Mas uma ação do sistema não respondeu corretamente. Ao persistir, contate o desenvolvedor!',
          E_USER_ERROR);
        echo json_encode($jSON);
    endif;
else:
    //ACESSO DIRETO
    die('<br><br><br><center><h1>Acesso Restrito!</h1></center>');
endif;
