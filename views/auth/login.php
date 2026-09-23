<?php
/**
 * PayTrack — Complete Scrollable Modern SaaS Landing Page
 * Matches all sections from the provided UI image reference
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayTrack — Manage School Fees</title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/auth.css">
</head>
<body class="landing-body">

    <!-- ── 1. Top Navigation Bar ── -->
    <header class="landing-header">
        <div class="container" style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
            <a href="#" class="landing-brand">
                <div class="brand-badge">A</div>
                <span class="brand-name">PayTrack</span>
            </a>

            <!-- Desktop Navigation Menu -->
            <ul class="nav-menu desktop-nav-menu">
                <li><a href="#home" class="nav-link active">Home</a></li>
                <li><a href="#features" class="nav-link">Features</a></li>
                <li><a href="#how-it-works" class="nav-link">How It Works</a></li>
                <li><a href="#about" class="nav-link">About</a></li>
            </ul>

            <!-- Desktop Button & Mobile Hamburger Toggle -->
            <div style="display: flex; align-items: center; gap: 10px;">
                <button class="btn-header-login btn-desktop-login" id="btnHeaderLogin">
                    <svg class="btn-ic" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                    <span>Login / Portal</span>
                    <svg class="btn-ic-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </button>
                <button class="nav-hamburger" id="btnNavToggle" aria-label="Open navigation menu">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </header>

    <!-- ── Mobile Navigation Drawer & Backdrop (Independent Stacking) ── -->
    <div class="mobile-drawer-overlay" id="mobileDrawerOverlay"></div>

    <aside class="mobile-drawer" id="mobileDrawer">
        <div class="mobile-drawer-top">
            <a href="#" class="landing-brand" onclick="closeMobileDrawer()">
                <div class="brand-badge">A</div>
                <span class="brand-name">PayTrack</span>
            </a>
            <button class="mobile-drawer-close-btn" id="btnNavClose" aria-label="Close menu">&times;</button>
        </div>

        <div class="mobile-drawer-content">
            <div class="mobile-section-label">Navigation</div>
            <ul class="mobile-nav-list">
                <li><a href="#home" class="mobile-nav-item active" onclick="closeMobileDrawer()"><span class="m-icon">🏠</span> Home</a></li>
                <li><a href="#features" class="mobile-nav-item" onclick="closeMobileDrawer()"><span class="m-icon">⚡</span> Features</a></li>
                <li><a href="#how-it-works" class="mobile-nav-item" onclick="closeMobileDrawer()"><span class="m-icon">📋</span> How It Works</a></li>
                <li><a href="#about" class="mobile-nav-item" onclick="closeMobileDrawer()"><span class="m-icon">ℹ️</span> About PayTrack</a></li>
            </ul>

            <div class="mobile-section-label" style="margin-top: 26px;">Account Access</div>
            <div class="mobile-portal-cards">
                <button type="button" class="mobile-portal-card student-card" onclick="openPortalFromNav()">
                    <div class="mp-icon">🔐</div>
                    <div class="mp-details">
                        <div class="mp-title">Sign In to PayTrack</div>
                        <div class="mp-desc">Access your student</div>
                    </div>
                    <div class="mp-arrow">&rarr;</div>
                </button>
            </div>
        </div>
    </aside>

    <!-- ── 2. Hero Section with Live Mockups ── -->
    <section class="hero-section" id="home">
        <div class="container">
            <div class="hero-grid">
                <!-- Left Content -->
                <div>
                    <div class="hero-tag">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        <span>Smart Payment Management</span>
                    </div>

                    <h1 class="hero-title">
                        Manage School Fees<br>with <span>PayTrack.</span>
                    </h1>

                    <p class="hero-desc">
                        A modern platform for students and parents to view balances and make secure payments. Simple, fast, and reliable.
                    </p>

                    <div class="hero-cta-group">
                        <button class="btn-primary-green" id="btnHeroGetStarted">
                            <svg class="btn-ic" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                            <span>Get started</span>
                            <svg class="btn-ic-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </button>
                        <a href="#features" class="btn-outline">
                            <svg class="btn-ic" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                            <span>Learn more</span>
                        </a>
                    </div>

                    <div class="hero-checklist">
                        <div class="check-item">
                            <svg class="check-svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            <span>Secure Payments</span>
                        </div>
                        <div class="check-item">
                            <svg class="check-svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <span>Real-time Updates</span>
                        </div>
                        <div class="check-item">
                            <svg class="check-svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            <span>Easy to Use</span>
                        </div>
                    </div>
                </div>

                <!-- Right Device Mockups -->
                <div class="mockup-wrapper">
                    <!-- Laptop screen preview -->
                    <div class="laptop-mockup">
                        <div class="laptop-screen">
                            <div style="background: #ffffff; padding: 16px 20px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 20px; height: 20px; border-radius: 4px; background: #0b3d2e; color: #fff; font-size: 10px; display: flex; align-items: center; justify-content: center; font-weight: 700;">A</div>
                                    <span style="font-weight: 700; font-size: 13px;">PayTrack</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; font-size: 11px; color: #64748b;">
                                    <span>Welcome, <strong>Juan Dela Cruz</strong></span>
                                </div>
                            </div>

                            <div style="padding: 16px 20px; background: #f8fafc;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
                                    <div>
                                        <div style="font-size: 15px; font-weight: 700;">Welcome back, Student!</div>
                                        <div style="font-size: 11px; color: #64748b;">Here's your account summary</div>
                                    </div>
                                </div>

                                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 14px;">
                                    <div style="background: #fff; padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                        <div style="font-size: 10px; color: #ef4444; font-weight: 600;">Total Balance</div>
                                        <div style="font-size: 14px; font-weight: 800;">₱ 2,500.00</div>
                                    </div>
                                    <div style="background: #fff; padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                        <div style="font-size: 10px; color: #10b981; font-weight: 600;">Paid This Month</div>
                                        <div style="font-size: 14px; font-weight: 800;">₱ 3,500.00</div>
                                    </div>
                                    <div style="background: #fff; padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                        <div style="font-size: 10px; color: #3b82f6; font-weight: 600;">Due Date</div>
                                        <div style="font-size: 12px; font-weight: 700; color: #1e40af;">Oct 15, 2026</div>
                                    </div>
                                </div>

                                <div style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 10px;">
                                    <div style="font-size: 11px; font-weight: 700; margin-bottom: 8px;">Recent Transactions</div>
                                    <div style="display: flex; justify-content: space-between; font-size: 11px; padding: 4px 0; border-bottom: 1px solid #f1f5f9;">
                                        <span>Tuition Fee</span>
                                        <span style="font-weight: 600;">₱ 2,000.00</span>
                                        <span class="badge-pill paid" style="font-size: 9px; padding: 2px 6px;">Paid</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; font-size: 11px; padding: 4px 0;">
                                        <span>Library Fee</span>
                                        <span style="font-weight: 600;">₱ 500.00</span>
                                        <span class="badge-pill paid" style="font-size: 9px; padding: 2px 6px;">Paid</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="laptop-base"></div>

                    <!-- Floating Phone Mockup -->
                    <div class="phone-mockup">
                        <div class="phone-screen">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span style="font-weight: 800; font-size: 11px;">PayTrack</span>
                                <span style="color: #64748b; font-size: 9px;">9:41</span>
                            </div>
                            <div style="background: #0b3d2e; color: #fff; padding: 10px; border-radius: 10px; margin-bottom: 10px; text-align: center;">
                                <div style="font-size: 9px; opacity: 0.8;">Tuition Balance</div>
                                <div style="font-size: 16px; font-weight: 800;">₱ 2,500.00</div>
                                <div style="font-size: 8px; margin-top: 4px; background: rgba(255,255,255,0.2); border-radius: 4px; padding: 3px;">Pay Now</div>
                            </div>
                            <div style="font-size: 9px; font-weight: 700; margin-bottom: 4px;">Recent Activity</div>
                            <div style="font-size: 9px; display: flex; justify-content: space-between; padding: 3px 0; border-bottom: 1px solid #f1f5f9;">
                                <span>Exam Fee</span>
                                <span style="color: #10b981; font-weight: 600;">Paid</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 3. Key Features Section ── -->
    <section class="features-section" id="features">
        <div class="container">
            <div class="section-header">
                <span class="section-pill">KEY FEATURES</span>
                <h2 class="section-title">Everything You Need in One Place</h2>
                <p class="section-subtitle">
                    PayTrack makes school fee management easier, faster, and more convenient for students, parents, and schools.
                </p>
            </div>

            <div class="features-grid">
                <div class="feature-box">
                    <div class="feature-icon-badge icon-green">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                    </div>
                    <h3 class="feature-box-title">Instant Account Creation</h3>
                    <p class="feature-box-desc">Real-time access to student accounts, default credentials, and balance breakdown.</p>
                    <span class="feature-arrow">&rarr;</span>
                </div>

                <div class="feature-box">
                    <div class="feature-icon-badge icon-blue">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    </div>
                    <h3 class="feature-box-title">Online Payment</h3>
                    <p class="feature-box-desc">Direct, secure fee payments using bank cards, GCash, or digital methods.</p>
                    <span class="feature-arrow">&rarr;</span>
                </div>

                <div class="feature-box">
                    <div class="feature-icon-badge icon-yellow">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    </div>
                    <h3 class="feature-box-title">Parental Email Notifications</h3>
                    <p class="feature-box-desc">Automatic alerts for fees, dues, payments, and balances sent directly to parents.</p>
                    <span class="feature-arrow">&rarr;</span>
                </div>

                <div class="feature-box">
                    <div class="feature-icon-badge icon-purple">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </div>
                    <h3 class="feature-box-title">Secure &amp; Reliable</h3>
                    <p class="feature-box-desc">Your financial data is protected with industry-standard encryption and safety.</p>
                    <span class="feature-arrow">&rarr;</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 4. How It Works (3 Steps) ── -->
    <section class="steps-section" id="how-it-works">
        <div class="container">
            <div class="section-header">
                <span class="section-pill">HOW IT WORKS</span>
                <h2 class="section-title">Get Started in 3 Simple Steps</h2>
                <p class="section-subtitle">Setting up your PayTrack account is quick and easy. Follow these steps to begin.</p>
            </div>

            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-num">1</div>
                    <div>
                        <div class="step-icon-box">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </div>
                        <h4 class="step-title">Create Your Account</h4>
                        <p class="step-desc">Sign up as a student or parent in just a few minutes using school ID.</p>
                    </div>
                </div>

                <div class="step-card">
                    <div class="step-num">2</div>
                    <div>
                        <div class="step-icon-box">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                        </div>
                        <h4 class="step-title">Add a Payment Method</h4>
                        <p class="step-desc">Link your bank card, e-wallet, or preferred online payment option.</p>
                    </div>
                </div>

                <div class="step-card">
                    <div class="step-num">3</div>
                    <div>
                        <div class="step-icon-box" style="background: #ecfdf5; color: #059669;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        </div>
                        <h4 class="step-title">Start Managing Fees</h4>
                        <p class="step-desc">View balances, make payments, and get notified instantly via email.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 5. Why Choose PayTrack ── -->
    <section class="why-section" id="about">
        <div class="container">
            <div class="why-grid">
                <div class="why-image-card">
                    <div style="background: #f1f5f9; border-radius: 12px; padding: 24px; text-align: center;">
                        <div style="width: 52px; height: 52px; border-radius: 50%; background: #10b981; color: #fff; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; box-shadow: 0 6px 16px rgba(16, 185, 129, 0.28);">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <h4 style="font-size: 18px; font-weight: 700; margin: 0 0 6px;">100% Automated Workflow</h4>
                        <p style="font-size: 13px; color: #64748b; margin: 0;">Instant Official Receipts &amp; Real-time Verification</p>
                    </div>
                </div>

                <div>
                    <span class="section-pill">WHY CHOOSE PAYTRACK</span>
                    <h2 class="section-title">Smarter Payments.<br>Smoother School Life.</h2>
                    <p class="section-subtitle">
                        We built PayTrack to remove the hassle of manual payment processes and give you more time for what matters most — learning and growing.
                    </p>

                    <ul class="why-benefit-list">
                        <li class="why-benefit-item">
                            <span class="benefit-check-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            </span>
                            <span>Save time with online payments</span>
                        </li>
                        <li class="why-benefit-item">
                            <span class="benefit-check-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                            </span>
                            <span>Get real-time updates and notifications</span>
                        </li>
                        <li class="why-benefit-item">
                            <span class="benefit-check-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                            </span>
                            <span>Accessible anytime, anywhere</span>
                        </li>
                        <li class="why-benefit-item">
                            <span class="benefit-check-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            </span>
                            <span>Trusted by students, parents, and schools</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 6. Testimonials Section ── -->
    <section class="testimonials-section">
        <div class="container">
            <div class="section-header">
                <span class="section-pill">TESTIMONIALS</span>
                <h2 class="section-title">What Our Users Say</h2>
                <p class="section-subtitle">Real stories from students, parents, and schools who trust PayTrack.</p>
            </div>

            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <p class="testimonial-text">"PayTrack made it so easy to pay my school fees. I love how simple and fast it is!"</p>
                    <div class="testimonial-user">
                        <div class="avatar-badge">JM</div>
                        <div>
                            <div class="user-name">Juan Mariano Dela Cruz</div>
                            <div class="user-role">Student, Grade 11</div>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <p class="testimonial-text">"As a parent, I appreciate the email notifications. I always know when payments are due."</p>
                    <div class="testimonial-user">
                        <div class="avatar-badge" style="background: #e0f2fe; color: #0284c7;">SR</div>
                        <div>
                            <div class="user-name">Sarah Reyes</div>
                            <div class="user-role">Parent</div>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <p class="testimonial-text">"Our school's finance process is now more efficient and organized thanks to PayTrack."</p>
                    <div class="testimonial-user">
                        <div class="avatar-badge" style="background: #fdf4ff; color: #c026d3;">AL</div>
                        <div>
                            <div class="user-name">Ms. Ana Lopez</div>
                            <div class="user-role">School Administrator</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 7. Footer Banner ── -->
    <footer class="landing-footer">
        <div class="container">
            <div class="footer-top">
                <div>
                    <div class="footer-brand">
                        <div class="brand-badge">A</div>
                        <span class="footer-brand-title">PayTrack</span>
                    </div>
                    <p class="footer-motto">Secure. Simple. For a better school experience.</p>
                </div>

                <div class="footer-cta">
                    <div class="footer-cta-title">Ready to Get Started?</div>
                    <div class="footer-cta-sub">Join thousands of students and parents who already trust PayTrack.</div>
                    <button class="btn-primary-green" id="btnFooterGetStarted">
                        <svg class="btn-ic" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                        <span>Get started</span>
                        <svg class="btn-ic-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>
                </div>
            </div>

            <div class="footer-bottom">
                <div>&copy; <?= date('Y') ?> PayTrack. All rights reserved.</div>
                <div class="footer-links">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                    <a href="#">Contact</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- ── Professional Pop-up Sign In Modal ── -->
    <div class="modal-backdrop" id="loginModalBackdrop">
        <div class="modal-window">
            <button class="modal-close-x" id="btnCloseLoginModal" aria-label="Close">&times;</button>

            <!-- Segmented Role Selector Tabs (Clear Options Right Up Front) -->
            <div class="modal-header-box" style="margin-top: 8px;">
                <div class="modal-header-icon" id="modalHeaderIcon" style="background: #ecfdf5; color: #0b3d2e;">🔐</div>
                <h2 class="modal-header-title" id="loginModalTitle">Sign In to PayTrack</h2>
                <p class="modal-header-sub" id="loginModalSub">Enter your Account ID or username and password</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="login-error-banner" role="alert">
                    <strong>Sign in failed.</strong> <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= APP_URL ?>/public/" id="loginForm">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="login">

                <div class="form-group">
                    <label class="form-label" for="inputUsername">Account ID / Username</label>
                    <input type="text" class="form-control" name="username" id="inputUsername" required placeholder="e.g. 2023-53512" autocomplete="username">
                </div>

                <div class="form-group">
                    <label class="form-label" for="inputPassword">Password</label>
                    <div style="position: relative;">
                        <input type="password" class="form-control" name="password" id="inputPassword" required placeholder="••••••••" autocomplete="current-password" style="padding-right: 42px;">
                        <button type="button" id="btnTogglePassword" title="Show/Hide Password" aria-label="Toggle password visibility" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #64748b; font-size: 16px; padding: 4px 6px; display: flex; align-items: center; justify-content: center; line-height: 1;">
                            👁️
                        </button>
                    </div>
                    <small id="passwordHint" style="color: #64748b; font-size: 11.5px; margin-top: 6px; display: block; line-height: 1.4;">
                        Default password for students & parents: <strong>Student's Last Name</strong> (e.g. <code>DELACRUZ</code>)
                    </small>
                </div>

                <div style="margin-top: 24px;">
                    <button type="submit" class="btn-signin-submit">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                        <span>Sign In</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- SweetAlert2 for error handling -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const loginBackdrop = document.getElementById('loginModalBackdrop');
        const btnHeaderLogin = document.getElementById('btnHeaderLogin');
        const btnHeroGetStarted = document.getElementById('btnHeroGetStarted');
        const btnFooterGetStarted = document.getElementById('btnFooterGetStarted');
        const btnCloseLoginModal = document.getElementById('btnCloseLoginModal');

        const inputUsername = document.getElementById('inputUsername');
        const inputPassword = document.getElementById('inputPassword');
        const btnTogglePassword = document.getElementById('btnTogglePassword');

        // Password visibility toggle
        if (btnTogglePassword && inputPassword) {
            btnTogglePassword.addEventListener('click', () => {
                const isPassword = inputPassword.getAttribute('type') === 'password';
                inputPassword.setAttribute('type', isPassword ? 'text' : 'password');
                btnTogglePassword.textContent = isPassword ? '🙈' : '👁️';
                btnTogglePassword.setAttribute('title', isPassword ? 'Hide password' : 'Show password');
            });
        }

        function openModal() {
            loginBackdrop.classList.add('active');
            inputUsername.focus();
        }

        function closeModal() {
            loginBackdrop.classList.remove('active');
        }

        btnHeaderLogin && btnHeaderLogin.addEventListener('click', openModal);
        btnHeroGetStarted && btnHeroGetStarted.addEventListener('click', openModal);
        btnFooterGetStarted && btnFooterGetStarted.addEventListener('click', openModal);
        btnCloseLoginModal && btnCloseLoginModal.addEventListener('click', closeModal);

        window.addEventListener('click', (e) => {
            if (e.target === loginBackdrop) closeModal();
        });

        // ── Mobile Drawer Navigation ──
        const btnNavToggle         = document.getElementById('btnNavToggle');
        const btnNavClose          = document.getElementById('btnNavClose');
        const mobileDrawer         = document.getElementById('mobileDrawer');
        const mobileDrawerOverlay  = document.getElementById('mobileDrawerOverlay');

        function openMobileDrawer() {
            if (!mobileDrawer) return;
            mobileDrawer.classList.add('open');
            mobileDrawerOverlay.classList.add('active');
            btnNavToggle && btnNavToggle.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        window.closeMobileDrawer = function () {
            if (!mobileDrawer) return;
            mobileDrawer.classList.remove('open');
            mobileDrawerOverlay.classList.remove('active');
            btnNavToggle && btnNavToggle.classList.remove('active');
            document.body.style.overflow = '';
        };

        window.openPortalFromNav = function() {
            closeMobileDrawer();
            setTimeout(openModal, 100);
        };

        if (btnNavToggle) {
            btnNavToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                if (mobileDrawer.classList.contains('open')) {
                    closeMobileDrawer();
                } else {
                    openMobileDrawer();
                }
            });
        }

        if (btnNavClose) {
            btnNavClose.addEventListener('click', closeMobileDrawer);
        }

        if (mobileDrawerOverlay) {
            mobileDrawerOverlay.addEventListener('click', closeMobileDrawer);
        }

        // Show flash error if login fails
        const flashError = <?= json_encode($error ?? '') ?>;
        const lastUsername = <?= json_encode($lastUsername ?? '') ?>;

        if (flashError) {
            openModal();
            if (lastUsername && inputUsername) {
                inputUsername.value = lastUsername;
            }
            // The sign-in modal has a high stacking level, so the alert must be
            // explicitly placed above it. Otherwise it is shown behind the modal.
            if (window.Swal) {
                Swal.fire({
                    icon: 'error',
                    title: 'Sign In Failed',
                    text: flashError,
                    confirmButtonText: 'Try again',
                    confirmButtonColor: '#0b3d2e',
                    allowOutsideClick: false,
                    didClose: () => inputPassword?.focus()
                });
            }
        }

        // ── Navigation Link Active Switcher & Smooth Scroll ──
        const navLinks = document.querySelectorAll('.desktop-nav-menu .nav-link, .mobile-nav-list .mobile-nav-item');
        const trackedSections = ['home', 'features', 'how-it-works', 'about'];

        function setActiveNav(targetHash) {
            navLinks.forEach(link => {
                const href = link.getAttribute('href');
                if (href === targetHash) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        }

        navLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                const targetHash = this.getAttribute('href');
                if (targetHash && targetHash.startsWith('#')) {
                    e.preventDefault();
                    setActiveNav(targetHash);
                    
                    const targetEl = document.querySelector(targetHash);
                    if (targetEl) {
                        targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                    if (window.history && window.history.replaceState) {
                        history.replaceState(null, null, targetHash);
                    }
                }
            });
        });

        // Keep the selected link aligned with the section directly below the sticky header.
        const sectionElements = trackedSections.map(id => document.getElementById(id)).filter(Boolean);
        let scrollSpyFrame = 0;
        function updateActiveSection() {
            scrollSpyFrame = 0;
            const header = document.querySelector('.landing-header');
            const activationLine = (header?.getBoundingClientRect().bottom || 0) + 24;
            let activeSection = sectionElements[0];
            sectionElements.forEach(section => {
                if (section.getBoundingClientRect().top <= activationLine) activeSection = section;
            });
            if (activeSection) setActiveNav('#' + activeSection.id);
        }
        window.addEventListener('scroll', () => {
            if (!scrollSpyFrame) scrollSpyFrame = requestAnimationFrame(updateActiveSection);
        }, { passive: true });
        window.addEventListener('resize', updateActiveSection);
        updateActiveSection();

        // ── Custom Required-Field Validation (red highlight instead of browser tooltip) ──
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.setAttribute('novalidate', '');
            loginForm.addEventListener('submit', function (e) {
                let firstInvalid = null;
                loginForm.querySelectorAll('[required]').forEach(function (field) {
                    // Clear previous error state
                    field.style.borderColor = '';
                    field.style.background  = '';
                    const existingMsg = field.parentElement.querySelector('.inline-field-error');
                    if (existingMsg) existingMsg.remove();

                    if (!field.value.trim()) {
                        e.preventDefault();
                        field.style.borderColor = '#ef4444';
                        field.style.background  = '#fef2f2';

                        const msg = document.createElement('span');
                        msg.className = 'inline-field-error';
                        msg.textContent = 'This field is required.';
                        msg.style.cssText = 'color:#ef4444;font-size:11.5px;display:block;margin-top:4px;font-weight:600;';
                        field.parentElement.appendChild(msg);

                        if (!firstInvalid) firstInvalid = field;

                        // Remove error style once user starts typing
                        field.addEventListener('input', function clear() {
                            field.style.borderColor = '';
                            field.style.background  = '';
                            const m = field.parentElement.querySelector('.inline-field-error');
                            if (m) m.remove();
                            field.removeEventListener('input', clear);
                        });
                    }
                });
                if (firstInvalid) firstInvalid.focus();
            });
        }
    </script>
</body>
</html>
