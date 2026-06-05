<?php

if (!$Read) {
    $Read = new Read;
}

$Read->exeRead(DB_PAGES, "WHERE page_name = :nm AND page_status = 1", "nm={$URL[0]}");
if (!$Read->getResult()) {
    require REQUIRE_PATH . '/404.php';
    return;
} else {
    extract($Read->getResult()[0]);
}
?>

<!-- ========================== -->
<!-- SERVICES - HEADER -->
<!-- ========================== -->
<main>
    <header class="top-header services-header with-bottom-effect transparent-effect dark">
        <div class="bottom-effect"></div>
        <div class="header-container wow fadeInUp">
            <div class="header-title">
                <div class="header-icon"><span class="icon icon-ChemicalGlass"></span></div>
                <h2 class="title">SOLUÇÕES PARA INDÚSTRIA B2B</h2>
                <em>Produtos com foco em geração de demanda qualificada e ganho de eficiência comercial.</em>
            </div>
        </div><!--container-->
    </header>

    <!-- ========================== -->
    <!-- SERVICES - STEPS  -->
    <!-- ========================== -->
    <section class="features-section">
        <div class="container">
            <div class="section-heading">
                <h1 class="section-title">Soluções produtizadas para indústria B2B</h1>
                <div class="section-subtitle">Oferta direta, escopo claro e foco em geração de oportunidade para o
                    comercial.
                </div>
                <div class="design-arrow"></div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-sm-6 wow fadeIn">
                    <article>
                        <div class="feature-item">
                            <div class="wrap-feature-icon">
                                <div class="feature-icon">
                                    <span class="icon icon-Magnet"></span>
                                </div>
                            </div>
                            <h2 class="title">Setup Máquina Comercial B2B</h2>
                            <p class="text">Implantação e integração do ecossistema RD Station + Exact Sales ao seu site
                                atual, com roteamento inteligente de leads para o comercial.</p>
                            <p>
                                <a href="https://wa.me/555434199425?text=Quero+falar+com+um+consultor+sobre+Setup+M%C3%A1quina+Comercial+B2B"
                                   target="_blank"
                                   title="Falar com consultor sobre Setup Máquina Comercial B2B"
                                   class="btn btn-primary">Falar com Consultor</a>
                            </p>
                        </div>
                    </article>
                </div>
                <div class="col-md-6 col-sm-6 wow fadeIn">
                    <article>
                        <div class="feature-item active">
                            <div class="wrap-feature-icon">
                                <div class="feature-icon">
                                    <span class="icon icon-Web"></span>
                                </div>
                            </div>
                            <h2 class="title">Hub Digital de Vendas (Portal B2B)</h2>
                            <p class="text">Desenvolvimento de portal de alta performance em PHP/Laravel com SEO técnico
                                e integrações via API para empresas com site defasado.</p>
                            <p>
                                <a href="https://wa.me/555434199425?text=Quero+solicitar+or%C3%A7amento+do+Portal+B2B"
                                   target="_blank"
                                   title="Solicitar orçamento do Portal B2B"
                                   class="btn btn-primary">Solicitar Orçamento do Portal</a>
                            </p>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <section class="core-features-section">
        <div class="container">
            <div class="section-heading">
                <h1 class="section-title">Principais Serviços</h1>
                <div class="section-subtitle">A Zen esta capacitada para entregar com excelência o seguintes serviços
                </div>
                <div class="design-arrow"></div>
            </div>
        </div>

        <div class="container">
            <div class="service-navigation">
                <ul class="row" role="tablist">
                    <li role="presentation">
                        <a href="#tabWeb" aria-controls="tabWeb" role="tab" data-toggle="tab">
                            <div class="col-md-3 col-sm-3 col-xs-3 wow zoomInUp" data-wow-delay="0.2s">
                                <div class="navigation-item">
                                    <div class="navigation-icon">
                                        <span class="icon icon-DesktopMonitor"></span>
                                    </div>
                                    <h3>Desenvolvimento Web</h3>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li role="presentation" class="active">
                        <a href="#tabMobile" aria-controls="tabMobile" role="tab" data-toggle="tab">
                            <div class="col-md-3 col-sm-3 col-xs-3 wow zoomInUp" data-wow-delay="0.3s">
                                <div class="navigation-item">
                                    <div class="navigation-icon">
                                        <span class="icon icon-Magnet"></span>
                                    </div>
                                    <h3>Inbound Marketing</h3>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#tabPhoto" aria-controls="tabPhoto" role="tab" data-toggle="tab">
                            <div class="col-md-3 col-sm-3 col-xs-3 wow zoomInUp" data-wow-delay="0.4s">
                                <div class="navigation-item">
                                    <div class="navigation-icon">
                                        <span class="icon icon-ChartUp"></span>
                                    </div>
                                    <h3>Mentoria Digital</h3>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#tabDesign" aria-controls="tabDesign" role="tab" data-toggle="tab">
                            <div class="col-md-3 col-sm-3 col-xs-3 wow zoomInUp" data-wow-delay="0.5s">
                                <div class="navigation-item">
                                    <div class="navigation-icon">
                                        <span class="icon icon-Web"></span>
                                    </div>
                                    <h3>Sistemas</h3>
                                </div>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="container tab-content wow fadeInUp">
            <div role="tabpanel" class="tab-pane" id="tabWeb">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="italic-title">Design e engenharia da informação</h3>
                        <h5>Nos referimos ao córtex visual do cérebro onde os dados sensoriais são recebidos, padrões
                            reconhecidos, e as imagens formuladas.</h5>
                        <p>
                            Uma estratégia bem-sucedida deve partir do conhecimento profundo das necessidades
                            subconcientes e antropológicas do consumidor <a
                                href="https://agencia.zen.ppg.br/diagnostico-digital-b2b"
                                title="Baixe um E-book exclusivo e aprenda a definir suas Buyer Personas"
                                target="_blank">(PERSONAS)</a>. Ao entender essas necessidades, as marcas podem
                            conectar-se com seus clientes e oferecer algo mais poderoso do que diferenciação funcional.
                        </p>

                        <ul class="marker-list">
                            <li>Usamos ferramentas sofisticadas para ler e decifrar esse consumidor</li>
                            <li>Podemos entregar um Design Único ou adaptar algum que você goste</li>
                            <li>Nosso CMS - Zen Controll possibilita o gerenciamento do conteúdo do seu site, além de
                                ter suporte especializado
                            </li>
                            <li>Seu projeto estará focado em trazer a melhor experiência ao seu usuário, e no
                                melhoramento continuo
                            </li>
                            <li>Seu website se adaptará aos principais dispositivos de acesso a internet (Smartphone,
                                TVs, Videogames, tablets).
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane active" id="tabMobile">
                <div class="row">
                    <div class="col-md-4 iphone-image">
                        <img src="<?= INCLUDE_PATH; ?>/assets/images/iphone-ipad.jpg" alt=""/>
                    </div>
                    <div class="col-md-8 ">
                        <h3 class="italic-title">Inbound Marketing</h3>
                        <h5>Marketing digital baseado no funil de vendas e jornada de compra</h5>
                        <p>O mercado está comprando a ideia de aplicar a metodologia do Inbound Marketing. Isso porque o
                            Inbound Marketing é mais barato (62%) que o o Marketing convencional – ou Outbound Marketing
                            -, que estávamos acostumados a fazer.
                            Não ter uma estratégia de Inbound Marketing para sua empresa pode ser um fator determinante
                            para você ficar atrás de seus concorrentes que já estão aplicando a metodologia.
                        </p>

                        <ul class="marker-list">
                            <li>Atração</li>
                            <li>Conversão</li>
                            <li>Relacionamento</li>
                            <li>Vendas</li>
                            <li>Análise</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="tabPhoto">
                <div class="row">
                    <div class="col-md-12 ">
                        <h3 class="italic-title">Mentoria em Desenvolvimento Web e Marketing Digital</h3>
                        <h5>Mas afinal o que é Mentoria?</h5>

                        <p>
                            O termo deriva da idioma inglês, <em>mentoring</em> (mentoria, aconselhamento, orientação,
                            tutoria, ensinamento). "O <em>mentoring</em> é uma ferramenta de desenvolvimento
                            profissional e consiste em uma pessoa experiente ajudar outra menos experiente."
                        </p>
                        <p>
                            No mundo empresarial, a mentoria vem ganhando popularidade nos últimos anos, pois tem
                            demonstrado ser uma ferramenta mais eficaz em relação a outros métodos de treinamento. A
                            mentoria utiliza o capital intelectual que existe dentro da empresa, por exemplo a sabedoria
                            de funcionários mais experientes, somados a expertise do mentor na area em que foi
                            contratada a mentoria, impulsionando a inovação e a criatividade da empresa, e
                            consequentemente tornando-a mais competitiva.
                        </p>
                        <p>
                            Os benefícios da mentoria não serão exclusividades do mentorado. A empresa após o término do
                            processo poderá contar com um profissional habilititado a conquistar metas e objetivos
                            relacionados a marketing digital. O mentorado poderá se tornar o mentor da empresa e
                            contribuir para o crescimento profissional e pessoal de um colega, equipando a empresa para
                            um futuro promissor.
                        </p>
                        <p>
                            O mentor é um guia, um mestre, conselheiro, alguém que tem vasta experiência profissional no
                            campo de trabalho da pessoa que está sendo ajudada. A mentoria inclui conversas e debates
                            acercas de assuntos que não estão necessariamente ligados ao trabalho. Este processo
                            possibilita o aprendizado e consequente desenvolvimento na carreira do profissional mais
                            jovem.
                        </p>

                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="tabDesign">
                <div class="row">
                    <div class="col-md-12 ">
                        <h3 class="italic-title">Sistemas de gestão e websites profissionais</h3>
                        <h5>Sistemas focados em resultados para sua empresa e prontos para marketing digital</h5>
                        <p>Estamos constantemente explorando novas disciplinas e evoluindo os produtos e serviços
                            existentes para melhor atender às necessidades dos nossos clientes. Manter várias
                            áreas-chave de especialização ajuda a melhorar a nossa criatividade, estratégia e
                            produção.</p>
                        <h5>Acesse abaixo as Demos das Soluções Zen</h5>
                        <ul class="rocket-list">
                            <li><h6>
                                    <a href="" target="_blank"
                                       title="Acessar a Demostrativo do Sistema para Imobiliária">Sistema e Website para
                                        Imobiliárias</a></h6></li>
                            <li><h6><a href="" target="_blank"
                                       title="Acessar a Demostrativo do Sistema para Loja Virtual"> Sistema de
                                        E-commerce - Lojas Virtuais</a></h6></li>
                            <li><h6><a href="" target="_blank"
                                       title="Acessar a Demostrativo do Sistema para Loja Virtual"> Sistema e Portal
                                        Educacional para EAD (Educação a distância)</a></h6></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- ========================== -->
    <!-- SERVICES - SERVICES  -->
    <!-- ========================== -->
    <section class="services-section service-service-section dark dark-strong with-bottom-effect">
        <div class="bottom-effect"></div>
        <div class="container dark-content">
            <div class="row wow zoomIn">
                <div class="col-md-4 col-sm-4 col-xs-4">
                    <div class="service-item">
                        <div class="media-left">
                            <div class="wrap-service-icon">
                                <div class="service-icon">
                                    <span class="icon icon-PowerOff"></span>
                                </div>
                            </div>
                        </div>
                        <div class="media-body">
                            <h5>Ícones incríveis</h5>
                            <p><em>soluções que funcionam</em></p>
                            <p>Utilizamos o que há de mais moderno, inovador e estável em nossos projetos.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-4">
                    <div class="service-item">
                        <div class="media-left">
                            <div class="wrap-service-icon">
                                <div class="service-icon">
                                    <span class="icon icon-Users"></span>
                                </div>
                            </div>
                        </div>
                        <div class="media-body">
                            <h5>Clean & Moderno</h5>
                            <p><em>foco na experiência do usuário</em></p>
                            <p>O design é pensado para proporcionar a melhor experiência ao usuário.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-4">
                    <div class="service-item">
                        <div class="media-left">
                            <div class="wrap-service-icon">
                                <div class="service-icon">
                                    <span class="icon icon-Planet"></span>
                                </div>
                            </div>
                        </div>
                        <div class="media-body">
                            <h5>Universal</h5>
                            <p><em>acessível a todos via Internet</em></p>
                            <p>Interfaces voltadas a interação, usabilidade, acessibilidade e facilidade de
                                navegação.</p>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="services-divider">
                    <div class="col-md-4 col-sm-4 col-xs-4"></div>
                    <div class="col-md-4 col-sm-4 col-xs-4"></div>
                    <div class="col-md-4 col-sm-4 col-xs-4"></div>
                </div>
            </div>
            <div class="row wow zoomIn">
                <div class="col-md-4 col-sm-4 col-xs-4">
                    <div class="service-item">
                        <div class="media-left">
                            <div class="wrap-service-icon">
                                <div class="service-icon">
                                    <span class="icon icon-Folder"></span>
                                </div>
                            </div>
                        </div>
                        <div class="media-body">
                            <h5>Bem documentado</h5>
                            <p><em>ajuda sempre que precisar</em></p>
                            <p>A documentação auxilia na agilidade e manutenção da aplicação.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-4">
                    <div class="service-item">
                        <div class="media-left">
                            <div class="wrap-service-icon">
                                <div class="service-icon">
                                    <span class="icon icon-Layers"></span>
                                </div>
                            </div>
                        </div>
                        <div class="media-body">
                            <h5>Multiplas cores</h5>
                            <p><em>mantendo a identidade visual</em></p>
                            <p>Respeitamos a harmonia e significado das cores, caso cliente ja possua sua marca.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-4">
                    <div class="service-item">
                        <div class="media-left">
                            <div class="wrap-service-icon">
                                <div class="service-icon">
                                    <span class="icon icon-Phone"></span>
                                </div>
                            </div>
                        </div>
                        <div class="media-body">
                            <h5>Design responsivo</h5>
                            <p><em>independente de dispositivos</em></p>
                            <p>Nossos projetos se adaptam a tela do dispositivo em que está sendo visualizados.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================== -->
    <!-- SERVICES - AREAS OF EXPERTISE  -->
    <!-- ========================== -->
    <section class="areas-section service-areas-section">
        <div class="container">
            <div class="row  wow fadeIn">
                <div class="col-md-7 col-sm-7 text-right">
                    <div class="clearfix " style="padding-right: 3px;">
                        <div class="above-title">Método, integração e operação comercial</div>
                        <h4>IMPLEMENTAÇÃO COM FOCO EM RECEITA</h4>
                    </div>
                    <div><em>Somos a <span class="text-orange">Zen Agência Web</span></em></div>
                    <div class="design-arrow inline-arrow"></div>
                    <p class="large">Atuamos na integração entre marketing e vendas com arquitetura robusta em PHP,
                        automação e governança de dados para transformar seu site em ativo comercial.</p>
                    <p>Mapeamos o funil, removemos fricções e implementamos melhorias contínuas para elevar conversão
                        e produtividade do time de vendas.</p>
                </div>
                <div class="col-md-5 col-sm-5 text-center">
                    <img src="<?= INCLUDE_PATH; ?>/assets/images/areas.png"
                         title="Uma agência especializada em prestar serviços na área do desenvolvimento técnico e criativo de produtos relacionados a Internet - SEO - DESIGN - CÓDIGO - MARKETING DIGITAL"
                         alt="Soluções inovadores em SEO, DESIGN, MARKETING" class="img-responsive">
                </div>
            </div>
        </div>
    </section>


    <!-- ========================== -->
    <!-- SERVICES - AREAS OF EXPERTISE  -->
    <!-- ========================== -->
    <section class="offers-section with-icon">
        <div class="section-icon"><span class="icon icon-Bulb"></span></div>
        <div class="container">
            <div class="section-heading  wow fadeIn">
                <h1 class="section-title">Nossas especialidades</h1>
                <div class="section-subtitle">Quer resultados? Apresente-nos sua ideia...</div>
                <div class="design-arrow"></div>
            </div>
        </div>
        <div class="container">
            <div class="row offers-list">
                <div class="col-md-4 wow fadeInUp" data-wow-delay="0.3s">
                    <div class=" col-md-12">
                        <div class="text-item top-item left-item">
                            <div class="dot-line"></div>
                            <h5>Estratégia</h5>
                            <em>Investigação e Planejamento do ecossistema</em>
                            <p>Desenvolvimento de persona,Estratégia social, Estratégia de marca, Estratégia de
                                conteúdo</p>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="text-item middle-item left-item">
                            <div class="dot-line"></div>
                            <h5>Criatividade</h5>
                            <em>Branding & Posicionamento</em>
                            <p>UX, Design visual, Design de interação, Redação, Campanha criativa</p>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="text-item bottom-item left-item">
                            <div class="dot-line"></div>
                            <h5>Tecnologia</h5>
                            <em>HTML / JS / CSS / PHP </em>
                            <p>Aplicações nativas, Sistemas modulares, Widgets, Gerenciador de Conteúdo - CMS
                                FullStack</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-image wow fadeInUp" data-wow-delay="0.6s">
                    <img src="<?= INCLUDE_PATH; ?>/assets/images/service-round.png" alt=""
                         class=" hidden-xs hidden-sm"/>
                </div>
                <div class="col-md-4 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="col-md-12">
                        <div class="text-item top-item right-item">
                            <div class="dot-line"></div>
                            <h5>Inbound marketing</h5>
                            <em>Gerenciamos sua máquina de vendas</em>
                            <p>Atração, Conversão, Relacionamento. Automação e E-mail marketing, Re-marketing.</p>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="text-item middle-item right-item">
                            <div class="dot-line"></div>
                            <h5>Marketing de conteúdo</h5>
                            <em>Gerando autoridade para sua marca</em>
                            <p>Desenvolvemos conteúdos relevantes e de qualidade para seu nicho de mercado.</p>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="text-item bottom-item right-item">
                            <div class="dot-line"></div>
                            <h5>Gestão digital</h5>
                            <em>gestão de campanhas & mentoria digital</em>
                            <p>Cuidamos para você da gestão digital de sua marca na Internet.</p>
                        </div>
                    </div>
                </div>
            </div><!--row-->
        </div>
    </section>
    <!-- ========================== -->
    <!-- SERVICES SECTION -->
    <!-- ========================== -->
    <?php
    $cta_bg = '#ff6600';
    $top_text = 'Quer saber onde sua operação digital perde oportunidades?';
    $bottom_text = 'Solicite um diagnóstico B2B e receba um plano de ação inicial.';
    $cta_link = 'https://agencia.zen.ppg.br/diagnostico-digital-b2b';
    $cta_text = 'DIAGNÓSTICO GRATUITO';
    include_once 'inc/cta.php';
    ?>

    <!-- ========================== -->
    <!-- SERVICE - PRICE -->
    <!-- ========================== -->
    <!--<section class="price-section">
        <div class="with-bottom-effect dark dark-strong pricing-background">
            <div class="bottom-effect"></div>
        </div>
        <div class="dark-content">
            <div class="container">
                <div class="section-heading">
                    <div class="section-title">our pricing plans</div>
                    <div class="section-subtitle">Lorem ipsum dolor amet consectetur adipisic elit</div>
                    <div class="design-arrow"></div>
                </div>
            </div>
        </div>
        <div class="dark-content">
            <div class="container">
                <div class="row no-gutter plans-list text-center">
                    <div class="col-md-4 col-sm-4 vcenter wow zoomIn" data-wow-delay="0.3s">
                        <div class="plan-item">
                            <div class="item-heading">
                                <span class="name">basic</span>
                                <div class="count">$125</div>
                                <em>per month</em>
                            </div>
                            <div class="item-body">
                                <ul class="list-features">
                                    <li class="active">2 Hosting Accounts</li>
                                    <li class="active">10 FREE Users</li>
                                    <li class="active">600GB Monthly Bandwidth</li>
                                    <li>Complete Analytics</li>
                                    <li>Unlimited Databses</li>
                                </ul>
                            </div>
                            <div class="item-footer text-center">
                                <a href="#" class="btn btn-default">sign up now</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4 vcenter wow zoomIn" data-wow-delay="0.6s">
                        <div class="plan-item active">
                            <div class="item-heading">
                                <span class="name">popular</span>
                                <div class="count">$175</div>
                                <em>per month</em>
                            </div>
                            <div class="item-body">
                                <ul class="list-features">
                                    <li class="active">8 Hosting Accounts </li>
                                    <li class="active">30 FREE Users</li>
                                    <li class="active">2 TB Monthly Bandwidth </li>
                                    <li class="active">Complete Analytics</li>
                                    <li>Unlimited Support</li>
                                </ul>
                            </div>
                            <div class="item-footer text-center">
                                <a href="#" class="btn btn-default">sign up now</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4  col-sm-4 vcenter wow zoomIn" data-wow-delay="0.3s">
                        <div class="plan-item">
                            <div class="item-heading">
                                <span class="name">premier</span>
                                <div class="count">$250</div>
                                <em>per month</em>
                            </div>
                            <div class="item-body">
                                <ul class="list-features">
                                    <li class="active">25 Hosting Accounts </li>
                                    <li class="active">Unlimited FREE Users </li>
                                    <li class="active">8 TB Monthly Bandwidth </li>
                                    <li class="active">Complete Analytics</li>
                                    <li class="active">Unlimited Storage</li>
                                </ul>
                            </div>
                            <div class="item-footer text-center">
                                <a href="#" class="btn btn-default">sign up now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>-->

    <!-- ========================== -->
    <!-- SERVICE - REVIEWS SECTION -->
    <!-- ========================== -->
    <section class="reviews-section with-icon with-top-effect clearfix">
        <div class="section-icon"><span class="icon icon-Message"></span></div>
        <div class="container">
            <div class="section-heading">
                <h3 class="section-title">o que dizem nossos clientes</h3>
                <div class="section-subtitle">veja a satisfação dos clientes que já conhecem nosso trabalho</div>
                <div class="design-arrow"></div>
            </div>
        </div>
        <div class="container">
            <div class="reviews-slider review-slider-seconds enable-owl-carousel" data-pagination="true"
                 data-min1200="2" data-min800="1" data-min600="1">
                <div class="slide-item">
                    <div class="media-left">
                        <div class="image-block">
                            <img src="<?= INCLUDE_PATH; ?>/assets/images/claudia-bampi-alb200.jpg"
                                 alt="Claudia Bampi – Diretora Comercial – ALB Industria e Comércio de Artefatos de Cimento e Ferro LTDA"/>
                        </div>
                    </div>
                    <div class="media-body">
                        <div class="description-block">
                            <div class="name">
                                <span>Claudia</span>
                                <em>ALB</em>
                            </div>
                            <div class="review">
                                <p>“A Zen Agência web, proporcionou a ALB Concretos, uma visão diferente de negócio,
                                    mudando totalmente nosso mindset, através de um Sistema de Gestão Moderno,
                                    proporcionando agilidade, confiabilidade e eficiência ao nosso trabalho.”</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="slide-item">
                    <div class="media-left">
                        <div class="image-block">
                            <img src="<?= INCLUDE_PATH; ?>/assets/images/ivanor-bonatto-belltoy200.jpg"
                                 alt="Ivanor Bonatto - Diretor Comercial da Belltoy Brinquedos"
                                 title="Ivanor Bonatto - Diretor Comercial da Belltoy Brinquedos"/>
                        </div>
                    </div>
                    <div class="media-body">
                        <div class="description-block">
                            <div class="name">
                                <span>Bonatto</span>
                                <em>Belltoy</em>
                            </div>
                            <div class="review">
                                <p>"A Zen Agência Web tem colaborado com o atendimento a nossos clientes e consumidores,
                                    estimulando o crescimento da Belltoy no mercado nacional."</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="slide-item">
                    <div class="media-left">
                        <div class="image-block">
                            <img src="<?= INCLUDE_PATH; ?>/assets/images/oraci-nepomoceno-iccserra200.jpg"
                                 alt="Oraci Nepomoceno - Diretor Geral da ICC SERRA"/>
                        </div>
                    </div>
                    <div class="media-body">
                        <div class="description-block">
                            <div class="name">
                                <span>Oraci</span>
                                <em>ICC Serra</em>
                            </div>
                            <div class="review">
                                <p>“A agência Zen venho para trazer mais autoridade e confiança para nossa marca na Web,
                                    através do nosso Site, aumentando nosso faturamento consideravelmente com captação
                                    de novos clientes.”</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="slide-item">
                    <div class="media-left">
                        <div class="image-block">
                            <img src="<?= INCLUDE_PATH; ?>/assets/images/alderico-mattos-goldstart200.jpg"
                                 alt="Aldérico Mattos - CEO da Goldstart Segurança do Trabalho"
                                 title="Aldérico Mattos - CEO da Goldstart Segurança do Trabalho"/>
                        </div>
                    </div>
                    <div class="media-body">
                        <div class="description-block">
                            <div class="name">
                                <span>Mattos</span>
                                <em>Goldstart</em>
                            </div>
                            <div class="review">
                                <p>"A parceria entre nossa Empresa e a Zen Agência Web revolucionou a forma de
                                    comunicação com nossos clientes através do nosso Site, ajudando a prospectar e
                                    fechar novos negócios, aproximando a Goldstart dos atuais e futuros clientes."</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ========================== -->
    <!-- SERVICES - REVIEWS SECTION -->
    <!-- ========================== -->
    <section class="browsers-section service-browse-section ">
        <h1 class="title-hidden">Sistemas para Internet (Lojas Virtuais, Portal de EAD, Sistema para Imobiliárias,
            Sistema para RH</h1>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <img src="<?= INCLUDE_PATH; ?>/assets/images/browsers-image.png"
                         title="Sistemas para Internet (Lojas Virtuais, Portal de EAD, Sistema para Imobiliárias, Sistema para RH"
                         alt="Sistemas para Internet (Lojas Virtuais, EAD, Sistema para Imobiliárias, Sistema para RH"
                         class="img-responsive">
                </div>
            </div>
        </div>
    </section>
    <br>
    <!-- ========================== -->
    <!-- SERVICES - CLIENTS -->
    <!-- ========================== -->
    <?php
    include_once 'inc/clients.php'; ?>
    <!-- ========================== -->
    <?php
    if (APP_COMMENTS && COMMENT_ON_PAGES) { ?>
        <div class="container" style="background: #fff; padding: 20px 0;">
            <div class="content">
                <?php
                $CommentKey = $page_id;
                $CommentType = 'page';
                require 'assets/widgets/comments/comments.php';
                ?>
                <div class="clear"></div>
            </div>
        </div>
        <?php
    } ?>

</main>
