<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Page - Features</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 120px 20px;
            text-align: center;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .hero p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.95;
        }

        .hero .cta-button {
            display: inline-block;
            padding: 14px 32px;
            background: white;
            color: #667eea;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .hero .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .features {
            padding: 80px 20px;
            background: #f8f9fa;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .features-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .features-header h2 {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 1rem;
        }

        .features-header p {
            font-size: 1.2rem;
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            margin-top: 60px;
        }

        .feature-card {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 20px;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 12px;
        }

        .feature-card p {
            color: #666;
            line-height: 1.7;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1.1rem;
            }

            .features-header h2 {
                font-size: 2rem;
            }

            .features-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }
        }
    </style>
</head>

<body>
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Welcome to Our Platform</h1>
            <p>Discover amazing features that will transform your workflow and boost your productivity</p>
            <a href="#features" class="cta-button">Explore Features</a>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <div class="features-header">
                <h2>Our Features</h2>
                <p>Everything you need to succeed, all in one place</p>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🚀</div>
                    <h3>Fast Performance</h3>
                    <p>Lightning-fast loading times and optimized performance ensure your users have the best experience
                        possible.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">🔒</div>
                    <h3>Secure & Safe</h3>
                    <p>Enterprise-grade security measures protect your data and keep your information safe from threats.
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">📱</div>
                    <h3>Mobile Responsive</h3>
                    <p>Beautiful design that works seamlessly across all devices, from desktop to mobile and tablet.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3>Easy Integration</h3>
                    <p>Simple APIs and comprehensive documentation make it easy to integrate with your existing tools.
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3>Analytics Dashboard</h3>
                    <p>Get insights into your data with powerful analytics and customizable reporting dashboards.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">🎨</div>
                    <h3>Customizable</h3>
                    <p>Tailor the platform to your needs with extensive customization options and flexible
                        configurations.</p>
                </div>
            </div>
        </div>
    </section>
    {{-- <div id="was-widget-container">
        <style>
            .was-button-container {
                background: green;
                cursor: pointer;
                width: 64px;
                height: 64px;
                margin: 20px;
                position: absolute;
                bottom: 0;
                border-radius: 32px;
                display: inline-block !important
            }

            .was-icon-button {
                width: auto !important;
                height: auto !important;
                line-height: auto !important;
                font-size: 16px;
                padding: 10px;
                color: #fff
            }

            #was-icon {
                height: 42px;
                width: 42px;
                margin: 11px
            }

            #was-icon-button-icon {
                height: 14px;
                padding-right: 4px
            }

            #was-agents-widget-container {
                height: 500px;
                width: 300px;
                background: #fff;
                border-radius: 10px;
                box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.1);
                position: absolute;
                bottom: 0;
                right: 0;
                margin-bottom: 90px;
                margin-right: 20px;
                z-index: 1000;
            }

            .was-agents-header {
                padding: 14px;
                border-radius: 10px 10px 0 0;
            }

            .was-agents-header-title {
                font-size: 1.2rem;
                font-weight: 600;
            }

            .was-agents-header-description {
                font-size: 0.9rem;
            }

            .was-agents-header-close {
                font-size: 1.2rem;
                font-weight: 600;
                cursor: pointer;
            }

            .was-agent-item-avatar {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                overflow: hidden;
                margin-right: 10px;
            }

            .was-agent-item-avatar img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .was-agent-item {
                display: flex;
                align-items: center;
                padding: 10px;
                border-bottom: 1px solid #e0e0e0;
            }

            .was-agent-item-info {
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .was-agent-item-name {
                font-size: 1.2rem;
            }

            .was-agent-item-role {
                color: #666;
            }
        </style>
        <div class="was-button-container" style="background: #42D74C;; right: 0;margin: 20px;">
            <div id="was-icon"
                style="background: #FFFFFF; -webkit-mask-image: url(https://cdn.shopify.com/s/files/1/0460/1839/6328/files/waiconmask.svg?v=1623288530); -webkit-mask-size: cover;">
            </div>
        </div>
        <div id="was-agents-widget-container">
            <div class="was-agents-header" style="background:#42D74C; color: #fff">
                <div class="was-agents-header-title">Chat with us</div>
                <div class="was-agents-header-description">We're here to help you with your questions and concerns.
                </div>
            </div>
            <div class="was-agents-list">
                <div class="was-agent-item">
                    <div class="was-agent-item-avatar">
                        <img src="/images/male-avatar.png" alt="Avatar">
                    </div>
                    <div class="was-agent-item-info">
                        <div class="was-agent-item-name">John Doe</div>
                        <div class="was-agent-item-role">Customer Support</div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    <script type="text/javascript" src="{{ config('app.url') . '/js/wa-chat.js?shop=msm5-2.myshopify.com' }}">
    </script>
</body>

</html>