<?php

$logo_mail = BASE.'/uploads/mail/logo.png';
$fb_page = SITE_SOCIAL_FB_PAGE !== '' ? '<a style="text-decoration: none; border:0; margin-right: 4px; outline:0;" title="Siga '
    .SITE_NAME
    .' no Face" href="https://www.facebook.com/'
    .SITE_SOCIAL_FB_PAGE.'"><img src="'.BASE.'/uploads/mail/facebook.png" alt="Facebook" /></a>' : '';
$instagram = SITE_SOCIAL_INSTAGRAM !== '' ? '<a style="text-decoration: none; border:0; margin-right: 4px; outline:0;" title="Siga a '
    .SITE_NAME
    .' no Instagram" href="https://instagram.com/'.SITE_SOCIAL_INSTAGRAM.'"><img src="'.BASE.'/uploads/mail/instagram.png" /></a>' : '';
$youtube = SITE_SOCIAL_YOUTUBE !== '' ? '<a style="text-decoration: none; border:0; margin-right: 4px; outline:0;" title="Inscreva-se no nosso Canal no Youtube" href="https://www.youtube.com/@'.SITE_SOCIAL_YOUTUBE.'?sub_confirmation=1"><img src="'.BASE.'/uploads/mail/youtube.png" alt="Youtube"/></a>' : '';
$whatsapp = SITE_ADDR_WHATS !== '' ? '<a style="text-decoration: none; border:0; margin-right: 4px; outline:0;" title="Chamar '.SITE_NAME.' no Whats" href="https://wa.me/'.SITE_ADDR_WHATS.'?text=Ol&aacute;%20meu%20nome%20&eacute;"><img src="'.BASE.'/uploads/mail/whats.png"/></a>' : '';

$MailContent = \mb_convert_encoding(
    '<!DOCTYPE html>
<html lang="pt-BR" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
<head> <title>'.SITE_NAME.'</title> </head>
<body>
<style> table{width: 100%; color: #1A1A1A; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 14px; border: 0px} td{height: 40px;} img{width: 30px; height: 30px; margin: 0; padding: 0; border: 0;} </style>

<table border="0" cellpadding="1" cellspacing="0" style="border-collapse: collapse; border:0;color: #1A1A1A; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 14px; border: 0px">
    <tbody>
        <tr> <td width="600" style="vertical-align: top; font-family: Verdana, sans-serif;"> #mail_body# </td> </tr>

        <tr>
            <td width="600">
                <table border="0" cellpadding="1" cellspacing="0">
                    <tbody>
                    <tr>
                        <td width="600">
                            <img class="picture" border="0" style="width: auto; height: 80px;" src="'.$logo_mail.'" alt="'
    .SITE_NAME.' - logo"/>
                         </td>
                    </tr>
                    
                    <tr>
                        <td width="600">
                            <span>Siga-nos nas Redes Sociais</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="600">
                        <p style="padding: 0; margin: 0;">'.$youtube.$fb_page.$instagram.$whatsapp.'</p>
                        </td>
                    </tr>
                    <tr>
                        <td>Visite nosso Site: &raquo;
                            <a style="text-decoration: none;" title="Visite nosso Portal" href="'.BASE.'">'.BASE.'</a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table style="margin-top: 5px;">
                                <tbody>
                                <tr>
                                    <td valign="top" style="vertical-align: top; padding: 8px 0; color: green; font-size: 10px; font-family: Verdana, sans-serif;">
                                    <img src="https://zen.ppg.br/mail/meio.png" alt="Seja sustent&aacute;vel"
                                            width="30px" height="30px"/>
                                            <span style="line-height: 30px; height: 30px">Antes de imprimir pense na sua responsabilidade com o Meio Ambiente</span>
                                    </td>
                                </tr>
                                
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>       
    </tbody>
</table>
</body>
</html>',
    'UTF-8',
    'ISO-8859-1'
);

$MailContent = '
<table width="550" style="font-family: "Trebuchet MS", sans-serif;">
 <tr><td>
  <font face="Trebuchet MS" size="3">
   #mail_body#
  </font>
  <p style="font-size: 0.875em;">
  <img src="'.BASE.'/admin/_img/mail.jpg" alt="Atenciosamente, '.SITE_NAME.'" title="Atenciosamente, '
.SITE_NAME.'" /><br><br>
   '.SITE_ADDR_NAME.'<br>
   Telefone: '.SITE_ADDR_PHONE_A.'<br>
   E-mail: '.SITE_ADDR_EMAIL.'<br><br>
   
   <a title="'.SITE_NAME.'" href="'.BASE.'">'.SITE_ADDR_SITE.'</a><br>'.SITE_ADDR_ADDR.'<br>'
    .SITE_ADDR_CITY.'/'.SITE_ADDR_UF.' - '.SITE_ADDR_ZIP.'<br>'.SITE_ADDR_COUNTRY.'
  </p>
  </td></tr>
</table>
<style>body, img{max-width: 550px !important; height: auto !important;} p{margin-botton: 15px 0 !important;}</style>';
