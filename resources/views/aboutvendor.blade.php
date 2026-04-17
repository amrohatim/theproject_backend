<!DOCTYPE html>
@php($isRtl = app()->getLocale() === 'ar')
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About | Vendor - Glowlabs Merchant</title>
    <link href="https://fonts.googleapis.com/css2?family=Jersey+15&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}" />
   
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
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
        html[dir="rtl"] .interface-copy,
        html[dir="rtl"] .step-content,
        html[dir="rtl"] .usage-info-section,
        html[dir="rtl"] .merchant-faq-wrap,
        html[dir="rtl"] .faq-answer,
        html[dir="rtl"] .faq-question {
            text-align: right;
        }

        html[dir="rtl"] .hero-overlay {
            left: auto;
            right: 40px;
            text-align: right;
        }

        html[dir="rtl"] .hero-overlay .cta-button {
            margin-left: 0;
            margin-right: auto;
        }

        html[dir="rtl"] .features-showcase .service-description {
            text-align: right;
        }

        html[dir="rtl"] .dashboard-section .step-list li {
            padding-left: 0;
            padding-right: 20px;
        }

        html[dir="rtl"] .dashboard-section .step-list li:before {
            left: auto;
            right: 0;
        }

        /* Flip inline list-dot spacing used across usage sections */
        html[dir="rtl"] [style*="margin-right: 15px"] {
            margin-right: 0 !important;
            margin-left: 15px !important;
        }

        html[dir="rtl"] [style*="padding-left:10px"] {
            padding-left: 0 !important;
            padding-right: 10px !important;
        }

        /* Move absolute text labels anchored with left in inline styles */
        html[dir="rtl"] [style*="bottom: 20px; left: 20px"] {
            left: auto !important;
            right: 20px !important;
            text-align: right !important;
        }

        html[dir="rtl"] [style*="left: 10px"][style*="right: 10px"],
        html[dir="rtl"] [style*="left: 15px"][style*="right: 15px"] {
            text-align: right !important;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Wix Banner */
        .wix-banner {
            background: #000;
            color: white;
            text-align: center;
            padding: 8px 0;
            font-size: 13px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1001;
        }

        .wix-banner a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 400;
        }

        .wix-logo {
            width: 50px;
            height: 16px;
        }

        .wix-get-started {
            background: #fff;
            color: #000;
            padding: 4px 12px;
            border-radius: 15px;
            margin-left: 8px;
            font-size: 12px;
            font-weight: 500;
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
            color: var(--primary);
        }

        .nav-menu a.active::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--primary);
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
            background: var(--primary);
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
            background: rgba(15, 15, 15, 0.95);
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }

        .hero-content {
            position: relative;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width: 100%;
        }

        /* Full-width image at the top */
        .hero-top-image {
            width: 100%;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .hero-top-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        /* Hero overlay content */
        .hero-overlay {
            position: absolute;
            bottom: 40px;
            left: 40px;
            text-align: left;
            color: #333;
            z-index: 10;
            max-width: 700px;
            padding: 50px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 25px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .hero-overlay .merchant-title {
            font-size: 4.5rem;
            font-weight: 700;
            margin-bottom: 2rem;
            color: #1f2937;
        }

        .hero-overlay .hero-description {
            font-size: 1.4rem;
            line-height: 1.6;
            margin-bottom: 2.5rem;
            color: #4b5563;
        }

        .hero-overlay .cta-button {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            border: none;
            font-size: 1.3rem;
            font-weight: 600;
            padding: 16px 32px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            margin: 0 auto;
        }

        .hero-overlay .cta-button:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
        }

        /* Responsive adjustments for hero section and background image */
        @media (max-width: 1200px) {
            .hero-section {
                min-height: 90vh;
            }
            
            .hero-content {
                min-height: 90vh;
            }
            
            .hero-top-image img {
                object-position: center top;
            }

            .hero-overlay .merchant-title {
                font-size: 3rem;
            }

            .hero-overlay .hero-description {
                font-size: 1.1rem;
            }
        }

        @media (max-width: 768px) {
            .hero-section {
                min-height: 80vh;
            }
            
            .hero-content {
                min-height: 80vh;
            }
            
            .hero-top-image {
                height: 60vh;
            }
            
            .hero-top-image img {
                object-position: center center;
                object-fit: cover;
            }

            .hero-overlay {
                max-width: 550px;
                padding: 40px;
                bottom: 30px;
                left: 30px;
            }

            .hero-overlay .merchant-title {
                font-size: 3.5rem;
            }

            .hero-overlay .hero-description {
                font-size: 1.2rem;
            }

            .hero-overlay .cta-button {
                font-size: 1.1rem;
                padding: 12px 24px;
            }
        }

        @media (max-width: 480px) {
            .hero-section {
                min-height: 70vh;
            }
            
            .hero-content {
                min-height: 70vh;
            }
            
            .hero-top-image {
                height: 50vh;
            }
            
            .hero-top-image img {
                object-position: center center;
                object-fit: cover;
            }

            .hero-overlay {
                max-width: 420px;
                padding: 35px;
                bottom: 20px;
                left: 20px;
            }

            .hero-overlay .merchant-title {
                font-size: 2.5rem;
                margin-bottom: 1.2rem;
            }

            .hero-overlay .hero-description {
                font-size: 1.1rem;
                margin-bottom: 1.8rem;
            }

            .hero-overlay .cta-button {
                font-size: 1rem;
                padding: 10px 20px;
            }
        }

        @media (max-width: 360px) {
            .hero-section {
                min-height: 60vh;
            }
            
            .hero-content {
                min-height: 60vh;
            }
            
            .hero-top-image {
                height: 40vh;
            }

            .hero-overlay {
                max-width: 360px;
                padding: 30px;
                bottom: 15px;
                left: 15px;
            }

            .hero-overlay .merchant-title {
                font-size: 2.2rem;
                margin-bottom: 1rem;
            }

            .hero-overlay .hero-description {
                font-size: 1rem;
                margin-bottom: 1.5rem;
            }

            .hero-overlay .cta-button {
                font-size: 0.9rem;
                padding: 8px 16px;
            }
        }

        /* Two-column layout at the bottom */
        .hero-bottom-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            min-height: 50vh;
            flex: 1;
        }

        .hero-grid-item {
            position: relative;
            padding: 40px;
            border: 1px solid #404040;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .hero-grid-item:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            z-index: 10;
        }

        .woman-image {
            width: 100%;
            height: 100%;
            background: url('/app images/vendoreps.png');
            background-size: fill;
            background-position: center;
            border: none;
            border-radius: 0;
            box-shadow: none;
        }

        /* Top Right - Dala3Chic Description */
        .hero-top-right {
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .black-circle {
            background: #000000;
            border-radius: 50%;
            width: 100%;
            max-width: 728px; /* Adjust as needed */
            aspect-ratio: 1.3 / 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            text-align: center;
        }

        .circle-content {
            color: #ffffff;
        }

        .merchant-title {
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 241px; /* Pushes the button down */
        }

        .cta-button {
            background: var(--primary);
            color: #ffffff;
            border: none;
            
            padding: 7px 28px 11px 19px;
            font-size: 18px;
            font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideLeftRight 2s ease-in-out infinite;
            position: relative;
        }

        @keyframes slideLeftRight {
            0% {
                transform: translateX(0px);
            }
            50% {
                transform: translateX(30px);
            }
            100% {
                transform: translateX(0px);
            }
        }

        .arrow-icon {
            width: 40px;
            height: 2px;
        }

        .merchant-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 24px;
            letter-spacing: -1px;
            line-height: 1.1;
            color: #ffffff;
        }

        .merchant-description {
            font-family: "Jersey 15", sans-serif;
            font-size: 20px;
            line-height: 1.7;
            margin-bottom: 32px;
            color: rgba(255,255,255,0.9);
            font-weight: 400;
        }

        /* Bottom Left - Vision Section */
        .hero-bottom-left {
            background: linear-gradient(135deg, rgba(30, 30, 30, 0.9) 0%, rgba(40, 40, 40, 0.9) 100%);
            text-align: left;
            color: #e0e0e0;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .vision-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #ffffff;
            line-height: 1.2;
        }

        .vision-description {
            font-size: 16px;
            line-height: 1.7;
            color: #c0c0c0;
            font-weight: 400;
        }

        /* Bottom Right - Dashboard Image */
        .hero-bottom-right {
            background: #2563eb;
            text-align: center;
            padding: 0;
            overflow: hidden;
        }

        .dashboard-image {
            width: 100%;
            height: 100%;
            background:  #2563eb;
            border-radius: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            box-shadow: none;
        }

        .dashboard-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            border-radius: 0;
        }

        .dashboard-icon {
            font-size: 48px;
            color: white;
        }

        .dashboard-overlay {
            position: absolute;
            bottom: 20px;
            left: 20px;
            right: 20px;
            background: rgba(0,0,0,0.3);
            padding: 15px;
            border-radius: 8px;
            backdrop-filter: blur(5px);
        }

        .dashboard-title {
            color: white;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .dashboard-subtitle {
            color: rgba(255,255,255,0.8);
            font-size: 14px;
        }

        /* Removed floating animation */

        .cta-button {
            background: linear-gradient(135deg, var(--primary) 0%, #2563eb 100%);
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
            box-shadow: 0 4px 20px rgba(59, 130, 246, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(59, 130, 246, 0.4);
            background: linear-gradient(135deg, #2563eb 0%, var(--primary) 100%);
        }

        .arrow-icon {
            width: 20px;
            height: 20px;
        }

        /* Hero Bottom Content Responsive Styles */
        @media (max-width: 1200px) {
            .hero-bottom-content {
                grid-template-columns: 1fr;
                gap: 20px;
                min-height: auto;
            }
            
            .hero-grid-item {
                min-height: 300px;
            }
            
            .merchant-title {
                font-size: 24px;
            }
            
            .vision-title {
                font-size: 28px;
            }
        }

        @media (max-width: 768px) {
            .hero-bottom-content {
                gap: 15px;
                padding: 0 10px;
            }
            
            .hero-grid-item {
                min-height: 250px;
                padding: 30px;
            }
            
            .merchant-title {
                font-size: 22px;
                margin-bottom: 20px;
            }
            
            .merchant-description {
                font-size: 18px;
                margin-bottom: 28px;
            }
            
            .vision-title {
                font-size: 24px;
                margin-bottom: 16px;
            }
            
            .vision-description {
                font-size: 15px;
            }
            
            .cta-button {
                padding: 15px 30px;
                font-size: 15px;
            }
        }

        @media (max-width: 480px) {
            .hero-grid-item {
                min-height: 200px;
                padding: 25px;
            }
            
            .merchant-title {
                font-size: 20px;
                margin-bottom: 16px;
            }
            
            .merchant-description {
                font-size: 16px;
                margin-bottom: 24px;
            }
            
            .vision-title {
                font-size: 22px;
                margin-bottom: 14px;
            }
            
            .vision-description {
                font-size: 14px;
            }
            
            .cta-button {
                padding: 12px 24px;
                font-size: 14px;
            }
            
            .dashboard-overlay {
                bottom: 15px;
                left: 15px;
                right: 15px;
                padding: 12px;
            }
            
            .dashboard-title {
                font-size: 16px;
            }
            
            .dashboard-subtitle {
                font-size: 13px;
            }
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
            padding: 100px 0;
            background: linear-gradient(135deg, rgba(20, 20, 20, 0.95) 0%, rgba(30, 30, 30, 0.95) 100%);
            position: relative;
            overflow: hidden;
        }

        .features-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="%23ED5829" opacity="0.05"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>') repeat;
            pointer-events: none;
        }

        .features-header {
            text-align: center;
            margin-bottom: 80px;
            position: relative;
            z-index: 2;
        }

        .features-title {
            font-size: 64px;
            margin-bottom: 30px;
            color: #ffffff;
            font-weight: 700;
            background: #2563eb;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
        }

        .features-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: #2563eb;
            border-radius: 2px;
        }

        .features-description {
            font-size: 18px;
            line-height: 1.8;
            color: #c0c0c0;
            max-width: 900px;
            margin: 0 auto;
            font-weight: 400;
        }

        .features-row-layout {
            display: flex;
        
            align-items: flex-start;
            margin-bottom: 60px;
        }

        .features-left-column {
            flex: 1;
            padding-right: 20px;
        }

        .features-right-column {
            flex: 1;
            padding-left: 20px;
            max-width: 730px;
        }

        .what-you-get-title {
            font-size: 28px;
            font-family: Verdana, Geneva, Tahoma, sans-serif;
            margin: 0 0 30px 0;
            text-align: center;
            color: #ffffff;
            font-weight: 700;
        }

        .features-grid {
            display: flex;
            flex-direction: column;
            gap: 0;
            background-color: var(--primary);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 700px;
            max-width: 100%;
            margin: 0 auto;
        }

        /* Tablet and smaller desktop */
        @media (max-width: 1024px) {
            .features-row-layout {
                gap: 30px;
            }

            .features-right-column {
                max-width: 100%;
            }

            .features-grid {
                width: 100%;
            }
        }

        /* Mobile and tablet */
        @media (max-width: 768px) {
            .features-row-layout {
                flex-direction: column;
                gap: 20px;
                margin-bottom: 40px;
            }

            .features-left-column,
            .features-right-column {
                padding: 0;
                max-width: 100%;
            }

            .what-you-get-title {
                text-align: center;
                font-size: 20px;
                margin-bottom: 20px;
            }

            .features-grid {
                width: 100%;
                border-radius: 8px;
                margin: 0;
            }

            .feature-item {
                padding: 25px 20px;
                min-height: 80px;
            }

            .feature-number {
                font-size: 24px;
                margin-right: 15px;
                margin-bottom: 10px;
            }

            .feature-text {
                font-size: 14px;
                line-height: 1.4;
            }
        }

        /* Small mobile devices */
        @media (max-width: 480px) {
            .features-row-layout {
                gap: 15px;
                margin-bottom: 30px;
            }

            .what-you-get-title {
                font-size: 18px;
                margin-bottom: 15px;
            }

            .features-grid {
                border-radius: 6px;
            }

            .feature-item {
                padding: 20px 15px;
                min-height: 70px;
                flex-direction: column;
                align-items: flex-start;
                text-align: left;
            }

            .feature-number {
                font-size: 20px;
                margin-right: 0;
                margin-bottom: 8px;
                align-self: flex-start;
            }

            .feature-text {
                font-size: 13px;
                line-height: 1.3;
                margin: 0;
            }
        }

        /* Cross Grid Layout Responsive Styles */
        @media (max-width: 768px) {
            .cross-grid-container {
                height: auto !important;
                display: flex !important;
                flex-direction: column !important;
                gap: 20px !important;
            }

            .deals-section {
                position: relative !important;
                top: auto !important;
                left: auto !important;
                width: 100% !important;
                height: auto !important;
                padding: 40px 20px !important;
                margin: 0 !important;
            }

            .deals-content {
                text-align: center !important;
                align-items: center !important;
            }

            .deals-image-section {
                position: relative !important;
                bottom: auto !important;
                right: auto !important;
                width: 100% !important;
                height: auto !important;
                padding: 20px !important;
                margin: 0 !important;
                justify-content: center !important;
            }

            .deals-image-section img {
                width: 70% !important;
                max-width: 400px !important;
            }

            .center-point {
                display: none !important;
            }
        }

        @media (max-width: 480px) {
            .cross-grid-container {
                margin: 30px auto !important;
                gap: 15px !important;
            }

            .deals-section {
                padding: 30px 15px !important;
            }

            .deals-content h3 {
                font-size: 20px !important;
            }

            .deals-content h4 {
                font-size: 18px !important;
            }

            .deals-content p {
                font-size: 14px !important;
            }

            .deals-image-section {
                padding: 15px !important;
            }

            .deals-image-section img {
                width: 80% !important;
                max-width: 300px !important;
            }
        }

        .feature-item {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding: 40px;
            min-height: 100px;
        }

        .feature-item:nth-child(odd) {
            background-color: var(--primary);
        }

        .feature-item:nth-child(even) {
            background-color: #2563eb;
        }

        .feature-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: #2563eb;
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .feature-item:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 30px 80px rgba(59, 130, 246, 0.2);
        }

        .feature-item:hover::before {
            transform: scaleX(1);
        }

        .feature-number {
            line-height: 36px;
            letter-spacing: 0;
            color: #ffffff;
            font-family: Itim, "PingFang SC", "Hiragino Sans GB", "Microsoft YaHei", SimHei, Arial, Helvetica, sans-serif;
            font-size: 30px;
            font-weight: 400;
            margin-bottom: 20px;
            margin-right: 20px;
            display: inline-block;
            position: relative;
            flex-shrink: 0;
        }

        .feature-text {
            color: white;
            font-size: 16px;
            font-family: 'Inter', sans-serif;
            line-height: 1.5;
            margin: 0;
            flex: 1;
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: #2563eb;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
            position: relative;
            overflow: hidden;
        }

        .feature-icon::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%) scale(0);
            transition: transform 0.4s ease;
        }

        .feature-item:hover .feature-icon::before {
            transform: translate(-50%, -50%) scale(1.5);
        }

        .feature-icon svg {
            width: 40px;
            height: 40px;
            fill: white;
            z-index: 1;
            position: relative;
        }

        .feature-title {
            font-size: 24px;
            margin-bottom: 20px;
            color: #ffffff;
            font-weight: 600;
            line-height: 1.3;
        }

        .feature-description {
            font-size: 16px;
            line-height: 1.7;
            color: #c0c0c0;
            margin-bottom: 25px;
        }

        .feature-link {
            display: inline-flex;
            align-items: center;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            margin-top: auto;
            padding-top: 1rem;
        }

        .feature-link:hover {
            color: #2563eb;
            transform: translateX(5px);
        }

        .feature-link svg {
            width: 16px;
            height: 16px;
            margin-left: 8px;
            transition: transform 0.3s ease;
        }

        .feature-link:hover svg {
            transform: translateX(3px);
        }

        /* Service Cards */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: repeat(2, 1fr);
            gap: 30px;
            margin: 60px auto 0 auto;
            max-width: 1460px;
            padding: 0 20px;
            transform: translateX(-40px);
        }

        .service-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: row;
            align-items: stretch;
            min-height: 200px;
            width: 600px;
            max-width: 100%;
        }

        .service-card:hover {
            transform: translateY(-5px);
        }

        .service-image {
            width: 50%;
            min-width: 150px;
            background-size: cover;
            background-position: center;
            flex-shrink: 0;
        }

        .service-content {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .service-title {
            font-size: 18px;
            margin-bottom: 10px;
            color: #000;
        }

        .service-description {
            font-size: 14px;
            line-height: 1.8;
            color: gray;
            margin-bottom: 15px;
            text-align: justify;
            
        }

        .read-more {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        /* Dashboard Usage Section */
        .dashboard-section {
            padding: 80px 0;
            background:#F9FCFD;
        }

        .dashboard-content {
            max-width: 1160px;
            margin: 0 auto;
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
            background: rgba(10, 10, 10, 0.95);
            color: #e0e0e0;
            text-align: center;
            padding: 30px 0;
        }

        /* Responsive Design */
         @media (max-width: 1500px) {
              .services-grid {
                  max-width: 1200px;
                  gap: 25px;
                  transform: translateX(-30px);
                  padding: 0 15px;
              }
              
              .service-card {
                  width: 580px;
              }
          }
          
          @media (max-width: 1024px) {
              .services-grid {
                  max-width: 800px;
                  gap: 20px;
                  transform: translateX(-20px);
                  padding: 0 10px;
              }
              
              .service-card {
                  width: 380px;
              }
          }

         @media (max-width: 768px) {
             .hero-content {
                 min-height: auto;
             }

             .hero-top-image {
                 height: 40vh;
                 min-height: 300px;
             }

             .hero-bottom-content {
                 grid-template-columns: 1fr;
                 min-height: auto;
                 gap: 0;
             }

             .hero-grid-item {
                 padding: 30px 20px;
                 min-height: 250px;
             }

             .dashboard-image {
                 min-height: 200px;
             }

             .dashboard-image img {
                 width: 100%;
                 height: 100%;
                 min-height: 200px;
                 object-fit: cover;
             }

             .hero-bottom-right {
                 padding: 10px;
             }

             .merchant-title {
                 font-size: 28px;
             }

             .vision-title {
                 font-size: 24px;
             }

             .vision-content {
                 grid-template-columns: 1fr;
                 gap: 30px;
             }

             .features-title {
                 font-size: 36px;
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

             .hero-section {
                 min-height: auto;
             }

             .services-grid {
                grid-template-columns: 1fr;
                grid-template-rows: auto;
                max-width: 100%;
                transform: translateX(-10px);
                padding: 0 15px;
            }
            
            .service-card {
                width: 100%;
                flex-direction: column;
                min-height: auto;
            }

             .service-image {
                 width: 100%;
                 height: 180px;
                 min-width: auto;
             }
         }

         @media (max-width: 480px) {
             .hero-top-image {
                 height: 35vh;
                 min-height: 250px;
             }

             .hero-grid-item {
                 padding: 20px 15px;
                 min-height: 200px;
             }

             .dashboard-image {
                 min-height: 180px;
             }

             .dashboard-image img {
                 min-height: 180px;
             }

             .hero-bottom-right {
                 padding: 8px;
             }

             .dashboard-overlay {
                 bottom: 10px;
                 left: 10px;
                 right: 10px;
                 padding: 10px;
             }

             .dashboard-title {
                 font-size: 16px;
             }

             .dashboard-subtitle {
                 font-size: 12px;
             }
         }

         @media (max-width: 480px) {
             .hero-grid-item {
                 padding: 15px;
                 min-height: 200px;
             }

             .merchant-title {
                 font-size: 24px;
             }

             .vision-title {
                 font-size: 20px;
             }

             .merchant-description,
             .vision-description {
                 font-size: 14px;
             }

             .features-grid,
             .services-grid {
                 grid-template-columns: 1fr;
                 gap: 20px;
                 transform: translateX(-5px);
                 padding: 0 10px;
             }

             .service-card {
                 width: 100%;
                 flex-direction: column;
                 min-height: auto;
             }

             .service-image {
                 width: 100%;
                 height: 150px;
                 min-width: auto;
             }

             .service-content {
                 justify-content: flex-start;
                 padding: 15px;
             }

             .service-description {
                 font-size: 13px;
                 line-height: 1.7;
             }
         }

         /* Merchant-matching hero + features overrides */
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
             background-image: url('{{ asset('assets/vendorHero.webp') }}');
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
             color: #ffffff;
             margin-bottom: clamp(22px, 3vw, 34px);
         }

         .merchant-hero__title span {
             color: #FFA006;
         }

         .merchant-hero__description {
             font-family: 'Proxima Nova', 'Segoe UI', sans-serif;
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
             background: #FFA006;
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

         .features-notes-row {
             margin-top: 52px;
             margin-inline: 0vh;
             display: grid;
             grid-template-columns: minmax(430px, 0.95fr) minmax(520px, 1fr);
             gap: clamp(24px, 3.5vw, 56px);
             align-items: start;
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

         .features-main-image {
             position: relative;
             z-index: 2;
             width: min(92%, 650px);
             height: auto;
             object-fit: contain;
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
             max-width: 600px;
         }

         .features-intro__description,
         .features-intro__secondary {
             font-family: 'Archivo', 'Segoe UI', sans-serif;
             font-size: 16px;
             line-height: 1.6;
             color: #636571;
             max-width: 620px;
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

         .features-btn--secondary {
             background: rgba(152, 179, 255, 0.07);
             border-color: rgba(152, 179, 255, 0.23);
             color: #a46bc1;
         }

         .features-purple-notes {
             width: 100%;
         }

         .features-notes-stack {
             position: relative;
             width: 100%;
             padding-top: 34px;
             padding-right: 24px;
         }

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

         .features-showcase--figma .services-grid {
             display: grid;
             grid-template-columns: repeat(2, minmax(0, 1fr));
             gap: 26px;
             margin: 0 auto;
             max-width: 1586px;
             padding: 0;
             transform: none;
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
             border-radius:20px;
             display: block;
         }

         html[dir="rtl"] .features-showcase--figma .service-icon,
         html[dir="rtl"] .features-showcase--figma .read-more {
             align-self: flex-end;
         }

         @media (max-width: 1200px) {
             .features-notes-row {
                 grid-template-columns: 1fr;
                 gap: 28px;
                 margin-inline: 10px;
             }

             .features-notes-side,
             .features-intro__media,
             .features-purple-notes {
                 max-width: 760px;
                 margin: 0 auto;
             }
         }

         @media (max-width: 1024px) {
             .hero-section {
                 min-height: 76vh;
             }

             .merchant-hero {
                 padding: 110px 36px 96px;
             }

             .merchant-hero__content {
                 max-width: 88%;
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
             .hero-section {
                 min-height: 68vh;
             }

              .features-showcase.features-showcase--figma {
             margin-top: 16px;
             padding: 12px 0;
         }
               
             .merchant-hero {
                 padding: 92px 22px 82px;
                 align-items: flex-start;
             }

             .merchant-hero__content {
                 max-width: 100%;
                 margin-top:150px;
             }

             .merchant-hero__description {
                 line-height: 1.35;
             }

             .merchant-hero .cta-button {
                 border-radius: 16px;
                 padding: 14px 30px;
             }

             .features-section {
                 padding-bottom: 68px;
             }

             .features-intro__media {
                 min-height: 0;
                 align-items: center;
                 padding-inline: 0;
             }

             .features-notes-row {
                 margin-top: 24px;
                 gap: 20px;
             }

             .features-section .features-notes-row:first-child {
                 margin-top: 8px;
             }

             .features-section .features-notes-row:first-child .features-intro__media {
                 min-height: 240px;
                 height: 240px;
                 margin-top: 10px;
                 border-radius: 18px;
                 overflow: hidden;
             }

             .features-section .features-notes-row:first-child .features-main-image {
                 width: 170%;
                 height: 170%;
                 margin-right:10px;
                 margin-top:15px;
                 object-fit: cover;
                 object-position: center top;
             }

             .features-checklist {
                 grid-template-columns: 1fr;
                 gap: 12px;
             }

             .features-btn {
                 width: 100%;
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

             .cloud-divider {
                 margin-top: -40px;
                 margin-bottom: -44px;
             }

             .cloud-divider img {
                 height: 130px;
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
             .hero-section {
                 min-height: 620px;
             }

             .merchant-hero {
                 padding: 82px 14px 72px;
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

             .features-shell {
                 width: min(1260px, calc(100% - 18px));
             }

             .features-intro__media {
                 min-height: 0;
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
             color: var(--primary);
             background-color: var(--primary-light);
             font-weight: 700;
             font-family: 'Coco Sharp';
             line-height: 1.2;
             text-align: center;
             margin-bottom: 28px;
             padding: 15px;
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
             font-family: 'Archivo', 'Segoe UI', sans-serif;
             font-size: 16px;
             line-height: 1.7;
             color: #4b5565;
             padding: 0 22px 18px;
         }

         .faq-icon {
             width: 28px;
             height: 28px;
             border-radius: 999px;
             flex: 0 0 28px;
             display: inline-flex;
             align-items: center;
             justify-content: center;
             border: 1px solid #d4d8df;
             color: #697185;
             margin-top: 2px;
             transition: transform .2s ease, border-color .2s ease, color .2s ease;
         }

         .faq-icon::before {
             content: '+';
             font-size: 18px;
             line-height: 1;
         }

         .faq-panel[open] .faq-icon {
             border-color: var(--primary);
             color: var(--primary);
             transform: rotate(45deg);
         }
    </style>
</head>
<body>
     <main class="main-content">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="merchant-hero">
                <div class="merchant-hero__media" aria-hidden="true"></div>
                <div class="merchant-hero__overlay" aria-hidden="true"></div>
                <div class="merchant-hero__content">
                    <h1 class="merchant-hero__title">{{ __('messages.about_vendor_hero_title_prefix') }} <span>{{ __('messages.about_vendor_hero_title_highlight') }}</span></h1>
                    <p class="merchant-hero__description">
                        {{ __('messages.about_vendor_hero_description_line_1') }}
                        {{ __('messages.about_vendor_hero_description_line_2') }}
                    </p>
                    <button class="cta-button">{{ __('messages.about_vendor_hero_cta') }}</button>
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
                        <img class="features-main-image" src="{{ asset('assets/vendorShowCard.webp') }}" alt="{{ __('messages.about_vendor_features_main_image_alt') }}">
                    </div>
                    <div class="features-notes-side">
                        <h2 class="features-heading">{{ __('messages.about_vendor_features_intro_heading') }}</h2>
                        <p class="features-intro__description">
                            {{ __('messages.about_vendor_features_intro_description_line_1') }}

                         {{ __('messages.about_vendor_features_intro_description_line_2') }}
                        </p>
                        <p class="features-intro__secondary">
                            {{ __('messages.about_vendor_features_intro_secondary') }}
                        </p>

                        <ul class="features-checklist">
                            <li>{{ __('messages.about_vendor_features_checklist_branch_management') }}</li>
                            <li>{{ __('messages.about_vendor_features_checklist_user_access_control') }}</li>
                            <li>{{ __('messages.about_vendor_features_checklist_order_tracking') }}</li>
                            <li>{{ __('messages.about_vendor_features_checklist_delivery_support') }}</li>
                        </ul>

                        <div class="features-actions">
                            <a class="features-btn features-btn--primary" href="#dashboard-section">{{ __('messages.about_vendor_features_learn_more') }}</a>
                            <a class="features-btn features-btn--secondary" href="/register/vendor">{{ __('messages.about_vendor_features_create_one_now') }}</a>
                        </div>
                    </div>
                </div>

                <div class="features-notes-row">
                    <div class="features-notes-side">
                        
                        <h2 class="features-heading">{{ __('messages.about_vendor_features_get_heading') }}</h2>
                        <p class="features-intro__description">{{ __('messages.about_vendor_features_get_description_line_1') }}</p>
                        <p class="features-intro__description">
                            {{ __('messages.about_vendor_features_get_description_line_2') }}
                        </p>
                        <p class="features-intro__secondary">
                            {{ __('messages.about_vendor_features_get_secondary') }}
                        </p>
                         <ul class="features-checklist">
                            <li>{{ __('messages.about_vendor_features_checklist_branch_management') }}</li>
                            <li>{{ __('messages.about_vendor_features_checklist_user_access_control') }}</li>
                            <li>{{ __('messages.about_vendor_features_checklist_order_tracking') }}</li>
                            <li>{{ __('messages.about_vendor_features_checklist_delivery_support') }}</li>
                        </ul>

                        <div class="features-actions">
                            <a class="features-btn features-btn--primary" href="#dashboard-section">{{ __('messages.about_vendor_features_learn_more') }}</a>
                            <a class="features-btn features-btn--secondary" href="/register/vendor">{{ __('messages.about_vendor_features_create_one_now') }}</a>
                        </div>
                    </div>
                    <div class="features-notes-stack">
                        <div class="features-purple-notes">
                            <ul class="feature-note-list">
                                <li>{{ __('messages.about_vendor_features_note_1') }}</li>
                                <li>{{ __('messages.about_vendor_features_note_2') }}</li>
                                <li>{{ __('messages.about_vendor_features_note_3') }}</li>
                                <li>{{ __('messages.about_vendor_features_note_4') }}</li>
                                <li>{{ __('messages.about_vendor_features_note_5') }}</li>
                                <li>{{ __('messages.about_vendor_features_note_6') }}</li>
                                <li>{{ __('messages.about_vendor_features_note_7') }}</li>
                            </ul>
                        </div>

                        <div class="features-purple-notes">
                            <ul class="feature-note-list text-transparent">
                                <li>{{ __('messages.about_vendor_features_note_1') }}</li>
                                <li>{{ __('messages.about_vendor_features_note_2') }}</li>
                                <li>{{ __('messages.about_vendor_features_note_3') }}</li>
                                <li>{{ __('messages.about_vendor_features_note_4') }}</li>
                                <li>{{ __('messages.about_vendor_features_note_5') }}</li>
                                <li>{{ __('messages.about_vendor_features_note_6') }}</li>
                                <li>{{ __('messages.about_vendor_features_note_7') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="features-showcase features-showcase--figma">
                    <div class="services-grid">
                        <div class="service-card">
                            <div class="service-content">
                                <h4 class="service-title">{{ __('messages.about_vendor_features_showcase_dashboard_title') }}</h4>
                                <img class="service-icon" src="{{ asset('assets/dashboardicon.png') }}" alt="{{ __('messages.about_vendor_features_showcase_dashboard_icon_alt') }}">
                                <p class="service-description">
                                    {{ __('messages.about_vendor_features_showcase_dashboard_description_line_1') }} <br>{{ __('messages.about_vendor_features_showcase_dashboard_description_line_2') }}
                                </p>
                                <a href="#dashboard-section" class="read-more">{{ __('messages.about_vendor_features_read_more') }}</a>
                            </div>
                            <div class="service-image">
                                <img src="{{ asset('assets/dashboardfeature.webp') }}" alt="{{ __('messages.about_vendor_features_showcase_dashboard_image_alt') }}">
                            </div>
                        </div>
                        <div class="service-card">
                            <div class="service-content">
                                <h4 class="service-title">{{ __('messages.about_vendor_features_showcase_delivery_title') }}</h4>
                                <img class="service-icon" src="{{ asset('assets/delivery-truckicon.png') }}" alt="{{ __('messages.about_vendor_features_showcase_delivery_icon_alt') }}">
                                <p class="service-description">
                                    {{ __('messages.about_vendor_features_showcase_delivery_description_line_1') }} <br>{{ __('messages.about_vendor_features_showcase_delivery_description_line_2') }}
                                </p>
                                <a href="#" class="read-more">{{ __('messages.about_vendor_features_read_more') }}</a>
                            </div>
                            <div class="service-image">
                                <img src="{{ asset('assets/deliveryfeature.jpg') }}" alt="{{ __('messages.about_vendor_features_showcase_delivery_image_alt') }}">
                            </div>
                        </div>
                        <div class="service-card">
                            <div class="service-content">
                                <h4 class="service-title">{{ __('messages.about_vendor_features_showcase_deals_title') }}</h4>
                                <img class="service-icon" src="{{ asset('assets/discounticon.png') }}" alt="{{ __('messages.about_vendor_features_showcase_deals_icon_alt') }}">
                                <p class="service-description">
                                    {{ __('messages.about_vendor_features_showcase_deals_description_line_1') }} <br>{{ __('messages.about_vendor_features_showcase_deals_description_line_2') }}
                                </p>
                                <a href="#deals-section" class="read-more">{{ __('messages.about_vendor_features_read_more') }}</a>
                            </div>
                            <div class="service-image">
                                <img src="{{ asset('assets/dealfeature.webp') }}" alt="{{ __('messages.about_vendor_features_showcase_deals_image_alt') }}">
                            </div>
                        </div>
                        <div class="service-card">
                            <div class="service-content">
                                <h4 class="service-title">{{ __('messages.about_vendor_features_showcase_branch_title') }}</h4>
                                <img class="service-icon" src="{{ asset('assets/shopicon.png') }}" alt="{{ __('messages.about_vendor_features_showcase_branch_icon_alt') }}">
                                <p class="service-description">
                                    {{ __('messages.about_vendor_features_showcase_branch_description_line_1') }} <br>{{ __('messages.about_vendor_features_showcase_branch_description_line_2') }}
                                </p>
                                <a href="#dashboard-section" class="read-more">{{ __('messages.about_vendor_features_read_more') }}</a>
                            </div>
                            <div class="service-image">
                                <img src="{{ asset('assets/branchm.webp') }}" alt="{{ __('messages.about_vendor_features_showcase_branch_image_alt') }}">
                            </div>
                        </div>
                        <div class="service-card">
                            <div class="service-content">
                                <h4 class="service-title">{{ __('messages.about_vendor_features_showcase_service_provider_title') }}</h4>
                                <img class="service-icon" src="{{ asset('assets/employee.png') }}" alt="{{ __('messages.about_vendor_features_showcase_service_provider_icon_alt') }}">
                                <p class="service-description">
                                    {{ __('messages.about_vendor_features_showcase_service_provider_description_line_1') }} <br>{{ __('messages.about_vendor_features_showcase_service_provider_description_line_2') }}
                                </p>
                                <a href="#service-provider-section" class="read-more">{{ __('messages.about_vendor_features_read_more') }}</a>
                            </div>
                            <div class="service-image">
                                <img src="{{ asset('assets/serviceprovider.webp') }}" alt="{{ __('messages.about_vendor_features_showcase_service_provider_image_alt') }}">
                            </div>
                        </div>
                        <div class="service-card">
                            <div class="service-content">
                                <h4 class="service-title">{{ __('messages.about_vendor_features_showcase_product_manager_title') }}</h4>
                                <img class="service-icon" src="{{ asset('assets/productstock.png') }}" alt="{{ __('messages.about_vendor_features_showcase_product_manager_icon_alt') }}">
                                <p class="service-description">
                                    {{ __('messages.about_vendor_features_showcase_product_manager_description_line_1') }}<br>{{ __('messages.about_vendor_features_showcase_product_manager_description_line_2') }}
                                </p>
                                <a href="#products-manager-section" class="read-more">{{ __('messages.about_vendor_features_read_more') }}</a>
                            </div>
                            <div class="service-image">
                                <img src="{{ asset('assets/productmanager.webp') }}" alt="{{ __('messages.about_vendor_features_showcase_product_manager_image_alt') }}">
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
                        <p class="interface-label">{{ __('messages.about_vendor_interface_label') }}</p>
                        <h2 class="interface-title">{!! __('messages.about_vendor_interface_title') !!}</h2>
                        <p class="interface-description">
            {{ __('messages.about_vendor_interface_description') }}                        </p>

                        <div class="interface-features">
                            <div class="interface-feature-item">
                                <div class="interface-feature-icon-wrap">
                                    <span class="material-symbols-outlined">analytics</span>
                                </div>
                                <div>
                                    <h4 class="interface-feature-title">{{ __('messages.about_vendor_interface_feature_1_title') }}</h4>
                                    <p class="interface-feature-text">{{ __('messages.about_vendor_interface_feature_1_text') }}</p>
                                </div>
                            </div>
                            <div class="interface-feature-item">
                                <div class="interface-feature-icon-wrap">
                                    <span class="material-symbols-outlined">touch_app</span>
                                </div>
                                <div>
                                    <h4 class="interface-feature-title">{{ __('messages.about_vendor_interface_feature_2_title') }}</h4>
                                    <p class="interface-feature-text">{{ __('messages.about_vendor_interface_feature_2_text') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="interface-mock-col">
                        <div class="interface-mock-frame">
                            <div class="interface-mock-image-wrap">
                                <img class="interface-mock-image" src="{{ asset('assets/vendorDashboard.png') }}" alt="{{ __('messages.about_vendor_interface_mock_image_alt') }}">
                            </div>
                        </div>

                        <div class="interface-floating-card">
                            <div class="interface-floating-head">
                                <span class="interface-pulse-dot"></span>
                                <span class="interface-floating-label">{{ __('messages.about_vendor_interface_floating_label') }}</span>
                            </div>
                            <p class="interface-floating-value">+18.4%</p>
                            <p class="interface-floating-text">{{ __('messages.about_vendor_interface_floating_text') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Deals Section -->
        <section class="parallax-deals-section">
            <div class="container">
                <div id="deals-section" class="parallax-deals-card" style="--deals-bg: url('/assets/vendorDeal.png');">
                    <div class="parallax-deals-bg" aria-hidden="true"></div>
                    <div class="parallax-deals-overlay" aria-hidden="true"></div>

                    <div class="parallax-deals-content">
                        <span class="parallax-deals-eyebrow">{{ __('messages.about_vendor_deals_eyebrow') }}</span>
                        <h3 class="parallax-deals-title">{{ __('messages.about_vendor_deals_title') }}</h3>
                        <h4 class="parallax-deals-subtitle">{{ __('messages.about_vendor_deals_subtitle') }}</h4>
                        <p class="parallax-deals-text">
                            {{ __('messages.about_vendor_deals_text') }}
                        </p>
                       <a href="/vendor/deals"> <button class="parallax-deals-btn">{{ __('messages.about_vendor_deals_cta') }}</button></a>
                    </div>
                </div>

                <!-- Payment Section -->
                {{-- <div style="text-align: center; margin: 60px 0; padding: 40px; background: #f8f9fa; border-radius: 15px;">
                    <h3 style="font-size: 24px; margin-bottom: 15px; color: #333;">{{ __('messages.about_vendor_dashboard_payment_title') }}</h3>
                    <p style="color: #666; margin-bottom: 20px;">
                        Receive your payments securely and promptly. Funds from each transaction are transferred directly to your registered bank account, and we supply comprehensive revenue analysis and reporting so you can easily track performance and growth.
                    </p>
                    <button class="cta-button">{{ __('messages.about_vendor_features_learn_more') }}</button>
                </div> --}}
            </div>
        </section>

        <!-- Dashboard Usage Section -->
        <section class="dashboard-section" id="dashboard-section">
            <div class="container">
                <div class="dashboard-content">
                    <h2 style="text-align: center; padding:10px; margin-bottom:20px; font-family:Coco Sharp; font-size: clamp(24px, 5.5vw, 48px); color:var(--primary); background-color:var(--primary-light);">{{ __('messages.about_vendor_dashboard_usage_title') }}</h2>
                              <div class="usage-step" style="display: flex; align-items: flex-start; flex-direction: column; gap: clamp(12px, 4vw, 40px);">
                        <!-- Video Box -->
                        <div class="video-container" style="flex: 1 1 600px; min-width: 280px; max-width: 100%; position: relative;  border-radius: 10px; overflow: hidden;">
                            <div style="aspect-ratio: 16/9; position: relative;">
                                <video 
                                    style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;" 
                                    controls 
                                    muted 
                                    loop
                                    preload="metadata"
                                    {{-- poster="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 225'%3E%3Crect width='400' height='225' fill='%23667eea'/%3E%3Ctext x='200' y='112' text-anchor='middle' fill='white' font-size='16' font-family='Arial'%3EProduct Tutorial Video%3C/text%3E%3C/svg%3E" --}}   >
                                    {{-- <source src="https://firebasestorage.googleapis.com/v0/b/dala3chic-e2b81.firebasestorage.app/o/product%20merchant%20usage.mov?alt=media&token=d174b7d9-f59c-4987-86f3-806d77cc2882" type="video/quicktime"> --}}
                                    <source src="https://firebasestorage.googleapis.com/v0/b/dala3chic-e2b81.firebasestorage.app/o/Branch%20Demo.mov?alt=media&token=02db0f94-78c7-44a3-9a44-687b44b17321" type="video/mp4">
                                    <div style="display: flex; align-items: center; justify-content: center; height: 100%; background: rgba(255, 255, 255, 0.1); color: white; text-align: center; padding: 20px;">
                                        <div>
                                            <div style="width: 80px; height: 80px; background: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; backdrop-filter: blur(1px);">
                                                <svg width="32" height="32" viewBox="0 0 24 24" fill="white">
                                                    <path d="M8 5v14l11-7z"/>
                                                </svg>
                                            </div>
                                            <p style="margin: 0; font-weight: 600;">{{ __('messages.about_vendor_dashboard_step_1_overlay_title') }}</p>
                                            <p style="margin: 5px 0 0; font-size: 14px; opacity: 0.8;">{{ __('messages.about_vendor_dashboard_video_loading') }}</p>
                                        </div>
                                    </div>
                                </video>
                                <div style="position: absolute; bottom: 20px; left: 20px; color: white; font-weight: 600; font-size: 18px; text-shadow: 2px 2px 4px rgba(0,0,0,0.7); z-index: 10;">{{ __('messages.about_vendor_dashboard_step_1_overlay_title') }}</div>
                            </div>
                        </div>
                        <!-- Content -->
                        <div style="flex: 1;">
                            <h3 class="step-title">{{ __('messages.about_vendor_dashboard_step_1_title') }}</h3>
                            <div class="step-content">
                                <p>{{ __('messages.about_vendor_dashboard_step_1_intro_1') }}</p>
                                <p>{{ __('messages.about_vendor_dashboard_step_1_intro_2') }}</p>
                                <ul class="step-list">
                                    <li>{{ __('messages.about_vendor_dashboard_step_1_item_1') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_1_item_2') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_1_item_3') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_1_item_4') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_1_item_5') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_1_item_6') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_1_item_7') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_1_item_8') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_1_item_9') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_1_item_10') }}</li>
                                </ul>
                                <p>{{ __('messages.about_vendor_dashboard_step_1_license_intro') }}</p>
                                <ul class="step-list">
                                    <li>{{ __('messages.about_vendor_dashboard_step_1_license_item_1') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_1_license_item_2') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_1_license_item_3') }}</li>
            
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="usage-step" style="display: flex; align-items: flex-start; flex-direction: column; gap: clamp(12px, 4vw, 40px); ">
                        <!-- Video Box -->
                        <div class="video-container" style="flex: 1 1 600px; min-width: 280px; max-width: 100%; position: relative;  border-radius: 10px; overflow: hidden;">
                            <div style="aspect-ratio: 16/9; position: relative;">
                                <video 
                                    style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;" 
                                    controls 
                                    muted 
                                    loop
                                    preload="metadata"
                                    {{-- poster="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 225'%3E%3Crect width='400' height='225' fill='%23667eea'/%3E%3Ctext x='200' y='112' text-anchor='middle' fill='white' font-size='16' font-family='Arial'%3EProduct Tutorial Video%3C/text%3E%3C/svg%3E" --}} >
                                    {{-- <source src="https://firebasestorage.googleapis.com/v0/b/dala3chic-e2b81.firebasestorage.app/o/product%20merchant%20usage.mov?alt=media&token=d174b7d9-f59c-4987-86f3-806d77cc2882" type="video/quicktime"> --}}
                                    <source src="https://firebasestorage.googleapis.com/v0/b/dala3chic-e2b81.firebasestorage.app/o/Product%20Demo.mov?alt=media&token=6d3128ba-f0c4-4900-ae15-2696fb93450a" type="video/mp4">
                                    <div style="display: flex; align-items: center; justify-content: center; height: 100%; background: rgba(255, 255, 255, 0.1); color: white; text-align: center; padding: 20px;">
                                        <div>
                                            <div style="width: 80px; height: 80px; background: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; backdrop-filter: blur(1px);">
                                                <svg width="32" height="32" viewBox="0 0 24 24" fill="white">
                                                    <path d="M8 5v14l11-7z"/>
                                                </svg>
                                            </div>
                                            <p style="margin: 0; font-weight: 600;">{{ __('messages.about_vendor_dashboard_step_2_overlay_title') }}</p>
                                            <p style="margin: 5px 0 0; font-size: 14px; opacity: 0.8;">{{ __('messages.about_vendor_dashboard_video_loading') }}</p>
                                        </div>
                                    </div>
                                </video>
                                <div style="position: absolute; bottom: 20px; left: 20px; color: white; font-weight: 600; font-size: 18px; text-shadow: 2px 2px 4px rgba(0,0,0,0.7); z-index: 10;">{{ __('messages.about_vendor_dashboard_step_2_overlay_title') }}</div>
                            </div>
                        </div>
                        <!-- Content -->
                        <div style="flex: 1;">
                            <h3 class="step-title">{{ __('messages.about_vendor_dashboard_step_2_title') }}</h3>
                            <div class="step-content">
                                <p>{{ __('messages.about_vendor_dashboard_step_2_intro_1') }}</p>
                                <p>{{ __('messages.about_vendor_dashboard_step_2_intro_2') }}</p>
                                <ul class="step-list">
                                    <li>{{ __('messages.about_vendor_dashboard_step_2_item_1') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_2_item_2') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_2_item_3') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_2_item_4') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_2_item_5') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_2_item_6') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_2_item_7') }}</li>
                                </ul>
                                <p>{{ __('messages.about_vendor_dashboard_step_2_colors_intro') }}</p>
                                <ul class="step-list">
                                    <li>{{ __('messages.about_vendor_dashboard_step_2_colors_item_1') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_2_colors_item_2') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_2_colors_item_3') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_2_colors_item_4') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_2_colors_item_5') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="usage-step" style="display: flex; align-items: flex-start; flex-direction: column; gap: clamp(12px, 4vw, 40px); ">
                        <!-- Video Box -->
                        <div class="video-container" style="flex: 1 1 600px; min-width: 280px; max-width: 100%; position: relative;">
                            <div style="aspect-ratio: 16/9; position: relative;">
                                <video 
                                    style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;" 
                                    controls 
                                    muted 
                                    loop
                                    preload="metadata"
                                    {{-- poster="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 225'%3E%3Crect width='400' height='225' fill='%23f093fb'/%3E%3Ctext x='200' y='112' text-anchor='middle' fill='white' font-size='16' font-family='Arial'%3EService Tutorial Video%3C/text%3E%3C/svg%3E" --}}   >
                                    {{-- <source src="https://firebasestorage.googleapis.com/v0/b/dala3chic-e2b81.firebasestorage.app/o/service%20merchant%20tutor.mov?alt=media&token=d174b7d9-f59c-4987-86f3-806d77cc2882" type="video/quicktime"> --}}
                                    <source src="https://firebasestorage.googleapis.com/v0/b/dala3chic-e2b81.firebasestorage.app/o/Sevice%20Demo.mov?alt=media&token=e001f22f-73a5-449a-9b50-ae3e6b20e7bc" type="video/mp4">
                                    <div style="display: flex; align-items: center; justify-content: center; height: 100%; background: rgba(255, 255, 255, 0.1); color: white; text-align: center; padding: 20px;">
                                        <div>
                                            <div style="width: 80px; height: 80px; background: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; backdrop-filter: blur(10px);">
                                                <svg width="32" height="32" viewBox="0 0 24 24" fill="white">
                                                    <path d="M8 5v14l11-7z"/>
                                                </svg>
                                            </div>
                                            <p style="margin: 0; font-weight: 600;">{{ __('messages.about_vendor_dashboard_step_3_overlay_title') }}</p>
                                            <p style="margin: 5px 0 0; font-size: 14px; opacity: 0.8;">{{ __('messages.about_vendor_dashboard_video_loading') }}</p>
                                        </div>
                                    </div>
                                </video>
                                <div style="position: absolute; bottom: 20px; left: 20px; color: white; font-weight: 600; font-size: 18px; text-shadow: 2px 2px 4px rgba(0,0,0,0.7); z-index: 10;">{{ __('messages.about_vendor_dashboard_step_3_overlay_title') }}</div>
                            </div>
                        </div>
                        <!-- Content -->
                        <div style="flex: 1;">
                            <h3 class="step-title">{{ __('messages.about_vendor_dashboard_step_3_title') }}</h3>
                            <div class="step-content">
                                <p>{{ __('messages.about_vendor_dashboard_step_3_intro_1') }}</p>
                                <p>{{ __('messages.about_vendor_dashboard_step_3_intro_2') }}</p>
                                <ul class="step-list">
                                    <li>{{ __('messages.about_vendor_dashboard_step_3_item_1') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_3_item_2') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_3_item_3') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_3_item_4') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_3_item_5') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_3_item_6') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_3_item_7') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_3_item_8') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_3_item_9') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>

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
                                    {{-- poster="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 225'%3E%3Crect width='400' height='225' fill='%23667eea'/%3E%3Ctext x='200' y='112' text-anchor='middle' fill='white' font-size='16' font-family='Arial'%3EDeal Tutorial Video%3C/text%3E%3C/svg%3E" --}}   >
                                    {{-- <source src="https://firebasestorage.googleapis.com/v0/b/dala3chic-e2b81.firebasestorage.app/o/deal%20merchant%20tutor.mov?alt=media&token=d174b7d9-f59c-4987-86f3-806d77cc2882" type="video/quicktime"> --}}
                                    <source src="https://firebasestorage.googleapis.com/v0/b/dala3chic-e2b81.firebasestorage.app/o/Deal%20Demo.mp4?alt=media&token=https://firebasestorage.googleapis.com/v0/b/dala3chic-e2b81.firebasestorage.app/o/Deal%20Demo.mp4?alt=media&token=087f44f8-3a3c-4139-9c2b-9033656838fc" type="video/mp4">
                                    <div style="display: flex; align-items: center; justify-content: center; height: 100%; background: rgba(255, 255, 255, 0.1); color: white; text-align: center; padding: 20px;">
                                        <div>
                                            <div style="width: 80px; height: 80px; background: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; backdrop-filter: blur(10px);">
                                                <svg width="32" height="32" viewBox="0 0 24 24" fill="white">
                                                    <path d="M8 5v14l11-7z"/>
                                                </svg>
                                            </div>
                                            <p style="margin: 0; font-weight: 600;">{{ __('messages.about_vendor_dashboard_step_4_overlay_title') }}</p>
                                            <p style="margin: 5px 0 0; font-size: 14px; opacity: 0.8;">{{ __('messages.about_vendor_dashboard_video_loading') }}</p>
                                        </div>
                                    </div>
                                </video>
                                <div style="position: absolute; bottom: 20px; left: 20px; color: white; font-weight: 600; font-size: 18px; text-shadow: 2px 2px 4px rgba(0,0,0,0.7); z-index: 10;">{{ __('messages.about_vendor_dashboard_step_4_overlay_title') }}</div>
                            </div>
                        </div>
                        <!-- Content -->
                        <div style="flex: 1;">
                            <h3 class="step-title">{{ __('messages.about_vendor_dashboard_step_4_title') }}</h3>
                            <div class="step-content">
                                <p>{{ __('messages.about_vendor_dashboard_step_4_intro_1') }}</p>
                                <p>{{ __('messages.about_vendor_dashboard_step_4_intro_2') }}</p>
                                <ul class="step-list">
                                    <li>{{ __('messages.about_vendor_dashboard_step_4_item_1') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_4_item_2') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_4_item_3') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_4_item_4') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_4_item_5') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_4_item_6') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_4_item_7') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_4_item_8') }}</li>
                                    <li>{{ __('messages.about_vendor_dashboard_step_4_item_9') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Service Provider Section -->
                    <div id="service-provider-section" class="service-provider-grid" style="position: relative; width: 100%; max-width: 1400px; margin: 60px auto; min-height: 1200px; background: transparent; padding: 0px;">
                        
                        <!-- Section Title -->
                        <div style="position: relative; width: 100%; text-align: center; margin-bottom: 0px;">
                    <h2 style="text-align: center; padding:10px; margin-bottom:0px; font-family:Coco Sharp; font-size: clamp(24px, 5.5vw, 48px);  color:var(--primary); background-color:var(--primary-light);">{{ __('messages.about_vendor_dashboard_sp_section_title') }}</h2>
                        </div>

                        <!-- Top Information Sections -->
                        <div style="display: flex;  3px; margin-bottom: 50px; width: 100%;">
                            <!-- Required Information Section -->
                            <div class="usage-info-section" style="flex: 1; background: transparent;  display: flex; flex-direction: column; padding: 30px;">
                                <h4 style="color: #1f1f1fea; font-size: clamp(18px, 3vw, 22px); font-weight: 600; margin-bottom: 15px; margin-top: 0;">{{ __('messages.about_vendor_dashboard_sp_creation_title') }}</h4>
                                <p style="margin-bottom: 5px; font-family:Coco Sharp; color: #383838ea;">{{ __('messages.about_vendor_dashboard_sp_creation_intro_1') }}<br>{{ __('messages.about_vendor_dashboard_sp_creation_intro_2') }}</p>
                                <ul style="list-style: none; padding: 0; font-family:Coco Sharp; margin: 0; font-size: clamp(16px, 2vw, 16px); color: #383838ea; width: 100%; flex: 1; display: flex; flex-direction: column;">
                                    <li style="padding: 15px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.1); display: flex; align-items: center;">
                                        <span style="width: 8px; height: 8px; background: var(--primary); border-radius: 50%; margin-right: 15px; flex-shrink: 0;"></span>
                                        {{ __('messages.about_vendor_dashboard_sp_item_1') }}
                                    </li>
                                    <li style="padding: 15px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.1); display: flex; align-items: center;">
                                        <span style="width: 8px; height: 8px; background: var(--primary); border-radius: 50%; margin-right: 15px; flex-shrink: 0;"></span>
                                        {{ __('messages.about_vendor_dashboard_sp_item_2') }}
                                    </li>
                                    <li style="padding: 15px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.1); display: flex; align-items: center;">
                                        <span style="width: 8px; height: 8px; background: var(--primary); border-radius: 50%; margin-right: 15px; flex-shrink: 0;"></span>
                                        {{ __('messages.about_vendor_dashboard_sp_item_3') }}
                                    </li>
                                    <li style="padding: 15px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.1); display: flex; align-items: center;">
                                        <span style="width: 8px; height: 8px; background: var(--primary); border-radius: 50%; margin-right: 15px; flex-shrink: 0;"></span>
                                        {{ __('messages.about_vendor_dashboard_sp_item_4') }}
                                    </li>
                                     <li style="padding: 15px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.1); display: flex; align-items: center;">
                                        <span style="width: 8px; height: 8px; background: var(--primary); border-radius: 50%; margin-right: 15px; flex-shrink: 0;"></span>
                                        {{ __('messages.about_vendor_dashboard_sp_item_5') }}
                                    </li>
                                       <li style="padding: 15px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.1); display: flex; align-items: center;">
                                        <span style="width: 8px; height: 8px; background: var(--primary); border-radius: 50%; margin-right: 15px; flex-shrink: 0;"></span>
                                        {{ __('messages.about_vendor_dashboard_sp_item_6') }}
                                    </li>
                                </ul>
                            </div>

                            <!-- Service Provider Privileges Section -->
                            <div class="privileges-section" style="flex: 1; display: flex; flex-direction: column;">
                                <h4 style="color: #f8fffd; font-size: clamp(18px, 3vw, 22px); font-weight: 600; margin-bottom: 30px; margin-top: 25px; text-align: center;">{{ __('messages.about_vendor_dashboard_privileges') }}</h4>
                                
                                <!-- Privileges Cards Grid -->
                                <div class="privileges-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; width: 100%; max-width: 800px; margin: 0 auto;">
                                    
                                    <!-- Service Management Card -->
                                    <div class="privilege-card" style="
                                        position: relative;
                                        aspect-ratio: 5/4;
                                        border-radius: 20px;
                                        overflow: hidden;
                                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                        cursor: pointer;
                                        transition: transform 0.3s ease, box-shadow 0.3s ease;
                                        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
                                    " onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 15px 35px rgba(0, 0, 0, 0.25)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 25px rgba(0, 0, 0, 0.15)'">
                                        
                                        <!-- Background Image -->
                                        <div style="
                                            position: absolute;
                                            top: 0;
                                            left: 0;
                                            width: 100%;
                                            height: 100%;
                                            background: url('assets/serviceprev.avif') center/cover;
                                            opacity: 0.8;
                                        ">
                                  
                                    </div>
                                        
                                        <!-- Gradient Overlay -->
                                        <div style="
                                            position: absolute;
                                            top: 0;
                                            left: 0;
                                            width: 100%;
                                            height: 150%;
                                            background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.1) 40%, rgba(0,0,0,0.8) 100%);
                                        "></div>
                                        
                                        <!-- Label Badge -->
                                        <div style="
                                            position: absolute;
                                            top: 16px;
                                            right: 16px;
                                            background: rgba(16, 95, 185, 0.9);
                                            color: white;
                                            padding: 6px 12px;
                                            border-radius: 20px;
                                            font-size: 12px;
                                            font-weight: 600;
                                            backdrop-filter: blur(10px);
                                        ">{{ __('messages.about_vendor_dashboard_badge_management') }}</div>
                                        
                                        <!-- Content -->
                                        <div style="
                                            position: absolute;
                                            bottom: 20px;
                                            left: 20px;
                                            right: 20px;
                                            color: white;
                                        ">
                                            <div style="
                                                font-size: 14px;
                                                opacity: 0.9;
                                                margin-bottom: 8px;
                                                font-weight: 500;
                                            ">{{ __('messages.about_vendor_dashboard_sp_card_1_label') }}</div>
                                            <div style="
                                                font-size: 18px;
                                                font-weight: 700;
                                                line-height: 1.3;
                                            ">{{ __('messages.about_vendor_dashboard_sp_card_1_text') }}</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Booking Management Card -->
                                    <div class="privilege-card" style="
                                        position: relative;
                                        aspect-ratio: 5/4;
                                        border-radius: 20px;
                                        overflow: hidden;
                
                                        cursor: pointer;
                                        transition: transform 0.3s ease, box-shadow 0.3s ease;
                                        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
                                    " onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 15px 35px rgba(0, 0, 0, 0.25)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 25px rgba(0, 0, 0, 0.15)'">
                                        
                                        <!-- Background Image -->
                                        <div style="
                                            position: absolute;
                                            top: 0;
                                            left: 0;
                                            width: 100%;
                                            height: 100%;
                                             background: url('assets/bookingsprev.avif') center/cover;
                                            opacity: 0.8;
                                        "></div>
                                        
                                        <!-- Gradient Overlay -->
                                        <div style="
                                            position: absolute;
                                            top: 0;
                                            left: 0;
                                            width: 100%;
                                            height: 150%;
                                            background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.1) 40%, rgba(0,0,0,0.8) 100%);
                                        "></div>
                                        
                                        <!-- Label Badge -->
                                        <div style="
                                            position: absolute;
                                            top: 16px;
                                            right: 16px;
                                            background: rgba(87, 245, 161, 0.9);
                                            color: white;
                                            padding: 6px 12px;
                                            border-radius: 20px;
                                            font-size: 12px;
                                            font-weight: 600;
                                            backdrop-filter: blur(10px);
                                        ">{{ __('messages.about_vendor_dashboard_badge_bookings') }}</div>
                                        
                                        <!-- Content -->
                                        <div style="
                                            position: absolute;
                                            bottom: 20px;
                                            left: 20px;
                                            right: 20px;
                                            color: white;
                                        ">
                                            <div style="
                                                font-size: 14px;
                                                opacity: 0.9;
                                                margin-bottom: 8px;
                                                font-weight: 500;
                                            ">{{ __('messages.about_vendor_dashboard_sp_card_2_label') }}</div>
                                            <div style="
                                                font-size: 18px;
                                                font-weight: 700;
                                                line-height: 1.3;
                                            ">{{ __('messages.about_vendor_dashboard_sp_card_2_text') }}</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Pricing Management Card -->
                                    <div class="privilege-card" style="
                                        position: relative;
                                        aspect-ratio: 5/4;
                                        border-radius: 20px;
                                        overflow: hidden;
                                        background: linear-gradient(135deg, #fee74f87 0%, #f1fe0063 100%);
                                        cursor: pointer;
                                        transition: transform 0.3s ease, box-shadow 0.3s ease;
                                        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
                                    " onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 15px 35px rgba(0, 0, 0, 0.25)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 25px rgba(0, 0, 0, 0.15)'">
                                        
                                        <!-- Background Image -->
                                        <div style="
                                            position: absolute;
                                            top: 0;
                                            left: 0;
                                            width: 100%;
                                            height: 100%;
                                            background: url('assets/pricingprev.avif') center/cover;
                                            opacity: 0.8;
                                        "></div>
                                        
                                        <!-- Gradient Overlay -->
                                        <div style="
                                            position: absolute;
                                            top: 0;
                                            left: 0;
                                            width: 100%;
                                            height: 150%;
                                            background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.1) 40%, rgba(0,0,0,0.8) 100%);
                                        "></div>
                                        
                                        <!-- Label Badge -->
                                        <div style="
                                            position: absolute;
                                            top: 16px;
                                            right: 16px;
                                            background: rgba(245, 226, 12, 0.9);
                                            color: white;
                                            padding: 6px 12px;
                                            border-radius: 20px;
                                            font-size: 12px;
                                            font-weight: 600;
                                            backdrop-filter: blur(10px);
                                        ">{{ __('messages.about_vendor_dashboard_badge_pricing') }}</div>
                                        
                                        <!-- Content -->
                                        <div style="
                                            position: absolute;
                                            bottom: 20px;
                                            left: 20px;
                                            right: 20px;
                                            color: white;
                                        ">
                                            <div style="
                                                font-size: 14px;
                                                opacity: 0.9;
                                                margin-bottom: 8px;
                                                font-weight: 500;
                                            ">{{ __('messages.about_vendor_dashboard_sp_card_3_label') }}</div>
                                            <div style="
                                                font-size: 18px;
                                                font-weight: 700;
                                                line-height: 1.3;
                                            ">{{ __('messages.about_vendor_dashboard_sp_card_3_text') }}</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Analytics Card -->
                                    <div class="privilege-card" style="
                                        position: relative;
                                        aspect-ratio: 5/4;
                                        border-radius: 20px;
                                        overflow: hidden;
                                        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
                                        cursor: pointer;
                                        transition: transform 0.3s ease, box-shadow 0.3s ease;
                                        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
                                    " onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 15px 35px rgba(0, 0, 0, 0.25)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 25px rgba(0, 0, 0, 0.15)'">
                                        
                                        <!-- Background Image -->
                                        <div style="
                                            position: absolute;
                                            top: 0;
                                            left: 0;
                                            width: 100%;
                                            height: 100%;
                                            background: url('assets/anaprev.avif') center/cover;
                                            opacity: 0.8;
                                        "></div>
                                        
                                        <!-- Gradient Overlay -->
                                        <div style="
                                            position: absolute;
                                            top: 0;
                                            left: 0;
                                            width: 100%;
                                            height: 150%;
                                            background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.1) 40%, rgba(0,0,0,0.8) 100%);
                                        "></div>
                                        
                                        <!-- Label Badge -->
                                        <div style="
                                            position: absolute;
                                            top: 16px;
                                            right: 16px;
                                            background: rgba(250, 112, 154, 0.9);
                                            color: white;
                                            padding: 6px 12px;
                                            border-radius: 20px;
                                            font-size: 12px;
                                            font-weight: 600;
                                            backdrop-filter: blur(10px);
                                        ">{{ __('messages.about_vendor_dashboard_badge_analytics') }}</div>
                                        
                                        <!-- Content -->
                                        <div style="
                                            position: absolute;
                                            bottom: 20px;
                                            left: 20px;
                                            right: 20px;
                                            color: white;
                                        ">
                                            <div style="
                                                font-size: 14px;
                                                opacity: 0.9;
                                                margin-bottom: 8px;
                                                font-weight: 500;
                                            ">{{ __('messages.about_vendor_dashboard_sp_card_4_label') }}</div>
                                            <div style="
                                                font-size: 18px;
                                                font-weight: 700;
                                                line-height: 1.3;
                                            ">{{ __('messages.about_vendor_dashboard_sp_card_4_text') }}</div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>

                        <!-- Cross-Style Media Layout -->
                        <div style="display: flex; flex-direction: column; gap: 40px; width: 100%; align-items: center;">
                            <!-- Video Section (Top of Cross) -->
                            <div class="video-container" style="flex: 1 1 600px; min-width: 280px; max-width: 100%; position: relative;">
                                <div style="aspect-ratio: 16/9; position: relative;">
                                    <video 
                                        style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;" 
                                        controls 
                                        muted 
                                        loop
                                        preload="metadata"
                                    >
                                        <source src="https://firebasestorage.googleapis.com/v0/b/dala3chic-e2b81.firebasestorage.app/o/Service%20Provider%20Demo.mov?alt=media&token=56e6cd73-7dda-458e-9d3a-511c1c1a8821" type="video/mp4">
                                        <div style="display: flex; align-items: center; justify-content: center; height: 100%; background: rgba(255, 255, 255, 0.1); color: white; text-align: center; padding: 20px;">
                                            <div>
                                                <div style="width: 80px; height: 80px; background: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; backdrop-filter: blur(10px);">
                                                    <svg width="32" height="32" viewBox="0 0 24 24" fill="white">
                                                        <path d="M8 5v14l11-7z"/>
                                                    </svg>
                                                </div>
                                                <p style="margin: 0; font-weight: 600;">{{ __('messages.about_vendor_dashboard_sp_tutorial_title') }}</p>
                                                <p style="margin: 5px 0 0; font-size: 14px; opacity: 0.8;">{{ __('messages.about_vendor_dashboard_video_loading') }}</p>
                                            </div>
                                        </div>
                                    </video>
                                </div>
                            </div>
                            
                            <!-- Image Section (Bottom of Cross) -->
                            <div class="image-section" style="width: 100%; max-width: 1200px; height: 600px; min-height: 600px; border-radius: 15px; position: relative; overflow: hidden; box-shadow: 0 8px 25px rgba(255, 255, 255, 0.1);">
                                <!-- Full Size Image -->
                                <div style="position: relative; width: 100%; height: 100%; overflow: hidden;">
                                    <img 
                                        src="/assets/Service provider screenshot.avif" 
                                        alt="{{ __('messages.about_vendor_dashboard_sp_screenshot_alt') }}" 
                                        style="width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.3s ease-out;"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                        onmousemove="this.style.transform = 'scale(1.05) translateY(-' + (event.offsetY * 0.02) + 'px)'"
                                        onmouseleave="this.style.transform = 'scale(1) translateY(0px)'"
                                    >
                                    <div style="display: none; align-items: center; justify-content: center; height: 100%; background: #f9fafb; color: #6b7280; text-align: center; padding: 20px; width: 100%;">
                                        <div>
                                            <svg width="80" height="80" viewBox="0 0 24 24" fill="#d1d5db" style="margin: 0 auto 20px;">
                                                <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2z"/>
                                            </svg>
                                            <p style="margin: 0; font-size: 18px; font-weight: 600;">{{ __('messages.about_vendor_dashboard_sp_screenshot_title') }}</p>
                                            <p style="margin: 10px 0 0; font-size: 14px; opacity: 0.7;">{{ __('messages.about_vendor_dashboard_screenshot_preview') }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Parallax Title Overlay at Bottom -->
                                <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(0, 0, 0, 0.341)); padding: 40px 20px 20px; transform: translateY(0px); transition: transform 0.3s ease-out;" 
                                     onmousemove="this.style.transform = 'translateY(' + (event.offsetY * -0.05) + 'px)'"
                                     onmouseleave="this.style.transform = 'translateY(0px)'">
                                    <h4 style="color: white; font-size: clamp(18px, 3vw, 24px); font-weight: 700; margin: 0; text-align: center; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5); letter-spacing: 1px;">{{ __('messages.about_vendor_dashboard_preview') }}</h4>
                     
                                </div>
                            </div>
                        </div>
                    </div>
                         <!-- Products Manager Section -->
                    <div id="service-provider-section" class="service-provider-grid" style="position: relative; width: 100%; max-width: 1400px; margin: 0px auto; min-height: 1200px; background: transparent; padding: 20px;">
                        
                        <!-- Section Title -->
                        <div id="products-manager-section" style="position: relative; width: 100%; text-align: center; margin-bottom: 0px;">
                       <h2 style="text-align: center; padding:10px; margin-bottom:0px; font-family:Coco Sharp; font-size: clamp(24px, 5.5vw, 48px); color:var(--primary); background-color:var(--primary-light);">{{ __('messages.about_vendor_dashboard_pm_section_title') }}</h2>

                        </div>

                        <!-- Top Information Sections -->
                        <div style="display: flex;  3px; margin-bottom: 50px; width: 100%;">
                            <!-- Required Information Section -->
                            <div class="usage-info-section" style="flex: 1; background: transparent;  display: flex; flex-direction: column; padding: 30px;">
                                <h4 style="color: #1f1f1fea; font-size: clamp(18px, 3vw, 22px); font-weight: 600; margin-bottom: 15px; margin-top: 0;">{{ __('messages.about_vendor_dashboard_pm_creation_title') }}</h4>
                                <p style="margin-bottom: 5px; color: #383838ea; font-family:Coco Sharp;" >{{ __('messages.about_vendor_dashboard_pm_creation_intro_1') }}<br>{{ __('messages.about_vendor_dashboard_pm_creation_intro_2') }}</p>
                                <ul style="list-style: none; padding: 0; font-family:Coco Sharp; margin: 0; font-size: clamp(16px, 2vw, 16px); color: #1f1f1fea; width: 100%; flex: 1; display: flex; flex-direction: column;">
                                    <li style="padding: 15px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.1); display: flex; align-items: center;">
                                        <span style="width: 8px; height: 8px;  background: var(--primary); border-radius: 50%; margin-right: 15px; flex-shrink: 0;"></span>
                                       {{ __('messages.about_vendor_dashboard_pm_item_1') }}
                                    </li>
                                    <li style="padding: 15px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.1); display: flex; align-items: center;">
                                        <span style="width: 8px; height: 8px; background: var(--primary); border-radius: 50%; margin-right: 15px; flex-shrink: 0;"></span>
                                        {{ __('messages.about_vendor_dashboard_pm_item_2') }}
                                    </li>
                                    <li style="padding: 15px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.1); display: flex; align-items: center;">
                                        <span style="width: 8px; height: 8px; background: var(--primary); border-radius: 50%; margin-right: 15px; flex-shrink: 0;"></span>
                                        {{ __('messages.about_vendor_dashboard_pm_item_3') }}
                                    </li>
                                    <li style="padding: 15px 0;  display: flex; align-items: center;">
                                        <span style="width: 8px; height: 8px; background: var(--primary); border-radius: 50%; margin-right: 15px; flex-shrink: 0;"></span>
                                        {{ __('messages.about_vendor_dashboard_pm_item_4') }}
                                    </li>
                                     <li style="padding: 8px 0; background:var(--primary); color:rgba(255, 255, 255, 1); padding-left:10px; border: 1px solid rgba(255, 255, 255, 0.1); border-radius:4px; border-color:f69f3b; display: flex; flex-direction:column; align-items: top; gap:4px;">
                                        <svg style="width: 20px; height: 20px; margin-right: 15px;  flex-shrink: 0;" fill="#f69f3b" viewBox="0 0 24 24">
                                            <path d="M12 2L1 21h22L12 2zm0 3.5L19.5 20h-15L12 5.5zM11 10v4h2v-4h-2zm0 6v2h2v-2h-2z"/>
                                        </svg>
                                        <strong>{{ __('messages.about_vendor_dashboard_important_note') }}</strong>{{ __('messages.about_vendor_dashboard_pm_important_text') }}
                                    </li>
                                      
                                </ul>
                            </div>

                            <!-- Service Provider Privileges Section -->
                            <div class="privileges-section" style="flex: 1; display: flex; flex-direction: column;">
                                <h4 style="color: #f8fffd; font-size: clamp(18px, 3vw, 22px); font-weight: 600; margin-bottom: 35px; margin-top: 25px; text-align: center;">{{ __('messages.about_vendor_dashboard_privileges') }}</h4>
                                
                                <!-- Privileges Cards Grid -->
                                <div class="privileges-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; width: 100%; max-width: 800px; margin: 0 auto;">
                                    
                                    <!-- Service Management Card -->
                                    <div class="privilege-card" style="
                                        position: relative;
                                        aspect-ratio: 5/4;
                                        border-radius: 20px;
                                        overflow: hidden;
                                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                        cursor: pointer;
                                        transition: transform 0.3s ease, box-shadow 0.3s ease;
                                        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
                                    " onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 15px 35px rgba(0, 0, 0, 0.25)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 25px rgba(0, 0, 0, 0.15)'">
                                        
                                        <!-- Background Image -->
                                        <div style="
                                            position: absolute;
                                            top: 0;
                                            left: 0;
                                            width: 100%;
                                            height: 100%;
                                            background: url('assets/managepr.avif') center/cover;
                                            opacity: 0.8;
                                        ">
                                  
                                    </div>
                                        
                                        <!-- Gradient Overlay -->
                                        <div style="
                                            position: absolute;
                                            top: 0;
                                            left: 0;
                                            width: 100%;
                                            height: 150%;
                                            background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.1) 40%, rgba(0,0,0,0.8) 100%);
                                        "></div>
                                        
                                        <!-- Label Badge -->
                                        <div style="
                                            position: absolute;
                                            top: 16px;
                                            right: 16px;
                                            background: rgba(16, 95, 185, 0.9);
                                            color: white;
                                            padding: 6px 12px;
                                            border-radius: 20px;
                                            font-size: 12px;
                                            font-weight: 600;
                                            backdrop-filter: blur(10px);
                                        ">{{ __('messages.about_vendor_dashboard_badge_management') }}</div>
                                        
                                        <!-- Content -->
                                        <div style="
                                            position: absolute;
                                            bottom: 20px;
                                            left: 20px;
                                            right: 20px;
                                            color: white;
                                        ">
                                            <div style="
                                                font-size: 14px;
                                                opacity: 0.9;
                                                margin-bottom: 8px;
                                                font-weight: 500;
                                            ">{{ __('messages.about_vendor_dashboard_pm_card_1_label') }}</div>
                                            <div style="
                                                font-size: 18px;
                                                font-weight: 700;
                                                line-height: 1.3;
                                            ">{{ __('messages.about_vendor_dashboard_pm_card_1_text') }}</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Orders Management Card -->
                                    <div class="privilege-card" style="
                                        position: relative;
                                        aspect-ratio: 5/4;
                                        border-radius: 20px;
                                        overflow: hidden;
                
                                        cursor: pointer;
                                        transition: transform 0.3s ease, box-shadow 0.3s ease;
                                        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
                                    " onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 15px 35px rgba(0, 0, 0, 0.25)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 25px rgba(0, 0, 0, 0.15)'">
                                        
                                        <!-- Background Image -->
                                        <div style="
                                            position: absolute;
                                            top: 0;
                                            left: 0;
                                            width: 100%;
                                            height: 100%;
                                             background: url('assets/ordermanage.avif') center/cover;
                                            opacity: 0.8;
                                        "></div>
                                        
                                        <!-- Gradient Overlay -->
                                        <div style="
                                            position: absolute;
                                            top: 0;
                                            left: 0;
                                            width: 100%;
                                            height: 150%;
                                            background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.1) 40%, rgba(0,0,0,0.8) 100%);
                                        "></div>
                                        
                                        <!-- Label Badge -->
                                        <div style="
                                            position: absolute;
                                            top: 16px;
                                            right: 16px;
                                            background: rgba(87, 245, 161, 0.9);
                                            color: white;
                                            padding: 6px 12px;
                                            border-radius: 20px;
                                            font-size: 12px;
                                            font-weight: 600;
                                            backdrop-filter: blur(10px);
                                        ">{{ __('messages.about_vendor_dashboard_badge_orders') }}</div>
                                        
                                        <!-- Content -->
                                        <div style="
                                            position: absolute;
                                            bottom: 20px;
                                            left: 20px;
                                            right: 20px;
                                            color: white;
                                        ">
                                            <div style="
                                                font-size: 14px;
                                                opacity: 0.9;
                                                margin-bottom: 8px;
                                                font-weight: 500;
                                            ">{{ __('messages.about_vendor_dashboard_pm_card_2_label') }}</div>
                                            <div style="
                                                font-size: 18px;
                                                font-weight: 700;
                                                line-height: 1.3;
                                            ">{{ __('messages.about_vendor_dashboard_pm_card_2_text') }}</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Pricing Management Card -->
                                    <div class="privilege-card" style="
                                        position: relative;
                                        aspect-ratio: 5/4;
                                        border-radius: 20px;
                                        overflow: hidden;
                                        background: linear-gradient(135deg, #fee74f87 0%, #f1fe0063 100%);
                                        cursor: pointer;
                                        transition: transform 0.3s ease, box-shadow 0.3s ease;
                                        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
                                    " onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 15px 35px rgba(0, 0, 0, 0.25)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 25px rgba(0, 0, 0, 0.15)'">
                                        
                                        <!-- Background Image -->
                                        <div style="
                                            position: absolute;
                                            top: 0;
                                            left: 0;
                                            width: 100%;
                                            height: 100%;
                                            background: url('assets/pricingprev.avif') center/cover;
                                            opacity: 0.8;
                                        "></div>
                                        
                                        <!-- Gradient Overlay -->
                                        <div style="
                                            position: absolute;
                                            top: 0;
                                            left: 0;
                                            width: 100%;
                                            height: 150%;
                                            background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.1) 40%, rgba(0,0,0,0.8) 100%);
                                        "></div>
                                        
                                        <!-- Label Badge -->
                                        <div style="
                                            position: absolute;
                                            top: 16px;
                                            right: 16px;
                                            background: rgba(245, 226, 12, 0.9);
                                            color: white;
                                            padding: 6px 12px;
                                            border-radius: 20px;
                                            font-size: 12px;
                                            font-weight: 600;
                                            backdrop-filter: blur(10px);
                                        ">{{ __('messages.about_vendor_dashboard_badge_pricing') }}</div>
                                        
                                        <!-- Content -->
                                        <div style="
                                            position: absolute;
                                            bottom: 20px;
                                            left: 20px;
                                            right: 20px;
                                            color: white;
                                        ">
                                            <div style="
                                                font-size: 14px;
                                                opacity: 0.9;
                                                margin-bottom: 8px;
                                                font-weight: 500;
                                            ">{{ __('messages.about_vendor_dashboard_pm_card_3_label') }}</div>
                                            <div style="
                                                font-size: 18px;
                                                font-weight: 700;
                                                line-height: 1.3;
                                            ">{{ __('messages.about_vendor_dashboard_pm_card_3_text') }}</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Analytics Card -->
                                    <div class="privilege-card" style="
                                        position: relative;
                                        aspect-ratio: 5/4;
                                        border-radius: 20px;
                                        overflow: hidden;
                                        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
                                        cursor: pointer;
                                        transition: transform 0.3s ease, box-shadow 0.3s ease;
                                        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
                                    " onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 15px 35px rgba(0, 0, 0, 0.25)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 25px rgba(0, 0, 0, 0.15)'">
                                        
                                        <!-- Background Image -->
                                        <div style="
                                            position: absolute;
                                            top: 0;
                                            left: 0;
                                            width: 100%;
                                            height: 100%;
                                            background: url('assets/anaprev.avif') center/cover;
                                            opacity: 0.8;
                                        "></div>
                                        
                                        <!-- Gradient Overlay -->
                                        <div style="
                                            position: absolute;
                                            top: 0;
                                            left: 0;
                                            width: 100%;
                                            height: 150%;
                                            background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.1) 40%, rgba(0,0,0,0.8) 100%);
                                        "></div>
                                        
                                        <!-- Label Badge -->
                                        <div style="
                                            position: absolute;
                                            top: 16px;
                                            right: 16px;
                                            background: rgba(250, 112, 154, 0.9);
                                            color: white;
                                            padding: 6px 12px;
                                            border-radius: 20px;
                                            font-size: 12px;
                                            font-weight: 600;
                                            backdrop-filter: blur(10px);
                                        ">{{ __('messages.about_vendor_dashboard_badge_analytics') }}</div>
                                        
                                        <!-- Content -->
                                        <div style="
                                            position: absolute;
                                            bottom: 20px;
                                            left: 20px;
                                            right: 20px;
                                            color: white;
                                        ">
                                            <div style="
                                                font-size: 14px;
                                                opacity: 0.9;
                                                margin-bottom: 8px;
                                                font-weight: 500;
                                            ">{{ __('messages.about_vendor_dashboard_pm_card_4_label') }}</div>
                                            <div style="
                                                font-size: 18px;
                                                font-weight: 700;
                                                line-height: 1.3;
                                            ">{{ __('messages.about_vendor_dashboard_pm_card_4_text') }}</div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>

                        <!-- Cross-Style Media Layout -->
                        <div style="display: flex; flex-direction: column; gap: 40px; width: 100%; align-items: center;">
                            <!-- Video Section (Top of Cross) -->
                            <div class="video-container" style="flex: 1 1 600px; min-width: 280px; max-width: 100%; position: relative;">
                                <div style="aspect-ratio: 16/9; position: relative;">
                                    <video 
                                        style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;" 
                                        controls 
                                        muted 
                                        loop
                                        preload="metadata"
                                    >
                                        <source src="https://firebasestorage.googleapis.com/v0/b/dala3chic-e2b81.firebasestorage.app/o/Product%20Manager%20Demo.mov?alt=media&token=bd2f5a06-d04a-4959-ba74-0f51159519f3" type="video/mp4">
                                        <div style="display: flex; align-items: center; justify-content: center; height: 100%; background: rgba(255, 255, 255, 0.1); color: white; text-align: center; padding: 20px;">
                                            <div>
                                                <div style="width: 80px; height: 80px; background: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; backdrop-filter: blur(10px);">
                                                    <svg width="32" height="32" viewBox="0 0 24 24" fill="white">
                                                        <path d="M8 5v14l11-7z"/>
                                                    </svg>
                                                </div>
                                                <p style="margin: 0; font-weight: 600;">{{ __('messages.about_vendor_dashboard_pm_tutorial_title') }}</p>
                                                <p style="margin: 5px 0 0; font-size: 14px; opacity: 0.8;">{{ __('messages.about_vendor_dashboard_video_loading') }}</p>
                                            </div>
                                        </div>
                                    </video>
                                </div>
                            </div>
                            
                            <!-- Image Section (Bottom of Cross) -->
                            <div class="image-section" style="width: 100%; max-width: 1200px; height: 600px; min-height: 600px; border-radius: 15px; position: relative; overflow: hidden; box-shadow: 0 8px 25px rgba(255, 255, 255, 0.1);">
                                <!-- Full Size Image -->
                                <div style="position: relative; width: 100%; height: 100%; overflow: hidden;">
                                    <img 
                                        src="/assets/products manager screenshot.avif" 
                                        alt="{{ __('messages.about_vendor_dashboard_pm_screenshot_alt') }}" 
                                        style="width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.3s ease-out;"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                        onmousemove="this.style.transform = 'scale(1.05) translateY(-' + (event.offsetY * 0.02) + 'px)'"
                                        onmouseleave="this.style.transform = 'scale(1) translateY(0px)'"
                                    >
                                    <div style="display: none; align-items: center; justify-content: center; height: 100%; background: #f9fafb; color: #6b7280; text-align: center; padding: 20px; width: 100%;">
                                        <div>
                                            <svg width="80" height="80" viewBox="0 0 24 24" fill="#d1d5db" style="margin: 0 auto 20px;">
                                                <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2z"/>
                                            </svg>
                                            <p style="margin: 0; font-size: 18px; font-weight: 600;">{{ __('messages.about_vendor_dashboard_pm_screenshot_title') }}</p>
                                            <p style="margin: 10px 0 0; font-size: 14px; opacity: 0.7;">{{ __('messages.about_vendor_dashboard_screenshot_preview') }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Parallax Title Overlay at Bottom -->
                                <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(0, 0, 0, 0.341)); padding: 40px 20px 20px; transform: translateY(0px); transition: transform 0.3s ease-out;" 
                                     onmousemove="this.style.transform = 'translateY(' + (event.offsetY * -0.05) + 'px)'"
                                     onmouseleave="this.style.transform = 'translateY(0px)'">
                                    <h4 style="color: white; font-size: clamp(18px, 3vw, 24px); font-weight: 700; margin: 0; text-align: center; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5); letter-spacing: 1px;">{{ __('messages.about_vendor_dashboard_preview') }}</h4>
                     
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Responsive Media Queries -->
                    <style>
                        @media (max-width: 768px) {
                            #service-provider-section {
                                height: auto !important;
                                min-height: 1400px !important;
                                padding: 10px !important;
                            }
                            
                            /* Top information sections stack vertically on mobile */
                            #service-provider-section > div:nth-child(2) {
                                flex-direction: column !important;
                                gap: 20px !important;
                            }
                            
                            /* Video container responsive adjustments */
                            #service-provider-section .video-container {
                                flex: 1 1 400px !important;
                                min-width: 250px !important;
                            }
                            
                            /* Image section responsive adjustments */
                            #service-provider-section .image-section {
                                width: 90% !important;
                                max-width: 900px !important;
                                height: 450px !important;
                                min-height: 450px !important;
                            }
                            
                            /* Parallax title adjustments for tablets */
                            #service-provider-section .image-section div[style*="position: absolute"] h4 {
                                font-size: clamp(16px, 4vw, 20px) !important;
                            }
                            
                            #service-provider-section .image-section div[style*="position: absolute"] p {
                                font-size: clamp(11px, 2.5vw, 13px) !important;
                            }
                            
                            /* Reduce gap between cross elements on mobile */
                            #service-provider-section > div:nth-child(3) {
                                gap: 25px !important;
                            }
                            
                            /* Privilege cards responsive adjustments for tablets */
                            .privileges-grid {
                                grid-template-columns: repeat(2, 1fr) !important;
                                gap: 20px !important;
                            }
                            
                            .privilege-card {
                                aspect-ratio: 5/4 !important;
                            }
                            
                            .privilege-card div[style*="bottom: 20px"] div:first-child {
                                font-size: 13px !important;
                            }
                            
                            .privilege-card div[style*="bottom: 20px"] div:last-child {
                                font-size: 18px !important;
                            }
                        }
                        
                        @media (max-width: 480px) {
                            #service-provider-section {
                                margin: 30px auto !important;
                                padding: 5px !important;
                                min-height: 1200px !important;
                            }
                            
                            /* Video container for small screens */
                            #service-provider-section .video-container {
                                flex: 1 1 300px !important;
                                min-width: 200px !important;
                            }
                            
                            /* Image section for small screens */
                            #service-provider-section .image-section {
                                width: 95% !important;
                                height: 350px !important;
                                min-height: 350px !important;
                            }
                            
                            /* Parallax title adjustments for mobile */
                            #service-provider-section .image-section div[style*="position: absolute"] {
                                padding: 30px 15px 15px !important;
                            }
                            
                            #service-provider-section .image-section div[style*="position: absolute"] h4 {
                                font-size: clamp(14px, 5vw, 18px) !important;
                                letter-spacing: 0.5px !important;
                            }
                            
                            #service-provider-section .image-section div[style*="position: absolute"] p {
                                font-size: clamp(10px, 3vw, 12px) !important;
                                margin: 5px 0 0 !important;
                            }
                            
                            /* Information sections adjustments */
                            #service-provider-section .usage-info-section,
                            #service-provider-section .privileges-section {
                                padding: 20px !important;
                                min-height: 180px !important;
                            }
                            
                            #service-provider-section > div:nth-child(3) {
                                gap: 20px !important;
                            }
                            
                            /* Privilege cards responsive adjustments */
                            .privileges-grid {
                                grid-template-columns: 1fr !important;
                                gap: 15px !important;
                            }
                            
                            .privilege-card {
                                aspect-ratio: 5/4 !important;
                            }
                            
                            .privilege-card div[style*="bottom: 20px"] {
                                bottom: 15px !important;
                                left: 15px !important;
                                right: 15px !important;
                            }
                            
                            .privilege-card div[style*="bottom: 20px"] div:first-child {
                                font-size: 12px !important;
                            }
                            
                            .privilege-card div[style*="bottom: 20px"] div:last-child {
                                font-size: 16px !important;
                            }
                            
                            .privilege-card div[style*="top: 16px"] {
                                top: 12px !important;
                                right: 12px !important;
                                padding: 4px 8px !important;
                                font-size: 11px !important;
                            }
                        }
                    </style>
                </div>
            </div>
        </section>

        <section class="merchant-faq-section" id="vendor-faq-section">
            <div class="merchant-faq-wrap">
                <h2 class="merchant-faq-title">{{ __('messages.about_vendor_faq_title') }}</h2>
                <div class="faq-panel" style="padding: 22px;">
                    <h3 class="faq-question">{{ __('messages.about_vendor_faq_q1') }}</h3>
                    <p class="faq-answer" style="padding: 12px 0 0;">
                        {{ __('messages.about_vendor_faq_a1_intro') }}
                    </p>
                    <p class="faq-answer" style="padding: 0;">{{ __('messages.about_vendor_faq_a1_lead') }}</p>
                    <p class="faq-answer" style="padding: 0;">- {{ __('messages.about_vendor_faq_a1_item_1') }}</p>
                    <p class="faq-answer" style="padding: 0;">- {{ __('messages.about_vendor_faq_a1_item_2') }}</p>
                    <p class="faq-answer" style="padding: 0;">- {{ __('messages.about_vendor_faq_a1_item_3') }}</p>
                    <p class="faq-answer" style="padding: 0;">- {{ __('messages.about_vendor_faq_a1_item_4') }}</p>
                    <p class="faq-answer" style="padding: 0;">{{ __('messages.about_vendor_faq_a1_outro') }}</p>
                </div>

                <div class="faq-panel" style="padding: 22px;">
                    <h3 class="faq-question">{{ __('messages.about_vendor_faq_q2') }}</h3>
                    <p class="faq-answer" style="padding: 12px 0 0;">{{ __('messages.about_vendor_faq_a2_lead') }}</p>
                    <p class="faq-answer" style="padding: 0;">- {{ __('messages.about_vendor_faq_a2_item_1_prefix') }} <a href="/register">{{ __('messages.about_vendor_faq_a2_item_1_link') }}</a></p>
                    <p class="faq-answer" style="padding: 0;">- {{ __('messages.about_vendor_faq_a2_item_2') }}</p>
                    <p class="faq-answer" style="padding: 0;">- {{ __('messages.about_vendor_faq_a2_item_3_prefix') }} <a href="/register/vendor">{{ __('messages.about_vendor_faq_a2_item_3_link') }}</a></p>
                    <p class="faq-answer" style="padding: 0;">{{ __('messages.about_vendor_faq_a2_outro') }}</p>
                </div>

                <div class="faq-panel" style="padding: 22px; margin-bottom: 0;">
                    <h3 class="faq-question">{{ __('messages.about_vendor_faq_q3') }}</h3>
                    <p class="faq-answer" style="padding: 12px 0 0;">- {{ __('messages.about_vendor_faq_a3_item_1') }}</p>
                    <p class="faq-answer" style="padding: 0;">- {{ __('messages.about_vendor_faq_a3_item_2') }}</p>
                    <p class="faq-answer" style="padding: 0;">- {{ __('messages.about_vendor_faq_a3_item_3') }}</p>
                    <p class="faq-answer" style="padding: 0;">- {{ __('messages.about_vendor_faq_a3_item_4') }}</p>
                    <p class="faq-answer" style="padding: 0;">- {{ __('messages.about_vendor_faq_a3_item_5') }}</p>
                </div>
            </div>
        </section>
          </main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth scrolling to all anchor links that start with #
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>
</body>
</html>
