<?php
/**
 * PayTrack — Student Portal
 * Multi-Page View Routing: 'fees' (Tuition & Assessment), 'history' (Payment History), 'password' (Account Security)
 * Preserves the client-approved light sidebar with active link indicators
 */
$studentName = $student ? ($student['first_name'] . ' ' . $student['last_name']) : 'Student';
$studentNum  = $student ? $student['student_id'] : '2023-53512';
$studentInitial = strtoupper(substr($student['first_name'] ?? 'J', 0, 1));

$primaryFee = !empty($fees) ? $fees[0] : null;
$breakdownItems = $primaryFee ? ($primaryFee['items'] ?? []) : [];
$termDescription = $primaryFee['description'] ?? 'S.Y. 2024-2025 - 1st Semester Tuition';

$calcPct = ($totalFees > 0) ? min(100, round(($totalPaid / $totalFees) * 100)) : 100;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>PayTrack — Student Portal</title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
    <style>
        body {
            background-color: #f8fafc;
        }

        /* Topbar additions */
        .search-container {
            display: flex;
            align-items: center;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 8px 16px;
            width: 100%;
            max-width: 400px;
            gap: 10px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        }
        .search-container input {
            border: none;
            outline: none;
            font-size: 12.5px;
            color: #1e293b;
            width: 100%;
            background: transparent;
        }
        .search-container input::placeholder {
            color: #94a3b8;
        }

        .notif-bell-btn {
            position: relative;
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px;
            color: #475569;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: background 0.15s ease;
        }
        .notif-bell-btn:hover {
            background: #f1f5f9;
        }
        .notif-dot-red {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 7px;
            height: 7px;
            background: #ef4444;
            border-radius: 50%;
            border: 1.5px solid #ffffff;
        }

        .user-chip-container {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 4px 8px;
            cursor: default;
        }
        .user-avatar-circle {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #1e3a8a;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
        }
        .user-chip-meta {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
            text-align: left;
        }
        .user-chip-name {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
        }
        .user-chip-id {
            font-size: 11px;
            color: #64748b;
        }

        /* Header layout */
        .portal-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
            flex-wrap: wrap;
            gap: 16px;
        }
        .portal-title-flex {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .wallet-icon-box {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: #eff6ff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 6px rgba(37,99,235,0.08);
        }
        .portal-page-title {
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            letter-spacing: -0.5px;
            line-height: 1.1;
        }
        .portal-page-sub {
            font-size: 13px;
            color: #64748b;
            margin: 4px 0 0;
        }

        .btn-pay-now-main {
            background: #1d4ed8;
            color: #ffffff;
            font-size: 13.5px;
            font-weight: 700;
            padding: 10px 22px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.15s ease;
            box-shadow: 0 4px 12px rgba(29,78,216,0.22);
            will-change: transform;
        }
        .btn-pay-now-main:hover {
            background: #1e40af;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(29,78,216,0.32);
        }
        .btn-pay-now-main:active {
            transform: translateY(0);
            box-shadow: none;
        }

        /* 2-Column Hero Grid */
        .hero-dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 1.05fr;
            gap: 20px;
            margin-bottom: 24px;
        }
        @media (max-width: 960px) {
            .hero-dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Card 1: Balance Card */
        .card-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 22px 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            position: relative;
        }
        .card-eyebrow {
            font-size: 13.5px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 2px;
        }
        .card-term-sub {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 16px;
        }
        .balance-amount-row {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }
        .balance-huge-red {
            font-size: 28px;
            font-weight: 900;
            color: #e11d48;
            letter-spacing: -0.5px;
            line-height: 1;
        }
        .btn-pay-pill-dark {
            background: #1e3a8a;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 7px 16px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s ease;
        }
        .btn-pay-pill-dark:hover {
            background: #172554;
            transform: translateY(-1px);
        }
        .balance-meta-bullet {
            font-size: 12px;
            color: #0f172a;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .balance-meta-bullet span.dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #0f172a;
            display: inline-block;
        }

        .progress-track-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .progress-bar-track {
            flex: 1;
            height: 7px;
            background: #e2e8f0;
            border-radius: 9999px;
            overflow: hidden;
            position: relative;
        }
        .progress-bar-fill-green {
            height: 100%;
            background: #10b981;
            border-radius: 9999px;
            transition: width 0.4s ease;
        }
        .progress-pct-label {
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
        }

        /* Card 2: Inspiration Quote Banner */
        .motivation-hero-card {
            background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 50%, #e2e8f0 100%);
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 155px;
        }
        .motivation-text-col {
            position: relative;
            z-index: 2;
            max-width: 250px;
        }
        .motivation-quote-text {
            font-size: 15.5px;
            font-weight: 600;
            color: #0f172a;
            line-height: 1.4;
            font-style: italic;
        }
        .quote-accent-bar {
            width: 30px;
            height: 3px;
            background: #2563eb;
            border-radius: 2px;
            margin-top: 10px;
        }

        .motivation-gfx-col {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .floating-grad-badge {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: rgba(37,99,235,0.12);
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 14px rgba(37,99,235,0.15);
        }

        /* Sections & Tables */
        .section-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 20px 24px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .section-box-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            flex-wrap: wrap;
            gap: 10px;
        }
        .section-header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section-header-icon {
            width: 28px;
            height: 28px;
            border-radius: 7px;
            background: #eff6ff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
        }
        .section-box-title {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
        }
        .section-box-meta {
            font-size: 12px;
            color: #64748b;
        }

        /* Clean Styled Table */
        .styled-fintech-table {
            width: 100%;
            border-collapse: collapse;
        }
        .styled-fintech-table thead tr {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        .styled-fintech-table thead th {
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 11px 16px;
        }
        .styled-fintech-table tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.1s ease;
        }
        .styled-fintech-table tbody tr:hover {
            background: #fbfcfe;
        }
        .styled-fintech-table tbody td {
            padding: 13px 16px;
            font-size: 13px;
            color: #1e293b;
            vertical-align: middle;
        }

        /* Aspect Item Icons */
        .aspect-cell {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            color: #0f172a;
        }
        .aspect-icon-badge {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
        }
        .aspect-icon-badge.blue {
            background: #eff6ff;
            color: #2563eb;
        }
        .aspect-icon-badge.purple {
            background: #faf5ff;
            color: #9333ea;
        }
        .aspect-icon-badge.green {
            background: #ecfdf5;
            color: #059669;
        }

        /* Classification Badges */
        .pill-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 11.5px;
            font-weight: 600;
        }
        .pill-badge.blue {
            background: #eff6ff;
            color: #2563eb;
        }
        .pill-badge.purple {
            background: #faf5ff;
            color: #9333ea;
        }
        .pill-badge.green {
            background: #ecfdf5;
            color: #059669;
        }

        /* Total Row */
        .table-total-row {
            background: #ffffff !important;
            border-top: 1px solid #e2e8f0;
            border-bottom: none !important;
            font-weight: 700;
        }
        .table-total-row td {
            padding: 16px 16px 8px !important;
        }

        /* Notice Banner */
        .green-notice-callout {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 10px;
            padding: 14px 18px;
            margin-top: 18px;
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .green-notice-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #10b981;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 800;
            flex-shrink: 0;
        }
        .green-notice-body {
            font-size: 12.5px;
            line-height: 1.5;
            color: #166534;
        }
        .green-notice-body strong.notice-title {
            display: block;
            font-size: 12.5px;
            color: #14532d;
            margin-bottom: 2px;
        }

        /* Payment history action button */
        .btn-view-receipt-outline {
            background: #ffffff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            padding: 5px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s ease;
        }
        .btn-view-receipt-outline:hover {
            background: #eff6ff;
            border-color: #93c5fd;
        }

        .badge-method-online {
            background: #ecfdf5;
            color: #059669;
            padding: 3px 10px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        /* Metric Mini Cards for History Page */
        .metrics-grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-bottom: 24px;
        }
        @media (max-width: 768px) {
            .metrics-grid-3 {
                grid-template-columns: 1fr;
            }
        }
        .metric-mini-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .metric-mini-label {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 6px;
        }
        .metric-mini-val {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
        }

        .btn-logout-prominent {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 14px;
            background: #fee2e2;
            border: 1.5px solid #f87171;
            border-radius: 8px;
            color: #b91c1c;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-logout-prominent:hover {
            background: #ef4444;
            color: #ffffff;
            border-color: #dc2626;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
        }

        /* ── Official Print Styling for National College of Science and Technology ── */
        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .sidebar, .topbar, .portal-header-row, .metrics-grid-3, .section-box,
            .hero-dashboard-grid, .modal-backdrop:not(#officialReceiptModal),
            .no-print, #sidebarOverlay, .notif-bell-btn, .search-container {
                display: none !important;
            }
            #officialReceiptModal {
                position: static !important;
                display: block !important;
                background: #ffffff !important;
                width: 100% !important;
                height: auto !important;
                padding: 0 !important;
                margin: 0 !important;
                box-shadow: none !important;
                opacity: 1 !important;
                visibility: visible !important;
                z-index: 999999 !important;
            }
            #officialReceiptModal .modal-window {
                position: static !important;
                transform: none !important;
                box-shadow: none !important;
                border: 2px solid #0b3d2e !important;
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                border-radius: 0 !important;
            }
            #ncstPrintableReceipt {
                padding: 24px 30px !important;
                background: #ffffff !important;
            }
            @page {
                size: portrait;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>

<div class="app-shell">
    <!-- Mobile Drawer Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar (Client-Approved Light Sidebar) -->
    <aside class="sidebar" id="sidebar">
        <div class="org">
            <div class="org-icon">S</div>
            <div class="org-text">
                <div class="org-name">PayTrack</div>
                <div class="org-team">Student Portal</div>
            </div>
            <button class="mobile-menu-btn" id="btnCloseSidebar" style="margin-left: auto; width: 28px; height: 28px; font-size: 14px;">&times;</button>
        </div>

        <ul class="nav">
            <li>
                <a href="<?= APP_URL ?>/public/student/?view=home" class="nav-item <?= $currentView === 'home' ? 'active' : '' ?>">
                    <span class="ic">&#8962;</span> Home
                </a>
            </li>
            <li>
                <a href="<?= APP_URL ?>/public/student/?view=fees" class="nav-item <?= $currentView === 'fees' ? 'active' : '' ?>">
                    <span class="ic">&#128179;</span> My Tuition &amp; Balance
                </a>
            </li>
            <li>
                <a href="<?= APP_URL ?>/public/student/?view=history" class="nav-item <?= $currentView === 'history' ? 'active' : '' ?>">
                    <span class="ic">&#8644;</span> Payment History
                    <span class="badge-count"><?= count($payments) ?></span>
                </a>
            </li>
            <li>
                <a href="<?= APP_URL ?>/public/student/?view=receipts" class="nav-item <?= $currentView === 'receipts' ? 'active' : '' ?>">
                    <span class="ic">&#128196;</span> Official Receipts
                    <span class="badge-count"><?= count($payments) ?></span>
                </a>
            </li>
            <li>
                <a href="<?= APP_URL ?>/public/student/?view=password" class="nav-item <?= $currentView === 'password' ? 'active' : '' ?>">
                    <span class="ic">&#128273;</span> Change Password
                </a>
            </li>
        </ul>

        <div style="margin-top: auto; padding-top: 18px; border-top: 1px solid #e2e8f0;">
            <button type="button" class="btn-logout-prominent" id="btnLogout">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                <span>Sign Out / Logout</span>
            </button>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="main">
        <!-- Topbar -->
        <header class="topbar" style="background: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 12px 28px;">
            <button class="mobile-menu-btn" id="btnOpenSidebar" aria-label="Toggle Navigation">&#9776;</button>

            <!-- Search Bar -->
            <div class="search-container">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" id="studentSearchInput" placeholder="Search my records...">
            </div>

            <!-- Topbar Right Profile & Notif Actions -->
            <div class="topbar-actions" style="display: flex; align-items: center; gap: 16px;">
                <button class="notif-bell-btn" aria-label="Notifications" title="Notifications">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <span class="notif-dot-red"></span>
                </button>

                <div class="user-chip-container" style="padding: 6px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
                    <div class="user-chip-meta">
                        <span class="user-chip-name" style="font-weight: 700; color: #0f172a;"><?= e($studentName) ?></span>
                        <span class="user-chip-id" style="color: #64748b; font-size: 11.5px;">(<?= e($studentNum) ?>)</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Flash Toast Messages -->
        <?php if (!empty($successMsg)): ?>
            <div class="app-toast success">
                <span>&#10003;</span> <?= e($successMsg) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errorMsg)): ?>
            <div class="app-toast error">
                <span>&#9888;</span> <?= e($errorMsg) ?>
            </div>
        <?php endif; ?>

        <!-- Content Area -->
        <main class="content" style="padding: 24px 28px;">

            <!-- ============================================== -->
            <!-- VIEW 0: STUDENT HOME / OVERVIEW               -->
            <!-- ============================================== -->
            <?php if ($currentView === 'home'): ?>
                <div class="portal-header-row">
                    <div class="portal-title-flex">
                        <div class="wallet-icon-box" style="background: #e0f2fe; color: #0284c7;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                        </div>
                        <div>
                            <h1 class="portal-page-title">Welcome back, <?= e($studentName) ?>!</h1>
                            <p class="portal-page-sub">
                                Student ID: <strong><?= e($studentNum) ?></strong> &bull; 
                                Section: <strong><?= e($student['grade_level'] ?? 'BSCS 11A1') ?></strong> &bull; 
                                School Year: <strong><?= e($student['school_year'] ?? (date('Y') . '-' . (date('Y') + 1))) ?></strong>
                            </p>
                        </div>
                    </div>

                    <?php if ($primaryFee && $totalRemaining > 0): ?>
                        <button class="btn-pay-now-main" onclick="openPaymentModal(<?= $primaryFee['id'] ?>, '<?= e($termDescription) ?>', <?= $totalRemaining ?>)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                <line x1="1" y1="10" x2="23" y2="10"></line>
                            </svg>
                            Pay Tuition Now &rarr;
                        </button>
                    <?php endif; ?>
                </div>

                <!-- 4 Quick Metric Summary Cards -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px; margin-bottom: 24px;">
                    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                        <div style="font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Remaining Tuition Balance</div>
                        <div style="font-size: 24px; font-weight: 800; color: <?= $totalRemaining > 0 ? '#dc2626' : '#059669' ?>;"><?= peso($totalRemaining) ?></div>
                        <div style="font-size: 11.5px; color: #64748b; margin-top: 4px;">
                            <?= $totalRemaining > 0 ? 'Due for active semester' : 'Tuition fully settled' ?>
                        </div>
                    </div>

                    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                        <div style="font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Total Paid to Date</div>
                        <div style="font-size: 24px; font-weight: 800; color: #059669;"><?= peso($totalPaid) ?></div>
                        <div style="font-size: 11.5px; color: #64748b; margin-top: 4px;">
                            <?= $calcPct ?>% of total assessment
                        </div>
                    </div>

                    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                        <div style="font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Account Billing Status</div>
                        <div>
                            <?php if ($totalRemaining <= 0): ?>
                                <span class="pill-badge green" style="font-size: 12px; padding: 4px 12px;">Fully Paid &#10003;</span>
                            <?php elseif ($totalPaid > 0): ?>
                                <span class="pill-badge blue" style="font-size: 12px; padding: 4px 12px; background: #e0f2fe; color: #0369a1;">Partial (<?= $calcPct ?>%)</span>
                            <?php else: ?>
                                <span class="pill-badge red" style="font-size: 12px; padding: 4px 12px; background: #fee2e2; color: #b91c1c;">Unpaid Assessment</span>
                            <?php endif; ?>
                        </div>
                        <div style="font-size: 11.5px; color: #64748b; margin-top: 6px;">Enrollment standing: Clear</div>
                    </div>

                    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                        <div style="font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Official Receipts</div>
                        <div style="font-size: 24px; font-weight: 800; color: #2563eb;"><?= count($payments) ?></div>
                        <div style="font-size: 11.5px; color: #64748b; margin-top: 4px;">
                            <a href="<?= APP_URL ?>/public/student/?view=receipts" style="color: #2563eb; text-decoration: none; font-weight: 600;">View Official Receipts &rarr;</a>
                        </div>
                    </div>
                </div>

                <!-- 2-Column Section: Recent Receipts & Quick Shortcuts -->
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 24px;">
                    <!-- Left: Recent Payments Log -->
                    <div class="section-box" style="margin-bottom: 0;">
                        <div class="section-box-header">
                            <div class="section-header-left">
                                <div class="section-header-icon">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                </div>
                                <h2 class="section-box-title">Recent Payment Transactions</h2>
                            </div>
                            <a href="<?= APP_URL ?>/public/student/?view=history" style="font-size: 12px; font-weight: 600; color: #2563eb; text-decoration: none;">View Ledger &rarr;</a>
                        </div>

                        <div class="table-responsive">
                            <table class="styled-fintech-table">
                                <thead>
                                    <tr>
                                        <th>RECEIPT (OR#)</th>
                                        <th>AMOUNT</th>
                                        <th>METHOD</th>
                                        <th>DATE</th>
                                        <th style="text-align: right;">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $recentStudentPayments = array_slice($payments, 0, 3);
                                    if (empty($recentStudentPayments)): 
                                    ?>
                                        <tr>
                                            <td colspan="5" style="text-align: center; padding: 36px; color: #64748b;">
                                                No payment transactions recorded yet. Click "Pay Tuition Now" to make a payment.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($recentStudentPayments as $sp): ?>
                                            <tr>
                                                <td><code style="font-weight: 700; color: #0f172a;"><?= e($sp['or_number']) ?></code></td>
                                                <td><strong style="color: #059669;"><?= peso((float)$sp['amount']) ?></strong></td>
                                                <td><span class="badge-method-online"><?= strtoupper(e($sp['payment_method'])) ?></span></td>
                                                <td style="color: #475569; font-size: 12px;"><?= date('M d, Y', strtotime($sp['paid_at'])) ?></td>
                                                <td style="text-align: right;">
                                                    <button class="btn-view-receipt-outline" onclick="openOfficialReceiptModal('<?= e($sp['or_number']) ?>', '<?= peso((float)$sp['amount']) ?>', '<?= date('M d, Y h:i A', strtotime($sp['paid_at'])) ?>', '<?= strtoupper(e($sp['payment_method'])) ?>', '<?= e($sp['fee_desc']) ?>', '<?= e($studentName) ?>', '<?= e($studentNum) ?>', '<?= e($student['grade_level'] ?? 'BSCS 11A1') ?>')">
                                                        View OR
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Right: Profile Info & Quick Actions -->
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                            <h4 style="font-size: 14px; font-weight: 700; color: #0f172a; margin: 0 0 12px;">Student Portal Shortcuts</h4>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <a href="<?= APP_URL ?>/public/student/?view=fees" class="btn" style="justify-content: flex-start; gap: 8px; width: 100%;">
                                    <span>💳</span> My Tuition Breakdown &amp; Balance
                                </a>
                                <a href="<?= APP_URL ?>/public/student/?view=receipts" class="btn" style="justify-content: flex-start; gap: 8px; width: 100%;">
                                    <span>🧾</span> Official Receipts (OR) Directory
                                </a>
                                <a href="<?= APP_URL ?>/public/student/?view=history" class="btn" style="justify-content: flex-start; gap: 8px; width: 100%;">
                                    <span>📜</span> Complete Payment History Ledger
                                </a>
                                <a href="<?= APP_URL ?>/public/student/?view=password" class="btn" style="justify-content: flex-start; gap: 8px; width: 100%;">
                                    <span>🔑</span> Security &amp; Password Settings
                                </a>
                            </div>
                        </div>

                        <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 12px; padding: 16px;">
                            <div style="display: flex; align-items: center; gap: 6px; font-weight: 700; font-size: 13px; color: #0b3d2e; margin-bottom: 6px;">
                                <span>📢</span> Student Advisory
                            </div>
                            <p style="font-size: 12px; color: #64748b; line-height: 1.5; margin: 0;">
                                Electronic Official Receipts are automatically timestamped and verified. Keep your official receipts for exam clearance and enrollment validation.
                            </p>
                        </div>
                    </div>
                </div>

            <!-- ============================================== -->
            <!-- VIEW 1: MY TUITION & ASSESSMENT (FEES)         -->
            <!-- ============================================== -->
            <?php elseif ($currentView === 'fees'): ?>
                <!-- PAGE HEADER ROW -->
                <div class="portal-header-row">
                    <div class="portal-title-flex">
                        <div class="wallet-icon-box">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 12V8H6a2 2 0 0 1-2-2c0-1.1.9-2 2-2h12v4"></path>
                                <path d="M4 6v12c0 1.1.9 2 2 2h14v-4"></path>
                                <circle cx="18" cy="14" r="1"></circle>
                            </svg>
                        </div>
                        <div>
                            <h1 class="portal-page-title">My Tuition &amp; Fees</h1>
                            <p class="portal-page-sub">View your tuition assessment breakdown and submit balance payments securely.</p>
                        </div>
                    </div>

                    <?php if ($primaryFee && $totalRemaining > 0): ?>
                        <button class="btn-pay-now-main" onclick="openPaymentModal(<?= $primaryFee['id'] ?>, '<?= e($termDescription) ?>', <?= $totalRemaining ?>)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                <line x1="1" y1="10" x2="23" y2="10"></line>
                            </svg>
                            Pay Tuition Now &rarr;
                        </button>
                    <?php endif; ?>
                </div>

                <!-- HERO 2-COLUMN GRID -->
                <div class="hero-dashboard-grid">
                    <!-- Left: Current Outstanding Balance -->
                    <div class="card-box">
                        <div class="card-eyebrow">Current Outstanding Balance</div>
                        <div class="card-term-sub">Term: <?= e($termDescription) ?></div>

                        <div class="balance-amount-row">
                            <div class="balance-huge-red"><?= peso($totalRemaining) ?></div>
                            <?php if ($primaryFee && $totalRemaining > 0): ?>
                                <button class="btn-pay-pill-dark" onclick="openPaymentModal(<?= $primaryFee['id'] ?>, '<?= e($termDescription) ?>', <?= $totalRemaining ?>)">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                        <line x1="1" y1="10" x2="23" y2="10"></line>
                                    </svg>
                                    Pay Tuition &gt;
                                </button>
                            <?php else: ?>
                                <span class="pill-badge green" style="font-size: 12px; padding: 5px 14px;">Fully Paid &#10003;</span>
                            <?php endif; ?>
                        </div>

                        <div class="balance-meta-bullet">
                            <span class="dot"></span>
                            <span><strong><?= peso($totalPaid) ?></strong> paid of <strong><?= peso($totalFees) ?></strong> total assessment (<?= $calcPct ?>%)</span>
                        </div>

                        <div class="progress-track-wrapper">
                            <div class="progress-bar-track">
                                <div class="progress-bar-fill-green" style="width: <?= $calcPct ?>%;"></div>
                            </div>
                            <span class="progress-pct-label"><?= $calcPct ?>%</span>
                        </div>
                    </div>

                    <!-- Right: Motivation Banner Card -->
                    <div class="motivation-hero-card">
                        <div class="motivation-text-col">
                            <div class="motivation-quote-text">
                                &ldquo; Small steps today,<br>big dreams tomorrow. &rdquo;
                            </div>
                            <div class="quote-accent-bar"></div>
                        </div>

                        <div class="motivation-gfx-col">
                            <div class="floating-grad-badge">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                                    <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                                </svg>
                            </div>

                            <svg width="115" height="75" viewBox="0 0 115 75" fill="none" style="display: block;">
                                <rect x="5" y="52" width="105" height="16" rx="3" fill="#1e3a8a"/>
                                <rect x="3" y="52" width="8" height="16" rx="2" fill="#0f172a"/>
                                <path d="M108 55H15v10h93c2 0 3-1 3-3v-4c0-2-1-3-3-3z" fill="#f8fafc"/>
                                <rect x="22" y="58" width="26" height="2.5" rx="1.2" fill="#93c5fd"/>
                                
                                <rect x="16" y="34" width="94" height="15" rx="3" fill="#0284c7"/>
                                <rect x="13" y="34" width="8" height="15" rx="2" fill="#0369a1"/>
                                <path d="M108 37H24v9h84c2 0 3-1 3-2.5v-4c0-1.5-1-2.5-3-2.5z" fill="#f8fafc"/>
                                <rect x="32" y="39" width="22" height="2" rx="1" fill="#bae6fd"/>

                                <rect x="26" y="17" width="84" height="14" rx="3" fill="#d97706"/>
                                <rect x="23" y="17" width="8" height="14" rx="2" fill="#b45309"/>
                                <path d="M108 20H34v8h74c2 0 3-1 3-2v-4c0-1-1-2-3-2z" fill="#f8fafc"/>
                                <rect x="42" y="22" width="18" height="2" rx="1" fill="#fde68a"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- TUITION FEE ASSESSMENT BREAKDOWN SECTION -->
                <div class="section-box">
                    <div class="section-box-header">
                        <div class="section-header-left">
                            <div class="section-header-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                            </div>
                            <h2 class="section-box-title">Tuition Fee Assessment Breakdown</h2>
                        </div>
                        <span class="section-box-meta"><?= e($termDescription) ?></span>
                    </div>

                    <div class="table-responsive">
                        <table class="styled-fintech-table">
                            <thead>
                                <tr>
                                    <th style="width: 45%;">
                                        <span style="display: inline-flex; align-items: center; gap: 6px;">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                                <line x1="3" y1="9" x2="21" y2="9"></line>
                                                <line x1="9" y1="21" x2="9" y2="9"></line>
                                            </svg>
                                            Fee Aspect / Category
                                        </span>
                                    </th>
                                    <th style="width: 35%;">Classification</th>
                                    <th style="text-align: right; width: 20%;">Assessed Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($breakdownItems)): ?>
                                    <tr>
                                        <td colspan="3" style="text-align: center; padding: 28px; color: #64748b;">
                                            No itemized breakdown assigned yet.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($breakdownItems as $item): ?>
                                        <?php
                                            $name = $item['category_name'];
                                            $isSubj = (stripos($name, 'Subject') !== false || stripos($name, 'Tuition') !== false);
                                            $isLms  = (stripos($name, 'LMS') !== false || stripos($name, 'E-Learning') !== false);
                                            $isLab  = (stripos($name, 'Lab') !== false);
                                            $isLib  = (stripos($name, 'Library') !== false);
                                            $isMed  = (stripos($name, 'Medical') !== false || stripos($name, 'Clinic') !== false);
                                            $isAth  = (stripos($name, 'Athletic') !== false || stripos($name, 'Activity') !== false);
                                            $isReg  = (stripos($name, 'Registration') !== false || stripos($name, 'Matriculation') !== false);
                                            
                                            $color = 'blue';
                                            $badgeText = 'Institutional Standard Fee';
                                            if ($isSubj) {
                                                $color = 'green';
                                                $badgeText = 'Course Units / Academic';
                                            } elseif ($isLab || $isLms) {
                                                $color = 'blue';
                                                $badgeText = 'Technology & Lab Facilities';
                                            } elseif ($isMed) {
                                                $color = 'green';
                                                $badgeText = 'Student Health & Welfare';
                                            } elseif ($isAth) {
                                                $color = 'purple';
                                                $badgeText = 'Co-Curricular & Sports';
                                            } elseif ($isReg || $isLib) {
                                                $color = 'purple';
                                                $badgeText = 'Academic Support';
                                            } else {
                                                $color = 'purple';
                                                $badgeText = 'Institutional Standard Fee';
                                            }
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="aspect-cell">
                                                    <div class="aspect-icon-badge <?= $color ?>">
                                                        <?php if ($isSubj): ?>
                                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                                                                <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                                                            </svg>
                                                        <?php elseif ($isLms || $isLab): ?>
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                                                <line x1="8" y1="21" x2="16" y2="21"></line>
                                                                <line x1="12" y1="17" x2="12" y2="21"></line>
                                                            </svg>
                                                        <?php elseif ($isMed): ?>
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                                                            </svg>
                                                        <?php else: ?>
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                                            </svg>
                                                        <?php endif; ?>
                                                    </div>
                                                    <span><?= e($name) ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="pill-badge <?= $color ?>">
                                                    <?= $badgeText ?>
                                                </span>
                                            </td>
                                            <td style="text-align: right; font-weight: 800; color: #0f172a;">
                                                <?= peso((float)$item['amount']) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>

                                    <!-- Total Row -->
                                    <tr class="table-total-row">
                                        <td colspan="2">
                                            <div class="aspect-cell">
                                                <div class="aspect-icon-badge blue">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <rect x="4" y="2" width="16" height="20" rx="2"></rect>
                                                        <line x1="8" y1="6" x2="16" y2="6"></line>
                                                        <line x1="16" y1="14" x2="16" y2="18"></line>
                                                        <path d="M8 10h.01M12 10h.01M16 10h.01M8 14h.01M12 14h.01M8 18h.01M12 18h.01"></path>
                                                    </svg>
                                                </div>
                                                <span>Total Institutional Assessment</span>
                                            </div>
                                        </td>
                                        <td style="text-align: right; font-size: 15px; font-weight: 900; color: #0f172a;">
                                            <?= peso($totalFees) ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Green Information Notice -->
                    <div class="green-notice-callout">
                        <div class="green-notice-circle">i</div>
                        <div class="green-notice-body">
                            <strong class="notice-title">Notice regarding fee aspects:</strong>
                            The breakdown above reflects your school fee assessment allocation. When you make a payment, it is directly deducted from your <strong>Total Remaining Balance</strong>.
                        </div>
                    </div>
                </div>

            <!-- ============================================== -->
            <!-- VIEW 2: DEDICATED PAYMENT HISTORY PAGE         -->
            <!-- ============================================== -->
            <?php elseif ($currentView === 'history'): ?>
                <div class="portal-header-row">
                    <div class="portal-title-flex">
                        <div class="wallet-icon-box" style="background: #ecfdf5; color: #059669;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <div>
                            <h1 class="portal-page-title">Payment History &amp; Official Receipts</h1>
                            <p class="portal-page-sub">Review all your recorded payment transactions and download or view electronic official receipts.</p>
                        </div>
                    </div>

                    <?php if ($primaryFee && $totalRemaining > 0): ?>
                        <button class="btn-pay-now-main" onclick="openPaymentModal(<?= $primaryFee['id'] ?>, '<?= e($termDescription) ?>', <?= $totalRemaining ?>)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                <line x1="1" y1="10" x2="23" y2="10"></line>
                            </svg>
                            Pay Tuition Now &rarr;
                        </button>
                    <?php endif; ?>
                </div>

                <!-- Metrics Summary Cards -->
                <div class="metrics-grid-3">
                    <div class="metric-mini-card">
                        <div class="metric-mini-label">Total Amount Paid</div>
                        <div class="metric-mini-val" style="color: #059669;"><?= peso($totalPaid) ?></div>
                    </div>
                    <div class="metric-mini-card">
                        <div class="metric-mini-label">Transactions Recorded</div>
                        <div class="metric-mini-val" style="color: #1d4ed8;"><?= count($payments) ?></div>
                    </div>
                    <div class="metric-mini-card">
                        <div class="metric-mini-label">Outstanding Balance</div>
                        <div class="metric-mini-val" style="color: <?= $totalRemaining > 0 ? '#dc2626' : '#059669' ?>;">
                            <?= peso($totalRemaining) ?>
                        </div>
                    </div>
                </div>

                <!-- Payments Table Section -->
                <div class="section-box">
                    <div class="section-box-header">
                        <div class="section-header-left">
                            <div class="section-header-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                            </div>
                            <h2 class="section-box-title">Transactions Log</h2>
                        </div>
                        <span class="section-box-meta"><?= count($payments) ?> Transactions Found</span>
                    </div>

                    <div class="table-responsive">
                        <table class="styled-fintech-table" id="paymentsTable">
                            <thead>
                                <tr>
                                    <th>OFFICIAL RECEIPT (OR#)</th>
                                    <th>ASSESSMENT TERM</th>
                                    <th>PAYMENT METHOD</th>
                                    <th>AMOUNT PAID</th>
                                    <th>DATE &amp; TIME</th>
                                    <th style="text-align: right;">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($payments)): ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 48px; color: #64748b;">
                                            No payment transactions recorded yet. Click "Pay Tuition Now" to submit your first installment.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($payments as $p): ?>
                                        <tr>
                                            <td><code style="font-weight: 700; color: #0f172a; font-size: 13px;"><?= e($p['or_number']) ?></code></td>
                                            <td style="color: #475569;"><?= e($p['fee_desc']) ?></td>
                                            <td>
                                                <span class="badge-method-online">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                        <path d="M5 12.55a11 11 0 0 1 14.08 0"></path>
                                                        <path d="M1.42 9a16 16 0 0 1 21.16 0"></path>
                                                        <path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path>
                                                        <line x1="12" y1="20" x2="12.01" y2="20"></line>
                                                    </svg>
                                                    <?= strtoupper(e($p['payment_method'])) ?>
                                                </span>
                                            </td>
                                            <td><strong style="color: #059669; font-weight: 800; font-size: 13.5px;"><?= peso((float)$p['amount']) ?></strong></td>
                                            <td style="color: #475569; font-size: 12.5px;"><?= date('M d, Y h:i A', strtotime($p['paid_at'])) ?></td>
                                            <td style="text-align: right; white-space: nowrap;">
                                                <button class="btn-view-receipt-outline" onclick="openOfficialReceiptModal('<?= e($p['or_number']) ?>', '<?= peso((float)$p['amount']) ?>', '<?= date('M d, Y h:i A', strtotime($p['paid_at'])) ?>', '<?= strtoupper(e($p['payment_method'])) ?>', '<?= e($p['fee_desc']) ?>', '<?= e($studentName) ?>', '<?= e($studentNum) ?>', '<?= e($student['grade_level'] ?? 'BSCS 11A1') ?>')">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                        <circle cx="12" cy="12" r="3"></circle>
                                                    </svg>
                                                    View OR
                                                </button>
                                                <a href="<?= APP_URL ?>/public/student/?view=receipt&or=<?= urlencode($p['or_number']) ?>" target="_blank" class="btn-view-receipt-outline" style="background: #0b3d2e; color: #ffffff; border-color: #0b3d2e; text-decoration: none;" title="Open and print official receipt">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                                                    Print
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <!-- ============================================== -->
            <!-- VIEW 2.5: DEDICATED OFFICIAL RECEIPTS PAGE     -->
            <!-- ============================================== -->
            <?php elseif ($currentView === 'receipts'): ?>
                <div class="portal-header-row">
                    <div class="portal-title-flex">
                        <div class="wallet-icon-box" style="background: #eff6ff; color: #2563eb;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                        </div>
                        <div>
                            <h1 class="portal-page-title">Official Electronic Receipts (OR)</h1>
                            <p class="portal-page-sub">Verified institutional payment vouchers generated upon transaction confirmation.</p>
                        </div>
                    </div>

                    <?php if ($primaryFee && $totalRemaining > 0): ?>
                        <button class="btn-pay-now-main" onclick="openPaymentModal(<?= $primaryFee['id'] ?>, '<?= e($termDescription) ?>', <?= $totalRemaining ?>)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                <line x1="1" y1="10" x2="23" y2="10"></line>
                            </svg>
                            Pay Tuition Now &rarr;
                        </button>
                    <?php endif; ?>
                </div>

                <!-- Metrics Row -->
                <div class="metrics-grid-3">
                    <div class="metric-mini-card">
                        <div class="metric-mini-label">Total Receipts Issued</div>
                        <div class="metric-mini-val" style="color: #2563eb;"><?= count($payments) ?></div>
                    </div>
                    <div class="metric-mini-card">
                        <div class="metric-mini-label">Total Verified Payments</div>
                        <div class="metric-mini-val" style="color: #059669;"><?= peso($totalPaid) ?></div>
                    </div>
                    <div class="metric-mini-card">
                        <div class="metric-mini-label">Official Status</div>
                        <div class="metric-mini-val" style="color: #0b3d2e; font-size: 18px;">
                            Clearance Valid
                        </div>
                    </div>
                </div>

                <!-- Receipts Grid / Card Directory -->
                <div class="section-box">
                    <div class="section-box-header">
                        <div class="section-header-left">
                            <div class="section-header-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                </svg>
                            </div>
                            <h2 class="section-box-title">Official Receipt Records</h2>
                        </div>
                        <span class="section-box-meta"><?= count($payments) ?> Official Receipts Available</span>
                    </div>

                    <?php if (empty($payments)): ?>
                        <div style="text-align: center; padding: 60px 20px; color: #64748b;">
                            <div style="font-size: 36px; margin-bottom: 12px;">🧾</div>
                            <h3 style="font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 6px;">No Official Receipts Available</h3>
                            <p style="font-size: 13px; max-width: 440px; margin: 0 auto 16px;">
                                You have not made any tuition payments yet. Once a payment is submitted and confirmed, your official electronic receipt will appear here for download and printing.
                            </p>
                            <?php if ($primaryFee && $totalRemaining > 0): ?>
                                <button class="btn-pay-now-main" onclick="openPaymentModal(<?= $primaryFee['id'] ?>, '<?= e($termDescription) ?>', <?= $totalRemaining ?>)">
                                    Pay Tuition Now &rarr;
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div style="padding: 20px; display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 18px;">
                            <?php foreach ($payments as $p): ?>
                                <div style="background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 18px; position: relative; box-shadow: 0 2px 4px rgba(0,0,0,0.03); transition: all 0.2s ease;">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; border-bottom: 1px dashed #e2e8f0; padding-bottom: 10px;">
                                        <div>
                                            <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Official Receipt No.</div>
                                            <div style="font-size: 16px; font-weight: 800; color: #0f172a; font-family: monospace;"><?= e($p['or_number']) ?></div>
                                        </div>
                                        <span class="badge-pill paid" style="font-size: 10.5px; padding: 3px 8px; background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;">
                                            &#10003; VERIFIED
                                        </span>
                                    </div>

                                    <div style="font-size: 12.5px; line-height: 1.8; color: #334155; margin-bottom: 14px;">
                                        <div style="display: flex; justify-content: space-between;">
                                            <span style="color: #64748b;">Assessment:</span>
                                            <strong style="text-align: right; max-width: 190px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= e($p['fee_desc']) ?></strong>
                                        </div>
                                        <div style="display: flex; justify-content: space-between;">
                                            <span style="color: #64748b;">Amount Paid:</span>
                                            <strong style="color: #059669; font-size: 14px;"><?= peso((float)$p['amount']) ?></strong>
                                        </div>
                                        <div style="display: flex; justify-content: space-between;">
                                            <span style="color: #64748b;">Payment Method:</span>
                                            <span style="font-weight: 600; text-transform: uppercase;"><?= e($p['payment_method']) ?></span>
                                        </div>
                                        <div style="display: flex; justify-content: space-between;">
                                            <span style="color: #64748b;">Date Issued:</span>
                                            <span><?= date('M d, Y h:i A', strtotime($p['paid_at'])) ?></span>
                                        </div>
                                    </div>

                                    <div style="display: flex; gap: 8px;">
                                        <button class="btn-view-receipt-outline" style="flex: 1; justify-content: center; padding: 8px;"
                                            onclick="openOfficialReceiptModal('<?= e($p['or_number']) ?>', '<?= peso((float)$p['amount']) ?>', '<?= date('M d, Y h:i A', strtotime($p['paid_at'])) ?>', '<?= strtoupper(e($p['payment_method'])) ?>', '<?= e($p['fee_desc']) ?>', '<?= e($studentName) ?>', '<?= e($studentNum) ?>', '<?= e($student['grade_level'] ?? 'BSCS 11A1') ?>')">
                                            <span>👁</span> View Details
                                        </button>
                                        <a href="<?= APP_URL ?>/public/student/?view=receipt&or=<?= urlencode($p['or_number']) ?>" target="_blank" class="btn" style="flex: 1; justify-content: center; padding: 8px; background: #0b3d2e; color: #ffffff; border: 1px solid #0b3d2e; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                                            <span>🖨</span> Print Voucher
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

            <!-- ============================================== -->
            <!-- VIEW 3: DEDICATED CHANGE PASSWORD PAGE         -->
            <!-- ============================================== -->
            <?php elseif ($currentView === 'password'): ?>
                <div class="portal-header-row">
                    <div class="portal-title-flex">
                        <div class="wallet-icon-box" style="background: #fef3c7; color: #b45309;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="portal-page-title">Account Security &amp; Password</h1>
                            <p class="portal-page-sub">Update your student portal login password to keep your account secure.</p>
                        </div>
                    </div>
                </div>

                <div class="card-box" style="max-width: 480px;">
                    <form method="POST" action="<?= APP_URL ?>/public/student/?view=password">
                        <input type="hidden" name="action" value="change_password">

                        <div class="form-group" style="margin-bottom: 16px;">
                            <label class="form-label" for="pageNewPassword" style="font-weight: 700; font-size: 13px;">New Password *</label>
                            <div style="position: relative;">
                                <input type="password" class="form-control" name="new_password" id="pageNewPassword" required minlength="6" placeholder="Minimum 6 characters" style="padding-right: 42px;">
                                <button type="button" id="btnTogglePagePassword" title="Show/Hide Password" aria-label="Toggle password visibility" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #64748b; font-size: 16px; padding: 4px 6px; display: flex; align-items: center; justify-content: center; line-height: 1;">
                                    👁️
                                </button>
                            </div>
                            <small style="color: #64748b; font-size: 11.5px; margin-top: 4px; display: block;">Use a strong combination of letters, numbers, and symbols.</small>
                        </div>

                        <div style="margin-top: 20px;">
                            <button type="submit" class="btn dark" style="padding: 10px 24px;">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

        </main>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: PAY TUITION MODAL                   -->
<!-- ========================================== -->
<div class="modal-backdrop" id="paymentInputModal">
    <div class="modal-window" style="max-width: 440px;">
        <button class="modal-close-x" id="btnClosePaymentInputModal">&times;</button>
        <h2 class="modal-header-title">Pay Tuition Fee</h2>

        <form method="POST" action="<?= APP_URL ?>/public/student/?view=<?= e($currentView) ?>" id="paymentForm">
            <input type="hidden" name="action" value="pay_tuition">
            <input type="hidden" name="fee_id" id="modalFeeId">

            <div class="form-group">
                <label class="form-label">Tuition Assessment</label>
                <div id="modalFeeDesc" style="font-weight: 600; padding: 8px 12px; background: #f4f4f5; border-radius: 6px; font-size: 13px;"></div>
            </div>

            <div class="form-group">
                <label class="form-label">Outstanding Balance</label>
                <div id="modalFeeBalance" style="font-size: 18px; font-weight: 700; color: #dc2626;"></div>
            </div>

            <!-- Quick Amount Select Buttons -->
            <div class="form-group">
                <label class="form-label">Quick Select Amount</label>
                <div class="quick-pay-btn-group" style="display: flex; gap: 8px; margin-bottom: 10px; flex-wrap: wrap;">
                    <button type="button" class="btn" style="padding: 5px 12px; font-size: 11.5px;" id="pillFullPay">Full Balance</button>
                    <button type="button" class="btn" style="padding: 5px 12px; font-size: 11.5px;" id="pillHalfPay">50% Balance</button>
                    <button type="button" class="btn" style="padding: 5px 12px; font-size: 11.5px;" id="pill1k">₱1,000</button>
                    <button type="button" class="btn" style="padding: 5px 12px; font-size: 11.5px;" id="pill5k">₱5,000</button>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="payAmount">Payment Amount (₱) *</label>
                <input type="number" step="0.01" min="1" class="form-control" name="amount" id="payAmount" required placeholder="Enter amount to pay">
            </div>

            <div class="form-group">
                <label class="form-label">Payment Channel</label>
                <select class="form-control" name="payment_method" required>
                    <option value="online">Online Payment Portal</option>
                    <option value="gcash">GCash</option>
                    <option value="maya">Maya</option>
                    <option value="bank_transfer">Bank Transfer (BDO / BPI / UnionBank)</option>
                    <option value="card">Debit / Credit Card</option>
                </select>
            </div>

            <div style="margin-top: 18px;">
                <button type="submit" class="btn dark" style="width: 100%; justify-content: center; padding: 11px;">
                    Confirm &amp; Submit Payment
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ==================================================== -->
<!-- MODAL: NATIONAL COLLEGE OF SCIENCE AND TECHNOLOGY   -->
<!--        OFFICIAL ELECTRONIC RECEIPT VOUCHER           -->
<!-- ==================================================== -->
<div class="modal-backdrop" id="officialReceiptModal">
    <div class="modal-window" style="max-width: 680px; padding: 0; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
        <!-- Top Modal Action Bar (Hidden on Print) -->
        <div class="no-print" style="display: flex; justify-content: space-between; align-items: center; background: #0b3d2e; color: #ffffff; padding: 12px 20px;">
            <div style="font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <span>🧾</span> Official Electronic Receipt
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <button type="button" onclick="window.printReceiptModal()" style="background: #ffffff; color: #0b3d2e; border: none; padding: 7px 16px; border-radius: 6px; font-weight: 700; font-size: 12.5px; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                    Print / Save PDF
                </button>
                <button type="button" onclick="closeReceiptModal()" style="background: rgba(255,255,255,0.2); color: #fff; border: none; width: 28px; height: 28px; border-radius: 50%; cursor: pointer; font-size: 16px; display: flex; align-items: center; justify-content: center;">&times;</button>
            </div>
        </div>

        <!-- Printable Receipt Content Area -->
        <div id="ncstPrintableReceipt" style="padding: 32px 36px; background: #ffffff; color: #0f172a; font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;">
            <!-- School Institutional Header -->
            <div style="text-align: center; border-bottom: 2.5px solid #0b3d2e; padding-bottom: 16px; margin-bottom: 20px;">
                <div style="font-size: 11px; font-weight: 700; letter-spacing: 2px; color: #0b3d2e; text-transform: uppercase; margin-bottom: 2px;">Republic of the Philippines</div>
                <h1 style="font-size: 21px; font-weight: 900; color: #0b3d2e; margin: 0; letter-spacing: 0.5px; text-transform: uppercase;">
                    National College of Science and Technology
                </h1>
                <p style="font-size: 11.5px; color: #475569; margin: 3px 0 0; line-height: 1.4;">
                    Emilio Aguinaldo Highway, Dasmariñas City, Cavite 4114, Philippines<br>
                    Student Tuition &amp; Assessment Management System (PayTrack)
                </p>
                <div style="display: inline-block; margin-top: 10px; background: #f0fdf4; border: 1.5px solid #86efac; color: #166534; font-weight: 800; font-size: 12px; padding: 4px 18px; border-radius: 20px; letter-spacing: 1px;">
                    OFFICIAL ELECTRONIC RECEIPT
                </div>
            </div>

            <!-- Receipt Meta Bar -->
            <div style="display: flex; justify-content: space-between; align-items: center; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 16px; margin-bottom: 18px;">
                <div>
                    <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Receipt No (OR#):</span>
                    <strong id="rcptOrNumber" style="font-size: 15px; color: #0b3d2e; font-family: monospace; margin-left: 6px;">OR-2024-00001</strong>
                </div>
                <div>
                    <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Date Issued:</span>
                    <strong id="rcptDate" style="font-size: 12.5px; color: #1e293b; margin-left: 6px;">—</strong>
                </div>
            </div>

            <!-- Student & Academic Particulars Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px 24px; font-size: 13px; margin-bottom: 20px;">
                <div style="border-bottom: 1px dashed #e2e8f0; padding-bottom: 6px;">
                    <div style="font-size: 10.5px; font-weight: 700; color: #64748b; text-transform: uppercase;">Student Name</div>
                    <div id="rcptStudentName" style="font-weight: 800; color: #0f172a; font-size: 14px;"><?= e($studentName) ?></div>
                </div>
                <div style="border-bottom: 1px dashed #e2e8f0; padding-bottom: 6px;">
                    <div style="font-size: 10.5px; font-weight: 700; color: #64748b; text-transform: uppercase;">Student ID Number</div>
                    <div id="rcptStudentId" style="font-weight: 800; color: #0f172a; font-family: monospace; font-size: 14px;"><?= e($studentNum) ?></div>
                </div>
                <div style="border-bottom: 1px dashed #e2e8f0; padding-bottom: 6px;">
                    <div style="font-size: 10.5px; font-weight: 700; color: #64748b; text-transform: uppercase;">Course &amp; Section</div>
                    <div id="rcptCourseSection" style="font-weight: 700; color: #0f172a;"><?= e($student['grade_level'] ?? 'BSCS 11A1') ?></div>
                </div>
                <div style="border-bottom: 1px dashed #e2e8f0; padding-bottom: 6px;">
                    <div style="font-size: 10.5px; font-weight: 700; color: #64748b; text-transform: uppercase;">Payment Channel</div>
                    <div id="rcptPaymentMethod" style="font-weight: 700; color: #0b3d2e; text-transform: uppercase;">ONLINE</div>
                </div>
            </div>

            <!-- Payment Purpose & Breakdown Box -->
            <div style="background: #fafafa; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: 700; color: #334155; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; margin-bottom: 8px;">
                    <span>Particulars / Payment Description</span>
                    <span>Amount</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 13.5px; color: #0f172a; padding: 4px 0;">
                    <span id="rcptFeeDesc">Tuition Fee Assessment Installment</span>
                    <strong id="rcptAmountSub" style="color: #0b3d2e;">₱0.00</strong>
                </div>
            </div>

            <!-- Big Highlighted Amount Box -->
            <div style="background: #f0fdf4; border: 2px solid #86efac; border-radius: 10px; padding: 16px; text-align: center; margin-bottom: 22px;">
                <div style="font-size: 11px; font-weight: 800; color: #166534; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px;">Total Amount Received</div>
                <div id="rcptAmount" style="font-size: 30px; font-weight: 900; color: #15803d; line-height: 1.2;">₱0.00</div>
                <div style="font-size: 11.5px; color: #166534; font-weight: 600; margin-top: 4px;">
                    &#10003; Verified &amp; Credited to Student Tuition Ledger
                </div>
            </div>

            <!-- Footer Signatures & Validation -->
            <div style="display: flex; justify-content: space-between; align-items: flex-end; padding-top: 14px; border-top: 1px solid #e2e8f0; font-size: 11.5px;">
                <div style="color: #64748b; line-height: 1.5; max-width: 320px;">
                    <strong>National College of Science and Technology</strong><br>
                    Official System Electronic Receipt<br>
                    Valid for Examination Clearance &amp; Verification
                </div>
                <div style="text-align: center; width: 200px;">
                    <div style="font-weight: 800; font-size: 12px; color: #0b3d2e; margin-bottom: 2px;">FINANCE &amp; CASHIERING</div>
                    <div style="border-top: 1.5px solid #0f172a; padding-top: 4px; color: #475569; font-size: 10.5px;">
                        Authorized Electronic Signature / Stamp
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Action Bar (Hidden on Print) -->
        <div class="no-print" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 14px 24px; display: flex; justify-content: flex-end; gap: 10px;">
            <button type="button" onclick="closeReceiptModal()" class="btn" style="padding: 9px 18px;">Close</button>
            <button type="button" onclick="window.printReceiptModal()" class="btn" style="background: #0b3d2e; color: #ffffff; border: 1px solid #0b3d2e; padding: 9px 22px; font-weight: 700; gap: 6px;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                <span>Print Official Receipt</span>
            </button>
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // ── SweetAlert Confirmations ──────────────────────────────
    const LOGOUT_URL = '<?= APP_URL ?>/public/?action=logout';

    document.getElementById('btnLogout').addEventListener('click', function () {
        Swal.fire({
            title: 'Log out?',
            text: 'You will be returned to the login page.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, log out',
            cancelButtonText: 'Stay',
            confirmButtonColor: '#18181b',
            cancelButtonColor: '#e4e4e7',
            customClass: { cancelButton: 'swal-cancel-dark' }
        }).then(result => {
            if (result.isConfirmed) window.location.href = LOGOUT_URL;
        });
    });

    // ── Drawer Navigation Elements ────────────────────────────
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const btnOpenSidebar = document.getElementById('btnOpenSidebar');
    const btnCloseSidebar = document.getElementById('btnCloseSidebar');

    function openSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('active');
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
    }

    if (btnOpenSidebar) btnOpenSidebar.addEventListener('click', openSidebar);
    if (btnCloseSidebar) btnCloseSidebar.addEventListener('click', closeSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);

    // ── Payment Modal Logic ───────────────────────────────────
    const paymentModal = document.getElementById('paymentInputModal');
    const btnClosePaymentModal = document.getElementById('btnClosePaymentInputModal');
    const modalFeeId = document.getElementById('modalFeeId');
    const modalFeeDesc = document.getElementById('modalFeeDesc');
    const modalFeeBalance = document.getElementById('modalFeeBalance');
    const payAmountInput = document.getElementById('payAmount');

    let currentRemaining = 0;

    function openPaymentModal(feeId, feeDesc, remaining) {
        modalFeeId.value = feeId;
        modalFeeDesc.textContent = feeDesc;
        modalFeeBalance.textContent = '₱' + parseFloat(remaining).toLocaleString('en-PH', {minimumFractionDigits: 2});
        payAmountInput.value = remaining > 0 ? remaining.toFixed(2) : '';
        payAmountInput.max = remaining;
        currentRemaining = remaining;
        paymentModal.classList.add('active');
    }

    if (btnClosePaymentModal) {
        btnClosePaymentModal.addEventListener('click', () => paymentModal.classList.remove('active'));
    }

    // Quick Select Handlers
    document.getElementById('pillFullPay')?.addEventListener('click', () => {
        payAmountInput.value = currentRemaining.toFixed(2);
    });
    document.getElementById('pillHalfPay')?.addEventListener('click', () => {
        payAmountInput.value = (currentRemaining / 2).toFixed(2);
    });
    document.getElementById('pill1k')?.addEventListener('click', () => {
        payAmountInput.value = Math.min(1000, currentRemaining).toFixed(2);
    });
    document.getElementById('pill5k')?.addEventListener('click', () => {
        payAmountInput.value = Math.min(5000, currentRemaining).toFixed(2);
    });

    // ── Password Eye Toggle in Page Form ──────────────────────
    const btnTogglePagePassword = document.getElementById('btnTogglePagePassword');
    const pageNewPasswordInput = document.getElementById('pageNewPassword');
    if (btnTogglePagePassword && pageNewPasswordInput) {
        btnTogglePagePassword.addEventListener('click', () => {
            const isPassword = pageNewPasswordInput.getAttribute('type') === 'password';
            pageNewPasswordInput.setAttribute('type', isPassword ? 'text' : 'password');
            btnTogglePagePassword.textContent = isPassword ? '🙈' : '👁️';
            btnTogglePagePassword.setAttribute('title', isPassword ? 'Hide password' : 'Show password');
        });
    }

    // ── Official Receipt Modal & Printable Logic ──────────────
    window.openOfficialReceiptModal = function(orNum, amount, date, method, feeDesc, studentName, studentNum, course) {
        const rcptOr = document.getElementById('rcptOrNumber');
        const rcptD = document.getElementById('rcptDate');
        const rcptName = document.getElementById('rcptStudentName');
        const rcptId = document.getElementById('rcptStudentId');
        const rcptSec = document.getElementById('rcptCourseSection');
        const rcptMeth = document.getElementById('rcptPaymentMethod');
        const rcptDesc = document.getElementById('rcptFeeDesc');
        const rcptAmtSub = document.getElementById('rcptAmountSub');
        const rcptAmt = document.getElementById('rcptAmount');

        if (rcptOr) rcptOr.textContent = orNum || 'OR-2024-00001';
        if (rcptD) rcptD.textContent = date || '—';
        if (rcptName) rcptName.textContent = studentName || '<?= e($studentName) ?>';
        if (rcptId) rcptId.textContent = studentNum || '<?= e($studentNum) ?>';
        if (rcptSec) rcptSec.textContent = course || '<?= e($student['grade_level'] ?? 'BSCS 11A1') ?>';
        if (rcptMeth) rcptMeth.textContent = (method || 'ONLINE').toUpperCase();
        if (rcptDesc) rcptDesc.textContent = feeDesc || 'Tuition Fee Assessment Installment';
        if (rcptAmtSub) rcptAmtSub.textContent = amount || '₱0.00';
        if (rcptAmt) rcptAmt.textContent = amount || '₱0.00';

        const modal = document.getElementById('officialReceiptModal');
        if (modal) {
            modal.classList.add('active');
        }
    };

    window.closeReceiptModal = function() {
        const modal = document.getElementById('officialReceiptModal');
        if (modal) {
            modal.classList.remove('active');
        }
    };

    window.printReceiptModal = function() {
        window.print();
    };

    window.printOfficialReceiptVoucher = function(orNum, amount, date, method, feeDesc, studentName, studentNum, course) {
        openOfficialReceiptModal(orNum, amount, date, method, feeDesc, studentName, studentNum, course);
        setTimeout(() => {
            window.print();
        }, 300);
    };

    window.viewReceiptSummary = function(orNum, amount, date, method, feeDesc) {
        openOfficialReceiptModal(orNum, amount, date, method, feeDesc, '<?= e($studentName) ?>', '<?= e($studentNum) ?>', '<?= e($student['grade_level'] ?? 'BSCS 11A1') ?>');
    };

    const officialReceiptModalEl = document.getElementById('officialReceiptModal');
    if (officialReceiptModalEl) {
        officialReceiptModalEl.addEventListener('click', function(e) {
            if (e.target === officialReceiptModalEl) {
                closeReceiptModal();
            }
        });
    }

    // Form Validation (Prevent Overpayment)
    const paymentForm = document.getElementById('paymentForm');
    if (paymentForm) {
        paymentForm.addEventListener('submit', function (e) {
            const enteredAmount = parseFloat(payAmountInput.value) || 0;
            if (enteredAmount <= 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Amount',
                    text: 'Please enter a valid payment amount greater than ₱0.',
                    confirmButtonColor: '#1e3a8a'
                });
                return false;
            }
            if (enteredAmount > currentRemaining) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Overpayment Not Allowed',
                    text: 'Payment amount (₱' + enteredAmount.toLocaleString('en-PH', {minimumFractionDigits: 2}) + ') cannot exceed your remaining balance of ₱' + currentRemaining.toLocaleString('en-PH', {minimumFractionDigits: 2}) + '.',
                    confirmButtonColor: '#e11d48'
                });
                return false;
            }
        });
    }

    // Trigger Success Popup on Payment Flash
    <?php if (!empty($paymentSuccess)): ?>
        Swal.fire({
            icon: 'success',
            title: 'Payment Successful!',
            html: `
                <div style="text-align: left; font-size: 13px; line-height: 1.8; color: #3f3f46;">
                    <p style="margin-bottom: 8px;">Your payment has been successfully recorded and an official receipt has been sent to your email.</p>
                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px; margin: 10px 0;">
                        <p style="margin: 3px 0;"><strong>Receipt (OR#):</strong> <?= e($paymentSuccess['or_number']) ?></p>
                        <p style="margin: 3px 0;"><strong>Amount Paid:</strong> <?= peso((float)$paymentSuccess['amount']) ?></p>
                        <p style="margin: 3px 0;"><strong>Remaining Balance:</strong> <?= peso((float)$paymentSuccess['remaining']) ?></p>
                        <p style="margin: 3px 0;"><strong>Payment Method:</strong> <?= e($paymentSuccess['method'] ?? 'ONLINE') ?></p>
                        <p style="margin: 3px 0;"><strong>Date:</strong> <?= e($paymentSuccess['date']) ?></p>
                    </div>
                    <p style="font-size: 12px; color: #059669; margin-top: 6px;">Your tuition balance has been updated immediately.</p>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'View in Payment History &rarr;',
            cancelButtonText: 'Stay on This Page',
            confirmButtonColor: '#059669',
            cancelButtonColor: '#64748b'
        }).then(result => {
            if (result.isConfirmed) {
                window.location.href = '<?= APP_URL ?>/public/student/?view=history';
            }
        });
    <?php endif; ?>

    // Search filter for tables
    const studentSearchInput = document.getElementById('studentSearchInput');
    if (studentSearchInput) {
        studentSearchInput.addEventListener('keyup', function() {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll('table tbody tr');
            rows.forEach(r => {
                const text = r.textContent.toLowerCase();
                r.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }
</script>

</body>
</html>
