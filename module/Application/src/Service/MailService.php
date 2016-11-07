<?php

namespace Application\Service;

use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

class MailService
{
    const TEMPLATE_COMMENT        = 1;
    const TEMPLATE_GROUP          = 2;
    const TEMPLATE_EVENT          = 3;
    const TEMPLATE_REMINDER       = 4;
    const TEMPLATE_EVENT_UPDATE   = 5;
    const TEMPLATE_PASSWORD       = 6;
    const TEMPLATE_CONTACT        = 7;
    const TEMPLATE_CONTACT_GROUP  = 8;
    const TEMPLATE_WELCOME        = 9;
    const TEMPLATE_WELCOME_GROUP  = 10;
    const TEMPLATE_ACCOUNT_VERIFY = 11;

    protected $_transport;
    protected $_mail;

    public function __construct($transport)
    {
        $this->_transport = $transport;
        $this->_mail = new Message();
    }

    public function send()
    {
        try {
            $this->_mail->addFrom('volleyball.eu@gmail.com');
            $this->_transport->send($this->_mail);
        } catch (\Exception $e) {}
    }

    public function addBcc($recipients)
    {
        $this->_mail->addBcc($recipients);
    }

    public function addTo($email)
    {
        $this->_mail->addTo($email);
    }


    public function setSubject($subject)
    {
        $this->_mail->setSubject($subject);
    }

