<!DOCTYPE html>
@php($isRtl = app()->getLocale() === 'ar')
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About | Merchant - Glowlabs Merchant</title>
    <link href="https://fonts.googleapis.com/css2?family=Jersey+15&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}" />

   
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            background-color: #F9FCFD;
        }

        body {
            font-family: 'Assistant', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #e0e0e0;
            background-color: #F9FCFD;
        }

        /* RTL support */
        html[dir="rtl"] body {
            direction: rtl;
        }

        html[dir="rtl"] .merchant-hero__content,
        html[dir="rtl"] .features-notes-side,
        html[dir="rtl"] .features-showcase .service-content,
        html[dir="rtl"] .features-showcase .service-description,
        html[dir="rtl"] .step-content,
        html[dir="rtl"] .merchant-faq-wrap,
        html[dir="rtl"] .faq-answer,
        html[dir="rtl"] .faq-question {
            text-align: right;
        }

        html[dir="rtl"] .features-showcase .service-description {
            text-align: right;
        }

        /* Dashboard usage custom bullets */
        html[dir="rtl"] .step-list li {
            padding-left: 0;
            padding-right: 20px;
        }

        html[dir="rtl"] .step-list li:before {
            left: auto;
            right: 0;
        }

        /* Inline style helpers used in this page */
        html[dir="rtl"] [style*="margin-left: 14px"] {
            margin-left: 0 !important;
            margin-right: 14px !important;
            text-align: right !important;
        }

        html[dir="rtl"] [style*="bottom: 20px; left: 20px"] {
            left: auto !important;
            right: 20px !important;
            text-align: right !important;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

  

     
        /* Header Styles */
        .header {
            background: rgba(20, 20, 20, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 1px 3px rgba(255,255,255,0.1);
            position: fixed;
            top: 35px;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 12px 0;
            transition: all 0.3s ease;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 40px;
            margin: 0;
            padding: 0;
        }

        .nav-menu a {
            text-decoration: none;
            color: #e0e0e0;
            font-weight: 500;
            font-size: 15px;
            transition: color 0.3s ease;
            position: relative;
        }

        .nav-menu a:hover,
        .nav-menu a.active {
            color: #ED5829;
        }

        .nav-menu a.active::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            right: 0;
            height: 2px;
            background: #ED5829;
        }

        .cart-icon {
            position: relative;
            width: 32px;
            height: 32px;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .cart-icon:hover {
            transform: scale(1.1);
        }

        .cart-count {
            position: absolute;
            top: -6px;
            right: -6px;
            background: #ED5829;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 600;
        }

        /* Main Content */
        .main-content {
            margin-top: 0px;
        }

        /* Hero Section */
        .hero-section {
            background: #0a0810;
            min-height: clamp(680px, 100vh, 1080px);
            position: relative;
            overflow: hidden;
            isolation: isolate;
            
            
        }

        .hero-section::after {
            content: '';
            position: absolute;
            left: -10%;
            right: -10%;
            bottom: -160px;
            height: 310px;
            border-radius: 50%;
            z-index: 4;
        }

        .merchant-hero {
            position: relative;
            min-height: inherit;
            display: flex;
            align-items: center;
            padding: clamp(120px, 12vw, 170px) clamp(18px, 5vw, 90px) clamp(100px, 10vw, 130px);
        }

        .merchant-hero__media,
        .merchant-hero__overlay {
            position: absolute;
            inset: 0;
            pointer-events: none;
        }

        .merchant-hero__media {
            background-image: url('{{ asset('assets/traderHero.webp') }}');
            background-size: cover;
            background-position: center right;
            z-index: 1;
        }

        .merchant-hero__overlay {
            z-index: 2;
            background:
                linear-gradient(94deg, rgba(0, 0, 0, 0.78) 0%, rgba(0, 0, 0, 0.6) 35%, rgba(0, 0, 0, 0.16) 70%, rgba(0, 0, 0, 0.1) 100%),
                linear-gradient(180deg, rgba(29, 15, 44, 0.1) 0%, rgba(0, 0, 0, 0.42) 100%);
        }

        .merchant-hero__overlay::before,
        .merchant-hero__overlay::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            filter: blur(170px);
        }

        .merchant-hero__overlay::before {
            width: 700px;
            height: 700px;
            right: -180px;
            top: -240px;
            background: rgba(55, 6, 101, 0.85);
        }

        .merchant-hero__overlay::after {
            width: 640px;
            height: 640px;
            left: -250px;
            top: 190px;
            background: rgba(245, 245, 245, 0.5);
            filter: blur(200px);
        }

        .merchant-hero__content {
            position: relative;
            z-index: 5;
            max-width: min(1044px, 76vw);
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .merchant-hero__title {
            font-family: 'Poppins', 'Segoe UI', sans-serif;
            font-size: clamp(40px, 5.1vw, 68px);
            line-height: 1.15;
            font-weight: 700;
            letter-spacing: 0;
            color: #ffffff;
            margin-bottom: clamp(22px, 3vw, 34px);
        }

        .merchant-hero__title span {
            color: #FFA006;
        }

        .merchant-hero__description {
            font-family:'Coco Sharp';
            font-size: clamp(20px, 2.15vw, 32px);
            line-height: 1.25;
            letter-spacing: 0.02em;
            color: rgba(255, 255, 255, 0.95);
            margin-bottom: clamp(24px, 3.2vw, 42px);
            max-width: 1044px;
        }

        .merchant-hero .cta-button {
            background: #FFA006;
            color: #E5E5E5;
            border: 0;
            border-radius: 20px;
            padding: 16px 40px;
            font-size: clamp(18px, 1.7vw, 24px);
            font-family: 'Proxima Nova', 'Segoe UI', sans-serif;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: 0.04em;
            text-decoration: underline;
            cursor: pointer;
            box-shadow: 0 0 21px rgba(226, 142, 8, 0.95);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .merchant-hero .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 26px rgba(226, 142, 8, 1);
        }

        .cta-button {
            background: #FFA006;
            color: white;
            border: none;
            padding: 18px 36px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            box-shadow: 0 4px 4px rgba(245, 159, 11, 0.2);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(245, 158, 11, 0.4);
            background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
        }

         .cta-button2 {
            background: var(--primary);
            color: white;
            border: none;
            padding: 18px 36px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            box-shadow: 0 4px 4px rgba(151, 11, 245, 0.2);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        

        .cta-button2:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 4px rgba(151, 11, 245, 0.2);
            background: linear-gradient(135deg, #7a06d9ff 0%, #ab0bf5ff 100%);
        }

        /* Vision Section */
        .vision-section {
            padding: 80px 0;
            background: rgba(20, 20, 20, 0.95);
        }

        .vision-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
        }

        .vision-text {
            text-align: center;
        }

        .vision-image {
            background: url('https://static.wixstatic.com/media/5904c2_5f838f055a2640aba66f77c54eba33d6~mv2.jpg/v1/fill/w_959,h_645,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/5904c2_5f838f055a2640aba66f77c54eba33d6~mv2.jpg');
            background-size: cover;
            background-position: center;
            height: 400px;
            border-radius: 10px;
        }

        .section-title {
            font-size: 38px;
            margin-bottom: 20px;
            color: #ffffff;
        }

        .section-description {
            font-size: 16px;
            line-height: 1.8;
            color: #c0c0c0;
        }

        /* Features Section */
        .features-section {
            position: relative;
            background: #f9fcfd;
            padding: 0 0 90px;
            overflow: hidden;
        }

        .features-shell {
            width: 80%;
            margin: 0 auto;
        }

        .cloud-divider {
            position: relative;
            margin-top: -80px;
            margin-bottom: -86px;
            z-index: 18;
            pointer-events: none;
        }

        .cloud-divider img {
            display: block;
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .features-wave-strip {
            position: relative;
            height: 72px;
            background: #f9fcfd;
            z-index: 1;
        }

        .features-wave-strip::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 24px 0, #e8ecef 22px, transparent 23px) 0 100%/48px 52px repeat-x;
        }

        .features-intro {
            margin-top: 24px;
            display: grid;
            grid-template-columns: 1fr;
            gap: 0;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .features-intro__media {
            position: relative;
            min-height: 560px;
            border-radius: 26px;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding: 0 22px 0;
        }

        .features-intro__media::before {
            content: '';
            position: absolute;
            width: min(95%, 540px);
            height: 285px;
            bottom: 26px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 180px 180px 28px 28px;
            background: rgba(164, 107, 193, 0);
            filter: blur(1px);
        }

        .features-intro__media::after {
            content: '';
            position: absolute;
            width: 76%;
            height: 74%;
            left: 12%;
            top: 8%;
            border-radius: 26px;
            z-index: 0;
        }

        .features-main-image {
            position: relative;
            z-index: 2;
            width: min(92%, 650px);
            height: auto;
            object-fit: contain;
        }

        .features-avatar {
            position: absolute;
            z-index: 3;
            width: 130px;
            height: 130px;
            border-radius: 50%;
            overflow: hidden;
            border: 7px solid rgba(255, 255, 255, 0.72);
            box-shadow: 3px 0 10px rgba(32, 145, 255, 0.13);
            background: rgba(255, 255, 255, 0.6);
        }

        .features-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .features-avatar--left {
            left: 16px;
            top: 234px;
            transform: rotate(-20deg);
        }

        .features-avatar--right {
            right: 24px;
            bottom: 26px;
            transform: rotate(15deg);
        }

        .features-notes-row {
            margin-top: 52px;
            margin-inline:10vh;
            display: grid;
            grid-template-columns: minmax(430px, 0.95fr) minmax(520px, 1fr);
            gap: clamp(24px, 3.5vw, 56px);
            align-items: start;
        }

        .features-notes-side {
            padding-top: 8px;
        }

        .features-heading {
            font-family: 'Manrope', 'Segoe UI', sans-serif;
            font-weight: 800;
            font-size: clamp(34px, 4vw, 52px);
            line-height: 1.1;
            letter-spacing: -1.5px;
            color: #000a2d;
            text-decoration: underline;
            margin-bottom: 18px;
            max-width: 480px;
        }

        .features-intro__description,
        .features-intro__secondary {
            font-family: 'Archivo', 'Segoe UI', sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: #636571;
            max-width: 450px;
        }

        .features-intro__description {
            margin-bottom: 14px;
        }

        .features-intro__secondary {
            margin-bottom: 28px;
        }

        .features-checklist {
            list-style: none;
            margin: 0 0 34px;
            padding: 0;
            display: grid;
            grid-template-columns: repeat(2, minmax(190px, 1fr));
            gap: 18px 14px;
            max-width: 620px;
        }

        .features-checklist li {
            font-family: 'Manrope', 'Segoe UI', sans-serif;
            font-size: 18px;
            line-height: 1.1;
            font-weight: 700;
            color: #000a2d;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .features-checklist li::before {
            content: "✓";
            font-size: 18px;
            line-height: 1;
            color: #a46bc1;
            font-weight: 700;
        }

        .features-actions {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }

        .features-btn {
            border-radius: 4px;
            border: 1px solid transparent;
            font-family: 'Manrope', 'Segoe UI', sans-serif;
            font-size: 15px;
            line-height: 24px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 59px;
            padding: 16px 34px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .features-btn:hover {
            transform: translateY(-2px);
        }

        .features-btn--primary {
            background: #a46bc1;
            color: #ffffff;
        }

        .features-btn--primary:hover {
            box-shadow: 0 10px 28px rgba(164, 107, 193, 0.32);
        }

        .features-btn--secondary {
            background: rgba(152, 179, 255, 0.07);
            border-color: rgba(152, 179, 255, 0.23);
            color: #a46bc1;
        }

        .features-btn--secondary:hover {
            box-shadow: 0 10px 24px rgba(117, 117, 117, 0.18);
        }

        .features-purple-notes { width: 100%; }

        .features-notes-stack {
            position: relative;
            width: 100%;
            /* padding gives room to show the back card peeking out */
            padding-top: 34px;
            padding-right: 24px;
        }

        /* Back card — lighter lavender, offset behind */
        .features-notes-stack .features-purple-notes:last-child {
            position: absolute;
            inset: 0;
            z-index: 1;
            transform: translate(70px, -5px);
        }

        .features-notes-stack .features-purple-notes:last-child .feature-note-list {
            background: #d4a8e8;
            box-shadow: none;
            height: 90%;
            width: 90%;
            color: transparent;
        }

        /* Front card — sits on top */
        .features-notes-stack .features-purple-notes:first-child {
            position: relative;
            z-index: 2;
        }

        .feature-note-list {
            list-style: disc;
            margin: 0;
            padding: 24px 26px 24px 36px;
            width: 100%;
            background: #a46bc1;
            border-radius: 10px;
            color: #ffffff;
            box-shadow: 0 14px 26px rgba(93, 62, 116, 0.2);
        }

        .feature-note-list li {
            font-family: 'Manrope', 'Segoe UI', sans-serif;
            font-size: 14px;
            line-height: 1.65;
        }

        .feature-note-list li + li {
            margin-top: 11px;
        }

        .features-showcase.features-showcase--figma {
            margin-top: 72px;
            background: #f9fcfd;
            border-radius: 0;
            padding: 24px 6px 20px;
            position: relative;
        }

        .features-showcase__heading {
            text-align: center;
            font-family: 'Manrope', 'Segoe UI', sans-serif;
            font-size: clamp(30px, 3.6vw, 40px);
            line-height: 1.18;
            font-size: 48px;
            color: #000;
            margin-bottom: 14px;
            font-weight: 700;
        }

        .features-showcase__subtitle {
            text-align: center;
            font-family: 'Roboto', 'Segoe UI', sans-serif;
            font-size: clamp(15px, 1.8vw, 21px);
            line-height: 1.3;
            color: #000;
            margin-bottom: 34px;
            opacity: 0.85;
        }

        .features-showcase--figma .services-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 26px;
            margin: 0 auto;
            max-width: 1586px;
            padding: 0;
        }

        .features-showcase--figma .service-card {
            background: #f2f2f2;
            border-radius: 0;
            overflow: hidden;
            transition: none;
            display: grid;
            grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr);
            align-items: stretch;
            min-height: 274px;
            width: 100%;
            box-shadow: none;
        }

        .features-showcase--figma .service-card:hover {
            transform: none;
        }

        .features-showcase--figma .service-content {
            padding: 36px 34px 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
             background: #ebe5f7ff;
        }

        .features-showcase--figma .service-title {
            font-family: 'Roboto', 'Segoe UI', sans-serif;
             font-size: clamp(28px, 2.1vw, 34px);
            line-height: 1.16;
            color: #a46bc1;
            margin-bottom: 12px;
            font-weight: 700;
        }

        .features-showcase--figma .service-icon {
            width: 30px;
            height: 30px;
            object-fit: contain;
            display: block;
            align-self: flex-start;
            margin-bottom: 10px;
        }

        .features-showcase--figma .service-description {
            font-family: 'Roboto', 'Segoe UI', sans-serif;
            font-size: 16px;
            line-height: 1.3;
            color: #000000;
            margin-bottom: 16px;
            text-align: left;
            max-width: 290px;
        }

        .features-showcase--figma .read-more {
            width: 114px;
            min-height: 30px;
            border-radius: 999px;
            background: #a46bc1;
            color: #ffffff;
            font-family: 'Nunito Sans', 'Segoe UI', sans-serif;
            font-size: 11px;
            line-height: 16px;
            text-decoration: underline;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            align-self: flex-start;
        }

        .features-showcase--figma .service-image {
            background: #ffffff;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            width:100%;
        }

        .features-showcase--figma .service-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            border-radius:20px;
        }

        html[dir="rtl"] .features-showcase--figma .service-icon,
        html[dir="rtl"] .features-showcase--figma .read-more {
            align-self: flex-end;
        }

        @media (max-width: 1200px) {
            .features-intro {
                grid-template-columns: 1fr;
                gap: 34px;
            }

            .features-intro__media {
                min-height: 500px;
                max-width: 760px;
                width: 100%;
                margin: 0 auto;
            }

            .features-notes-row {
                grid-template-columns: 1fr;
                gap: 28px;
                margin-inline: 10px;
            }

            .features-notes-side {
                max-width: 760px;
                margin: 0 auto;
            }

            .features-purple-notes {
                max-width: 760px;
                margin: 0 auto;
            }
        }

        @media (max-width: 992px) {
            .features-shell {
                width: min(1260px, calc(100% - 28px));
            }

            .features-notes-row {
                grid-template-columns: 1fr;
            }

            .features-showcase.features-showcase--figma {
                padding-inline: 18px;
            }

            .features-showcase--figma .services-grid {
                gap: 20px;
                padding: 0 10px;
            }

            .features-showcase--figma .service-title {
                font-size: 22px;
            }
        }

        @media (max-width: 768px) {
            .features-section {
                padding-bottom: 68px;
            }

            .features-wave-strip {
                height: 56px;
            }

            .features-wave-strip::before {
                background-size: 42px 48px;
            }

            .features-intro {
                margin-top: 12px;
            }

            .features-intro__media {
                min-height: 0;
                align-items: center;
                padding-inline: 0;
            }

            .features-notes-row {
                margin-top: 24px;
                gap: 50px;
            }

            .features-section .features-notes-row:first-child {
                margin-top: 8px;
            }

            .features-section .features-notes-row:first-child .features-intro__media {
                min-height: 240px;
                height: 260px;
                margin-top: 10px;
                border-radius: 18px;
            }

            .features-section .features-notes-row:first-child .features-main-image {
                width: 140%;
                height: 140%;
                object-fit: cover;
                object-position: center top;
            }

            .features-avatar {
                width: 92px;
                height: 92px;
                border-width: 5px;
            }

            .features-avatar--left {
                left: 8px;
                top: 190px;
            }

            .features-avatar--right {
                right: 12px;
                bottom: 18px;
            }

            .features-checklist {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .features-actions {
                gap: 12px;
            }

            .features-btn {
                width: 100%;
            }

            .feature-note-list {
                padding: 20px 18px 20px 28px;
            }

            .features-showcase.features-showcase--figma {
                margin-top: 52px;
                padding: 12px 0;
            }

            .features-showcase--figma .services-grid {
                grid-template-columns: 1fr;
                grid-template-rows: auto;
                max-width: 100%;
                padding: 0 5px;
            }

            .features-showcase--figma .service-card {
                width: 100%;
                grid-template-columns: 1fr;
                min-height: auto;
            }

            .features-showcase--figma .service-image {
                width: 100%;
                height: 220px;
                min-width: auto;
            }
              .features-notes-stack {

             padding-right: 0px;
         }
              .features-notes-stack .features-purple-notes:first-child {
             position: relative;
             z-index: 2;
             width:auto;
         }

              .features-notes-stack .features-purple-notes:last-child {
             position: absolute;
             inset: 0;
             z-index: 1;
             transform: translate(70px, -5px);
             visibility:hidden;
         }
        }

        @media (max-width: 480px) {
            .features-shell {
                width: min(1260px, calc(100% - 18px));
            }

            .features-intro__media {
                min-height: 360px;
            }

            .features-main-image {
                width: 96%;
            }

            .features-heading {
                font-size: clamp(30px, 8.8vw, 36px);
                letter-spacing: -1px;
            }

            .features-intro__description,
            .features-intro__secondary {
                font-size: 15px;
            }

            .features-checklist li {
                font-size: 17px;
            }

            .features-showcase__subtitle {
                margin-bottom: 24px;
            }

            .features-showcase--figma .service-content {
                padding: 24px 18px;
            }

            .features-showcase--figma .service-image {
                height: 180px;
            }

            .features-showcase--figma .service-description {
                font-size: 13px;
                line-height: 1.5;
            }
        }

        .read-more {
            color: #ED5829;
            text-decoration: none;
            font-weight: 500;
        }

        /* Dashboard Usage Section */
        .dashboard-section {
            padding: 0px 0px;
            background: #F9FCFD;
        }

        .dashboard-content {
            max-width: 1160px;
            margin: 0 0;
            
        }

        .usage-step {
            margin-bottom: 40px;
            padding: 30px;
            background: transparent;
            border-radius: 10px;
            
        }

        .step-title {
            font-size: 20px;
            margin-bottom: 15px;
            color: #1f1f1fea;
        }

        .step-content {
            font-size: 14px;
            line-height: 1.6;
            color: #3d3d3dff;
        }

        .step-list {
            list-style: none;
            margin: 15px 0;
        }

        .step-list li {
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
        }

        .step-list li:before {
            content: '•';
            position: absolute;
            left: 0;
        }

        /* Footer */
        .footer {
            background: rgba(112, 22, 163, 0.95);
            color: #e0e0e0;
            text-align: center;
            padding: 30px 0;
        }

        /* Responsive Design */
         @media (max-width: 1500px) {
              .features-shell {
                  width: min(1260px, calc(100% - 28px));
              }
          }
          
          @media (max-width: 1024px) {
              .hero-section {
                  min-height: 76vh;
              }

              .hero-section::after {
                  bottom: -185px;
                  height: 300px;
              }

              .merchant-hero {
                  padding: 110px 36px 96px;
              }

              .merchant-hero__content {
                  max-width: 88%;
              }

              .merchant-hero__media {
                  background-position: 66% center;
              }

              .merchant-hero__overlay {
                  background:
                      linear-gradient(100deg, rgba(0, 0, 0, 0.82) 0%, rgba(0, 0, 0, 0.62) 44%, rgba(0, 0, 0, 0.34) 100%),
                      linear-gradient(180deg, rgba(29, 15, 44, 0.14) 0%, rgba(0, 0, 0, 0.44) 100%);
              }

          }

         @media (max-width: 768px) {
            .merchant-hero__content {
            position: relative;
            z-index: 5;
            max-width: min(1044px, 76vw);
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-top:150px;
        }
             .hero-section {
                 min-height: 68vh;
             }
             
             .hero-section::after {
                 bottom: -155px;
                 height: 250px;
             }

             .merchant-hero {
                 padding: 92px 22px 82px;
                 align-items: flex-start;
             }

             .merchant-hero__content {
                 max-width: 100%;
             }

             .merchant-hero__media {
                 background-position: 73% center;
             }

             .merchant-hero__overlay {
                 background:
                     linear-gradient(102deg, rgba(0, 0, 0, 0.9) 0%, rgba(0, 0, 0, 0.74) 58%, rgba(0, 0, 0, 0.48) 100%),
                     linear-gradient(180deg, rgba(29, 15, 44, 0.22) 0%, rgba(0, 0, 0, 0.58) 100%);
             }

             .merchant-hero__overlay::before {
                 width: 520px;
                 height: 520px;
                 right: -200px;
                 top: -220px;
             }

             .merchant-hero__overlay::after {
                 width: 420px;
                 height: 420px;
                 left: -220px;
                 top: 180px;
                 filter: blur(160px);
             }

             .merchant-hero__description {
                 line-height: 1.35;
             }

             .merchant-hero .cta-button {
                 border-radius: 16px;
                 padding: 14px 30px;
             }

             .cloud-divider {
                 margin-top: -40px;
                 margin-bottom: -44px;
             }

             .cloud-divider img {
                 height: 130px;
             }

             .section-title {
                 font-size: 28px;
             }

             .nav-menu {
                 display: none;
             }

             .container {
                 padding: 0 15px;
             }

         }

         @media (max-width: 480px) {
             .hero-section {
                 min-height: 620px;
             }

             .hero-section::after {
                 bottom: -138px;
                 height: 212px;
             }

             .merchant-hero {
                 padding: 82px 14px 72px;
             }

             .merchant-hero__media {
                 background-position: 76% center;
             }

             .merchant-hero__overlay {
                 background:
                     linear-gradient(105deg, rgba(0, 0, 0, 0.94) 0%, rgba(0, 0, 0, 0.8) 60%, rgba(0, 0, 0, 0.54) 100%),
                     linear-gradient(180deg, rgba(29, 15, 44, 0.25) 0%, rgba(0, 0, 0, 0.62) 100%);
             }

             .merchant-hero__title {
                 font-size: clamp(33px, 11vw, 42px);
                 margin-bottom: 16px;
             }

             .merchant-hero__description {
                 font-size: clamp(16px, 4.8vw, 20px);
                 margin-bottom: 24px;
                 line-height: 1.38;
             }

             .merchant-hero .cta-button {
                 font-size: 18px;
                 padding: 12px 24px;
             }

             .features-notes-row {
                 margin-top: 18px;
             }

             .features-section .features-notes-row:first-child {
                 margin-top: 4px;
             }

             .features-section .features-notes-row:first-child .features-intro__media {
                 min-height: 210px;
                 height: 210px;
             }

             .cloud-divider {
                 margin-top: -26px;
                 margin-bottom: -26px;
             }

             .cloud-divider img {
                 height: 96px;
             }

         }

         .merchant-faq-section {
             background: #F9FCFD;
             padding: 0px 0 90px;
         }

         .merchant-faq-wrap {
             max-width: 980px;
             margin: 0 auto;
             padding: 0 20px;
         }

         .merchant-faq-title {
            font-size: clamp(24px, 5.5vw, 48px); 
            color:var(--primary); 
            background-color:var(--primary-light); 
            font-weight: 700;
             font-family:'Coco Sharp'; 
             line-height: 1.2;
             text-align: center;
             margin-bottom: 28px;
             padding:10px;
         }

         .faq-panel {
             border: 1px solid #e5e7eb;
             border-radius: 16px;
             background: #fff;
             transition: border-color .2s ease, box-shadow .2s ease;
             margin-bottom: 14px;
         }

         .faq-panel[open] {
             border-color: var(--primary);
             box-shadow: 0 10px 30px rgba(30, 37, 54, 0.06);
         }

         .faq-summary {
             list-style: none;
             cursor: pointer;
             display: flex;
             align-items: flex-start;
             justify-content: space-between;
             gap: 14px;
             padding: 18px 22px;
         }

         .faq-summary::-webkit-details-marker {
             display: none;
         }

         .faq-question {
             font-family: 'Manrope', 'Segoe UI', sans-serif;
             font-size: clamp(16px, 2vw, 20px);
             line-height: 1.45;
             color: #1e2536;
             font-weight: 700;
         }

         .faq-answer {
             padding: 0 22px 18px;
             color: #525866;
             font-size: 15px;
             line-height: 1.75;
         }

         .faq-icon {
             transition: transform .2s ease;
             width: 28px;
             height: 28px;
             border: 1px solid #e5e7eb;
             border-radius: 999px;
             display: inline-flex;
             align-items: center;
             justify-content: center;
             font-size: 20px;
             color: #1e2536;
             flex-shrink: 0;
         }

         .faq-panel[open] .faq-icon {
             transform: rotate(45deg);
         }
    </style>
</head>
<body>
   

    {{-- <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <nav>
                    <ul class="nav-menu">
                        <li><a href="#" class="active">About</a></li>
                        <li><a href="#">Book Online</a></li>
                    </ul>
                </nav>
                <div class="cart-icon">
                    <svg viewBox="5.7 0 105.5 126.1" width="30" height="30" fill="#333">
                        <path d="M99.8 28.4c0-1.2-0.9-2-2.1-2h-15c0 3.2 0 7.6 0 8.2 0 1.5-1.2 2.6-2.6 2.9 -1.5 0.3-2.9-0.9-3.2-2.3 0-0.3 0-0.3 0-0.6 0-0.9 0-4.7 0-8.2H40.1c0 3.2 0 7.3 0 8.2 0 1.5-1.2 2.9-2.6 2.9 -1.5 0-2.9-0.9-3.2-2.3 0-0.3 0-0.3 0-0.6 0-0.6 0-5 0-8.2h-15c-1.2 0-2 0.9-2 2L8.3 124c0 1.2 0.9 2.1 2.1 2.1h96.3c1.2 0 2.1-0.9 2.1-2.1L99.8 28.4z"/>
                        <path d="M59.1 5.9c-2.9 0-2 0-2.9 0 -2 0-4.4 0.6-6.4 1.5 -3.2 1.5-5.9 4.1-7.6 7.3 -0.9 1.8-1.5 3.5-1.8 5.6 0 0.9-0.3 1.5-0.3 2.3 0 1.2 0 2.1 0 3.2 0 1.5-1.2 2.9-2.6 2.9 -1.5 0-2.9-0.9-3.2-2.3 0-0.3 0-0.3 0-0.6 0-1.2 0-2.3 0-3.5 0-3.2 0.9-6.4 2-9.4 1.2-2.3 2.6-4.7 4.7-6.4 3.2-2.9 6.7-5 11.1-5.9C53.5 0.3 55 0 56.7 0c1.5 0 2.9 0 4.4 0 2.9 0 5.6 0.6 7.9 1.8 2.6 1.2 5 2.6 6.7 4.4 3.2 3.2 5.3 6.7 6.4 11.1 0.3 1.5 0.6 3.2 0.6 4.7 0 1.2 0 2.3 0 3.2 0 1.5-1.2 2.6-2.6 2.9s-2.9-0.9-3.2-2.3c0-0.3 0-0.3 0-0.6 0-1.2 0-2.6 0-3.8 0-2.3-0.6-4.4-1.8-6.4 -1.5-3.2-4.1-5.9-7.3-7.3 -1.8-0.9-3.5-1.8-5.9-1.8C61.1 5.9 59.1 5.9 59.1 5.9L59.1 5.9z"/>
                        <text x="58.5" y="77" dy=".35em" text-anchor="middle" fill="#ED5829" font-size="24">0</text>
                    </svg>
                </div>
            </div>
        </div>
    </header> --}}

    <!-- Main Content -->
    <main class="main-content">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="merchant-hero">
                <div class="merchant-hero__media" aria-hidden="true"></div>
                <div class="merchant-hero__overlay" aria-hidden="true"></div>
                <div class="merchant-hero__content">
                    <h1 class="merchant-hero__title">{{ __('messages.about_merchant_hero_title_prefix') }} <span>{{ __('messages.about_merchant_hero_title_highlight') }}</span></h1>
                    <p class="merchant-hero__description">
                        {{ __('messages.about_merchant_hero_description_line_1') }}
                        {{ __('messages.about_merchant_hero_description_line_2') }}
                    </p>
                    <button class="cta-button">{{ __('messages.about_merchant_hero_cta') }}</button>
                </div>
            </div>
        </section>

        <div class="cloud-divider" aria-hidden="true">
            <img src="{{ asset('assets/c.png') }}" alt="">
        </div>

        <!-- Features Section -->
        <section class="features-section">
            <div class="features-shell">
                <div class="features-notes-row">
                    <div class="features-intro__media">
                        <img class="features-main-image" src="{{ asset('assets/div.elementor-widget-wrap.webp') }}" alt="{{ __('messages.about_merchant_features_main_image_alt') }}">
                
                    </div>
                        <div class="features-notes-side">
                        <h2 class="features-heading">{{ __('messages.about_merchant_features_intro_heading') }}</h2>
                        <p class="features-intro__description">
                            {{ __('messages.about_merchant_features_intro_description_line_1') }}

                        {{ __('messages.about_merchant_features_intro_description_line_2') }}
                        </p>
                       

                        <ul class="features-checklist">
                            <li>{{ __('messages.about_merchant_features_checklist_item_1') }}</li>
                            <li>{{ __('messages.about_merchant_features_checklist_item_2') }}</li>
                            <li>{{ __('messages.about_merchant_features_checklist_item_3') }}</li>
                            <li>{{ __('messages.about_merchant_features_checklist_item_4') }}</li>
                        </ul>

                        <div class="features-actions">
                            <a class="features-btn features-btn--primary" href="#dashboard-section">{{ __('messages.about_merchant_features_learn_more') }}</a>
                            <a class="features-btn features-btn--secondary" href="/register/merchant">{{ __('messages.about_merchant_features_create_one_now') }}</a>
                        </div>
                    </div>
                    
                </div>

                <div class="features-notes-row">
                    <div class="features-notes-side">
                        <h2 class="features-heading">{{ __('messages.about_merchant_features_get_heading') }}</h2>
                        <p class="features-intro__description">
                            {{ __('messages.about_merchant_features_get_description_line_1') }}
                        </p>
                        <p class="features-intro__secondary">
                        {{ __('messages.about_merchant_features_get_secondary') }}
                        </p>

                        <ul class="features-checklist">
                            <li>{{ __('messages.about_merchant_features_checklist_item_1') }}</li>
                            <li>{{ __('messages.about_merchant_features_checklist_item_2') }}</li>
                            <li>{{ __('messages.about_merchant_features_checklist_item_3') }}</li>
                            <li>{{ __('messages.about_merchant_features_checklist_item_4') }}</li>
                        </ul>

                        <div class="features-actions">
                            <a class="features-btn features-btn--primary" href="#dashboard-section">{{ __('messages.about_merchant_features_learn_more') }}</a>
                            <a class="features-btn features-btn--secondary" href="/register/merchant">{{ __('messages.about_merchant_features_create_one_now') }}</a>
                        </div>
                    </div>
                    <div class="features-notes-stack">
                        <div class="features-purple-notes">
                            <ul class="feature-note-list">
                                <li>{{ __('messages.about_merchant_features_note_1') }}</li>
                                <li>{{ __('messages.about_merchant_features_note_2') }}</li>
                                <li>{{ __('messages.about_merchant_features_note_3') }}</li>
                                <li>{{ __('messages.about_merchant_features_note_4') }}</li>
                                <li>{{ __('messages.about_merchant_features_note_5') }}</li>
                            </ul>
                        </div>

                        <div class="features-purple-notes">
                            <ul class="feature-note-list text-transparent">
                                <li>{{ __('messages.about_merchant_features_note_1') }}</li>
                                <li>{{ __('messages.about_merchant_features_note_2') }}</li>
                                <li>{{ __('messages.about_merchant_features_note_3') }}</li>
                                <li>{{ __('messages.about_merchant_features_note_4') }}</li>
                                <li>{{ __('messages.about_merchant_features_note_5') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="features-showcase features-showcase--figma">
                    <div class="services-grid">
                        <div class="service-card">
                            <div class="service-content">
                                <h4 class="service-title">{{ __('messages.about_merchant_features_showcase_dashboard_title') }}</h4>
                                   <img class="service-icon" src="{{ asset('assets/dashboardicon.png') }}" alt="{{ __('messages.about_merchant_features_showcase_dashboard_icon_alt') }}">

                                <p class="service-description">
                                    {{ __('messages.about_merchant_features_showcase_dashboard_description_line_1') }}
                                    {{ __('messages.about_merchant_features_showcase_dashboard_description_line_2') }}
                                </p>
                                <a class="read-more" href="#dashboard-section">{{ __('messages.about_merchant_features_read_more') }}</a>
                            </div>
                            <div class="service-image">
                                <img src="{{ asset('assets/dashboardfeature.webp') }}" alt="{{ __('messages.about_merchant_features_showcase_dashboard_image_alt') }}">
                            </div>
                        </div>

                        <div class="service-card">
                            <div class="service-content">
                                <h4 class="service-title">{{ __('messages.about_merchant_features_showcase_delivery_title') }}</h4>
                                    <img class="service-icon" src="{{ asset('assets/delivery-truckicon.png') }}" alt="{{ __('messages.about_merchant_features_showcase_delivery_icon_alt') }}">

                                <p class="service-description">
                                    {{ __('messages.about_merchant_features_showcase_delivery_description_line_1') }} <br>{{ __('messages.about_merchant_features_showcase_delivery_description_line_2') }}
                                </p>
                                <a class="read-more" href="#ministore-usage-section">{{ __('messages.about_merchant_features_read_more') }}</a>
                            </div>
                            <div class="service-image">
                                <img src="{{ asset('assets/deliveryfeature.jpg') }}" alt="{{ __('messages.about_merchant_features_showcase_delivery_image_alt') }}">
                            </div>
                        </div>

                        <div class="service-card">
                            <div class="service-content">
                                <h4 class="service-title">{{ __('messages.about_merchant_features_showcase_deals_title') }}</h4>
                           <img class="service-icon" src="{{ asset('assets/discounticon.png') }}" alt="{{ __('messages.about_merchant_features_showcase_deals_icon_alt') }}">

                                <p class="service-description">
                                    {{ __('messages.about_merchant_features_showcase_deals_description_line_1') }} <br>{{ __('messages.about_merchant_features_showcase_deals_description_line_2') }}
                                </p>
                                <a class="read-more" href="#deals-section">{{ __('messages.about_merchant_features_read_more') }}</a>
                            </div>
                            <div class="service-image">
                                <img src="{{ asset('assets/dealfeature.webp') }}" alt="{{ __('messages.about_merchant_features_showcase_deals_image_alt') }}">
                            </div>
                        </div>

                        <div class="service-card">
                            <div class="service-content">
                                <h4 class="service-title">{{ __('messages.about_merchant_features_showcase_ministore_title') }}</h4>
                                   <img class="service-icon" src="{{ asset('assets/shopicon.png') }}" alt="{{ __('messages.about_merchant_features_showcase_ministore_icon_alt') }}">

                                <p class="service-description">
                                    {{ __('messages.about_merchant_features_showcase_ministore_description_line_1') }} <br>{{ __('messages.about_merchant_features_showcase_ministore_description_line_2') }}
                                </p>
                                <a class="read-more" href="#ministore-usage-section">{{ __('messages.about_merchant_features_read_more') }}</a>
                            </div>
                            <div class="service-image">
                                <img src="{{ asset('assets/ministorefeature.avif') }}" alt="{{ __('messages.about_merchant_features_showcase_ministore_image_alt') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="interface-section">
            <div class="interface-container">
                <div class="interface-grid">
                    <div class="interface-copy">
                        <p class="interface-label">{{ __('messages.about_merchant_interface_label') }}</p>
                        <h2 class="interface-title">{!! __('messages.about_merchant_interface_title') !!}</h2>
                        <p class="interface-description">
                            {{ __('messages.about_merchant_interface_description') }}
                        </p>

                        <div class="interface-features">
                            <div class="interface-feature-item">
                                <div class="interface-feature-icon-wrap">
                                    <span class="material-symbols-outlined">local_shipping</span>
                                </div>
                                <div>
                                    <h4 class="interface-feature-title">{{ __('messages.about_merchant_interface_feature_1_title') }}</h4>
                                    <p class="interface-feature-text">{{ __('messages.about_merchant_interface_feature_1_text') }}</p>
                                </div>
                            </div>
                            <div class="interface-feature-item">
                                <div class="interface-feature-icon-wrap">
                                    <span class="material-symbols-outlined">touch_app</span>
                                </div>
                                <div>
                                    <h4 class="interface-feature-title">{{ __('messages.about_merchant_interface_feature_2_title') }}</h4>
                                    <p class="interface-feature-text">{{ __('messages.about_merchant_interface_feature_2_text') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="interface-mock-col">
                        <div class="interface-mock-frame">
                            <div class="interface-mock-image-wrap">
                                <img class="interface-mock-image" src="{{ asset('assets/traderDashboard.png') }}" alt="{{ __('messages.about_merchant_interface_mock_image_alt') }}">
                            </div>
                        </div>

                        <div class="interface-floating-card">
                            <div class="interface-floating-head">
                                <span class="interface-pulse-dot"></span>
                                <span class="interface-floating-label">{{ __('messages.about_merchant_interface_floating_label') }}</span>
                            </div>
                            <p class="interface-floating-value">+18.4%</p>
                            <p class="interface-floating-text">{{ __('messages.about_merchant_interface_floating_text') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Deals Section -->
        <section class="parallax-deals-section">
            <div class="container">
                <div id="deals-section" class="parallax-deals-card" style="--deals-bg: url('/assets/traderDeal.png');">
                    <div class="parallax-deals-bg" aria-hidden="true"></div>
                    <div class="parallax-deals-overlay" aria-hidden="true"></div>

                    <div class="parallax-deals-content">
                        <span class="parallax-deals-eyebrow">{{ __('messages.about_merchant_deals_eyebrow') }}</span>
                        <h3 class="parallax-deals-title">{{ __('messages.about_merchant_deals_title') }}</h3>
                        <h4 class="parallax-deals-subtitle">{{ __('messages.about_merchant_deals_subtitle') }}</h4>
                        <p class="parallax-deals-text">
                            {{ __('messages.about_merchant_deals_text') }}
                        </p>
                        <a href="/merchant/deals"><button class="parallax-deals-btn">{{ __('messages.about_merchant_deals_cta') }}</button></a>
                    </div>
                </div>
            </div>
        </section>


        <section class="dashboard-section" id="dashboard-section">
            <div class="container">
                <div class="dashboard-content">
                    <h2 style="text-align: center; padding:10px; margin-bottom:20px; font-family:Coco Sharp; font-size: clamp(24px, 5.5vw, 48px);  color:var(--primary); background-color:var(--primary-light);">{{ __('messages.about_merchant_dashboard_usage_title') }}</h2>
                    
                    <div class="usage-step" style="display: flex; align-items: flex-start; flex-direction: column;  gap: clamp(12px, 4vw, 40px);">
                        <!-- Video Box -->
                        <div class="video-container" style="flex: 1 1 600px; min-width: 280px; max-width: 100%; position: relative;  border-radius: 10px; overflow: hidden;">
                            <div style="aspect-ratio: 16/9; position: relative;">
                                <video 
                                    style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;" 
                                    controls 
                                    muted 
                                    loop
                                    preload="metadata"
                                    {{-- poster="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 225'%3E%3Crect width='400' height='225' fill='%23667eea'/%3E%3Ctext x='200' y='112' text-anchor='middle' fill='white' font-size='16' font-family='Arial'%3EProduct Tutorial Video%3C/text%3E%3C/svg%3E" --}}
                                >
                                    {{-- <source src="https://firebasestorage.googleapis.com/v0/b/dala3chic-e2b81.firebasestorage.app/o/product%20merchant%20usage.mov?alt=media&token=d174b7d9-f59c-4987-86f3-806d77cc2882" type="video/quicktime"> --}}
                                    <source src="https://firebasestorage.googleapis.com/v0/b/dala3chic-e2b81.firebasestorage.app/o/product%20merchant%20usage.mov?alt=media&token=d174b7d9-f59c-4987-86f3-806d77cc2882" type="video/mp4">
                                    <div style="display: flex; align-items: center; justify-content: center; height: 100%; background: rgba(255, 255, 255, 0.1); color: white; text-align: center; padding: 20px;">
                                        <div>
                                            <div style="width: 80px; height: 80px; background: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; backdrop-filter: blur(1px);">
                                                <svg width="32" height="32" viewBox="0 0 24 24" fill="white">
                                                    <path d="M8 5v14l11-7z"/>
                                                </svg>
                                            </div>
                                            <p style="margin: 0; font-weight: 600; color:black;">{{ __('messages.about_merchant_dashboard_step_1_overlay_title') }}</p>
                                            <p style="margin: 5px 0 0; font-size: 14px; opacity: 0.8; color:black;">{{ __('messages.about_merchant_dashboard_video_loading') }}</p>
                                        </div>
                                    </div>
                                </video>
                                <div style="position: absolute; bottom: 20px; left: 20px; color: white; font-weight: 600; font-size: 18px; text-shadow: 2px 2px 4px rgba(0,0,0,0.7); z-index: 10;">{{ __('messages.about_merchant_dashboard_step_1_overlay_title') }}</div>
                            </div>
                        </div>
                        <!-- Content -->
                        <div style="flex: 1;">
                            <h3 class="step-title">{{ __('messages.about_merchant_dashboard_step_1_title') }}</h3>
                            <div class="step-content">
                                <p>{{ __('messages.about_merchant_dashboard_step_1_intro_1') }}</p>
                                <p>{{ __('messages.about_merchant_dashboard_step_1_intro_2') }}</p>
                                <ul class="step-list">
                                    <li>{{ __('messages.about_merchant_dashboard_step_1_item_1') }}</li>
                                    <li>{{ __('messages.about_merchant_dashboard_step_1_item_2') }}</li>
                                    <li>{{ __('messages.about_merchant_dashboard_step_1_item_3') }}</li>
                                    <li>{{ __('messages.about_merchant_dashboard_step_1_item_4') }}</li>
                                    <li>{{ __('messages.about_merchant_dashboard_step_1_item_5') }}</li>
                                    <li>{{ __('messages.about_merchant_dashboard_step_1_item_6') }}</li>
                                </ul>
                                <p>{{ __('messages.about_merchant_dashboard_step_1_colors_intro') }}</p>
                                <ul class="step-list">
                                    <li>{{ __('messages.about_merchant_dashboard_step_1_colors_item_1') }}</li>
                                    <li>{{ __('messages.about_merchant_dashboard_step_1_colors_item_2') }}</li>
                                    <li>{{ __('messages.about_merchant_dashboard_step_1_colors_item_3') }}</li>
                                    <li>{{ __('messages.about_merchant_dashboard_step_1_colors_item_4') }}</li>
                                    <li>{{ __('messages.about_merchant_dashboard_step_1_colors_item_5') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="usage-step" style="display: flex; align-items: flex-start; flex-wrap: wrap; gap: clamp(12px, 4vw, 40px); margin-bottom: clamp(24px, 8vw, 60px);">
                        <div class="video-container" style="flex: 1 1 600px; min-width: 280px; max-width: 100%; position: relative;">
                            <div style="aspect-ratio: 16/9; position: relative;">
                                <video 
                                    style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;" 
                                    controls 
                                    muted 
                                    loop
                                    preload="metadata"
                                    {{-- poster="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 225'%3E%3Crect width='400' height='225' fill='%23f093fb'/%3E%3Ctext x='200' y='112' text-anchor='middle' fill='white' font-size='16' font-family='Arial'%3EService Tutorial Video%3C/text%3E%3C/svg%3E" --}}
                                >
                                    {{-- <source src="https://firebasestorage.googleapis.com/v0/b/dala3chic-e2b81.firebasestorage.app/o/service%20merchant%20tutor.mov?alt=media&token=d174b7d9-f59c-4987-86f3-806d77cc2882" type="video/quicktime"> --}}
                                    <source src="https://firebasestorage.googleapis.com/v0/b/dala3chic-e2b81.firebasestorage.app/o/service%20merchant%20tutor.mov?alt=media&token=d174b7d9-f59c-4987-86f3-806d77cc2882" type="video/mp4">
                                    <div style="display: flex; align-items: center; justify-content: center; height: 100%; background: rgba(255, 255, 255, 0.1); color: white; text-align: center; padding: 20px;">
                                        <div>
                                            <div style="width: 80px; height: 80px; background: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; backdrop-filter: blur(10px);">
                                                <svg width="32" height="32" viewBox="0 0 24 24" fill="white">
                                                    <path d="M8 5v14l11-7z"/>
                                                </svg>
                                            </div>
                                            <p style="margin: 0; font-weight: 600;">Service Creation Tutorial</p>
                                            <p style="margin: 5px 0 0; font-size: 14px; opacity: 0.8;">Video loading...</p>
                                        </div>
                                    </div>
                                </video>
                                <div style="position: absolute; bottom: 20px; left: 20px; color: white; font-weight: 600; font-size: 18px; text-shadow: 2px 2px 4px rgba(0,0,0,0.7); z-index: 10;">Service Creation Tutorial</div>
                            </div>
                        </div>
                        <div style="flex: 1;">
                            <h3 class="step-title">2 / Service creation</h3>
                            <div class="step-content">
                                <p>From dashboard's sidebar click on services tap then click on "Add new service" button.</p>
                                <p>Then enter the following service information:</p>
                                <ul class="step-list">
                                    <li>Name (English & Arabic)</li>
                                    <li>Category</li>
                                    <li>Price</li>
                                    <li>Duration (should be in minutes)</li>
                                    <li>Image select a clear one that follow our rules and recommendations</li>
                                    <li>Available (on default true)</li>
                                    <li>Home Service (on default false check it to make the service available at customer home)</li>
                                    <li>Description optional but if you enter it you should write both Arabic and English versions</li>
                                </ul>
                            </div>
                        </div>
                    </div> -->

                    <div class="usage-step" style="display: flex; align-items: flex-start; flex-direction: column; gap: clamp(12px, 4vw, 40px);">
                        <!-- Video Box -->
                        <div class="video-container" style="flex: 1 1 600px; min-width: 280px; max-width: 100%; position: relative;">
                            <div style="aspect-ratio: 16/9; position: relative;">
                                <video 
                                    style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;" 
                                    controls 
                                    muted 
                                    loop
                                    preload="metadata"
                                    {{-- poster="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 225'%3E%3Crect width='400' height='225' fill='%23667eea'/%3E%3Ctext x='200' y='112' text-anchor='middle' fill='white' font-size='16' font-family='Arial'%3EDeal Tutorial Video%3C/text%3E%3C/svg%3E" --}}
                                >
                                    {{-- <source src="https://firebasestorage.googleapis.com/v0/b/dala3chic-e2b81.firebasestorage.app/o/deal%20merchant%20tutor.mov?alt=media&token=d174b7d9-f59c-4987-86f3-806d77cc2882" type="video/quicktime"> --}}
                                    <source src="https://firebasestorage.googleapis.com/v0/b/dala3chic-e2b81.firebasestorage.app/o/deal%20merchant%20tutor.mov?alt=media&token=d174b7d9-f59c-4987-86f3-806d77cc2882" type="video/mp4">
                                    <div style="display: flex; align-items: center; justify-content: center; height: 100%; background: rgba(255, 255, 255, 0.1); color: white; text-align: center; padding: 20px;">
                                        <div>
                                            <div style="width: 80px; height: 80px; background: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; backdrop-filter: blur(10px);">
                                                <svg width="32" height="32" viewBox="0 0 24 24" fill="white">
                                                    <path d="M8 5v14l11-7z"/>
                                                </svg>
                                            </div>
                                            <p style="margin: 0; font-weight: 600;">{{ __('messages.about_merchant_dashboard_step_2_overlay_title') }}</p>
                                            <p style="margin: 5px 0 0; font-size: 14px; opacity: 0.8;">{{ __('messages.about_merchant_dashboard_video_loading') }}</p>
                                        </div>
                                    </div>
                                </video>
                                <div style="position: absolute; bottom: 20px; left: 20px; color: white; font-weight: 600; font-size: 18px; text-shadow: 2px 2px 4px rgba(0,0,0,0.7); z-index: 10;">{{ __('messages.about_merchant_dashboard_step_2_overlay_title') }}</div>
                            </div>
                        </div>
                        <!-- Content -->
                        <div style="flex: 1;">
                            <h3 class="step-title">{{ __('messages.about_merchant_dashboard_step_2_title') }}</h3>
                            <div class="step-content">
                                <p>{{ __('messages.about_merchant_dashboard_step_2_intro_1') }}</p>
                                <p>{{ __('messages.about_merchant_dashboard_step_2_intro_2') }}</p>
                                <ul class="step-list">
                                    <li>{{ __('messages.about_merchant_dashboard_step_2_item_1') }}</li>
                                    <li>{{ __('messages.about_merchant_dashboard_step_2_item_2') }}</li>
                                    <li>{{ __('messages.about_merchant_dashboard_step_2_item_3') }}</li>
                                    <li>{{ __('messages.about_merchant_dashboard_step_2_item_4') }}</li>
                                    <li>{{ __('messages.about_merchant_dashboard_step_2_item_5') }}</li>
                                    <li>{{ __('messages.about_merchant_dashboard_step_2_item_6') }}</li>
                                    <li>{{ __('messages.about_merchant_dashboard_step_2_item_7') }}</li>
                                    <li>{{ __('messages.about_merchant_dashboard_step_2_item_8') }}</li>
                                    <li>{{ __('messages.about_merchant_dashboard_step_2_item_9') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mini Store Usage Section -->
        <section id="ministore-usage-section" class="ministore-section" style="background: #F9FCFD; padding: clamp(40px, 6vw, 80px) 0; position: relative; overflow: hidden;">
            <!-- Background Pattern -->
            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: radial-gradient(circle at 25% 25%, rgba(255,255,255,0.1) 0%, transparent 50%), radial-gradient(circle at 75% 75%, rgba(255,255,255,0.05) 0%, transparent 50%); pointer-events: none;"></div>
            
            <div class="container" style="position: relative; z-index: 2;">
                <div class="ministore-content">
                    <!-- Section Header -->
                    <div style="text-align: center; margin-bottom: 32px;">
                        <h2 style="font-family:Coco Sharp; font-size: clamp(24px, 5.5vw, 48px);  color:var(--primary); background-color:var(--primary-light); font-weight: 700; padding:10px; margin-bottom:20px;">{{ __('messages.about_merchant_ministore_title') }}</h2>
                        <p style="color:  rgba(0, 0, 0, 0.81); font-size: clamp(16px, 3vw, 20px); max-width: 600px; margin: 0 auto; line-height: 1.6;">{{ __('messages.about_merchant_ministore_subtitle') }}</p>
                        
                    </div>

                    <!-- Main Content -->   
                    <div style="display: flex; align-items: center; flex-wrap: wrap;">
                        <!-- Image Container -->
                        <div style="flex: 1 1 1200px; min-width: 300px; position: relative;">
                            <div style="position: relative; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.3); transform: perspective(1000px) rotateY(-5deg); transition: transform 0.3s ease;">
                                <img 
                                    src="{{ asset('assets/ministoreusage.webp') }}" 
                                    alt="{{ __('messages.about_merchant_ministore_image_alt') }}" 
                                    style="width: 100%; height: auto; display: block; border-radius: 20px;"
                                    onmouseover="this.parentElement.style.transform='perspective(1000px) rotateY(0deg) scale(1.02)'"
                                    onmouseout="this.parentElement.style.transform='perspective(1000px) rotateY(-5deg) scale(1)'"
                                >
                                <!-- Overlay Gradient -->
                                <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 100px; background: linear-gradient(transparent, rgba(0,0,0,0.7)); border-radius: 0 0 20px 20px;"></div>
                                <!-- Image Label -->
                                <div style="position: absolute; bottom: 20px; left: 20px; color: white; font-weight: 600; font-size: clamp(16px, 3vw, 20px); text-shadow: 2px 2px 4px rgba(0,0,0,0.8);">{{ __('messages.about_merchant_ministore_image_label') }}</div>
                            </div>
                            <!-- Floating Elements -->
                            <div style="position: absolute; top: -10px; right: -10px; width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 50%; backdrop-filter: blur(10px); display: flex; align-items: center; justify-content: center;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </div>
                        </div>
                         
                        <!-- Content Container -->
                        <div style="flex: 1 1 1000px; min-width: 300px;">
                            <div style="background: rgba(255, 255, 255, 0);  border-radius: 20px; padding: clamp(60px, 10vw, 80px); border: 1px solid rgba(255,255,255,0.2);">
                                <h3 style="color:  rgba(0, 0, 0, 0.81); font-size: clamp(22px, 4vw, 28px); font-weight: 600; margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
                                    <span style="width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill=" rgba(0, 0, 0, 0.81);">
                                            <path d="M19 7h-3V6a4 4 0 0 0-8 0v1H5a1 1 0 0 0-1 1v11a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V8a1 1 0 0 0-1-1zM10 6a2 2 0 0 1 4 0v1h-4V6zm8 13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V9h2v1a1 1 0 0 0 2 0V9h4v1a1 1 0 0 0 2 0V9h2v10z"/>
                                        </svg>
                                    </span>
                                    {{ __('messages.about_merchant_ministore_manage_title') }}
                                </h3>
                                
                                <div style="space-y: 16px;">
                                    <div style="margin-bottom: 16px;">
                                        <h4 style="color:  rgba(0, 0, 0, 0.81);; font-size: clamp(16px, 3vw, 18px); font-weight: 600; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                                            <span style="width: 6px; height: 6px; background: var(--primary); border-radius: 50%;"></span>
                                            {{ __('messages.about_merchant_ministore_usage_heading') }}
                                        </h4>
                                        <p style="color:  rgba(0, 0, 0, 0.81); font-size: clamp(14px, 2.5vw, 16px); line-height: 1.6; margin-left: 14px;">{{ __('messages.about_merchant_ministore_usage_step_1') }}</p>
                                                                                <p style="color:  rgba(0, 0, 0, 0.81); font-size: clamp(14px, 2.5vw, 16px); line-height: 1.6; margin-left: 14px;">{{ __('messages.about_merchant_ministore_usage_step_2') }}</p>
                                        <p style="color:  rgba(0, 0, 0, 0.81); font-size: clamp(14px, 2.5vw, 16px); line-height: 1.6; margin-left: 14px;">{{ __('messages.about_merchant_ministore_usage_step_3') }}</p>
                                        <p style="color:  rgba(0, 0, 0, 0.81); font-size: clamp(14px, 2.5vw, 16px); line-height: 1.6; margin-left: 14px;">{{ __('messages.about_merchant_ministore_usage_step_4') }}</p>
                                        <p style="color:  rgba(0, 0, 0, 0.81); font-size: clamp(14px, 2.5vw, 16px); line-height: 1.6; margin-left: 14px;">{{ __('messages.about_merchant_ministore_usage_step_5') }}</p>

                                    </div>
                                    
                                    
                     
                                    
                                    <div style="margin-bottom: 16px;">
                                        <h4 style="color:  rgba(0, 0, 0, 0.81);; font-size: clamp(16px, 3vw, 18px); font-weight: 600; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                                            <span style="width: 6px; height: 6px; background: #8b5cf6; border-radius: 50%;"></span>
                                            {{ __('messages.about_merchant_ministore_change_heading') }}
                                        </h4>
                                        <p style="color:  rgba(0, 0, 0, 0.81); font-size: clamp(14px, 2.5vw, 16px); line-height: 1.6; margin-left: 14px;">{{ __('messages.about_merchant_ministore_change_text') }}</p>
                                    </div>
                                </div>

                                <!-- CTA Button -->
                                <div style="margin-top: 30px;">
                                  <a href="merchant/mini-store">  <button  style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); color: white; border: none; padding: 14px 28px; border-radius: 12px; font-weight: 600; font-size: clamp(14px, 2.5vw, 16px); cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(251, 191, 36, 0.4); width: 100%;" 
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(251, 191, 36, 0.6)'"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(251, 191, 36, 0.4)'">
                                        {{ __('messages.about_merchant_ministore_cta') }}
                                    </button></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Responsive Styles -->
            <style>
                /* Tablet Styles */
                @media (max-width: 1024px) {
                    .ministore-section {
                        padding: 0 0 !important;
                    }
                    .ministore-section .container > div > div:nth-child(2) > div:first-child {
                        flex: 1 1 600px !important;
                        min-width: 400px !important;
                    }
                    .ministore-section .container > div > div:nth-child(2) > div:last-child {
                        flex: 1 1 500px !important;
                        min-width: 400px !important;
                    }
                }

                /* Mobile Landscape */
                @media (max-width: 768px) {
                    .ministore-section {
                        padding: clamp(40px, 8vw, 60px) 0 !important;
                    }
                    .ministore-section .container > div > div:first-child {
                        margin-bottom: clamp(0px, 8vw, 60px) !important;
                    }
                    .ministore-section .container > div > div:nth-child(2) {
                        flex-direction: column !important;
                        gap: clamp(30px, 6vw, 40px) !important;
                    }
                    .ministore-section .container > div > div:nth-child(2) > div:first-child {
                        flex: 1 1 auto !important;
                        min-width: 100% !important;
                        order: 2;
                    }
                    .ministore-section .container > div > div:nth-child(2) > div:last-child {
                        flex: 1 1 auto !important;
                        min-width: 100% !important;
                        order: 1;
                    }
                    .ministore-section .container > div > div:nth-child(2) > div:last-child > div {
                        padding: clamp(30px, 6vw, 40px) !important;
                    }
                    .ministore-section img {
                        transform: none !important;
                    }
                    .ministore-section img:hover {
                        transform: scale(1.02) !important;
                    }
                }
                
                /* Mobile Portrait */
                @media (max-width: 480px) {
                    .ministore-section {
                        padding: clamp(30px, 6vw, 40px) 0 !important;
                    }
                    .ministore-section .container {
                        padding: 0 15px !important;
                    }
                    .ministore-section .container > div > div:first-child {
                        margin-bottom: clamp(30px, 6vw, 40px) !important;
                    }
                    .ministore-section .container > div > div:nth-child(2) {
                        gap: clamp(20px, 4vw, 30px) !important;
                    }
                    .ministore-section .container > div > div:nth-child(2) > div:last-child > div {
                        padding: clamp(20px, 4vw, 30px) !important;
                        border-radius: 15px !important;
                    }
                    .ministore-section h2 {
                        font-size: clamp(24px, 5vw, 32px) !important;
                    }
                    .ministore-section h3 {
                        font-size: clamp(18px, 4vw, 22px) !important;
                    }
                    .ministore-section h4 {
                        font-size: clamp(14px, 3vw, 16px) !important;
                    }
                    .ministore-section p {
                        font-size: clamp(12px, 2.5vw, 14px) !important;
                    }
                    /* Hide floating elements on mobile */
                    .ministore-section .container > div > div:nth-child(2) > div:first-child > div > div:last-child {
                        display: none !important;
                    }
                    /* Simplify image container */
                    .ministore-section .container > div > div:nth-child(2) > div:first-child > div {
                        border-radius: 15px !important;
                        box-shadow: 0 10px 20px rgba(0,0,0,0.3) !important;
                    }
                }

                /* Extra Small Mobile */
                @media (max-width: 360px) {
                    .ministore-section {
                        padding: 20px 0 !important;
                    }
                    .ministore-section .container {
                        padding: 0 10px !important;
                    }
                    .ministore-section .container > div > div:first-child {
                        margin-bottom: 20px !important;
                    }
                    .ministore-section .container > div > div:nth-child(2) {
                        gap: 15px !important;
                    }
                    .ministore-section .container > div > div:nth-child(2) > div:last-child > div {
                        padding: 15px !important;
                    }
                    .ministore-section h2 {
                        font-size: 20px !important;
                    }
                    .ministore-section h3 {
                        font-size: 16px !important;
                    }
                    .ministore-section h4 {
                        font-size: 13px !important;
                    }
                    .ministore-section p {
                        font-size: 11px !important;
                    }
                }
            </style>
        </section>

        <section class="merchant-faq-section" id="merchant-faq-section">
            <div class="merchant-faq-wrap">
                <h2 class="merchant-faq-title">{{ __('messages.about_merchant_faq_title') }}</h2>
                <div class="faq-panel" style="padding: 22px;">
                    <h3 class="faq-question">{{ __('messages.about_merchant_faq_q1') }}</h3>
                    <p class="faq-answer" style="padding: 12px 0 0;">
                        {{ __('messages.about_merchant_faq_a1_intro') }}
                    </p>
                    <p class="faq-answer" style="padding: 0;">{{ __('messages.about_merchant_faq_a1_lead') }}</p>
                    <p class="faq-answer" style="padding: 0;">- {{ __('messages.about_merchant_faq_a1_item_1') }}</p>
                    <p class="faq-answer" style="padding: 0;">- {{ __('messages.about_merchant_faq_a1_item_2') }}</p>
                    <p class="faq-answer" style="padding: 0;">- {{ __('messages.about_merchant_faq_a1_item_3') }}</p>
                    <p class="faq-answer" style="padding: 0;">- {{ __('messages.about_merchant_faq_a1_item_4') }}</p>
                    <p class="faq-answer" style="padding: 0;">{{ __('messages.about_merchant_faq_a1_outro') }}</p>
                </div>

                <div class="faq-panel" style="padding: 22px;">
                    <h3 class="faq-question">{{ __('messages.about_merchant_faq_q2') }}</h3>
                    <p class="faq-answer" style="padding: 12px 0 0;">{{ __('messages.about_merchant_faq_a2_lead') }}</p>
                    <p class="faq-answer" style="padding: 0;">- {{ __('messages.about_merchant_faq_a2_item_1_prefix') }} <a href="/register">{{ __('messages.about_merchant_faq_a2_item_1_link') }}</a></p>
                    <p class="faq-answer" style="padding: 0;">- {{ __('messages.about_merchant_faq_a2_item_2') }}</p>
                    <p class="faq-answer" style="padding: 0;">- {{ __('messages.about_merchant_faq_a2_item_3_prefix') }} <a href="/register/merchant">{{ __('messages.about_merchant_faq_a2_item_3_link') }}</a></p>
                    <p class="faq-answer" style="padding: 0;">{{ __('messages.about_merchant_faq_a2_outro') }}</p>
                </div>

                <div class="faq-panel" style="padding: 22px; margin-bottom: 0;">
                    <h3 class="faq-question">{{ __('messages.about_merchant_faq_q3') }}</h3>
                    <p class="faq-answer" style="padding: 12px 0 0;">- {{ __('messages.about_merchant_faq_a3_item_1') }}</p>
                    <p class="faq-answer" style="padding: 0;">- {{ __('messages.about_merchant_faq_a3_item_2') }}</p>
                    <p class="faq-answer" style="padding: 0;">- {{ __('messages.about_merchant_faq_a3_item_3') }}</p>
                    <p class="faq-answer" style="padding: 0;">- {{ __('messages.about_merchant_faq_a3_item_4') }}</p>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 by glowlabs. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Add smooth scrolling for anchor links with enhanced animation
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const target = document.querySelector(targetId);
                
                if (target) {
                    // Enhanced smooth scrolling with custom duration
                    const targetPosition = target.offsetTop - 80; // Offset for header
                    const startPosition = window.pageYOffset;
                    const distance = targetPosition - startPosition;
                    const duration = 500; // 500ms duration
                    let start = null;

                    function animation(currentTime) {
                        if (start === null) start = currentTime;
                        const timeElapsed = currentTime - start;
                        const run = ease(timeElapsed, startPosition, distance, duration);
                        window.scrollTo(0, run);
                        if (timeElapsed < duration) requestAnimationFrame(animation);
                    }

                    // Easing function for smooth animation
                    function ease(t, b, c, d) {
                        t /= d / 2;
                        if (t < 1) return c / 2 * t * t + b;
                        t--;
                        return -c / 2 * (t * (t - 2) - 1) + b;
                    }

                    requestAnimationFrame(animation);
                }
            });
        });

        // Add scroll effect to header
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.header');
            if (window.scrollY > 100) {
                header.style.background = 'rgba(255, 255, 255, 0.95)';
                header.style.backdropFilter = 'blur(10px)';
            } else {
                header.style.background = '#fff';
                header.style.backdropFilter = 'none';
            }
        });

        // Add hover effects to redesigned feature cards
       
        // Add click handlers for CTA buttons
        document.querySelectorAll('.cta-button').forEach(button => {
            button.addEventListener('click', function() {
                // Add your action here
                console.log('CTA button clicked:', this.textContent.trim());
            });
        });
    </script>
</body>
</html>
