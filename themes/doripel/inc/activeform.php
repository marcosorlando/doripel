<form name="lead_capture" class="j_formsubmit" action="" method="post" enctype="multipart/form-data">
  <div class="position-relative">
    <div class="callback_return trigger_ajax"></div>
    <input type="hidden" name="callback" value="Leads" />
    <input type="hidden" name="callback_action" value="<?= (empty($CAPTION) ? 'news1' : $CAPTION); ?>" />
    <input type="text" name="name" class="bg-transparent text-small margin-5px-bottom border-color-extra-light-gray medium-input pull-left" placeholder="Digite seu nome..." required />
    <input type="email" name="email" class="bg-transparent text-small no-margin border-color-extra-light-gray medium-input pull-left" placeholder="Digite seu email..." required />
  <!--    <button type="submit" class="bg-transparent text-large btn position-absolute right-0 top-3"><i class="fa fa-envelope-o no-margin-left"></i></button>-->

    <button name="public" type="submit" class="btn btn_optin text-deep-pink bg-transparent text-large btn position-absolute right-0 top-48"><i class="fa fa-envelope-o no-margin-left"></i><img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!" title="Enviando Requisição!" src="<?= INCLUDE_PATH; ?>/images/icons/load.gif"/>
    </button>
  </div>   
</form>