    private function _getCss($name, $more = null)
    {
        $css = [
            'h1' => '
                color:#34495e;
                display:block;
                font-family:Helvetica;
                font-size:60px;
                font-style:normal;
                font-weight:bold;
                line-height:60%;
                letter-spacing:normal;
                margin-top:0;
                margin-right:0;
                margin-bottom:10px;
                margin-left:0;
                text-align:right;
            ',
            'h2' => '
                color:#404040;
                display:block;
                font-family:Helvetica;
                font-size:40px;
                font-style:normal;
                font-weight:bold;
                line-height:100%;
                letter-spacing:normal;
                margin-top:0;
                margin-right:0;
                margin-bottom:10px;
                margin-left:0;
                text-align:right;
            ',
            'h3' => '
                color:#34495e;
                display:block;
                font-family:Helvetica;
                font-size:30px;
                font-style:normal;
                font-weight:bold;
                line-height:100%;
                letter-spacing:normal;
                margin-top:0;
                margin-right:0;
                margin-bottom:10px;
                margin-left:0;
                text-align:left;
            ',
            'h4' => '
                color:#808080;
                display:block;
                font-family:Helvetica;
                font-size:20px;
                font-style:normal;
                font-weight:bold;
                line-height:100%;
                letter-spacing:normal;
                margin-top:0;
                margin-right:0;
                margin-bottom:10px;
                margin-left:0;
                text-align:left;
            ',
            '#outlook-a' => '
                padding:0;
            ',
            'body' => '
                width:100% !important;
                -webkit-text-size-adjust:none;
                margin:0;
                padding:0;
                background-color:#F4F4F4;
            ',
            'ReadMsgBody' => '
                width:100%;
            ',
            'ExternalClass' => '
                width:100%;
            ',
            'img' => '
                border:0;
                height:auto;
                line-height:100%;
                outline:none;
                text-decoration:none;
            ',
            'td' => '
                border-collapse:collapse;
            ',
            'bodyTable' => '
                height:100% !important;
                margin:0;
                padding:0;
                width:100% !important;
            ',
            'templatePreheader' => '
                background-color:#000000;
                border-bottom:0;
            ',
            'preheaderContent' => '
                color:#FFFFFF;
                font-family:Helvetica;
                font-size:10px;
                line-height:125%;
                text-align:left;
            ',
            'preheaderContent-a' => '
                color:#E60101;
                font-weight:normal;
                text-decoration:underline;
            ',
            'yshortcuts' => '
                color:#E60101;
                font-weight:normal;
                text-decoration:underline;
            ',
            'templateHeader' => '
                background-color:#F4F4F4;
                border-top:0;
                border-bottom:0;
            ',
            'headerContent' => '
                color:#505050;
                font-family:Helvetica;
                font-size:20px;
                font-weight:bold;
                line-height:100%;
                padding-top:0;
                padding-right:0;
                padding-bottom:0;
                padding-left:0;
                text-align:left;
                vertical-align:middle;
            ',
            'headerContent-a' => '
                color:#EB4102;
                font-weight:normal;
                text-decoration:underline;
            ',
            'headerImage' => '
                height:auto;
                max-width:600px !important;
            ',
            'templateBody' => '
                background-color:#F4F4F4;
            ',
            'calendarTitleBar' => '
                background-color:#D00000;
                border-bottom:1px solid #B00000;
            ',
            'calendarTitleBarContent' => '
                color:#FFFFFF;
                font-family:Helvetica;
                font-size:10px;
                font-style:normal;
                font-weight:bold;
                line-height:100%;
                letter-spacing:normal;
                text-align:center;
                text-decoration:none;
            ',
            'calendarTitleBarContent-a' => '
                color:#FFFFFF;
                font-family:Helvetica;
                font-size:10px;
                font-style:normal;
                font-weight:bold;
                line-height:100%;
                letter-spacing:normal;
                text-align:center;
                text-decoration:none;
            ',
            'calendarContentBlock' => '
                -webkit-border-radius:0 0 10px 10px;
                background-color:#FFFFFF;
                border:1px solid #E8E8E8;
                border-radius:0 0 10px 10px;
            ',
            'calendarContent' => '
                color:#303030;
                font-family:Helvetica;
                font-size:40px;
                font-style:normal;
                font-weight:bold;
                line-height:100%;
                letter-spacing:-1px;
                text-align:center;
                text-decoration:none;
            ',
            'calendarContent-a' => '
                color:#303030;
                font-family:Helvetica;
                font-size:40px;
                font-style:normal;
                font-weight:bold;
                line-height:100%;
                letter-spacing:-1px;
                text-align:center;
                text-decoration:none;
            ',
            'calendarTitleBar-title' => '
                line-height:100% !important;
                margin:0 !important;
            ',
            'calendarContent-title' => '
                line-height:100% !important;
                margin:0 !important;
            ',
            'bodyContentBlock' => '
                border-top:1px solid #CCCCCC;
            ',
            'bodyContent' => '
                color:#505050;
                font-family:Helvetica;
                font-size:13px;
                line-height:150%;
                text-align:left;
                display:inline;
                height:auto;
            ',
            'bodyContent-a' => '
                color:#D00000;
                font-weight:normal;
                text-decoration:underline;
            ',
            'templateFooter' => '
                border-top:0;
            ',
            'footerContent' => '
                .footerContent{
                  color:#808080;
                  font-family:Helvetica;
                  font-size:10px;
                  line-height:150%;
                  text-align:left;
            ',
            'footerContent-a' => '
                color:#606060;
                font-weight:normal;
                text-decoration:underline;
            ',
            'monkeyRewards-img' => '
                max-width:190px !important;
            ',
            'footerContent-img' => '
                display:inline;
            ',
            'button' => '
                -webkitborder-radius:5px;
                background-color:#4A90BE;
                border-radius:5px;
                color:#FFFFFF;
                font-family:Helvetica;
                font-size:16px;
                font-weight:bold;
                line-height:100%;
                text-decoration:none;
            ',
            'link' => '
                font-size:13px;
                color:#505050;
                font-family: Helvetica;
                text-decoration:none;
            ',
            'timeline-date' => '
                color: #FFF;
                padding-top: 20px;
                left: 0;
                margin-left: 0;
                width: 80px;
                height: 60px;
                z-index: 100;
                background-color: #88CB88;
                border-radius: 100%;
                border: 7px solid #FFF;
                font-size:26px;
            '
        ];
        if (!empty($more)) {
             return 'style="' . $css[$name] . $more . '"';

        }
        return 'style="' . $css[$name] . '"';
    }

    public function getHeader()
    {
        return '
            <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" ' . $this->_getCss('body') . '>
              <center>
                  <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" ' . $this->_getCss('bodyTable') . '">
                      <tr>
                          <td align="center" valign="top" ' . $this->_getCss('td') . '>
                              <!-- // BEGIN CONTAINER -->
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateContainer">
                                  <tr>
                                      <td align="center" valign="top">
                                          <!-- // BEGIN HEADER -->
                                          <table border="0" cellpadding="20" cellspacing="0" width="100%" ' . $this->_getCss('templateHeader') . '>
                                              <tr>
                                                  <td align="center" valign="top">
                                                      <table border="0" cellpadding="0" cellspacing="0" width="600">
                                                          <tr>
                                                                <td ' . $this->_getCss('headerContent') . '>
                                                                    <img src="{baseUrl}/img/logo-mail.png" style="max-width:600px;" id="headerImage campaign-icon">
                                                                </td>
                                                                <td>
                                                                    <h1 ' . $this->_getCss('h1') . '>Volley-ball.eu</h1>
                                                                    <h2  ' . $this->_getCss('h2') . '>{subtitle}</h2>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                            <!-- END HEADER \\ -->
                                        </td>
                                    </tr>
        ';
    }

    public function getFooter()
    {
        return '
                                </table>
                                <!-- END CONTAINER \\ -->
                            </td>
                        </tr>
                    </table>
                </center>
            </body>
        </html>
        ';
    }

    public function setTemplate($template, $data)
    {
        $data;
        $content = $this->getHeader();
        switch ($template) {
            case self::TEMPLATE_COMMENT:
                $content .= '
                    <tr>
                      <td align="center" valign="top">
                          <!-- // BEGIN BODY -->
                          <table border="0" cellpadding="40" cellspacing="0" width="100%">
                              <tr>
                                  <td align="center" valign="top" ' . $this->_getCss('templateBody') . '>
                                      <table border="0" cellpadding="0" cellspacing="0" width="600">
                                          <tr>
                                                <td valign="top" ' . $this->_getCss('bodyContent', 'padding-bottom:20px;') . '>
                                                    <h3 ' . $this->_getCss('h3') . '>{title}</h3>
                                                    <h4 ' . $this->_getCss('h4', 'text-align:left') . '>Nouveau Commentaire</h4>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center" valign="top" ' . $this->_getCss('bodyContentBlock', 'padding-top:30px;') . '>
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                        <tr>
                                                            <td align="center" valign="top" width="100">
                                                                <div '. $this->_getCss('timeline-date') . '>
                                                                    <b>{date}</b>
                                                                </div>
                                                            </td>
                                                            <td width="20">
                                                              <br>
                                                            </td>
                                                            <td valign="top" ' . $this->_getCss('bodyContent', 'padding-bottom:30px;') . '>
                                                                <h3>{username}</h3>
                                                                {comment}
                                                                <br>
                                                                <br>
                                                                <table border="0" cellpadding="10" cellspacing="0" ' . $this->_getCss('button') . '>
                                                                    <tr>
                                                                        <td align="center" valign="middle">
                                                                            <a href="{baseUrl}/event/detail/{eventId}#comment" target="_blank" style="color:#FFFFFF;text-decoration:none;">Répondre</a>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <!-- END BODY \\ -->
                        </td>
                    </tr>
                ';
                break;
            case self::TEMPLATE_GROUP_SHARE:
                $content .= '
                    <tr>
                      <td align="center" valign="top">
                          <!-- // BEGIN BODY -->
                          <table border="0" cellpadding="40" cellspacing="0" width="100%">
                              <tr>
                                  <td align="center" valign="top" ' . $this->_getCss('templateBody') . '>
                                      <table border="0" cellpadding="0" cellspacing="0" width="600">
                                          <tr>
                                                <td valign="top" ' . $this->_getCss('bodyContent', 'padding-bottom:20px;') . '>
                                                    <h3 ' . $this->_getCss('h3') . '>{name}</h3>
                                                    <h4 ' . $this->_getCss('h4', 'text-align:left') . '>Vous avez été invité</h4>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center" valign="top" ' . $this->_getCss('bodyContentBlock', 'padding-top:30px;') . '>
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                        <tr>
                                                            <td valign="top" ' . $this->_getCss('bodyContent', 'padding-bottom:30px;') . '>
                                                                Bonjour,
                                                                <br>
                                                                Volley-ball.eu est un outil de gestion de groupe de volley.<br>
                                                                Vous êtes invité à rejoindre le groupe {name}. Merci de suivre le lien ci dessous. <br>
                                                                <table border="0" cellpadding="10" cellspacing="0" ' . $this->_getCss('button') . '>
                                                                    <tr>
                                                                        <td align="center" valign="middle">
                                                                            <a href="{baseUrl}/welcome-to/{brand}" target="_blank" style="color:#FFFFFF;text-decoration:none;">Rejoindre le groupe</a>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <!-- END BODY \\ -->
                        </td>
                    </tr>
                ';
                break;

            case self::TEMPLATE_PASSWORD:
                $content .= '
                    <div style="">
                        <div style ="">
                            <h1 ' . $this->_getCss('h1') . '>
                                Votre nouveau mot de passe
                            </h1>
                        </div>
                        <div style="">
                            <h3 style="margin-left:50px;font-weight: 500; font-family: HelveticaNeue-Thin;text-decoration:underline;">
                                Pour le compte {email}
                            </h3>
                            <div style="margin-left:50px;">
                                Ce mot de passe à été généré aléatoirement : <i>{password}</i> <br/>
                                Vous pouvez maintenant vous connecter à votre compte avec ce nouveau mot de passe.
                            </div>
                            <div style ="">
                                <a href="{baseUrl}/auth/login" style="">Repondre</a>
                            </div>
                        </div>
                    </div>
                ';
                break;

            case self::TEMPLATE_EVENT_UPDATE:
            case self::TEMPLATE_REMINDER:
            case self::TEMPLATE_EVENT:
                $content .= '
                    <tr>
                        <td align="center" valign="top">
                          <!-- // BEGIN BODY -->
                            <table border="0" cellpadding="40" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center" valign="top" ' . $this->_getCss('templateBody') . '>
                                        <table border="0" cellpadding="0" cellspacing="0" width="600">
                                            <tr>
                                                <td valign="top" ' . $this->_getCss('bodyContent', 'padding-bottom:20px;') . '>
                                                  <h3 ' . $this->_getCss('h3') . '>{title}</h3>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center" valign="top" ' . $this->_getCss('bodyContentBlock', 'padding-top:30px;') . '>
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                        <tr>
                                                          <td align="center" valign="top" width="100">
                                                                <table border="0" cellpadding="6" cellspacing="0" width="100" ' . $this->_getCss('calendarContentBlock') . '>
                                                                    <tr>
                                                                        <td align="center" valign="top" ' . $this->_getCss('calendarTitleBar', 'color:#FFFFFF;font-size:0.7em;') . '>
                                                                            {month}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align="center" valign="top" ' . $this->_getCss('calendarContent', 'padding-top:15px; padding-bottom:15px;') . '>
                                                                            {day}
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                            <td width="20">
                                                              <br>
                                                            </td>
                                                            <td valign="top" ' . $this->_getCss('bodyContent', 'padding-bottom:10px;') . '>
                                                                Voici les informations concernant l\'évènement:<br />
                                                                Vous êtes attendu le <b>{date}</b> à l\'adresse suivante:
                                                                <br><br>
                                                                <i>
                                                                    {name}<br />
                                                                    {address}<br />
                                                                    {zip} {city}<br />
                                                                </i>
                                                                <br>
                                                                Merci de donner au plus vite votre disponibilité pour cette date en cliquant sur un des liens ci-dessous<br><br>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td></td>
                                                            <td colspan="2" align="center" valign="top" width="100%">
                                                                  <table border="0" cellpadding="6" cellspacing="0" width="100%">
                                                                        <tr>
                                                                            <td align="center" valign="top">
                                                                                <a href="{baseUrl}/guest/response/{eventId}/{ok}" title="présent" ' . $this->_getCss('link') . '>Présent<br><img src="{baseUrl}/img/fa-check-square-o.png" /></a>
                                                                            </td>
                                                                            <td align="center" valign="top">
                                                                                <a href="{baseUrl}/guest/response/{eventId}/{no}" title="Absent" ' . $this->_getCss('link') . '>Absent<br><img src="{baseUrl}/img/fa-times.png" /></a>
                                                                            </td>
                                                                            <td align="center" valign="top">
                                                                                <a href="{baseUrl}/guest/response/{eventId}/{perhaps}" title="incertain" ' . $this->_getCss('link') . '>Incertain<br><img src="{baseUrl}/img/fa-question-circle.png" /></a>
                                                                            </td>
                                                                            <td align="center" valign="top">
                                                                                <table border="0" cellpadding="10" cellspacing="0" ' . $this->_getCss('button', 'margin-top:15px;') . '>
                                                                                    <tr>
                                                                                        <td align="center" valign="middle">
                                                                                            <a href="{baseUrl}/event/detail/{eventId}" target="_blank" style="color:#FFFFFF;text-decoration:none;">Détails</a>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                  </table>
                                                              </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <!-- END BODY \\ -->
                        </td>
                    </tr>
                ';
                break;

            case self::TEMPLATE_GROUP:
                $content .= '
                <tr>
                    <td align="center" valign="top">
                      <!-- // BEGIN BODY -->
                        <table border="0" cellpadding="40" cellspacing="0" width="100%">
                            <tr>
                                <td align="center" valign="top" ' . $this->_getCss('templateBody') . '>
                                    <table border="0" cellpadding="0" cellspacing="0" width="600">
                                        <tr>
                                            <td valign="top" ' . $this->_getCss('bodyContent', 'padding-bottom:20px;') . '>
                                              <h3 ' . $this->_getCss('h3') . '>{title}</h3>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center" valign="top" ' . $this->_getCss('bodyContentBlock', 'padding-top:30px;') . '>
                                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                    <tr>
                                                        <td valign="top" ' . $this->_getCss('bodyContent', 'padding-bottom:10px;') . '>
                                                            <h3>Nouvel demande d\'adhésion au groupe</h3>
                                                            {username} a demandé à faire parti du groupe <b>{groupname}</b><br />

                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center" valign="top" width="100%">
                                                            <table border="0" cellpadding="6" cellspacing="0" width="100%">
                                                                <tr>
                                                                    <td align="center" valign="top">
                                                                        <a href="{baseUrl}/group/update/{groupId}/{userId}/{ok}" title="Accepter" ' . $this->_getCss('link') . '>Accepter<br><img src="{baseUrl}/img/fa-check-square-o.png" /></a>
                                                                    </td>
                                                                    <td align="center" valign="top">
                                                                        <a href="{baseUrl}/group/update/{groupId}/{userId}/{no}" title="Refuser" ' . $this->_getCss('link') . '>Refuser<br><img src="{baseUrl}/img/fa-times.png" /></a>
                                                                    </td>
                                                                    <td align="center" valign="top">
                                                                        <table border="0" cellpadding="10" cellspacing="0" ' . $this->_getCss('button', 'margin-top:15px;') . '>
                                                                            <tr>
                                                                                <td align="center" valign="middle">
                                                                                    <a href="/group/users/{groupId}" target="_blank" style="color:#FFFFFF;text-decoration:none;">Voir les membres</a>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                          </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <!-- END BODY \\ -->
                    </td>
                </tr>
                ';
                break;

                case self::TEMPLATE_CONTACT_GROUP:
                    $content .= '
                    <tr>
                        <td align="center" valign="top">
                          <!-- // BEGIN BODY -->
                            <table border="0" cellpadding="40" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center" valign="top" ' . $this->_getCss('templateBody') . '>
                                        <table border="0" cellpadding="0" cellspacing="0" width="600">
                                            <tr>
                                                <td valign="top" ' . $this->_getCss('bodyContent', 'padding-bottom:20px;') . '>
                                                  <h3 ' . $this->_getCss('h3') . '>{title}</h3>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center" valign="top" ' . $this->_getCss('bodyContentBlock', 'padding-top:30px;') . '>
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                        <tr>
                                                            <td valign="top" ' . $this->_getCss('bodyContent', 'padding-bottom:10px;') . '>
                                                                <h3>Nouveau message</h3>
                                                                <b>{name}</b> vous a comtacté</b><br />

                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" valign="top" width="100%">
                                                                <table border="0" cellpadding="6" cellspacing="0" width="100%">
                                                                    <tr>
                                                                        <td align="center" valign="top">
                                                                            email 
                                                                        </td>
                                                                        <td align="center" valign="top">
                                                                            {email}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align="center" valign="top">
                                                                            message 
                                                                        </td>
                                                                        <td align="center" valign="top">
                                                                            {message}
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                              </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <!-- END BODY \\ -->
                        </td>
                    </tr>
                    ';
                    break;
                case self::TEMPLATE_WELCOME:
                    $content .= '
                    <tr>
                        <td align="center" valign="top">
                          <!-- // BEGIN BODY -->
                            <table border="0" cellpadding="40" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center" valign="top" ' . $this->_getCss('templateBody') . '>
                                        <table border="0" cellpadding="0" cellspacing="0" width="600">
                                            <tr>
                                                <td valign="top" ' . $this->_getCss('bodyContent', 'padding-bottom:20px;') . '>
                                                  <h3 ' . $this->_getCss('h3') . '>Votre compte a été créé</h3>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center" valign="top" ' . $this->_getCss('bodyContentBlock', 'padding-top:30px;') . '>
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                        <tr>
                                                            <td valign="top" ' . $this->_getCss('bodyContent', 'padding-bottom:10px;') . '>
                                                                <h3>Merci de vous être enregistré</h3>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" valign="top" width="100%">
                                                                <table border="0" cellpadding="6" cellspacing="0" width="100%">
                                                                    <tr>
                                                                        <td align="center" valign="top">
                                                                            Bonjour {name},<br/><br/> merci de vous être enregistré sur http://volley-ball.eu,<br/>
                                                                            Vous pouvez dès à présent créer un groupe, en rejoindre et surtout faire du volley.
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align="center" valign="top">
                                                                            <a href="{baseUrl}/group/add" target="_blank" ' . $this->_getCss('button', 'margin-top:15px;') . '>Créer un groupe</a>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                              </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <!-- END BODY \\ -->
                        </td>
                    </tr>
                    ';
                    break;
                case self::TEMPLATE_WELCOME_GROUP:
                    $content .= '
                    <tr>
                        <td align="center" valign="top">
                          <!-- // BEGIN BODY -->
                            <table border="0" cellpadding="40" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center" valign="top" ' . $this->_getCss('templateBody') . '>
                                        <table border="0" cellpadding="0" cellspacing="0" width="600">
                                            <tr>
                                                <td valign="top" ' . $this->_getCss('bodyContent', 'padding-bottom:20px;') . '>
                                                  <h3 ' . $this->_getCss('h3') . '>Votre compte a été créé</h3>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center" valign="top" ' . $this->_getCss('bodyContentBlock', 'padding-top:30px;') . '>
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                        <tr>
                                                            <td valign="top" ' . $this->_getCss('bodyContent', 'padding-bottom:10px;') . '>
                                                                <h3>merci de vous être enregistré</h3>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" valign="top" width="100%">
                                                                <table border="0" cellpadding="6" cellspacing="0" width="100%">
                                                                    <tr>
                                                                        <td valign="top">
                                                                            Bonjour {name},<br />merci de vous être enregistré sur http://volley-ball.eu,<br /><br />
                                                                            Votre demande pour rejoindre le group {group} a bien été enregistré.<br/>Une réponse vous sera donné sous peu. Peut-être voulez vous créer votre propre groupe? 
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align="center" valign="top">
                                                                            <table border="0" cellpadding="10" cellspacing="0" ' . $this->_getCss('button', 'margin-top:15px;') . '>
                                                                                <tr>
                                                                                    <td align="center" valign="middle">
                                                                                        <a href="{baseUrl}/group/add" target="_blank" ' . $this->_getCss('button', 'margin-top:15px;') . '>Créer un groupe
                                                                                        </a>
                                                                                    </td>
                                                                                </tr>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                              </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <!-- END BODY \\ -->
                        </td>
                    </tr>
                    ';
                    break;

                case self::TEMPLATE_ACCOUNT_VERIFY:
                    $content .= '
                    <tr>
                        <td align="center" valign="top">
                          <!-- // BEGIN BODY -->
                            <table border="0" cellpadding="40" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center" valign="top" ' . $this->_getCss('templateBody') . '>
                                        <table border="0" cellpadding="0" cellspacing="0" width="600">
                                            <tr>
                                                <td valign="top" ' . $this->_getCss('bodyContent', 'padding-bottom:20px;') . '>
                                                  <h3 ' . $this->_getCss('h3') . '>Vérification de compte</h3>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center" valign="top" ' . $this->_getCss('bodyContentBlock', 'padding-top:30px;') . '>
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                        <tr>
                                                            <td valign="top" ' . $this->_getCss('bodyContent', 'padding-bottom:10px;') . '>
                                                                <h3>Merci de vous être enregistré</h3>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" valign="top" width="100%">
                                                                <table border="0" cellpadding="6" cellspacing="0" width="100%">
                                                                    <tr>
                                                                        <td valign="top">
                                                                            Bonjour ,<br />merci de vous être enregistré sur http://volley-ball.eu,<br /><br />
                                                                            Afin de pouvoir utiliser votre compte sur http://volley-ball.eu, vous devez confirmer votre adresse email en cliquant sur le lien ci-dessous :
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align="center" valign="top">
                                                                            <table border="0" cellpadding="10" cellspacing="0" ' . $this->_getCss('button', 'margin-top:15px;') . '>
                                                                                <tr>
                                                                                    <td align="center" valign="middle">
                                                                                        <a href="{url}" target="_blank" ' . $this->_getCss('button', 'margin-top:15px;') . '>Confirmation d\'email
                                                                                        </a>
                                                                                    </td>
                                                                                </tr>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                              </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <!-- END BODY \\ -->
                        </td>
                    </tr>
                    ';
                    break;
        }


        $content .= $this->getFooter();

        $keys = array_keys($data);
        foreach ($keys as $key) $result[] = '/\{' . $key . '\}/';
        $content = preg_replace($result, array_values($data), $content);

        $html = new MimePart($content);
        $html->type = "text/html";

        $body = new MimeMessage();
        $body->addPart($html);
        $this->_mail->setBody($body);
    }
}
