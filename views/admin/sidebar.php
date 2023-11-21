<?php

/**
 * Security check. No one can access without Wordpress itself
 *
 * */
if (!\defined('ABSPATH')) {
    exit();
}
$assetUrl = esc_html(BFM_ASSET_URL);
?>
 <style>
        @font-face { 
            font-family: 'Outfit';
             src: url('<?php echo $assetUrl;?>fonts/Outfit/Outfit-VariableFont_wght.ttf');
        }
        .deal-container {
            min-width: 400px;
            background: #fff;
        }
        .f-col {
            flex-direction: column;
        }
        .f {
            display: flex;
        }

        .row:nth-child(1) {
            flex: 1;
        }

        .row:nth-child(2) {
            flex: 2;
        }

        .column {
            flex: 1;
            box-sizing: border-box;
        }

        .column:nth-child(1) {
            flex: 1.5;
        }
        .banner {
            max-height: 90px;
            background: linear-gradient(154deg, #be2b2a 44%, #19115d 77%);
            min-width: 360px;
            padding-bottom: 12px;
        }
        .hero {
            align-items: flex-start;
            gap: 5px;
        }
        .txt-block {
            padding-left: 21px;
            padding-top: 12px;
        }
        
        .txt {
            font-family: 'Outfit';
            font-style: normal;
            font-weight: 700;
            text-transform: uppercase;
        }
        .txt-1 {
            color: #FFF;
            font-size: 9.045px;
            line-height: 12.061px; /* 133.333% */
        }
        .txt-2 {
            color: #FFC700;
            font-size: 13.753px;
            line-height: 18.337px; /* 133.333% */
        }
        .txt-3 {
            color: #FFF;
            font-size: 12.724px;
            line-height: 16.965px; /* 133.333% */
        }
        .grab-it {
            border-radius: 16.898px;
            background: #00FFA3;
            color: #000;
            font-family: 'Outfit';
            font-size:  10.449px;
            font-style: normal;
            font-weight: 500;
            line-height: 13.866px; /* 164.106% */
            text-decoration: none;
            padding: 2px 5px;
        }

        .logo-section {
            justify-content: end;
            gap: 5px;
            padding: 5px 0;
        }
        .logo {
            width: max-content;
            border-radius: 3px;
            background: #FFF;
            display: flex;
            padding: 2px 5px;
        }
        
        .logo-txt {
            color: #000;
            font-family: 'Outfit';
            font-size: 8px;
            font-style: normal;
            font-weight: 600;
            line-height: 17.066px;
            /* align-self: center; */
            padding-left: 3px;
        }
        
        .logo-pd {
            color: #000;
            font-family: 'Outfit';
            font-size: 14px;
            font-style: normal;
            font-weight: 600;
            line-height: 17.066px;
            /* align-self: center; */
            padding-left: 3px;
        }
        .deal-img {
            max-width: 100%;
            max-height: 100%;
        }
        .pd {
           padding-top: 15px;
           align-items: flex-start;
           gap: 5px;
        }
        .description {
            color: #393939;
            font-family: 'Outfit';
            font-size: 10px;
            font-style: normal;
            font-weight: 400;
            line-height: 13px;
        }
        .star-count {
            color: #474747;
            font-family: 'Outfit';
            font-size: 10px;
            font-style: normal;
            font-weight: 500;
            line-height: 13px;
        }
</style>
<div class='col-sidebar'>
<div class="f f-col deal-container">
        <div class="row f">
            <a href="https://bitapps.pro/black-friday-deals-2023/?utm=file-manager">
                <img src="<?php echo $assetUrl;?>img/banner.png" class="deal-img"/>
            </a>
        </div>
        <div class="f f-col" style="padding: 5px;">
            <div class="f pd">
                <img src="<?php echo $assetUrl;?>img/logo/form.svg" width="15px"/>
                <div class="f f-col">
                <span class="logo-pd">Bit Form</span>
                <span class="description">Bit Form is an amazing drag & drop form builder that allows you to create custom forms to interact with your visitors. It gives you the freedom to create any form you want, no coding required.</span>
                <div style="color:#ffb900; padding-top:2px;">
                    <span class="dashicons dashicons-star-filled"></span>
                    <span class="dashicons dashicons-star-filled"></span>
                    <span class="dashicons dashicons-star-filled"></span>
                    <span class="dashicons dashicons-star-filled"></span>
                    <span class="dashicons dashicons-star-filled"></span>
                    <span class="star-count">(52)</span>
                </div>
                </div>
            </div>
            <div class="f pd">
                <img src="<?php echo $assetUrl;?>img/logo/assist.svg" width="15px"/>
                <div class="f f-col">
                <span class="logo-pd">Bit Assist</span>
                <span class="description">Bit Assist Connect your all support assistant in a single button. Connect with your visitor using their favorite Facebook Messenger, WhatsApp, Tawk to, Telegram, Viber, Slack etc.</span>
                <div style="color:#ffb900; padding-top:2px;">
                    <span class="dashicons dashicons-star-filled"></span>
                    <span class="dashicons dashicons-star-filled"></span>
                    <span class="dashicons dashicons-star-filled"></span>
                    <span class="dashicons dashicons-star-filled"></span>
                    <span class="dashicons dashicons-star-half"></span>
                    <span class="star-count">(20)</span>
                </div>
                </div>
            </div>
            <div class="f pd">
                <img src="<?php echo $assetUrl;?>img/logo/integration.svg" width="15px"/>
                <div class="f f-col">
                <span class="logo-pd">Bit Integrations</span>
                <span class="description">Bit Integrations Send WordPress Forms, WooCommerce, LMS, Membership plugin and other data to your Google Sheet, CRM, Email Marketing Tools and other platforms.</span>
                <div style="color:#ffb900; padding-top:2px;">
                    <span class="dashicons dashicons-star-filled"></span>
                    <span class="dashicons dashicons-star-filled"></span>
                    <span class="dashicons dashicons-star-filled"></span>
                    <span class="dashicons dashicons-star-filled"></span>
                    <span class="dashicons dashicons-star-filled"></span>
                    <span class="star-count">(52)</span>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>