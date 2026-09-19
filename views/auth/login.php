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

            <ul class="nav-menu">
                <li><a href="#home" class="nav-link active">Home</a></li>
                <li><a href="#features" class="nav-link">Features</a></li>
                <li><a href="#how-it-works" class="nav-link">How It Works</a></li>
                <li><a href="#about" class="nav-link">About</a></li>
            </ul>

            <button class="btn-header-login" id="btnHeaderLogin">
                Login / Portal <span>&rarr;</span>
            </button>
        </div>
    </header>

    <!-- ── 2. Hero Section with Live Mockups ── -->
    <section class="hero-section" id="home">
        <div class="container">
            <div class="hero-grid">
                <!-- Left Content -->
                <div>
                    <div class="hero-tag">
                        <span class="hero-tag-dot"></span>
                        Smart Payment Management
                    </div>

                    <h1 class="hero-title">
                        Manage School Fees<br>with <span>PayTrack.</span>
                    </h1>

                    <p class="hero-desc">
                        A modern platform for students and parents to view balances and make secure payments. Simple, fast, and reliable.
                    </p>

                    <div class="hero-cta-group">
                        <button class="btn-primary-green" id="btnHeroGetStarted">
                            Get started <span>&rarr;</span>
                        </button>
                        <a href="#features" class="btn-outline">Learn more</a>
                    </div>

                    <div class="hero-checklist">
                        <div class="check-item">
                            <span class="check-ic">&#10004;</span> Secure Payments
                        </div>
                        <div class="check-item">
                            <span class="check-ic">&#10004;</span> Real-time Updates
                        </div>
                        <div class="check-item">
                            <span class="check-ic">&#10004;</span> Easy to Use
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
                                    <div style="width: 22px; height: 22px; border-radius: 50%; background: #cbd5e1;"></div>
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
                    <div class="feature-icon-badge icon-green">&#129658;</div>
                    <h3 class="feature-box-title">Instant Account Creation</h3>
                    <p class="feature-box-desc">Real-time access to student accounts, default credentials, and balance breakdown.</p>
                    <span class="feature-arrow">&rarr;</span>
                </div>

                <div class="feature-box">
                    <div class="feature-icon-badge icon-blue">&#128179;</div>
                    <h3 class="feature-box-title">Online Payment</h3>
                    <p class="feature-box-desc">Direct, secure fee payments using bank cards, GCash, or digital methods.</p>
                    <span class="feature-arrow">&rarr;</span>
                </div>

                <div class="feature-box">
                    <div class="feature-icon-badge icon-yellow">&#128276;</div>
                    <h3 class="feature-box-title">Parental Email Notifications</h3>
                    <p class="feature-box-desc">Automatic alerts for fees, dues, payments, and balances sent directly to parents.</p>
                    <span class="feature-arrow">&rarr;</span>
                </div>

                <div class="feature-box">
                    <div class="feature-icon-badge icon-purple">&#128737;</div>
                    <h3 class="feature-box-title">Secure &amp; Reliable</h3>
                    <p class="feature-box-desc">Your financial data is protected with industry-standard encryption and safety.</p>
                    <span class="feature-arrow">&rarr;</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 4. Why Choose PayTrack ── -->
    <section class="why-section" id="about">
        <div class="container">
            <div class="why-grid">
                <div class="why-image-card">
                    <div style="background: #f1f5f9; border-radius: 12px; padding: 24px; text-align: center;">
                        <div style="width: 50px; height: 50px; border-radius: 50%; background: #10b981; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 16px;">
                            &#10004;
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
                            <span style="color: #10b981;">&#10004;</span> Save time with online payments
                        </li>
                        <li class="why-benefit-item">
                            <span style="color: #10b981;">&#10004;</span> Get real-time updates and notifications
                        </li>
                        <li class="why-benefit-item">
                            <span style="color: #10b981;">&#10004;</span> Accessible anytime, anywhere
                        </li>
                        <li class="why-benefit-item">
                            <span style="color: #10b981;">&#10004;</span> Trusted by students, parents, and schools
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 5. How It Works (3 Steps) ── -->
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
                        <div class="step-icon-box">&#128100;</div>
                        <h4 class="step-title">Create Your Account</h4>
                        <p class="step-desc">Sign up as a student or parent in just a few minutes using school ID.</p>
                    </div>
                </div>

                <div class="step-card">
                    <div class="step-num">2</div>
                    <div>
                        <div class="step-icon-box">&#128179;</div>
                        <h4 class="step-title">Add a Payment Method</h4>
                        <p class="step-desc">Link your bank card, e-wallet, or preferred online payment option.</p>
                    </div>
                </div>

                <div class="step-card">
                    <div class="step-num">3</div>
                    <div>
                        <div class="step-icon-box">&#9989;</div>
                        <h4 class="step-title">Start Managing Fees</h4>
                        <p class="step-desc">View balances, make payments, and get notified instantly via email.</p>
                    </div>
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
                        Get started <span>&rarr;</span>
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
            
            <div class="modal-header-box">
                <div class="modal-header-icon" id="modalHeaderIcon">🎓</div>
                <h2 class="modal-header-title" id="loginModalTitle">Student / Parent Sign In</h2>
                <p class="modal-header-sub" id="loginModalSub">Enter your Student ID and password to access your portal</p>
            </div>

            <form method="POST" action="<?= APP_URL ?>/public/" id="loginForm">
                <input type="hidden" name="action" value="login">
                <input type="hidden" name="role" id="loginRoleInput" value="student">

                <div class="form-group">
                    <label class="form-label" id="usernameFieldLabel" for="inputUsername">Student ID</label>
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
                        Default password: <strong>Student's Last Name</strong> (e.g. <code>DELACRUZ</code> or <code>Dela Cruz</code>)
                    </small>
                </div>

                <div style="margin-top: 24px;">
                    <button type="submit" class="btn-signin-submit">Sign In</button>
                </div>

                <!-- Subtle role switcher link -->
                <div style="text-align: center;">
                    <a href="javascript:void(0)" class="role-switcher-link" id="toggleRoleLink">
                        Sign in as Administrator &rarr;
                    </a>
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

        const modalHeaderIcon = document.getElementById('modalHeaderIcon');
        const loginModalTitle = document.getElementById('loginModalTitle');
        const loginModalSub = document.getElementById('loginModalSub');
        const usernameFieldLabel = document.getElementById('usernameFieldLabel');
        const inputUsername = document.getElementById('inputUsername');
        const loginRoleInput = document.getElementById('loginRoleInput');
        const toggleRoleLink = document.getElementById('toggleRoleLink');

        let currentRole = 'student';

        function applyRole(role) {
            currentRole = role;
            loginRoleInput.value = role;

            if (role === 'admin') {
                modalHeaderIcon.textContent = '⚙️';
                modalHeaderIcon.style.background = '#f1f5f9';
                modalHeaderIcon.style.color = '#0f172a';
                loginModalTitle.textContent = 'Admin Sign In';
                loginModalSub.textContent = 'Enter your administrative credentials';
                usernameFieldLabel.textContent = 'Admin Username';
                inputUsername.placeholder = 'admin';
                toggleRoleLink.innerHTML = '&larr; Sign in as Student / Parent';
                const pwHint = document.getElementById('passwordHint');
                if (pwHint) pwHint.style.display = 'none';
            } else {
                modalHeaderIcon.textContent = '🎓';
                modalHeaderIcon.style.background = '#ecfdf5';
                modalHeaderIcon.style.color = '#059669';
                loginModalTitle.textContent = 'Student / Parent Sign In';
                loginModalSub.textContent = 'Enter your Student ID and password to access your portal';
                usernameFieldLabel.textContent = 'Student ID';
                inputUsername.placeholder = 'e.g. 2023-53512';
                toggleRoleLink.innerHTML = 'Sign in as Administrator &rarr;';
                const pwHint = document.getElementById('passwordHint');
                if (pwHint) pwHint.style.display = 'block';
            }
        }

        // Password visibility toggle
        const btnTogglePassword = document.getElementById('btnTogglePassword');
        const inputPassword = document.getElementById('inputPassword');

        if (btnTogglePassword && inputPassword) {
            btnTogglePassword.addEventListener('click', () => {
                const isPassword = inputPassword.getAttribute('type') === 'password';
                inputPassword.setAttribute('type', isPassword ? 'text' : 'password');
                btnTogglePassword.textContent = isPassword ? '🙈' : '👁️';
                btnTogglePassword.setAttribute('title', isPassword ? 'Hide password' : 'Show password');
            });
        }

        function openModal(role = 'student') {
            applyRole(role);
            loginBackdrop.classList.add('active');
            inputUsername.focus();
        }

        function closeModal() {
            loginBackdrop.classList.remove('active');
        }

        btnHeaderLogin.addEventListener('click', () => openModal('student'));
        btnHeroGetStarted.addEventListener('click', () => openModal('student'));
        btnFooterGetStarted.addEventListener('click', () => openModal('student'));
        btnCloseLoginModal.addEventListener('click', closeModal);

        toggleRoleLink.addEventListener('click', () => {
            applyRole(currentRole === 'student' ? 'admin' : 'student');
            inputUsername.value = '';
            document.getElementById('inputPassword').value = '';
            inputUsername.focus();
        });

        window.addEventListener('click', (e) => {
            if (e.target === loginBackdrop) closeModal();
        });

        // Show flash error if login fails
        const flashError = <?= json_encode($error) ?>;
        const openRole = <?= json_encode($openRole) ?>;

        if (flashError) {
            openModal(openRole || 'student');
            Swal.fire({
                icon: 'error',
                title: 'Login Error',
                text: flashError,
                confirmButtonColor: '#18181b'
            });
        }
    </script>
</body>
</html>
