<?php

    use App\Conn\Read;

    if (!$Read) {
        $Read = new Read;
    }
    if (!ACC_MANAGER) {
        require REQUIRE_PATH . '/404.php';
    } else {
        ?>
		<section class="wow fadeIn bg-light-gray padding-35px-tb page-title-small top-space margin-25px-bottom">
			<div class="container">
				<div class="row equalize xs-equalize-auto">
					<div class="col-lg-8 col-md-6 col-sm-6 col-xs-12 display-table">
						<div class="display-table-cell vertical-align-middle text-left xs-text-center">
							<!-- start page title -->
							<h1 class="alt-font text-extra-dark-gray font-weight-600 no-margin-bottom text-uppercase">
								<i class="fa fa-user"></i> Conta do Usuário
							</h1>
							<!-- end page title -->
						</div>
					</div>
				</div>
			</div>
		</section>
		<div class="container">
			<div class="row">
				<div>
                    <?php
                        require 'assets/widgets/account/account.php'; ?>
					<div class="clear"></div>
				</div>
			</div>
		</div>

        <?php
    }
