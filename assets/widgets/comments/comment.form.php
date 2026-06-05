<form name="add_comment" action="" method="post" enctype="multipart/form-data">
    <label style="width: 100%">
        <span>Comentar:</span>
        <textarea name="comment" rows="5" required></textarea>
    </label>

    <label>
        <select name="rank" required>
            <option disabled value="">Qual sua nota para esse conteúdo?</option>
            <option value="5">&#11088;&#11088;&#11088;&#11088;&#11088;</option>
            <option value="4">&#11088;&#11088;&#11088;&#11088;</option>
            <option value="3">&#11088;&#11088;&#11088;</option>
            <option value="2">&#11088;&#11088;</option>
            <option value="1">&#11088;</option>
        </select>
    </label>

    <img class="load" alt="Enviando Comentário" title="Enviando Comentário"
         src="<?= BASE; ?>/assets/widgets/comments/load.gif">
    <button class="btn"><i class="fa fa-paper-plane"></i> Enviar Comentários</button>
    <span class="btn btn_red wc_close"><i class="fa fa-times-circle"></i> Fechar</span>
    <div class="clear"></div>
</form>
