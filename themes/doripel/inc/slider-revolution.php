<!-- start slider header section -->
<section class="wow fadeIn example no-padding no-transition">
  <article class="content">
    <div id="rev_slider_1078_1_wrapper" class="rev_slider_wrapper fullwidthbanner-container" data-alias="classic4export" data-source="gallery" style="margin:0px auto;background-color:transparent;padding:0px;margin-top:0px;margin-bottom:0px;">

      <!-- start revolution slider 5.4.1 fullwidth mode -->
      <div id="rev_slider_1078_1" class="rev_slider fullwidthabanner" style="display:none;" data-version="5.4.1">
        <ul>
            <?php
            $Read = new Read;
            $Read->ExeRead(DB_SLIDES,"WHERE slide_status = 1 AND slide_start <= NOW() AND (slide_end >= NOW() OR slide_end IS NULL) ORDER BY slide_date DESC");
            if (!$Read->getResult()):
                ?>
              <!-- slides  -->
              <li data-index="rs-3045" data-transition="fade" data-slotamount="default" data-hideafterloop="0" data-hideslideonmobile="off" data-easein="Power4.easeInOut" data-easeout="Power4.easeInOut" data-masterspeed="200" data-thumb="" data-rotate="0" data-fstransition="fade" data-fsmasterspeed="100" data-fsslotamount="0" data-saveperformance="off" data-title="" data-param1="" data-param2="" data-param3="" data-param4="" data-param5="" data-param6="" data-param7="" data-param8="" data-param9="" data-param10="" data-description="">
                <div class="opacity-extra-medium bg-black position-relative z-index-1"></div>
                <!-- main image -->
                <img src="<?= BASE; ?>/uploads/default.jpg" alt="Móveis Doripel - Indústria de Móveis em Lagoa Vermelha/RS - Brasil" title="Móveis Doripel - Indústria de Móveis em Lagoa Vermelha/RS - Brasil" data-bgposition="center center" data-bgfit="cover" data-bgrepeat="no-repeat" data-bgparallax="1" class="rev-slidebg full">
                <img src="<?= BASE; ?>/uploads/default.jpg" alt="Móveis Doripel - Indústria de Móveis em Lagoa Vermelha/RS - Brasil" title="Móveis Doripel - Indústria de Móveis em Lagoa Vermelha/RS - Brasil" data-bgposition="center center" data-bgfit="cover" data-bgrepeat="no-repeat" data-bgparallax="1" class="rev-slidebg mobile">
                <!-- HEADLINE -->
                <div class="tp-caption NotGeneric-Title tp-resizeme"
                     data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']"
                     data-y="['middle','middle','middle','middle']" data-voffset="['-115','-110','-90','-90']"
                     data-fontsize="['12','12','12','12']"
                     data-lineheight="['20','20','20','20']"
                     data-width="none"
                     data-height="none"
                     data-whitespace="nowrap"
                     data-type="text"
                     data-responsive_offset="on"
                     data-frames='[{"from":"y:50px;opacity:0;","speed":1000,"to":"o:1;","delay":300,"ease":"Power4.easeInOut"},{"delay":"wait","speed":1000,"to":"y:[175%];","mask":"x:inherit;y:inherit;s:inherit;e:inherit;","ease":"Power2.easeInOut"},{"frame":"hover","speed":"300","ease":"Power1.easeInOut","to":"o:1;rX:0;rY:0;rZ:0;z:0;","style":"c:rgba(255, 255, 255, 1.00);bc:rgba(255, 255, 255, 1.00);bw:2px 2px 2px 2px;"}]'
                     data-textAlign="['center','center','center','center']"
                     data-paddingtop="[10,10,10,10]"
                     data-paddingright="[0,0,0,0]"
                     data-paddingbottom="[10,10,10,10]"
                     data-paddingleft="[0,0,0,0]"
                     style="z-index: 7; white-space: nowrap; text-transform: uppercase; font-family:'Montserrat', sans-serif !important; font-weight: 500; letter-spacing:2px;">
                    De Lagoa Vermelha para o mundo
                </div>
                <!-- TITLE -->
                <div class="tp-caption NotGeneric-SubTitle tp-resizeme "
                     data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']"
                     data-y="['middle','middle','middle','middle']" data-voffset="['0','0','0','0']"
                     data-fontsize="['70','70','60','50']"
                     data-lineheight="['75','75','60','60']"
                     data-width="none"
                     data-height="none"
                     data-whitespace="nowrap"
                     data-type="text"
                     data-responsive_offset="on"
                     data-frames='[{"from":"y:50px;opacity:0;","speed":1000,"to":"o:1;","delay":600,"ease":"Power4.easeInOut"},{"delay":"wait","speed":1000,"to":"y:[175%];","mask":"x:inherit;y:inherit;s:inherit;e:inherit;","ease":"Power2.easeInOut"},{"frame":"hover","speed":"300","ease":"Power1.easeInOut","to":"o:1;rX:0;rY:0;rZ:0;z:0;","style":"c:rgba(255, 255, 255, 1.00);bc:rgba(255, 255, 255, 1.00);bw:2px 2px 2px 2px;"}]'
                     data-textAlign="['center','center','center','center']"
                     data-paddingtop="[10,10,10,10]"
                     data-paddingright="[0,0,0,0]"
                     data-paddingbottom="[10,10,10,10]"
                     data-paddingleft="[0,0,0,0]"
                     style="z-index: 8; white-space: nowrap; font-family:'Montserrat', sans-serif !important; font-weight: 600; letter-spacing: -1px;">
                    Móveis com a qualidade Doripel<br>
                </div>
                <!-- BUTTON -->
                <a href="<?= BASE; ?>/#entrar" class="tp-caption btn btn-transparent-white btn-medium border-radius-4 z-index-5"
                   data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']"
                   data-y="['middle','middle','middle','middle']" data-voffset="['130','130','110','110']"
                   data-fontsize="['12','14','14','14']"
                   data-whitespace="nowrap"
                   data-visibility="['on', 'on', 'on', 'on']"
                   data-type="text"
                   data-color="['#FFFFFF']"
                   data-responsive_offset="on"
                   data-responsive="on"
                   data-frames='[{"from":"y:150px;opacity:0;","speed":1500,"to":"o:1;","delay":900,"ease":"Power4.easeInOut"},{"delay":"wait","speed":1000,"to":"y:[175%];","mask":"x:inherit;y:inherit;s:inherit;e:inherit;","ease":"Power2.easeInOut"},{"frame":"hover","speed":"300","ease":"Power1.easeInOut","to":"o:1;rX:0;rY:0;rZ:0;z:0;","style":"c:rgba(0, 0, 0, 1.00);bc:rgba(255, 255, 255, 1.00);bw:2px 2px 2px 2px;"}]'
                   data-textAlign="['inherit', 'inherit', 'right', 'center']"
                   data-paddingtop="[6,6,6,6]"
                   data-paddingright="[26,26,26,26]"
                   data-paddingbottom="[6,6,6,6]"
                   data-paddingleft="[26,26,26,26]"
                   data-basealign="slide">ENTRAR</a>
              </li>

            <?php
            else:
                foreach ($Read->getResult() as $Slide):
                    extract($Slide);
                    ?>
                  <!-- slides  -->
                  <li data-index="rs-3045" data-transition="fade" data-slotamount="default" data-hideafterloop="0" data-hideslideonmobile="on" data-easein="Power4.easeInOut" data-easeout="Power4.easeInOut" data-masterspeed="200" data-thumb="" data-rotate="0" data-fstransition="fade" data-fsmasterspeed="100" data-fsslotamount="0" data-saveperformance="off" data-title="" data-param1="" data-param2="" data-param3="" data-param4="" data-param5="" data-param6="" data-param7="" data-param8="" data-param9="" data-param10="" data-description="">

                    <div class="opacity-extra-medium bg-black position-relative z-index-1"></div>
                    <!-- main image -->
                    <img src="<?= BASE; ?>/uploads/<?= $slide_image ?>" alt="<?= $slide_title ?>" title="<?= $slide_title ?>" data-bgposition="center center" data-bgfit="cover" data-bgrepeat="no-repeat" data-bgparallax="1" class="rev-slidebg full">
                    <img src="<?= BASE; ?>/uploads/<?= $slide_image_mobile ?>" alt="<?= $slide_title ?>" title="<?= $slide_title ?>" data-bgposition="center center" data-bgfit="cover" data-bgrepeat="no-repeat" data-bgparallax="1" class="rev-slidebg mobile">

                    <!-- HEADLINE  -->
                    <div class="tp-caption NotGeneric-Title tp-resizeme"
                         data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']"
                         data-y="['middle','middle','middle','middle']" data-voffset="['-115','-110','-90','-90']"
                         data-fontsize="['12','12','12','12']"
                         data-lineheight="['20','20','20','20']"
                         data-width="none"
                         data-height="none"
                         data-whitespace="nowrap"
                         data-type="text"
                         data-responsive_offset="on"
                         data-frames='[{"from":"y:50px;opacity:0;","speed":1000,"to":"o:1;","delay":300,"ease":"Power4.easeInOut"},{"delay":"wait","speed":1000,"to":"y:[175%];","mask":"x:inherit;y:inherit;s:inherit;e:inherit;","ease":"Power2.easeInOut"},{"frame":"hover","speed":"300","ease":"Power1.easeInOut","to":"o:1;rX:0;rY:0;rZ:0;z:0;","style":"c:rgba(255, 255, 255, 1.00);bc:rgba(255, 255, 255, 1.00);bw:2px 2px 2px 2px;"}]'
                         data-textAlign="['center','center','center','center']"
                         data-paddingtop="[10,10,10,10]"
                         data-paddingright="[0,0,0,0]"
                         data-paddingbottom="[10,10,10,10]"
                         data-paddingleft="[0,0,0,0]"
                         style="z-index: 7; white-space: nowrap; text-transform: uppercase; font-family:'Montserrat', sans-serif !important; font-weight: 500; letter-spacing:2px;">
                        <?= $slide_desc ?>
                    </div>
                    <!-- TITLE -->
                    <div class="tp-caption NotGeneric-SubTitle tp-resizeme "
                         data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']"
                         data-y="['middle','middle','middle','middle']" data-voffset="['0','0','0','0']"
                         data-fontsize="['70','70','60','50']"
                         data-lineheight="['75','75','60','60']"
                         data-width="none"
                         data-height="none"
                         data-whitespace="nowrap"
                         data-type="text"
                         data-responsive_offset="on"
                         data-frames='[{"from":"y:50px;opacity:0;","speed":1000,"to":"o:1;","delay":600,"ease":"Power4.easeInOut"},{"delay":"wait","speed":1000,"to":"y:[175%];","mask":"x:inherit;y:inherit;s:inherit;e:inherit;","ease":"Power2.easeInOut"},{"frame":"hover","speed":"300","ease":"Power1.easeInOut","to":"o:1;rX:0;rY:0;rZ:0;z:0;","style":"c:rgba(255, 255, 255, 1.00);bc:rgba(255, 255, 255, 1.00);bw:2px 2px 2px 2px;"}]'
                         data-textAlign="['center','center','center','center']"
                         data-paddingtop="[10,10,10,10]"
                         data-paddingright="[0,0,0,0]"
                         data-paddingbottom="[10,10,10,10]"
                         data-paddingleft="[0,0,0,0]"
                         style="z-index: 8; white-space: nowrap; font-family:'Montserrat', sans-serif !important; font-weight: 600; letter-spacing: -1px;">
                        <?= $slide_title ?><br>
                    </div>
                    <!-- BUTTON -->
                    <a href="<?= $slide_content ?>" class="tp-caption btn btn-transparent-white btn-medium border-radius-4 z-index-5"
                       data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']"
                       data-y="['middle','middle','middle','middle']" data-voffset="['130','130','110','110']"
                       data-fontsize="['12','14','14','14']"
                       data-whitespace="nowrap"
                       data-visibility="['on', 'on', 'on', 'on']"
                       data-type="text"
                       data-color="['#FFFFFF']"
                       data-responsive_offset="on"
                       data-responsive="on"
                       data-frames='[{"from":"y:150px;opacity:0;","speed":1500,"to":"o:1;","delay":900,"ease":"Power4.easeInOut"},{"delay":"wait","speed":1000,"to":"y:[175%];","mask":"x:inherit;y:inherit;s:inherit;e:inherit;","ease":"Power2.easeInOut"},{"frame":"hover","speed":"300","ease":"Power1.easeInOut","to":"o:1;rX:0;rY:0;rZ:0;z:0;","style":"c:rgba(0, 0, 0, 1.00);bc:rgba(255, 255, 255, 1.00);bw:2px 2px 2px 2px;"}]'
                       data-textAlign="['inherit', 'inherit', 'right', 'center']"
                       data-paddingtop="[6,6,6,6]"
                       data-paddingright="[26,26,26,26]"
                       data-paddingbottom="[6,6,6,6]"
                       data-paddingleft="[26,26,26,26]"
                       data-basealign="slide">Ver Produto</a>

                    <!-- LAYER NR. 6 -->
                  </li>
                  <!-- end slider item -->
                <?php
                endforeach;
            endif;
            ?>

        </ul>
      </div>
    </div>
    <!-- end revolution slider -->
  </article>
</section>
<!-- end slider section -->