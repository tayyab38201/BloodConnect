<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BloodConnect - Save Lives Through Blood Donation</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #dc3545;
            --secondary-color: #ff6b6b;
            --dark-color: #2c3e50;
            --light-color: #ecf0f1;
            --white: #ffffff;
            --gradient: linear-gradient(135deg, #dc3545 0%, #ff6b6b 100%);
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: var(--dark-color);
            overflow-x: hidden;
        }

        /* Navigation */
        .navbar {
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            padding: 1rem 0;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo i {
            color: var(--primary-color);
            -webkit-text-fill-color: var(--primary-color);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
            align-items: center;
        }

        .nav-menu a {
            text-decoration: none;
            color: var(--dark-color);
            font-weight: 500;
            transition: all 0.3s;
            position: relative;
            padding: 0.5rem 0;
        }

        .nav-menu a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary-color);
            transition: width 0.3s;
        }

        .nav-menu a:hover::after {
            width: 100%;
        }

        .nav-menu a:hover {
            color: var(--primary-color);
            transform: translateY(-2px);
        }

        .btn {
            padding: 0.8rem 2rem;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: var(--gradient);
            color: var(--white);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-primary:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(220, 53, 69, 0.5);
        }

        /* Login Button Special Style */
        .nav-menu .btn-primary {
            padding: 0.7rem 1.8rem;
            font-size: 0.95rem;
            border: 2px solid transparent;
        }

        .nav-menu .btn-primary:hover {
            border-color: var(--primary-color);
            background: white;
            color: var(--primary-color);
        }

        .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
            gap: 5px;
        }

        .hamburger span {
            width: 25px;
            height: 3px;
            background: var(--primary-color);
            transition: all 0.3s;
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            padding-top: 80px;
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.88) 0%, rgba(255, 107, 107, 0.85) 100%),
                        url('https://images.unsplash.com/photo-1615461065639-568e0b23e2d2?w=1920&q=80') center/cover fixed;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 30% 50%, rgba(255, 255, 255, 0.15) 0%, transparent 60%);
        }

        .hero-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            width: 100%;
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
        }

        .hero-image {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .hero-image img {
            width: 100%;
            max-width: 500px;
            height: auto;
            border-radius: 20px;
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.4);
            animation: float 3s ease-in-out infinite;
            border: 5px solid rgba(255, 255, 255, 0.3);
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        .hero-text h1 {
            font-size: 3rem;
            color: var(--white);
            margin-bottom: 1.5rem;
            line-height: 1.3;
        }

        .hero-text h1 span {
            color: #ffe66d;
        }

        .hero-text p {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.95);
            margin-bottom: 2rem;
            line-height: 1.7;
            max-width: 600px;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 2rem;
        }

        .btn-light {
            background: var(--white);
            color: var(--primary-color);
            border: 2px solid var(--white);
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.3);
        }

        .btn-light:hover {
            background: transparent;
            color: var(--white);
            border-color: var(--white);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 255, 255, 0.4);
        }

        .hero-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 1.2rem;
            border-radius: 15px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            text-align: center;
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px) scale(1.05);
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.5);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .stat-card h3 {
            font-size: 2rem;
            color: var(--white);
            margin-bottom: 0.5rem;
            line-height: 1;
        }

        .stat-card p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.85rem;
            margin: 0;
        }

        /* Features Section */
        .features {
            padding: 6rem 2rem;
            background: var(--white);
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
            padding: 0 1rem;
        }

        .section-title h2 {
            font-size: 2.2rem;
            color: var(--dark-color);
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        .section-title p {
            color: #7f8c8d;
            font-size: 1.05rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .features-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background: var(--white);
            padding: 2rem 1.5rem;
            border-radius: 20px;
            text-align: center;
            transition: all 0.3s;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border: 2px solid transparent;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            border-color: var(--primary-color);
            box-shadow: 0 15px 40px rgba(220, 53, 69, 0.15);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 1.2rem;
            background: var(--gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: var(--white);
        }

        .feature-card h3 {
            font-size: 1.3rem;
            margin-bottom: 0.8rem;
            color: var(--dark-color);
        }

        .feature-card p {
            color: #7f8c8d;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        /* Blood Types Section */
        .blood-types {
            padding: 6rem 2rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        }

        .blood-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 2rem;
        }

        .blood-card {
            background: linear-gradient(145deg, #ffffff, #f8f9fa);
            padding: 2.5rem 2rem;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            border: 3px solid transparent;
        }

        .blood-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: var(--gradient);
            transform: scaleX(0);
            transition: transform 0.3s;
        }

        .blood-card:hover::before {
            transform: scaleX(1);
        }

        .blood-card:hover {
            transform: translateY(-15px) scale(1.03);
            box-shadow: 0 15px 40px rgba(220, 53, 69, 0.25);
            border-color: var(--primary-color);
            background: linear-gradient(145deg, #fff5f5, #ffffff);
        }

        .blood-type {
            font-size: 3.5rem;
            font-weight: 700;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.8rem;
            display: inline-block;
            position: relative;
        }

        .blood-type::after {
            content: '💉';
            position: absolute;
            top: -10px;
            right: -30px;
            font-size: 1.2rem;
            opacity: 0;
            transition: all 0.3s;
        }

        .blood-card:hover .blood-type::after {
            opacity: 1;
            right: -35px;
        }

        .blood-card p {
            color: #5a6c7d;
            font-size: 0.95rem;
            font-weight: 500;
            margin-top: 0.5rem;
        }

        .blood-card:hover p {
            color: var(--primary-color);
        }

        /* How It Works */
        .how-it-works {
            padding: 6rem 2rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            position: relative;
        }

        .steps-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
        }

        .step {
            text-align: center;
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s;
        }

        .step:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(220, 53, 69, 0.15);
        }

        .step-number {
            width: 70px;
            height: 70px;
            margin: 0 auto 1.5rem;
            background: var(--gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 700;
            color: var(--white);
        }

        .step h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--dark-color);
        }

        .step p {
            color: #7f8c8d;
            line-height: 1.8;
        }

        /* CTA Section */
        .cta {
            padding: 6rem 2rem;
            background: var(--gradient);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .cta h2 {
            font-size: 2.5rem;
            color: var(--white);
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
        }

        .cta p {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2rem;
            position: relative;
            z-index: 1;
        }

        footer {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: var(--white);
            padding: 4rem 2rem 2rem;
            position: relative;
        }

        footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--gradient);
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
            margin-bottom: 2rem;
        }

        .footer-section h3 {
            margin-bottom: 1.5rem;
            color: var(--primary-color);
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .footer-section ul li i {
            color: var(--primary-color);
            width: 18px;
        }

        .footer-section a {
            color: #bdc3c7;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-section a:hover {
            color: var(--white);
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .social-links a {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            font-size: 1.2rem;
            color: white;
        }

        .social-links a.facebook {
            background: #1877f2;
        }

        .social-links a.twitter {
            background: #1da1f2;
        }

        .social-links a.instagram {
            background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
        }

        .social-links a.linkedin {
            background: #0077b5;
        }

        .social-links a.youtube {
            background: #ff0000;
        }

        .social-links a.whatsapp {
            background: #25d366;
        }

        .social-links a:hover {
            transform: translateY(-5px) scale(1.1);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: #7f8c8d;
        }

        /* Scroll to Top Button */
        .scroll-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--gradient);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 5px 20px rgba(220, 53, 69, 0.4);
            transition: all 0.3s;
            z-index: 999;
        }

        .scroll-top:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(220, 53, 69, 0.6);
        }

        .scroll-top.show {
            display: flex;
        }

        /* Loading Animation */
        .loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.5s;
        }

        .loading.hide {
            opacity: 0;
            pointer-events: none;
        }

        .loader {
            width: 60px;
            height: 60px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

            @media (max-width: 968px) {
            .hamburger {
                display: flex;
            }

            .nav-menu {
                position: fixed;
                left: -100%;
                top: 70px;
                flex-direction: column;
                background: var(--white);
                width: 100%;
                text-align: center;
                transition: 0.3s;
                box-shadow: 0 10px 27px rgba(0,0,0,0.05);
                padding: 2rem 0;
            }

            .nav-menu.active {
                left: 0;
            }

            .hero {
                min-height: auto;
                padding: 100px 0 60px;
            }

            .hero-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .hero-image {
                order: -1;
            }

            .hero-image img {
                max-width: 350px;
            }

            .hero-text h1 {
                font-size: 2rem;
            }

            .hero-text p {
                font-size: 1rem;
            }

            .hero-stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 640px) {
            .hero-text h1 {
                font-size: 1.75rem;
            }

            .section-title h2 {
                font-size: 1.75rem;
            }

            .hero-stats {
                grid-template-columns: 1fr;
            }

            .stat-card h3 {
                font-size: 1.75rem;
            }

            .blood-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .hero-image img {
                max-width: 280px;
            }
        }
    </style>
</head>
<body>
    <!-- Loading Screen -->
    <div class="loading" id="loading">
        <div class="loader"></div>
    </div>

    <!-- Scroll to Top Button -->
    <button class="scroll-top" id="scrollTop">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <i class="fas fa-heartbeat"></i>
                BloodConnect
            </div>
            <ul class="nav-menu">
                <li><a href="#home">Home</a></li>
                <li><a href="#features">Features</a></li>
                <li><a href="#how-it-works">How It Works</a></li>
                <li><a href="#blood-types">Blood Types</a></li>
                <li><a href="login.php" class="btn btn-primary">Login</a></li>
            </ul>
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-content">
            <div class="hero-text" data-aos="fade-right">
                <h1>Donate Blood, <span>Save Lives</span></h1>
                <p>Join our community of heroes and make a difference. Connect with blood recipients in real-time and be someone's lifeline.</p>
                <div class="hero-buttons">
                    <a href="register.php" class="btn btn-light">Get Started</a>
                    <a href="#how-it-works" class="btn btn-primary">Learn More</a>
                </div>
                <div class="hero-stats">
                    <div class="stat-card" data-aos="fade-up" data-aos-delay="100">
                        <h3 class="counter" data-target="5000">0</h3>
                        <p>Lives Saved</p>
                    </div>
                    <div class="stat-card" data-aos="fade-up" data-aos-delay="200">
                        <h3 class="counter" data-target="3500">0</h3>
                        <p>Active Donors</p>
                    </div>
                    <div class="stat-card" data-aos="fade-up" data-aos-delay="300">
                        <h3 class="counter" data-target="250">0</h3>
                        <p>Partner Hospitals</p>
                    </div>
                    <div class="stat-card" data-aos="fade-up" data-aos-delay="400">
                        <h3 class="counter" data-target="50">0</h3>
                        <p>Cities Covered</p>
                    </div>
                </div>
            </div>
            <div class="hero-image" data-aos="fade-left" data-aos-delay="200">
                <img src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=800&q=80" alt="Blood Donation - Medical Professional" onerror="this.src='https://images.unsplash.com/photo-1631815589968-fdb09a223b1e?w=800&q=80'">
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="section-title" data-aos="fade-up">
            <h2>Why Choose BloodConnect?</h2>
            <p>Making blood donation simple, efficient, and life-saving</p>
        </div>
        <div class="features-grid">
            <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-icon">
                    <i class="fas fa-search-location"></i>
                </div>
                <h3>Find Donors Nearby</h3>
                <p>Advanced geolocation search to find compatible blood donors in your area instantly.</p>
            </div>
            <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <h3>Real-Time Alerts</h3>
                <p>Get instant notifications for urgent blood requests matching your blood type.</p>
            </div>
            <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Secure & Private</h3>
                <p>Your personal information is protected with enterprise-grade security.</p>
            </div>
            <div class="feature-card" data-aos="fade-up" data-aos-delay="400">
                <div class="feature-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3>Mobile Friendly</h3>
                <p>Access BloodConnect anytime, anywhere from any device.</p>
            </div>
            <div class="feature-card" data-aos="fade-up" data-aos-delay="500">
                <div class="feature-icon">
                    <i class="fas fa-history"></i>
                </div>
                <h3>Donation History</h3>
                <p>Track your donation journey and see the lives you've impacted.</p>
            </div>
            <div class="feature-card" data-aos="fade-up" data-aos-delay="600">
                <div class="feature-icon">
                    <i class="fas fa-hospital"></i>
                </div>
                <h3>Hospital Network</h3>
                <p>Connected with 250+ verified hospitals and blood banks.</p>
            </div>
        </div>
    </section>

    <!-- Blood Types Section -->
    <section class="blood-types" id="blood-types">
        <div class="section-title" data-aos="fade-up">
            <h2>Blood Type Compatibility</h2>
            <p>Know your blood type and who you can help</p>
        </div>
        <div class="blood-grid">
            <div class="blood-card" data-aos="zoom-in" data-aos-delay="100">
                <div class="blood-type">A+</div>
                <p><i class="fas fa-arrow-right"></i> Can donate to A+, AB+</p>
            </div>
            <div class="blood-card" data-aos="zoom-in" data-aos-delay="150">
                <div class="blood-type">A-</div>
                <p><i class="fas fa-arrow-right"></i> Can donate to A+, A-, AB+, AB-</p>
            </div>
            <div class="blood-card" data-aos="zoom-in" data-aos-delay="200">
                <div class="blood-type">B+</div>
                <p><i class="fas fa-arrow-right"></i> Can donate to B+, AB+</p>
            </div>
            <div class="blood-card" data-aos="zoom-in" data-aos-delay="250">
                <div class="blood-type">B-</div>
                <p><i class="fas fa-arrow-right"></i> Can donate to B+, B-, AB+, AB-</p>
            </div>
            <div class="blood-card" data-aos="zoom-in" data-aos-delay="300">
                <div class="blood-type">AB+</div>
                <p><i class="fas fa-star"></i> Universal Receiver</p>
            </div>
            <div class="blood-card" data-aos="zoom-in" data-aos-delay="350">
                <div class="blood-type">AB-</div>
                <p><i class="fas fa-arrow-right"></i> Can donate to AB+, AB-</p>
            </div>
            <div class="blood-card" data-aos="zoom-in" data-aos-delay="400">
                <div class="blood-type">O+</div>
                <p><i class="fas fa-arrow-right"></i> Can donate to A+, B+, AB+, O+</p>
            </div>
            <div class="blood-card" data-aos="zoom-in" data-aos-delay="450">
                <div class="blood-type">O-</div>
                <p><i class="fas fa-star"></i> Universal Donor</p>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works" id="how-it-works">
        <div class="section-title" data-aos="fade-up">
            <h2>How It Works</h2>
            <p>Simple steps to save a life</p>
        </div>
        <div class="steps-container">
            <div class="step" data-aos="fade-up" data-aos-delay="100">
                <div class="step-number">1</div>
                <h3>Register</h3>
                <p>Create your account with basic information and blood type details.</p>
            </div>
            <div class="step" data-aos="fade-up" data-aos-delay="200">
                <div class="step-number">2</div>
                <h3>Get Verified</h3>
                <p>Complete your profile and verify your contact information.</p>
            </div>
            <div class="step" data-aos="fade-up" data-aos-delay="300">
                <div class="step-number">3</div>
                <h3>Receive Alerts</h3>
                <p>Get notified when someone nearby needs your blood type.</p>
            </div>
            <div class="step" data-aos="fade-up" data-aos-delay="400">
                <div class="step-number">4</div>
                <h3>Donate & Save</h3>
                <p>Visit the hospital and donate blood to save a life.</p>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <h2 data-aos="fade-up">Ready to Make a Difference?</h2>
        <p data-aos="fade-up" data-aos-delay="100">Join thousands of donors who are saving lives every day</p>
        <a href="register.php" class="btn btn-light" data-aos="fade-up" data-aos-delay="200">Register Now</a>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3><i class="fas fa-heartbeat"></i> BloodConnect</h3>
                <p>Connecting donors with recipients to save lives across the nation.</p>
                <div class="social-links">
                    <a href="https://facebook.com" target="_blank" class="facebook" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com" target="_blank" class="twitter" title="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://instagram.com" target="_blank" class="instagram" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://linkedin.com" target="_blank" class="linkedin" title="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="https://youtube.com" target="_blank" class="youtube" title="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="https://wa.me/1234567890" target="_blank" class="whatsapp" title="WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><i class="fas fa-angle-right"></i><a href="#home">Home</a></li>
                    <li><i class="fas fa-angle-right"></i><a href="#features">Features</a></li>
                    <li><i class="fas fa-angle-right"></i><a href="#how-it-works">How It Works</a></li>
                    <li><i class="fas fa-angle-right"></i><a href="register.php">Register</a></li>
                    <li><i class="fas fa-angle-right"></i><a href="login.php">Login</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Resources</h3>
                <ul>
                    <li><i class="fas fa-book"></i><a href="#">Donation Guidelines</a></li>
                    <li><i class="fas fa-info-circle"></i><a href="#">Blood Type Guide</a></li>
                    <li><i class="fas fa-question-circle"></i><a href="#">FAQs</a></li>
                    <li><i class="fas fa-newspaper"></i><a href="#">Blog</a></li>
                    <li><i class="fas fa-file-alt"></i><a href="#">Privacy Policy</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contact Us</h3>
                <ul>
                    <li><i class="fas fa-envelope"></i><a href="mailto:info@bloodconnect.org">info@bloodconnect.org</a></li>
                    <li><i class="fas fa-phone"></i><a href="tel:+15551234567">+1 (555) 123-4567</a></li>
                    <li><i class="fas fa-map-marker-alt"></i><span>123 Health Street, Medical City</span></li>
                    <li><i class="fas fa-clock"></i><span>24/7 Support Available</span></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 BloodConnect. All rights reserved. | <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    
    <script>
        // Loading screen
        $(window).on('load', function() {
            $('#loading').addClass('hide');
        });

        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true
        });

        // Navbar scroll effect
        $(window).scroll(function() {
            if ($(window).scrollTop() > 50) {
                $('.navbar').addClass('scrolled');
            } else {
                $('.navbar').removeClass('scrolled');
            }

            // Scroll to top button
            if ($(window).scrollTop() > 300) {
                $('#scrollTop').addClass('show');
            } else {
                $('#scrollTop').removeClass('show');
            }
        });

        // Scroll to top functionality
        $('#scrollTop').click(function() {
            $('html, body').animate({
                scrollTop: 0
            }, 800);
        });

        // Mobile menu
        $('.hamburger').click(function() {
            $('.nav-menu').toggleClass('active');
        });

        $('.nav-menu a').click(function() {
            $('.nav-menu').removeClass('active');
        });

        // Smooth scrolling
        $('a[href^="#"]').click(function(e) {
            e.preventDefault();
            var target = $($(this).attr('href'));
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 70
                }, 1000);
            }
        });

        // Counter animation
        function animateCounter() {
            $('.counter').each(function() {
                var $this = $(this);
                var countTo = $this.attr('data-target');
                $({ countNum: 0 }).animate({
                    countNum: countTo
                }, {
                    duration: 2000,
                    easing: 'swing',
                    step: function() {
                        $this.text(Math.floor(this.countNum).toLocaleString());
                    },
                    complete: function() {
                        $this.text(this.countNum.toLocaleString());
                    }
                });
            });
        }

        var counterAnimated = false;
        $(window).scroll(function() {
            var heroBottom = $('.hero').offset().top + $('.hero').outerHeight();
            var scrollPos = $(window).scrollTop() + $(window).height();
            
            if (scrollPos > heroBottom && !counterAnimated) {
                animateCounter();
                counterAnimated = true;
            }
        });
    </script>
</body>
</html>