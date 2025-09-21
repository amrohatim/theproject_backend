<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About | Bootcamp - Dala3Chic Merchant</title>
    <link href="https://fonts.googleapis.com/css2?family=Jersey+15&display=swap" rel="stylesheet">
    <link rel="icon" sizes="192x192" href="https://www.wix.com/favicon.ico" type="image/x-icon">
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
            background-color: rgba(0, 0, 0, 0.9);
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
            margin-top: 75px;
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
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
            gap: 0;
            min-height: 100vh;
            width: 100%;
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

        /* Top Left - Woman Image */
        .hero-top-left {
            background: rgba(25, 25, 25, 0.9);
            text-align: center;
            padding: 0;
            overflow: hidden;
        }

        .woman-image {
            width: 100%;
            height: 100%;
            background: url('/app images/medium-shot-smiley-woman-sewing-with-machine.jpg');
            background-size: cover;
            background-position: center;
            border: none;
            border-radius: 0;
            box-shadow: none;
        }

        /* Top Right - Dala3Chic Description */
        .hero-top-right {
            background: #f59e0b;
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
            background: #f59e0b;
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
            background: #d97706;
            text-align: center;
            padding: 0;
            overflow: hidden;
        }

        .dashboard-image {
            width: 100%;
            height: 100%;
            background:  #d97706;
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
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
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
            box-shadow: 0 4px 20px rgba(245, 158, 11, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(245, 158, 11, 0.4);
            background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
        }

        .arrow-icon {
            width: 20px;
            height: 20px;
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
            background: #d97706;
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
            background: #d97706;
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
            background-color: #f59e0b;
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
            background-color: #f59e0b;
        }

        .feature-item:nth-child(even) {
            background-color: #c07901;
        }

        .feature-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: #d97706;
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .feature-item:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 30px 80px rgba(237, 88, 41, 0.2);
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
            background: #d97706;
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
            color: #ED5829;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            margin-top: auto;
            padding-top: 1rem;
        }

        .feature-link:hover {
            color: #d97706;
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
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
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
            color: #ED5829;
            text-decoration: none;
            font-weight: 500;
        }

        /* Dashboard Usage Section */
        .dashboard-section {
            padding: 80px 0;
            background: rgba(20, 20, 20, 0.95);
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
            color: #ffffff;
        }

        .step-content {
            font-size: 14px;
            line-height: 1.6;
            color: #c0c0c0;
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
            color: #d97706;
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
                 grid-template-columns: 1fr;
                 grid-template-rows: auto auto auto auto;
                 gap: 20px;
                 min-height: auto;
                 padding: 20px 0;
             }

             .hero-grid-item {
                 padding: 20px;
                 min-height: 250px;
             }

             .woman-image,
             .dashboard-image {
                 min-height: 200px;
             }

             .dashboard-image img {
                 width: 100%;
                 height: auto;
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
             .hero-grid-item {
                 padding: 15px;
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
    </style>
</head>
<body>
    <!-- Wix Banner -->
    <div class="wix-banner">
        <a href="https://www.wix.com/lpviral/enviral?utm_campaign=vir_wixad_live&adsVersion=banner_2024" target="_blank">
            <svg class="wix-logo" viewBox="0 0 39 15" fill="white">
                 <path d="M38.9999 0H36.8456C36.248 0 35.6894 0.280264 35.3538 0.74896L32.4728 4.77402C32.396 4.88076 32.2297 4.88076 32.1529 4.77402L29.2719 0.74896C28.9369 0.280264 28.3777 0 27.7801 0H25.6258L30.7581 7.16998L25.6541 14.3H27.8084C28.406 14.3 28.9646 14.0197 29.3002 13.551L32.1529 9.56593C32.2297 9.45919 32.396 9.45919 32.4728 9.56593L35.3254 13.551C35.6604 14.0197 36.2196 14.3 36.8172 14.3H38.9715L33.8676 7.16998L38.9999 0Z"/>
                 <path d="M21.193 1.46274V14.3H22.2226C23.0758 14.3 23.7673 13.6453 23.7673 12.8373V0H22.7377C21.8844 0 21.193 0.654743 21.193 1.46274Z"/>
                 <path d="M19.3054 0H18.3892C17.3804 0 16.5063 0.660706 16.2828 1.59214L14.233 10.1259L12.4389 2.15982C12.1071 0.68754 10.512 -0.305308 8.87973 0.121646C7.84005 0.393561 7.06927 1.22541 6.84446 2.22422L5.06864 10.1151L3.02267 1.59273C2.79849 0.661302 1.92443 0 0.915617 0H0L3.43388 14.2994H4.73489C5.91562 14.2994 6.93577 13.5176 7.18199 12.424L9.37721 2.66787C9.40429 2.54682 9.5189 2.45916 9.64925 2.45916C9.7796 2.45916 9.89421 2.54682 9.92129 2.66787L12.1184 12.4246C12.3646 13.5182 13.3848 14.2994 14.5655 14.2994H15.8709L19.3054 0Z"/>
             </svg>
             This website was built on Wix. Create yours today.
             <span class="wix-get-started">Get Started</span>
        </a>
    </div>

    <!-- Header -->
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
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Hero Section -->
         <section class="hero-section">
             <div class="hero-content">
                     <!-- Top Left: Woman Image -->
                     <div class="hero-grid-item hero-top-left">
                         
                            <img src="{{ asset('assets/medium-shot-smiley-woman-sewing-with-machine.jpg') }}" height="100%" width="1280" alt="Woman Image">
                         
                     </div>

                     <!-- Top Right: Dala3Chic Merchant Description -->
                     <div class="hero-grid-item hero-top-right">
                        <div class="black-circle">
                            <h1 class="merchant-title">Dala3Chic Merchant</h1>
                            <p class="merchant-description">
                                Join our Merchant community and start your journey with us, access a wide range of customers across the <strong style="color:#feb538; font-weight:300">United Arab Emirates. </strong> <br> Harness the power of marketing with our ready to go Dashboard and App.
                            </p>
                            <button class="cta-button" style="border-radius: 90px; padding: 10px 20px;" >
                                Start Now <p style="font-size: 1.5em;">&rarr;</p>
                            </button>
                        </div>
                     </div>

                     <!-- Bottom Left: Vision Section -->
                     <div class="hero-grid-item hero-bottom-left">
                         <h2 class="vision-title">Vision</h2>
                         <p class="vision-description">
                             This is a Paragraph. Click on "Edit Text" or double click on the text box to start editing the content and make sure to add any relevant details or information that you want to share with your visitors.
                         </p>
                     </div>

                     <!-- Bottom Right: Dashboard Image -->
                     <div class="hero-grid-item hero-bottom-right">
                         <div class="dashboard-image">
                             <img src="{{ asset('assets/MerchantDashboard.png') }}" alt="Dashboard Image">
                         </div>
                     </div>
              </div>
          </section>

        

        <!-- Features Section -->
        <section class="features-section">
            <div class="container">
                <div class="features-row-layout">
                    <div class="features-left-column">
                        <div class="features-header">
                            <h2 class="features-title">Features</h2>
                            <p class="features-description">
                                Our Merchant package offer multiple features to facilitate the process of selling and managing your inventory, it also provide you with door-to-door free delivery to allow you access customers all over the United Arab Emirates. You may also want to serve customer at your place and for this purpose we offer you a mini store location to allow customers to reach you and benefit from your services.
                            </p>
                        </div>
                    </div>
                    
                    <div class="features-right-column">
                        <h3 class="what-you-get-title">What you will get</h3>
                        <div class="features-grid">
                    <div class="feature-item">
                        <p class="feature-number">01</p>
                        <p class="feature-text">Nationwide, door-to-door delivery at no extra cost — reach customers in every emirate with complimentary delivery that removes the logistics headache and helps you compete on convenience.</p>
                    </div>
                    <div class="feature-item">
                        <p class="feature-number">02</p>
                        <p class="feature-text">Flexible fulfilment choices — offer home delivery, in-store pickup, or scheduled service visits so your customers can buy the way they want.</p>
                    </div>
                    <div class="feature-item">
                        <p class="feature-number">03</p>
                        <p class="feature-text">Promotions & visibility tools — built-in features to run discounts, highlight bestsellers, and appear in local discovery so new customers can find you faster.</p>
                    </div>
                    <div class="feature-item">
                        <p class="feature-number">04</p>
                        <p class="feature-text">Easy inventory & order management — add, update, and track products and bookings in real time from a clean, intuitive dashboard so you always know what's in stock and what's selling.</p>
                    </div>
                    <div class="feature-item">
                        <p class="feature-number">05</p>
                        <p class="feature-text">Mini-store location option — prefer to welcome customers in person? Reserve a compact, branded mini-store space where people can see your products, book services, or pick up orders — a perfect bridge between online reach and face-to-face service.</p>
                     </div>
                         </div>
                     </div>
                 </div>

                <!-- Service Cards -->
                <div class="services-grid">
                    <div class="service-card">
                        <div class="service-image">
                            <img src="{{ asset('assets/dashboardfeature.jpg') }}" alt="Dashboard Feature" style="width: 100%; height: 100%; object-fit: cover; ">
                        </div>
                        <div class="service-content">
                            <h4 class="service-title">Dashboard</h4>
                            <img src="{{ asset('assets/dashboardicon.png') }}" alt="Dashboard Icon" style="width: 30px; text-align: left; display: block; margin-bottom: 10px;">
                            <p class="service-description">
                                Our Merchant dashboard is offering off-the-shelf features customized for individual Merchant. <br>Easy manage your stock, add products, add services update order status, print invoices, and more.
                            </p>
                            <a href="#" class="read-more">Read More</a>
                        </div>
                    </div>
                    <div class="service-card">
                        <div class="service-image">
                            <img src="{{ asset('assets/deliveryfeature.jpg') }}" alt="Dashboard Feature" style="width: 100%; height: 100%; object-fit: cover; ">
                        </div>
                        <div class="service-content">
                            <h4 class="service-title">Free Delivery</h4>
                            <img src="{{ asset('assets/delivery-truckicon.png') }}" alt="Delivery Truck Icon" style="width: 30px; text-align: left; display: block; margin-bottom: 10px;">
                            <p class="service-description">
                                Make it possible to reach all customers all over the United Arab Emirates. <br>Our thirty-party services allow to ship your products faster and expand your business geographically.
                            </p>
                            <a href="#" class="read-more">Read More</a>
                        </div>
                    </div>
                    <div class="service-card">
                        <div class="service-image">
                            <img src="{{ asset('assets/dealfeature.jpg') }}" alt="Dashboard Feature" style="width: 100%; height: 100%; object-fit: cover; ">
                        </div>
                        <div class="service-content">
                            <h4 class="service-title">Deals</h4>
                            <img src="{{ asset('assets/discounticon.png') }}" alt="Discount Icon" style="width: 30px; text-align: left; display: block; margin-bottom: 10px;">
                            <p class="service-description">
                                Add and manage your offers easily and quickly with the merchant dashboard to grow you customers satisfaction. <br>Set deal's percentage, timespan, specific products and services.
                            </p>
                            <a href="#" class="read-more">Read More</a>
                        </div>
                    </div>
                    <div class="service-card">
                        <div class="service-image">
                            <img src="{{ asset('assets/ministorefeature.jpg') }}" alt="Dashboard Feature" style="width: 100%; height: 100%; object-fit: cover; ">
                        </div>
                        <div class="service-content">
                            <h4 class="service-title">Mini Store</h4>
                            <img src="{{ asset('assets/shopicon.png') }}" alt="Shop Icon" style="width: 30px; text-align: left; display: block; margin-bottom: 10px;">
                            <p class="service-description">
                                Expose your mini-store for serving and pickups perfect for merchants who want both reach and relationship. <br>Our mini store feature gives you the power to grow your local reputation.
                            </p>
                            <a href="#" class="read-more">Read More</a>
                        </div>
                    </div>
                </div>

                <!-- Deals Section -->
                <!-- Cross Grid Layout Container -->
                <div class="cross-grid-container" style="position: relative; width: 100%; max-width: 2000px; margin: 60px auto; height: 800px;">
                    <!-- Top-Left Deals Section -->
                    <div class="deals-section" style="position: absolute; top: 0; left: 0; width: 60%; height: 60%; padding: 60px; background: linear-gradient(135deg, #000000 0%, #000000 100%); border-radius: 15px; color: white; display: flex; flex-direction: column; justify-content: center; align-items: flex-start; z-index: 2;">
                        <div class="deals-content" style="text-align: left;">
                            <h3 style="font-size: 28px; margin-bottom: 15px; margin-top: 0;">Reach customers with your discounts</h3>
                            <h4 style="font-size: 24px; margin-bottom: 15px; margin-top: 0;">Deals and Discounts</h4>
                            <p style="margin-bottom: 20px;">Easily manage and add deals and discounts for the selected products or services within your customised timespan</p>
                            <button class="cta-button" style="background: #d97706; color: #fff; padding: 12px 24px; border: none; border-radius: 2px; font-weight: bold; cursor: pointer;">Create one now</button>
                        </div>
                    </div>
                    
                    <!-- Bottom-Right Image Section -->
                    <div class="deals-image-section" style="position: absolute; bottom: 0; right: 0; width: 60%; height: 60%;  background: rgba(0, 0, 0, 0.95); border-radius: 15px; display: flex; justify-content: center; align-items: center; z-index: 2; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);">
                        <img src="/assets/Untitled – Figma.jpeg" alt="Deals illustration" style="width: 100%; height: 100%;  object-fit: cover; border-radius: 8px;">
                    </div>
                    
                   
                </div>

                <!-- Payment Section -->
                <div style="text-align: center; margin: 60px 0; padding: 40px; background: #f8f9fa; border-radius: 15px;">
                    <h3 style="font-size: 24px; margin-bottom: 15px; color: #333;">Payment</h3>
                    <p style="color: #666; margin-bottom: 20px;">
                        Receive your payments securely and promptly. Funds from each transaction are transferred directly to your registered bank account, and we supply comprehensive revenue analysis and reporting so you can easily track performance and growth.
                    </p>
                    <button class="cta-button">Learn More</button>
                </div>
            </div>
        </section>

        <!-- Dashboard Usage Section -->
        <section class="dashboard-section">
            <div class="container">
                <div class="dashboard-content">
                    <h2 style="text-align: center; font-size: clamp(22px, 6vw, 36px); margin-bottom: clamp(20px, 6vw, 40px);">Dashboard Usage</h2>
                    
                    <div class="usage-step" style="display: flex; align-items: flex-start; flex-wrap: wrap; gap: clamp(12px, 4vw, 40px); margin-bottom: clamp(24px, 8vw, 60px);">
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
                                            <p style="margin: 0; font-weight: 600;">Product Creation Tutorial</p>
                                            <p style="margin: 5px 0 0; font-size: 14px; opacity: 0.8;">Video loading...</p>
                                        </div>
                                    </div>
                                </video>
                                <div style="position: absolute; bottom: 20px; left: 20px; color: white; font-weight: 600; font-size: 18px; text-shadow: 2px 2px 4px rgba(0,0,0,0.7); z-index: 10;">Product Creation Tutorial</div>
                            </div>
                        </div>
                        <!-- Content -->
                        <div style="flex: 1;">
                            <h3 class="step-title">1 / Product creation</h3>
                            <div class="step-content">
                                <p>From dashboard's sidebar click on products tap then click on "Add new product" button.</p>
                                <p>You should enter these required information in order to create product:</p>
                                <ul class="step-list">
                                    <li>Name (English & Arabic)</li>
                                    <li>Category</li>
                                    <li>Price (the real price that will appear to customers)</li>
                                    <li>Original Price (the price that will be shown with strikethrough style, normally bigger than the price)</li>
                                    <li>Stock (the product will be out-stock if the stock value equal to zero)</li>
                                    <li>Description optional but if you enter it you should write both Arabic and English versions</li>
                                </ul>
                                <p>On the colours and images tap you should enter the following:</p>
                                <ul class="step-list">
                                    <li>Select at least one colour for the product</li>
                                    <li>Enter the stock for that colour</li>
                                    <li>Add one associated image for the colour, you should upload a high-quality image</li>
                                    <li>Enter all sizes that apply to this colour option</li>
                                    <li>Specifications section is optional</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="usage-step" style="display: flex; align-items: flex-start; flex-wrap: wrap; gap: clamp(12px, 4vw, 40px); margin-bottom: clamp(24px, 8vw, 60px);">
                        <!-- Video Box -->
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
                        <!-- Content -->
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
                    </div>

                    <div class="usage-step" style="display: flex; align-items: flex-start; flex-wrap: wrap; gap: clamp(12px, 4vw, 40px); margin-bottom: clamp(24px, 8vw, 60px);">
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
                                            <p style="margin: 0; font-weight: 600;">Deal Creation Tutorial</p>
                                            <p style="margin: 5px 0 0; font-size: 14px; opacity: 0.8;">Video loading...</p>
                                        </div>
                                    </div>
                                </video>
                                <div style="position: absolute; bottom: 20px; left: 20px; color: white; font-weight: 600; font-size: 18px; text-shadow: 2px 2px 4px rgba(0,0,0,0.7); z-index: 10;">Deal Creation Tutorial</div>
                            </div>
                        </div>
                        <!-- Content -->
                        <div style="flex: 1;">
                            <h3 class="step-title">3 / Deals creation</h3>
                            <div class="step-content">
                                <p>From dashboard's sidebar click on services tap then click on "create deal" or the plus button.</p>
                                <p>After deal creation page opens enter the related deal information:</p>
                                <ul class="step-list">
                                    <li>Title (English & Arabic)</li>
                                    <li>Discount Percentage (1-100%) the percentage deducted from the price</li>
                                    <li>Description (Optional: If you enter a description in one language, you must enter it in both languages)</li>
                                    <li>Promotional Message</li>
                                    <li>Start Date when the deal will be applied to the items</li>
                                    <li>End Date after this date all products/services will be reset to their normal price</li>
                                    <li>Deal image</li>
                                    <li>Deal Status can be either active or inactive</li>
                                    <li>Select the product/services that you want to apply deal to</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2023 by Bootcamp. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Add smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
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

        // Add hover effects to service cards
        document.querySelectorAll('.service-card, .feature-item').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px)';
                this.style.boxShadow = '0 15px 30px rgba(0,0,0,0.2)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 5px 20px rgba(0,0,0,0.1)';
            });
        });

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