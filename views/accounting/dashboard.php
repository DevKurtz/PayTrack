<?php
/**
 * PayTrack — Accounting Office Dashboard
 * Manages Student Tuition Fee Assessments, Class Details, Dynamic Fee Breakdown,
 * Manual Payments, Official Receipts, Fee Categories, and Email Logs.
 */
$accountingName = Auth::username() ?? 'accounting';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayTrack — Accounting Portal</title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/portal-chrome.css?v=<?= filemtime(__DIR__ . '/../../assets/css/portal-chrome.css') ?>">
    <style>
        .badge-pending {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 11.5px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .badge-assessed {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 11.5px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .fee-category-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 8px;
            transition: all 0.15s ease;
        }
        .fee-category-row:hover {
            border-color: #cbd5e1;
            background: #ffffff;
        }
        .fee-category-row.excluded {
            opacity: 0.5;
            background: #f1f5f9;
        }
        .fee-category-row .cat-info {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
        }
        .fee-category-row input[type="number"] {
            width: 130px;
            padding: 6px 10px;
            font-size: 13px;
            font-weight: 700;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            text-align: right;
        }
        .total-computed-box {
            background: #ecfdf5;
            border: 2px solid #10b981;
            border-radius: 10px;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 14px;
        }
    </style>
</head>
<body class="admin-body">

<div class="app-shell" id="dashboardLayout">
    <!-- Mobile Backdrop Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar Navigation (Client-Approved Light Sidebar) -->
    <aside class="sidebar" id="sidebar">
        <div class="org">
            <div class="org-icon">₱</div>
            <div class="org-text">
                <div class="org-name">PayTrack</div>
                <div class="org-team">Accounting Portal</div>
            </div>
            <button class="mobile-menu-btn" id="btnCloseSidebar" style="margin-left: auto; width: 28px; height: 28px; font-size: 14px;">&times;</button>
        </div>

        <ul class="nav">
            <li>
                <a href="<?= APP_URL ?>/public/accounting/?view=home" class="nav-item <?= $currentView === 'home' ? 'active' : '' ?>">
                    <span class="ic">&#8962;</span> Home
                </a>
            </li>
            <li>
                <a href="<?= APP_URL ?>/public/accounting/?view=students" class="nav-item <?= $currentView === 'students' ? 'active' : '' ?>">
                    <span class="ic">&#127891;</span> Students &amp; Balances
                    <?php if ($pendingAssessmentCount > 0): ?>
                        <span class="badge-count"><?= $pendingAssessmentCount ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li>
                <a href="<?= APP_URL ?>/public/accounting/?view=transactions" class="nav-item <?= $currentView === 'transactions' ? 'active' : '' ?>">
                    <span class="ic">&#8644;</span> Payment Transactions
                    <span class="badge-count"><?= count($payments) ?></span>
                </a>
            </li>
            <li>
                <a href="<?= APP_URL ?>/public/accounting/?view=fees" class="nav-item <?= $currentView === 'fees' ? 'active' : '' ?>">
                    <span class="ic">&#128179;</span> Tuition Assessments
                    <span class="badge-count"><?= count($fees) ?></span>
                </a>
            </li>
            <li>
                <a href="<?= APP_URL ?>/public/accounting/?view=categories" class="nav-item <?= $currentView === 'categories' ? 'active' : '' ?>">
                    <span class="ic">&#9881;</span> Fee Categories
                </a>
            </li>
            <li>
                <a href="<?= APP_URL ?>/public/accounting/?view=logs" class="nav-item <?= $currentView === 'logs' ? 'active' : '' ?>">
                    <span class="ic">&#9993;</span> Notification Logs
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
        <header class="topbar" style="background: #ffffff; border-bottom: 1px solid #e2e8f0;">
            <button class="mobile-menu-btn" id="btnOpenSidebar" aria-label="Toggle Navigation">&#9776;</button>
            <div class="search-container" style="position: relative;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" id="accountingSearch" placeholder="Search student, OR#, or assessment..." autocomplete="off">
                <span class="kbd-badge">Ctrl + K</span>
            </div>

            <div class="topbar-actions" style="display: flex; align-items: center; gap: 12px;">
                <?php
                $accNotifs = [];
                if ($pendingAssessmentCount > 0) {
                    $accNotifs[] = [
                        'type' => 'warning',
                        'icon' => '⏳',
                        'title' => $pendingAssessmentCount . ' student(s) awaiting assessment',
                        'desc' => 'Assign class details and publish tuition fee breakdowns.',
                        'time' => 'Action Required',
                        'link' => APP_URL . '/public/accounting/?view=students',
                        'unread' => true,
                    ];
                }
                $accNotifs[] = [
                    'type' => 'success',
                    'icon' => '₱',
                    'title' => 'Revenue collected: ' . peso($totalRevenue),
                    'desc' => count($payments) . ' verified payments · ' . $collectionRate . '% collection rate',
                    'time' => 'Finance Snapshot',
                    'link' => APP_URL . '/public/accounting/?view=transactions',
                    'unread' => false,
                ];
                if ($totalReceivables > 0) {
                    $accNotifs[] = [
                        'type' => 'info',
                        'icon' => '📊',
                        'title' => 'Outstanding receivables',
                        'desc' => peso($totalReceivables) . ' remaining across active tuition assessments.',
                        'time' => 'Receivables',
                        'link' => APP_URL . '/public/accounting/?view=fees',
                        'unread' => true,
                    ];
                }
                $accUnread = 0;
                foreach ($accNotifs as $n) {
                    if (!empty($n['unread'])) $accUnread++;
                }
                ?>
                <div class="notif-wrapper">
                    <button type="button" class="notif-bell-btn" id="btnAccNotif" aria-label="Notifications" title="Notifications">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <?php if ($accUnread > 0): ?>
                            <span class="notif-dot-red"></span>
                        <?php endif; ?>
                    </button>
                    <div class="notif-dropdown-menu" id="accNotifDropdown" role="region" aria-label="Notifications">
                        <div class="notif-header">
                            <div class="notif-header-title">
                                <span>🔔 Notifications</span>
                                <span class="notif-header-badge"><?= $accUnread ?> new</span>
                            </div>
                        </div>
                        <ul class="notif-list-body">
                            <?php foreach ($accNotifs as $notif): ?>
                                <li>
                                    <a href="<?= e($notif['link']) ?>" class="notif-row <?= !empty($notif['unread']) ? 'unread' : '' ?>">
                                        <div class="notif-icon-circle <?= e($notif['type']) ?>"><?= $notif['icon'] ?></div>
                                        <div>
                                            <div class="notif-title"><?= e($notif['title']) ?></div>
                                            <div class="notif-desc"><?= e($notif['desc']) ?></div>
                                            <div class="notif-meta-time"><?= e($notif['time']) ?></div>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="notif-footer">
                            <a href="<?= APP_URL ?>/public/accounting/?view=students">Review Students &amp; Balances &rarr;</a>
                        </div>
                    </div>
                </div>

                <div class="user-chip-container">
                    <div class="user-chip-meta">
                        <span class="user-chip-name">Accounting Office</span>
                        <span class="user-chip-id">(<?= e($accountingName) ?>)</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <?php if (!empty($successMsg)): ?>
            <div class="app-toast success"><span>&#10003;</span> <?= e($successMsg) ?></div>
        <?php endif; ?>
        <?php if (!empty($errorMsg)): ?>
            <div class="app-toast error"><span>&#9888;</span> <?= e($errorMsg) ?></div>
        <?php endif; ?>

        <main class="content">

            <!-- ============================================== -->
            <!-- VIEW 0: ACCOUNTING HOME / FINANCIAL OVERVIEW  -->
            <!-- ============================================== -->
            <?php if ($currentView === 'home'): ?>
                <div class="portal-header-row">
                    <div class="portal-title-flex">
                        <div class="wallet-icon-box" style="background: #ecfdf5; color: #059669;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                        </div>
                        <div>
                            <h1 class="portal-page-title">Welcome back, Accounting!</h1>
                            <p class="portal-page-sub">
                                Signed in as <strong><?= e($accountingName) ?></strong> &bull;
                                <?= date('M d, Y') ?> &bull;
                                Finance &amp; tuition operations
                            </p>
                        </div>
                    </div>
                    <?php if ($pendingAssessmentCount > 0): ?>
                        <a href="<?= APP_URL ?>/public/accounting/?view=students" class="btn" style="background:#b45309;color:#fff;font-weight:700;padding:10px 18px;">
                            Review <?= $pendingAssessmentCount ?> Pending &rarr;
                        </a>
                    <?php else: ?>
                        <a href="<?= APP_URL ?>/public/accounting/?view=transactions" class="btn" style="background:#0b3d2e;color:#fff;font-weight:700;padding:10px 18px;">
                            Record Payment &rarr;
                        </a>
                    <?php endif; ?>
                </div>

                <div class="metric-summary-grid">
                    <div class="metric-summary-card">
                        <div class="metric-summary-label">Total Revenue Collected</div>
                        <div class="metric-summary-value" style="color:#065f46;"><?= peso($totalRevenue) ?></div>
                        <div class="metric-summary-sub" style="color:#059669;font-weight:600;"><?= count($payments) ?> verified payments</div>
                    </div>
                    <div class="metric-summary-card">
                        <div class="metric-summary-label">Total Assessed Tuition</div>
                        <div class="metric-summary-value" style="color:#1e3a8a;"><?= peso($totalAssessed) ?></div>
                        <div class="metric-summary-sub"><?= count($fees) ?> active term assessments</div>
                    </div>
                    <div class="metric-summary-card">
                        <div class="metric-summary-label">Outstanding Receivables</div>
                        <div class="metric-summary-value" style="color:#ea580c;"><?= peso($totalReceivables) ?></div>
                        <div class="metric-summary-sub"><?= $collectionRate ?>% collection rate</div>
                    </div>
                    <div class="metric-summary-card">
                        <div class="metric-summary-label">Enrolled Students</div>
                        <div class="metric-summary-value"><?= count($students) ?></div>
                        <div class="metric-summary-sub" style="font-weight:700;color:<?= $pendingAssessmentCount > 0 ? '#b45309' : '#15803d' ?>;">
                            <?= $pendingAssessmentCount > 0 ? "{$pendingAssessmentCount} awaiting assessment" : 'All assessed' ?>
                        </div>
                    </div>
                </div>

                <?php if ($pendingAssessmentCount > 0): ?>
                    <div class="section-box" style="background:#fffbeb;border-color:#fde68a;">
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                            <div>
                                <h4 style="margin:0;font-size:15px;color:#92400e;font-weight:700;">Action Required: <?= $pendingAssessmentCount ?> Student(s) Awaiting Tuition Assessment</h4>
                                <p style="margin:4px 0 0;font-size:12.5px;color:#b45309;">Set class details, configure fee breakdown, and publish tuition assessments.</p>
                            </div>
                            <a href="<?= APP_URL ?>/public/accounting/?view=students" class="btn" style="background:#b45309;color:#fff;font-weight:700;">Review Students &rarr;</a>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="dashboard-2col-grid">
                    <div class="section-box" style="margin-bottom:0;">
                        <div class="section-box-header">
                            <div class="section-header-left">
                                <div class="section-header-icon" style="background:#ecfdf5;color:#059669;">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                                        <polyline points="17 6 23 6 23 12"></polyline>
                                    </svg>
                                </div>
                                <h2 class="section-box-title">Recent Payment Collections</h2>
                            </div>
                            <a href="<?= APP_URL ?>/public/accounting/?view=transactions" style="font-size:12px;font-weight:600;color:#2563eb;text-decoration:none;">View all &rarr;</a>
                        </div>
                        <div class="table-responsive">
                            <table class="styled-fintech-table">
                                <thead>
                                    <tr>
                                        <th>OR Number</th>
                                        <th>Student</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($payments)): ?>
                                        <tr><td colspan="5" style="text-align:center;color:#94a3b8;padding:24px;">No transactions recorded yet.</td></tr>
                                    <?php else: ?>
                                        <?php foreach (array_slice($payments, 0, 5) as $p): ?>
                                            <tr>
                                                <td><code style="font-weight:700;"><?= e($p['or_number']) ?></code></td>
                                                <td><strong><?= e($p['first_name'] . ' ' . $p['last_name']) ?></strong></td>
                                                <td style="color:#059669;font-weight:700;"><?= peso($p['amount']) ?></td>
                                                <td><span class="badge success"><?= strtoupper(e($p['payment_method'])) ?></span></td>
                                                <td><?= date('M d, Y', strtotime($p['paid_at'] ?? $p['created_at'])) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:16px;">
                        <div class="portal-shortcuts-card">
                            <h4>Accounting Portal Shortcuts</h4>
                            <div class="portal-shortcut-list">
                                <a href="<?= APP_URL ?>/public/accounting/?view=students" class="btn"><span>🎓</span> Students &amp; Balances</a>
                                <a href="<?= APP_URL ?>/public/accounting/?view=transactions" class="btn"><span>💳</span> Payment Transactions</a>
                                <a href="<?= APP_URL ?>/public/accounting/?view=fees" class="btn"><span>📋</span> Tuition Assessments</a>
                                <a href="<?= APP_URL ?>/public/accounting/?view=categories" class="btn"><span>⚙️</span> Fee Categories</a>
                            </div>
                        </div>
                        <div class="portal-advisory-card">
                            <div class="portal-advisory-title"><span>📢</span> Accounting Advisory</div>
                            <p>Publish tuition assessments promptly after enrollment so students can pay online and receive official electronic receipts.</p>
                        </div>
                    </div>
                </div>

            <!-- ============================================== -->
            <!-- VIEW 1: STUDENTS & BALANCES                   -->
            <!-- ============================================== -->
            <?php elseif ($currentView === 'students'): ?>

                <div class="portal-header-row">
                    <div class="portal-title-flex">
                        <div class="wallet-icon-box" style="background:#ecfdf5;color:#059669;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="portal-page-title">Students &amp; Tuition Assessments</h1>
                            <p class="portal-page-sub">Assign tuition assessments, update class details, and customize fee categories per student.</p>
                        </div>
                    </div>
                    <?php if ($pendingAssessmentCount > 0): ?>
                        <span class="pill-badge" style="background:#fef3c7;color:#b45309;font-weight:700;padding:7px 14px;border-radius:9999px;font-size:12px;border:1px solid #fde68a;">
                            <?= $pendingAssessmentCount ?> Pending Assessment
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Guide UI Table Card with Controls -->
                <div class="card-box" style="padding: 0; overflow: hidden;">
                    <!-- Table Top Controls -->
                    <div class="table-top-controls-row">
                        <div class="table-top-title-col">
                            <div class="table-icon-badge" style="background: #ecfdf5; color: #059669;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                    <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                                </svg>
                            </div>
                            <div>
                                <div style="font-size: 15px; font-weight: 700; color: #0f172a;">Student Roster</div>
                                <div style="font-size: 12px; color: #64748b;"><?= count($students) ?> enrolled students</div>
                            </div>
                        </div>
                        <div class="table-top-actions-col">
                            <div class="table-search-box">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                                <input type="text" id="studentsTableSearch" placeholder="Search student..." autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <!-- Filter Pill Row -->
                    <div class="pills-and-legend-row" style="padding: 0 20px 12px;">
                        <div class="filter-pills-group" id="studentFilterPills">
                            <button class="filter-tab-pill active" data-filter="all">All Students</button>
                            <button class="filter-tab-pill" data-filter="pending">⏳ Pending Assessment</button>
                            <button class="filter-tab-pill" data-filter="assessed">✓ Assessed</button>
                        </div>
                    </div>

                    <div class="table-responsive" style="padding: 0 20px 20px;">
                        <table class="styled-fintech-table" id="tableStudents">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student ID</th>
                                    <th>Student Name</th>
                                    <th>Class / Section</th>
                                    <th>Assessment Status</th>
                                    <th>Assessed Total</th>
                                    <th>Remaining Balance</th>
                                    <th style="text-align: right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($students)): ?>
                                    <tr><td colspan="8" style="text-align: center; color: #94a3b8; padding: 32px;">No students enrolled yet.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($students as $idx => $s): ?>
                                        <?php 
                                            $hasAssmt = !empty($s['has_assessment']);
                                            $totalAmt = $s['primary_fee']['total_amount'] ?? 0;
                                            $paidAmt  = $s['primary_fee']['amount_paid'] ?? 0;
                                            $remAmt   = max(0, $totalAmt - $paidAmt);
                                            $avatarColors = ['#3b82f6','#8b5cf6','#ec4899','#f59e0b','#10b981','#ef4444','#06b6d4'];
                                            $avatarColor  = $avatarColors[$idx % count($avatarColors)];
                                            $initials = strtoupper(mb_substr($s['first_name'],0,1) . mb_substr($s['last_name'],0,1));
                                        ?>
                                        <tr data-status="<?= $hasAssmt ? 'assessed' : 'pending' ?>">
                                            <td style="color:#94a3b8; font-size:12px; width:36px;"><?= $idx + 1 ?></td>
                                            <td><code><?= e($s['student_id']) ?></code></td>
                                            <td>
                                                <div style="display:flex; align-items:center; gap:10px;">
                                                    <div style="width:32px; height:32px; border-radius:50%; background:<?= $avatarColor ?>; color:#fff; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; flex-shrink:0;"><?= $initials ?></div>
                                                    <div>
                                                        <strong><?= e($s['first_name'] . ' ' . $s['last_name']) ?></strong>
                                                        <div style="font-size: 11px; color: #64748b;"><?= e($s['email']) ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span style="font-weight: 600; color: #334155;"><?= e($s['grade_level'] ?? '—') ?></span>
                                                <div style="font-size: 11px; color: #94a3b8;">S.Y. <?= e($s['school_year'] ?? '—') ?></div>
                                            </td>
                                            <td>
                                                <?php if ($hasAssmt): ?>
                                                    <span class="status-badge-pill active">✓ Assessed</span>
                                                <?php else: ?>
                                                    <span class="status-badge-pill inactive">⏳ Pending</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $hasAssmt ? '<strong>' . peso($totalAmt) . '</strong>' : '<span style="color:#94a3b8;">—</span>' ?></td>
                                            <td>
                                                <?php if ($hasAssmt): ?>
                                                    <strong style="color: <?= $remAmt > 0 ? '#b45309' : '#166534' ?>;"><?= peso($remAmt) ?></strong>
                                                <?php else: ?>
                                                    <span style="color:#94a3b8;">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="text-align: right;">
                                                <button type="button" 
                                                    class="btn-primary-blue"
                                                    style="<?= !$hasAssmt ? 'background:#b45309; border-color:#b45309;' : '' ?>"
                                                    onclick="openAssignAssessmentModal(<?= htmlspecialchars(json_encode($s), ENT_QUOTES) ?>)">
                                                    <?= $hasAssmt ? 'Update' : 'Assign Tuition' ?> &rarr;
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <script>
                // Students table filter pills
                document.querySelectorAll('#studentFilterPills .filter-tab-pill').forEach(pill => {
                    pill.addEventListener('click', function() {
                        document.querySelectorAll('#studentFilterPills .filter-tab-pill').forEach(p => p.classList.remove('active'));
                        this.classList.add('active');
                        const filter = this.dataset.filter;
                        document.querySelectorAll('#tableStudents tbody tr').forEach(row => {
                            if (filter === 'all') { row.style.display = ''; return; }
                            row.style.display = row.dataset.status === filter ? '' : 'none';
                        });
                    });
                });
                // Inline search for students table
                document.getElementById('studentsTableSearch')?.addEventListener('input', function() {
                    const q = this.value.toLowerCase().trim();
                    document.querySelectorAll('#tableStudents tbody tr').forEach(row => {
                        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
                    });
                });
                </script>

            <!-- ============================================== -->
            <!-- VIEW 2: TRANSACTIONS & MANUAL PAYMENTS        -->
            <!-- ============================================== -->
            <?php elseif ($currentView === 'transactions'): ?>

                <div class="portal-header-row">
                    <div class="portal-title-flex">
                        <div class="wallet-icon-box" style="background:#eff6ff;color:#2563eb;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="portal-page-title">Payment Transactions &amp; Receipts</h1>
                            <p class="portal-page-sub">Complete ledger of tuition payments and official receipts generated.</p>
                        </div>
                    </div>
                </div>

                <!-- Table Card with Controls -->
                <div class="card-box" style="padding: 0; overflow: hidden;">
                    <div class="table-top-controls-row">
                        <div class="table-top-title-col">
                            <div class="table-icon-badge" style="background: #eff6ff; color: #2563eb;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="1" x2="12" y2="23"></line>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                </svg>
                            </div>
                            <div>
                                <div style="font-size: 15px; font-weight: 700; color: #0f172a;">Payment Ledger</div>
                                <div style="font-size: 12px; color: #64748b;"><?= count($payments) ?> transactions recorded</div>
                            </div>
                        </div>
                        <div class="table-top-actions-col">
                            <div class="table-search-box">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                                <input type="text" id="txnSearch" placeholder="Search OR#, student..." autocomplete="off">
                            </div>
                            <button type="button" class="btn-primary-blue" onclick="openManualPaymentModal()">
                                + Record Payment
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive" style="padding: 0 20px 20px;">
                        <table class="styled-fintech-table">
                            <thead>
                                <tr>
                                    <th>OR Number</th>
                                    <th>Student</th>
                                    <th>Amount Paid</th>
                                    <th>Payment Method</th>
                                    <th>Notes / Remarks</th>
                                    <th>Date &amp; Time</th>
                                    <th style="text-align: right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($payments)): ?>
                                    <tr><td colspan="7" style="text-align: center; color: #94a3b8; padding: 32px;">No payment records found.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($payments as $p): ?>
                                        <tr>
                                            <td><code style="font-weight: 700;"><?= e($p['or_number']) ?></code></td>
                                            <td>
                                                <strong><?= e($p['first_name'] . ' ' . $p['last_name']) ?></strong>
                                                <div style="font-size: 11px; color: #64748b;"><?= e($p['student_num']) ?></div>
                                            </td>
                                            <td style="color: #059669; font-weight: 700;"><?= peso($p['amount']) ?></td>
                                            <td><span class="badge success"><?= strtoupper(e($p['payment_method'])) ?></span></td>
                                            <td><small style="color: #64748b;"><?= e($p['notes'] ?? 'Tuition installment') ?></small></td>
                                            <td><?= date('M d, Y h:i A', strtotime($p['paid_at'] ?? $p['created_at'])) ?></td>
                                            <td style="text-align: right;">
                                                <button type="button" class="btn" style="padding: 5px 10px; font-size: 11.5px;" onclick="viewReceiptVoucher(<?= htmlspecialchars(json_encode($p), ENT_QUOTES) ?>)">
                                                    <span>🖨 Print OR</span>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div><!-- /table-responsive -->
                </div><!-- /card-box -->
                <script>
                document.getElementById('txnSearch')?.addEventListener('input', function() {
                    const q = this.value.toLowerCase().trim();
                    document.querySelectorAll('.styled-fintech-table tbody tr').forEach(row => {
                        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
                    });
                });
                </script>

            <!-- ============================================== -->
            <!-- VIEW 3: TUITION ASSESSMENTS DIRECTORY         -->
            <!-- ============================================== -->
            <?php elseif ($currentView === 'fees'): ?>

                <div class="portal-header-row">
                    <div class="portal-title-flex">
                        <div class="wallet-icon-box" style="background:#fff7ed;color:#ea580c;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                            </svg>
                        </div>
                        <div>
                            <h1 class="portal-page-title">Tuition Assessments Directory</h1>
                            <p class="portal-page-sub">Comprehensive list of tuition fee assessments assigned across academic terms.</p>
                        </div>
                    </div>
                </div>

                <!-- Table Card with Controls -->
                <div class="card-box" style="padding: 0; overflow: hidden;">
                    <div class="table-top-controls-row">
                        <div class="table-top-title-col">
                            <div class="table-icon-badge" style="background: #fffbeb; color: #b45309;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                </svg>
                            </div>
                            <div>
                                <div style="font-size: 15px; font-weight: 700; color: #0f172a;">Assessment Records</div>
                                <div style="font-size: 12px; color: #64748b;"><?= count($fees) ?> active assessments</div>
                            </div>
                        </div>
                        <div class="table-top-actions-col">
                            <div class="table-search-box">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                                <input type="text" id="feesSearch" placeholder="Search student, assessment..." autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive" style="padding: 0 20px 20px;">
                        <table class="styled-fintech-table">
                            <thead>
                                <tr>
                                    <th>Assessment ID</th>
                                    <th>Student</th>
                                    <th>Term &amp; Description</th>
                                    <th>Total Assessment</th>
                                    <th>Amount Paid</th>
                                    <th>Remaining Balance</th>
                                    <th>Status</th>
                                    <th style="text-align: right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($fees)): ?>
                                    <tr><td colspan="8" style="text-align: center; color: #94a3b8; padding: 32px;">No tuition assessments recorded yet.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($fees as $f): ?>
                                        <?php $fRem = max(0, (float)$f['total_amount'] - (float)$f['amount_paid']); ?>
                                        <tr>
                                            <td><code>#TF-<?= $f['id'] ?></code></td>
                                            <td>
                                                <strong><?= e($f['first_name'] . ' ' . $f['last_name']) ?></strong>
                                                <div style="font-size: 11px; color: #64748b;"><?= e($f['student_num']) ?></div>
                                            </td>
                                            <td>
                                                <strong><?= e($f['description']) ?></strong>
                                                <div style="font-size: 11px; color: #94a3b8;">Due: <?= !empty($f['due_date']) ? date('M d, Y', strtotime($f['due_date'])) : 'Open' ?></div>
                                            </td>
                                            <td><strong><?= peso($f['total_amount']) ?></strong></td>
                                            <td style="color: #059669; font-weight: 700;"><?= peso($f['amount_paid']) ?></td>
                                            <td style="color: <?= $fRem > 0 ? '#b45309' : '#166534' ?>; font-weight: 700;"><?= peso($fRem) ?></td>
                                            <td>
                                                <?php if ($f['status'] === 'paid'): ?>
                                                    <span class="badge success">Paid</span>
                                                <?php elseif ($f['status'] === 'partial'): ?>
                                                    <span class="badge warning">Partial</span>
                                                <?php else: ?>
                                                    <span class="badge danger">Unpaid</span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="text-align: right;">
                                                <form method="POST" action="<?= APP_URL ?>/public/accounting/?view=fees" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this tuition assessment?');">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="delete_fee">
                                                    <input type="hidden" name="fee_id" value="<?= $f['id'] ?>">
                                                    <button type="submit" class="btn" style="padding: 4px 8px; color: #ef4444; border-color: #fee2e2; background: #fff5f5;">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div><!-- /table-responsive -->
                </div><!-- /card-box -->
                <script>
                document.getElementById('feesSearch')?.addEventListener('input', function() {
                    const q = this.value.toLowerCase().trim();
                    document.querySelectorAll('.styled-fintech-table tbody tr').forEach(row => {
                        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
                    });
                });
                </script>

            <!-- ============================================== -->
            <!-- VIEW 4: FEE CATEGORIES CATALOG                -->
            <!-- ============================================== -->
            <?php elseif ($currentView === 'categories'): ?>

                <div class="portal-header-row">
                    <div class="portal-title-flex">
                        <div class="wallet-icon-box" style="background:#f5f3ff;color:#7c3aed;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                            </svg>
                        </div>
                        <div>
                            <h1 class="portal-page-title">Institutional Fee Categories</h1>
                            <p class="portal-page-sub">Configure standard default fee aspects and rates. Accounting staff can modify or exclude them per student.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-primary-blue" onclick="openAddCategoryModal()">+ Add Category</button>
                </div>

                <!-- Table Card -->
                <div class="card-box" style="padding: 0; overflow: hidden;">
                    <div class="table-top-controls-row">
                        <div class="table-top-title-col">
                            <div class="table-icon-badge" style="background: #f5f3ff; color: #7c3aed;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="3" width="7" height="7"></rect>
                                    <rect x="3" y="14" width="7" height="7"></rect>
                                    <rect x="14" y="14" width="7" height="7"></rect>
                                </svg>
                            </div>
                            <div>
                                <div style="font-size: 15px; font-weight: 700; color: #0f172a;">Fee Category Catalog</div>
                                <div style="font-size: 12px; color: #64748b;"><?= count($feeCategories) ?> configured categories</div>
                            </div>
                        </div>
                        <div class="table-top-actions-col">
                            <button type="button" class="btn-primary-blue" onclick="openAddCategoryModal()">
                                + Add Category
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive" style="padding: 0 20px 20px;">
                        <table class="styled-fintech-table">
                            <thead>
                                <tr>
                                    <th>Category Code</th>
                                    <th>Fee Category Name</th>
                                    <th>Default Amount</th>
                                    <th>Type</th>
                                    <th style="text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($feeCategories as $cat): ?>
                                    <tr>
                                        <td><code><?= e($cat['code']) ?></code></td>
                                        <td><strong><?= e($cat['name']) ?></strong></td>
                                        <td><strong style="color: #0b3d2e;"><?= peso($cat['default_amount']) ?></strong></td>
                                        <td>
                                            <?php if (!empty($cat['is_variable'])): ?>
                                                <span class="badge info">Variable Remainder</span>
                                            <?php else: ?>
                                                <span class="badge success">Fixed Aspect</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="text-align: right;">
                                            <button type="button" class="btn" style="padding: 4px 8px; font-size: 11.5px;" onclick="openEditCategoryModal(<?= htmlspecialchars(json_encode($cat), ENT_QUOTES) ?>)">Edit</button>
                                            <form method="POST" action="<?= APP_URL ?>/public/accounting/?view=categories" style="display: inline;" onsubmit="return confirm('Delete this category?');">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="action" value="delete_fee_category">
                                                <input type="hidden" name="category_id" value="<?= $cat['id'] ?>">
                                                <button type="submit" class="btn" style="padding: 4px 8px; color: #ef4444; border-color: #fee2e2; background: #fff5f5;">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <!-- ============================================== -->
            <!-- VIEW 5: EMAIL NOTIFICATION AUDIT LOGS         -->
            <!-- ============================================== -->
            <?php elseif ($currentView === 'logs'): ?>

                <div class="portal-header-row">
                    <div class="portal-title-flex">
                        <div class="wallet-icon-box" style="background:#f1f5f9;color:#334155;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                        </div>
                        <div>
                            <h1 class="portal-page-title">Notification &amp; Email Audit Trail</h1>
                            <p class="portal-page-sub">Historical log of all electronic tuition assessment notices, payment confirmations, and receipts.</p>
                        </div>
                    </div>
                </div>

                <!-- Table Card -->
                <div class="card-box" style="padding: 0; overflow: hidden;">
                    <div class="table-top-controls-row">
                        <div class="table-top-title-col">
                            <div class="table-icon-badge" style="background: #f1f5f9; color: #334155;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                            </div>
                            <div>
                                <div style="font-size: 15px; font-weight: 700; color: #0f172a;">Email Audit Log</div>
                                <div style="font-size: 12px; color: #64748b;"><?= count($emailLogs) ?> records found</div>
                            </div>
                        </div>
                        <div class="table-top-actions-col">
                            <div class="table-search-box">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                                <input type="text" id="logsSearch" placeholder="Search email, subject..." autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive" style="padding: 0 20px 20px;">
                        <table class="styled-fintech-table">
                            <thead>
                                <tr>
                                    <th>Recipient Email</th>
                                    <th>Subject</th>
                                    <th>Notification Type</th>
                                    <th>Status</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($emailLogs)): ?>
                                    <tr><td colspan="5" style="text-align: center; color: #94a3b8; padding: 32px;">No notification logs recorded.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($emailLogs as $log): ?>
                                        <tr>
                                            <td><strong><?= e($log['recipient_email']) ?></strong></td>
                                            <td><?= e($log['subject']) ?></td>
                                            <td><span class="badge info"><?= e($log['type']) ?></span></td>
                                            <td>
                                                <?php if ($log['status'] === 'sent'): ?>
                                                    <span class="status-badge-pill active">✓ Sent</span>
                                                <?php else: ?>
                                                    <span class="status-badge-pill suspended">✗ Failed</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('M d, Y h:i A', strtotime($log['sent_at'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <script>
                document.getElementById('logsSearch')?.addEventListener('input', function() {
                    const q = this.value.toLowerCase().trim();
                    document.querySelectorAll('.styled-fintech-table tbody tr').forEach(row => {
                        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
                    });
                });
                </script>

            <?php endif; ?>

        </main>
    </div>
</div>

<!-- ==================================================== -->
<!-- MODAL: ASSIGN TUITION & CLASS DETAILS WITH EDITABLE FEES -->
<!-- ==================================================== -->
<div class="modal-backdrop" id="assignTuitionModal">
    <div class="modal-window" style="max-width: 620px;">
        <button class="modal-close-x" id="btnCloseAssignModal">&times;</button>
        <h2 class="modal-header-title">Tuition Fee Assessment &amp; Class Details</h2>
        <p class="modal-header-sub" id="assignModalStudentLabel">Student: Juan Dela Cruz (2023-53512)</p>

        <form method="POST" action="<?= APP_URL ?>/public/accounting/?view=students" id="assignTuitionForm">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="assign_tuition">
            <input type="hidden" name="student_id" id="assignStudentId" value="">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                <div class="form-group">
                    <label class="form-label" for="assignGradeLevel">Class / Program &amp; Section *</label>
                    <input type="text" class="form-control" name="grade_level" id="assignGradeLevel" required placeholder="e.g. BSCS 11A1">
                </div>
                <div class="form-group">
                    <label class="form-label" for="assignSchoolYear">School Year *</label>
                    <input type="text" class="form-control" name="school_year" id="assignSchoolYear" required value="<?= date('Y') . '-' . (date('Y') + 1) ?>">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                <div class="form-group">
                    <label class="form-label" for="assignSemester">Semester / Term *</label>
                    <select class="form-control" name="semester" id="assignSemester" required onchange="updateAssessmentDesc()">
                        <option value="1st Semester">1st Semester</option>
                        <option value="2nd Semester">2nd Semester</option>
                        <option value="Summer">Summer</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="assignDueDate">Assessment Due Date</label>
                    <input type="date" class="form-control" name="due_date" id="assignDueDate" value="<?= date('Y-m-d', strtotime('+30 days')) ?>">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label" for="assignDescription">Assessment Description *</label>
                <input type="text" class="form-control" name="description" id="assignDescription" required value="S.Y. <?= date('Y') . '-' . (date('Y') + 1) ?> - 1st Semester Tuition">
            </div>

            <!-- Dynamic Fee Categories Checklist & Rate Customization -->
            <div style="margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <label class="form-label" style="margin: 0; font-weight: 700;">Fee Breakdown &amp; Institutional Aspects</label>
                    <small style="color: #64748b;">Uncheck to exclude; edit rate directly</small>
                </div>

                <div id="feeCategoriesContainer" style="max-height: 240px; overflow-y: auto; padding-right: 4px;">
                    <?php foreach ($feeCategories as $cat): ?>
                        <div class="fee-category-row" id="catRow_<?= $cat['id'] ?>">
                            <div class="cat-info">
                                <input type="checkbox" 
                                       name="fee_included[<?= $cat['id'] ?>]" 
                                       id="chk_<?= $cat['id'] ?>" 
                                       value="1" 
                                       checked 
                                       onchange="toggleCategoryRow(<?= $cat['id'] ?>)">
                                <label for="chk_<?= $cat['id'] ?>" style="margin: 0; font-size: 13px; font-weight: 600; cursor: pointer;">
                                    <?= e($cat['name']) ?>
                                </label>
                                <input type="hidden" name="fee_name[<?= $cat['id'] ?>]" value="<?= e($cat['name']) ?>">
                            </div>
                            <div>
                                <span style="font-size: 12px; color: #64748b; margin-right: 4px;">₱</span>
                                <input type="number" 
                                       step="0.01" 
                                       min="0" 
                                       name="fee_amount[<?= $cat['id'] ?>]" 
                                       id="amt_<?= $cat['id'] ?>" 
                                       value="<?= number_format($cat['default_amount'], 2, '.', '') ?>" 
                                       oninput="recalcAssessmentTotal()">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Total Computed -->
                <div class="total-computed-box">
                    <div>
                        <span style="font-size: 12px; color: #047857; font-weight: 700; text-transform: uppercase;">Total Tuition Assessment</span>
                        <div style="font-size: 11px; color: #065f46;">Auto-calculated sum of included fees</div>
                    </div>
                    <div style="font-size: 22px; font-weight: 900; color: #047857;" id="computedTotalDisplay">₱0.00</div>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: #334155; cursor: pointer;">
                    <input type="checkbox" name="notify_email" value="1" checked>
                    <span><strong>Send automated email notification</strong> with fee breakdown to Student &amp; Parents</span>
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn" id="btnCancelAssignModal">Cancel</button>
                <button type="submit" class="btn" style="background: #0b3d2e; color: #fff; font-weight: 700; padding: 10px 22px;">
                    Post &amp; Save Assessment
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ==================================================== -->
<!-- MODAL: RECORD MANUAL PAYMENT                         -->
<!-- ==================================================== -->
<div class="modal-backdrop" id="manualPaymentModal">
    <div class="modal-window" style="max-width: 480px;">
        <button class="modal-close-x" id="btnCloseManualPay">&times;</button>
        <h2 class="modal-header-title">Record Counter Payment</h2>
        <p class="modal-header-sub">Process cash, GCash, or bank deposit at Accounting Cashier.</p>

        <form method="POST" action="<?= APP_URL ?>/public/accounting/?view=transactions">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="record_payment">

            <div class="form-group" style="margin-bottom: 14px;">
                <label class="form-label" for="payFeeId">Select Student Assessment *</label>
                <select class="form-control" name="fee_id" id="payFeeId" required>
                    <option value="">-- Choose student assessment --</option>
                    <?php foreach ($fees as $feeItem): ?>
                        <?php $rem = max(0, (float)$feeItem['total_amount'] - (float)$feeItem['amount_paid']); ?>
                        <?php if ($rem > 0): ?>
                            <option value="<?= $feeItem['id'] ?>">
                                <?= e($feeItem['first_name'] . ' ' . $feeItem['last_name']) ?> (<?= e($feeItem['student_num']) ?>) — Balance: <?= peso($rem) ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 14px;">
                <label class="form-label" for="payAmount">Payment Amount (₱) *</label>
                <input type="number" step="0.01" min="1" class="form-control" name="amount" id="payAmount" required placeholder="0.00">
            </div>

            <div class="form-group" style="margin-bottom: 14px;">
                <label class="form-label" for="payMethod">Payment Method *</label>
                <select class="form-control" name="payment_method" id="payMethod" required>
                    <option value="cash">Cash (Over-the-Counter)</option>
                    <option value="gcash">GCash</option>
                    <option value="maya">Maya</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="card">Debit / Credit Card</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 18px;">
                <label class="form-label" for="payNotes">Notes / Reference No</label>
                <input type="text" class="form-control" name="notes" id="payNotes" placeholder="e.g. Cashier Window 2, GCash Ref #12345">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn" onclick="document.getElementById('manualPaymentModal').classList.remove('active')">Cancel</button>
                <button type="submit" class="btn" style="background: #0b3d2e; color: #fff; font-weight: 700;">Record &amp; Issue Receipt</button>
            </div>
        </form>
    </div>
</div>

<!-- ==================================================== -->
<!-- MODAL: ADD / EDIT FEE CATEGORY                       -->
<!-- ==================================================== -->
<div class="modal-backdrop" id="categoryModal">
    <div class="modal-window" style="max-width: 440px;">
        <button class="modal-close-x" onclick="document.getElementById('categoryModal').classList.remove('active')">&times;</button>
        <h2 class="modal-header-title" id="catModalTitle">Add Fee Category</h2>
        <form method="POST" action="<?= APP_URL ?>/public/accounting/?view=categories">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="save_fee_category">
            <input type="hidden" name="category_id" id="catModalId" value="0">

            <div class="form-group" style="margin-bottom: 14px;">
                <label class="form-label" for="catModalName">Category Name *</label>
                <input type="text" class="form-control" name="name" id="catModalName" required placeholder="e.g. Laboratory Fee">
            </div>

            <div class="form-group" style="margin-bottom: 14px;">
                <label class="form-label" for="catModalAmount">Default Amount (₱) *</label>
                <input type="number" step="0.01" min="0" class="form-control" name="default_amount" id="catModalAmount" required placeholder="0.00">
            </div>

            <div class="form-group" style="margin-bottom: 14px;">
                <label style="display: flex; align-items: center; gap: 8px; font-size: 13px;">
                    <input type="checkbox" name="is_variable" id="catModalVariable" value="1">
                    <span>Variable Aspect (receives tuition remainder)</span>
                </label>
            </div>

            <div class="form-group" style="margin-bottom: 18px;">
                <label class="form-label" for="catModalSort">Sort Order</label>
                <input type="number" class="form-control" name="sort_order" id="catModalSort" value="10">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn" onclick="document.getElementById('categoryModal').classList.remove('active')">Cancel</button>
                <button type="submit" class="btn" style="background: #0b3d2e; color: #fff;">Save Category</button>
            </div>
        </form>
    </div>
</div>

<!-- ==================================================== -->
<!-- MODAL: PRINTABLE OFFICIAL RECEIPT                    -->
<!-- ==================================================== -->
<div class="modal-backdrop" id="receiptModal">
    <div class="modal-window" style="max-width: 540px;">
        <button class="modal-close-x" onclick="document.getElementById('receiptModal').classList.remove('active')">&times;</button>
        <div id="receiptPrintArea" style="padding: 20px; font-family: sans-serif;">
            <div style="text-align: center; border-bottom: 2px solid #0b3d2e; padding-bottom: 12px; margin-bottom: 16px;">
                <h3 style="margin: 0; color: #0b3d2e; font-size: 18px;">PayTrack — Official Payment Receipt</h3>
                <div style="font-size: 12px; color: #64748b;">National College of Science and Technology</div>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 8px;">
                <span>Official Receipt No:</span>
                <strong id="vOrNumber">OR-00000</strong>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 8px;">
                <span>Student Name:</span>
                <strong id="vStudentName">—</strong>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 8px;">
                <span>Student ID:</span>
                <span id="vStudentNum">—</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 8px;">
                <span>Payment Method:</span>
                <strong id="vMethod">CASH</strong>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 14px;">
                <span>Date &amp; Time:</span>
                <span id="vDate">—</span>
            </div>
            <div style="background: #ecfdf5; border: 1.5px solid #10b981; border-radius: 8px; padding: 14px; text-align: center; margin-bottom: 16px;">
                <div style="font-size: 11px; font-weight: bold; color: #065f46; text-transform: uppercase;">Amount Received</div>
                <div style="font-size: 26px; font-weight: 900; color: #059669;" id="vAmount">₱0.00</div>
                <div style="font-size: 11px; color: #047857; margin-top: 4px;">Verified &amp; Recorded by Accounting Cashier</div>
            </div>
            <div style="display: flex; justify-content: space-between; margin-top: 24px; padding-top: 14px; border-top: 1px solid #e2e8f0; font-size: 11px; color: #64748b;">
                <div>Accounting Office Copy<br>System Generated</div>
                <div style="text-align: right; border-top: 1px solid #334155; width: 140px; padding-top: 2px;">Authorized Signature</div>
            </div>
        </div>
        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 14px; padding: 0 20px 14px;">
            <button type="button" class="btn" onclick="document.getElementById('receiptModal').classList.remove('active')">Close</button>
            <button type="button" class="btn" style="background: #0b3d2e; color: #fff;" onclick="window.print()">Print Voucher</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // ── Drawer Mobile Toggle ──
    const btnOpenSidebar = document.getElementById('btnOpenSidebar');
    const btnCloseSidebar = document.getElementById('btnCloseSidebar');
    const sidebar = document.getElementById('sidebar') || document.getElementById('sidebarNav');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    if (btnOpenSidebar && sidebar && sidebarOverlay) {
        btnOpenSidebar.addEventListener('click', () => {
            sidebar.classList.add('open');
            sidebarOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
        const closeSidebar = () => {
            sidebar.classList.remove('open');
            sidebarOverlay.classList.remove('active');
            document.body.style.overflow = '';
        };
        btnCloseSidebar && btnCloseSidebar.addEventListener('click', closeSidebar);
        sidebarOverlay.addEventListener('click', closeSidebar);
    }

    // Logout
    document.getElementById('btnLogout')?.addEventListener('click', () => {
        Swal.fire({
            title: 'Sign Out?',
            text: 'Are you sure you want to end your accounting session?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0b3d2e',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Sign Out'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= APP_URL ?>/public/?action=logout';
            }
        });
    });

    // ── Open Assign Assessment Modal ──
    const assignTuitionModal = document.getElementById('assignTuitionModal');
    const btnCloseAssignModal = document.getElementById('btnCloseAssignModal');
    const btnCancelAssignModal = document.getElementById('btnCancelAssignModal');

    window.openAssignAssessmentModal = function(student) {
        if (!assignTuitionModal) return;
        document.getElementById('assignStudentId').value = student.id;
        document.getElementById('assignModalStudentLabel').textContent = 
            `Student: ${student.first_name} ${student.last_name} (${student.student_id})`;
        document.getElementById('assignGradeLevel').value = student.grade_level || 'BSCS 11A1';
        document.getElementById('assignSchoolYear').value = student.school_year || '<?= date('Y') . '-' . (date('Y') + 1) ?>';
        
        updateAssessmentDesc();
        recalcAssessmentTotal();
        assignTuitionModal.classList.add('active');
    };

    function closeAssignModal() {
        assignTuitionModal && assignTuitionModal.classList.remove('active');
    }
    btnCloseAssignModal && btnCloseAssignModal.addEventListener('click', closeAssignModal);
    btnCancelAssignModal && btnCancelAssignModal.addEventListener('click', closeAssignModal);

    window.updateAssessmentDesc = function() {
        const sy = document.getElementById('assignSchoolYear').value.trim() || '<?= date('Y') . '-' . (date('Y') + 1) ?>';
        const sem = document.getElementById('assignSemester').value;
        const descInput = document.getElementById('assignDescription');
        if (descInput) {
            descInput.value = `S.Y. ${sy} - ${sem} Tuition`;
        }
    };

    window.toggleCategoryRow = function(catId) {
        const row = document.getElementById('catRow_' + catId);
        const chk = document.getElementById('chk_' + catId);
        const amt = document.getElementById('amt_' + catId);
        if (row && chk) {
            if (chk.checked) {
                row.classList.remove('excluded');
                amt.removeAttribute('disabled');
            } else {
                row.classList.add('excluded');
                amt.setAttribute('disabled', 'disabled');
            }
            recalcAssessmentTotal();
        }
    };

    window.recalcAssessmentTotal = function() {
        let total = 0;
        document.querySelectorAll('.fee-category-row').forEach(row => {
            const chk = row.querySelector('input[type="checkbox"]');
            const num = row.querySelector('input[type="number"]');
            if (chk && chk.checked && num) {
                const val = parseFloat(num.value) || 0;
                total += Math.max(0, val);
            }
        });
        const disp = document.getElementById('computedTotalDisplay');
        if (disp) {
            disp.textContent = '₱' + total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    };

    // Initialize total calculation
    recalcAssessmentTotal();

    // ── Manual Payment Modal ──
    window.openManualPaymentModal = function() {
        document.getElementById('manualPaymentModal')?.classList.add('active');
    };
    document.getElementById('btnCloseManualPay')?.addEventListener('click', () => {
        document.getElementById('manualPaymentModal')?.classList.remove('active');
    });

    // ── Category Modal ──
    window.openAddCategoryModal = function() {
        document.getElementById('catModalTitle').textContent = 'Add Fee Category';
        document.getElementById('catModalId').value = '0';
        document.getElementById('catModalName').value = '';
        document.getElementById('catModalAmount').value = '';
        document.getElementById('catModalVariable').checked = false;
        document.getElementById('catModalSort').value = '10';
        document.getElementById('categoryModal')?.classList.add('active');
    };

    window.openEditCategoryModal = function(cat) {
        document.getElementById('catModalTitle').textContent = 'Edit Fee Category';
        document.getElementById('catModalId').value = cat.id;
        document.getElementById('catModalName').value = cat.name;
        document.getElementById('catModalAmount').value = cat.default_amount;
        document.getElementById('catModalVariable').checked = cat.is_variable == 1;
        document.getElementById('catModalSort').value = cat.sort_order;
        document.getElementById('categoryModal')?.classList.add('active');
    };

    // ── Receipt Voucher Modal ──
    window.viewReceiptVoucher = function(p) {
        document.getElementById('vOrNumber').textContent = p.or_number;
        document.getElementById('vStudentName').textContent = p.first_name + ' ' + p.last_name;
        document.getElementById('vStudentNum').textContent = p.student_num;
        document.getElementById('vMethod').textContent = (p.payment_method || 'CASH').toUpperCase();
        document.getElementById('vDate').textContent = p.paid_at || p.created_at;
        document.getElementById('vAmount').textContent = '₱' + parseFloat(p.amount).toLocaleString('en-US', { minimumFractionDigits: 2 });
        document.getElementById('receiptModal')?.classList.add('active');
    };


    // Notification dropdown
    const btnAccNotif = document.getElementById('btnAccNotif');
    const accNotifDropdown = document.getElementById('accNotifDropdown');
    btnAccNotif?.addEventListener('click', (e) => {
        e.stopPropagation();
        accNotifDropdown?.classList.toggle('open');
        btnAccNotif.classList.toggle('active');
    });
    document.addEventListener('click', () => {
        accNotifDropdown?.classList.remove('open');
        btnAccNotif?.classList.remove('active');
    });

    // Live search filter in accounting
    const accountingSearch = document.getElementById('accountingSearch');
    if (accountingSearch) {
        accountingSearch.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            document.querySelectorAll('table tbody tr').forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });

        // Ctrl+K keyboard shortcut to focus search
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                accountingSearch.focus();
                accountingSearch.select();
            }
            if (e.key === 'Escape' && document.activeElement === accountingSearch) {
                accountingSearch.blur();
                accountingSearch.value = '';
                accountingSearch.dispatchEvent(new Event('input'));
            }
        });
    }
</script>
</body>
</html>
