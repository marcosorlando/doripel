<?php

    if (!$Read) {
        $Read = new Read;
    }
    if (!ACC_MANAGER) {
        require REQUIRE_PATH . '/404.php';
    } else {
        ?>
		<div class="clear"></div>
		<section class="blog-content-section">
			<div class="breadcrumbs" id="acc">
				<div class="content">
					<p><a href="<?= BASE; ?>" title="<?= SITE_NAME; ?>"><?= SITE_NAME; ?></a> <i
								class="icon icon-Arrow"> </i> Conta do Usuário</p>
					<div class="clear"></div>
				</div>
			</div>
			<div class="container">
				<div class="row">
					<div>
						<div>
                            <?php
                                require 'assets/widgets/account/account.php'; ?>
							<div class="clear"></div>
						</div>
					</div>
				</div>
			</div>
		</section>
        <?php
    }
