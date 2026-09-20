<?php
/**
 * PayTrack — Admin Dashboard v2
 * Fee Breakdown System, Live Aspect Preview, SweetAlert Dialogs, Responsive Mobile Drawer
 */
$categoryCount = count($feeCategories ?? []);
$variableCat = null;
foreach ($feeCategories ?? [] as $fc) {
    if (!empty($fc['is_variable'])) {
        $variableCat = $fc;
        break;
    }
}
$variableName = $variableCat['name'] ?? 'Subject Fee';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>PayTrack — Admin Dashboard</title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
    <style>
        .breakdown-preview-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 16px;
            margin-top: 10px;
            margin-bottom: 14px;
        }
        .breakdown-preview-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
        }
        .breakdown-row {
            display: flex;
            justify-content: space-between;
            font-size: 12.5px;
            padding: 4px 0;
            color: #334155;
            border-bottom: 1px dashed #f1f5f9;
        }
        .breakdown-row:last-child {
            border-bottom: none;
        }
        .breakdown-row.total-row {
            border-top: 1px solid #cbd5e1;
            margin-top: 4px;
            padding-top: 6px;
            font-weight: 700;
            color: #0f172a;
        }
        .badge.fixed {
            background: #e0e7ff;
            color: #3730a3;
        }
        .badge.variable {
            background: #ecfdf5;
            color: #065f46;
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
        .topbar-admin-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 700;
            color: #1e293b;
            letter-spacing: 0.3px;
        }

        /* ── Live Search Dropdown ── */
        .search-live-dropdown {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            width: 100%;
            min-width: 340px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.15);
            z-index: 99999;
            max-height: 380px;
            overflow-y: auto;
            padding: 6px 0;
            text-align: left;
        }
        .search-dropdown-group-title {
            font-size: 10.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #64748b;
            padding: 8px 14px 4px;
            background: #f8fafc;
            border-top: 1px solid #f1f5f9;
        }
        .search-dropdown-group-title:first-child {
            border-top: none;
        }
        .search-dropdown-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 9px 14px;
            cursor: pointer;
            font-size: 12.5px;
            color: #0f172a;
            transition: background 0.12s ease;
            text-decoration: none;
            border-bottom: 1px solid #f8fafc;
        }
        .search-dropdown-item:hover {
            background: #eff6ff;
        }
        .search-dropdown-item .item-meta {
            font-size: 11px;
            color: #64748b;
        }
        .search-dropdown-empty {
            padding: 16px 14px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
        }
    </style>
</head>
<body>

<div class="app-shell">
    <!-- Mobile Backdrop Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar (Drawer on mobile) -->
    <aside class="sidebar" id="sidebar">
        <div class="org">
            <div class="org-icon">A</div>
            <div class="org-text">
                <div class="org-name">PayTrack</div>
                <div class="org-team">Administrator</div>
            </div>
            <button class="mobile-menu-btn" id="btnCloseSidebar" style="margin-left: auto; width: 28px; height: 28px; font-size: 14px;">&times;</button>
        </div>

        <ul class="nav">
            <li>
                <a href="<?= APP_URL ?>/public/admin/?view=home" class="nav-item <?= $currentView === 'home' ? 'active' : '' ?>">
                    <span class="ic">&#8962;</span> Home
                </a>
            </li>
            <li>
                <a href="<?= APP_URL ?>/public/admin/?view=students" class="nav-item <?= $currentView === 'students' ? 'active' : '' ?>">
                    <span class="ic">&#128101;</span> Students &amp; Balances
                    <span class="badge-count"><?= count($students) ?></span>
                </a>
            </li>
            <li>
                <a href="<?= APP_URL ?>/public/admin/?view=transactions" class="nav-item <?= $currentView === 'transactions' ? 'active' : '' ?>">
                    <span class="ic">&#8644;</span> Transactions
                    <span class="badge-count"><?= count($payments) ?></span>
                </a>
            </li>
            <li>
                <a href="<?= APP_URL ?>/public/admin/?view=fees" class="nav-item <?= $currentView === 'fees' ? 'active' : '' ?>">
                    <span class="ic">&#128179;</span> Tuition Fees
                    <span class="badge-count"><?= count($fees) ?></span>
                </a>
            </li>
            <li>
                <a href="<?= APP_URL ?>/public/admin/?view=categories" class="nav-item <?= $currentView === 'categories' ? 'active' : '' ?>">
                    <span class="ic">&#9881;</span> Fee Categories
                    <span class="badge-count"><?= $categoryCount ?></span>
                </a>
            </li>
            <li>
                <a href="<?= APP_URL ?>/public/admin/?view=logs" class="nav-item <?= $currentView === 'logs' ? 'active' : '' ?>">
                    <span class="ic">&#9993;</span> Email Logs
                    <span class="badge-count"><?= count($emailLogs) ?></span>
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
        <header class="topbar">
            <button class="mobile-menu-btn" id="btnOpenSidebar" aria-label="Toggle Navigation">&#9776;</button>

            <div class="search" style="position: relative;">
                <span class="search-ic">&#128269;</span>
                <input type="text" id="adminSearchInput" placeholder="Search students, OR#, fees..." autocomplete="off" value="<?= e($_GET['q'] ?? '') ?>">
                <span class="kbd">⌘K</span>
                <div id="adminSearchDropdown" class="search-live-dropdown" style="display: none;"></div>
            </div>

            <div class="topbar-actions">
                <?php if ($currentView === 'students' || $currentView === 'home'): ?>
                    <button class="btn dark" id="btnOpenCreateStudentModal">
                        <span>+</span> Create Student
                    </button>
                <?php endif; ?>
                <span class="topbar-admin-badge">Administrator</span>
            </div>
        </header>

        <!-- Flash Messages -->
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
        <main class="content">

            <!-- VIEW 0: ADMIN HOME / OVERVIEW -->
            <?php if ($currentView === 'home'): ?>
                <div class="page-head">
                    <div>
                        <h1>System Overview</h1>
                        <p>Welcome to PayTrack Administrative Portal &bull; School Year <?= date('Y') ?>-<?= date('Y') + 1 ?></p>
                    </div>
                    <div>
                        <button class="btn dark" onclick="document.getElementById('createStudentModal').classList.add('active');">
                            <span>+</span> Create Student
                        </button>
                    </div>
                </div>

                <!-- KPI Metric Cards Grid -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
                    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                        <div style="font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Total Collections</div>
                        <div style="font-size: 24px; font-weight: 800; color: #059669;"><?= peso($totalRevenue) ?></div>
                        <div style="font-size: 11.5px; color: #64748b; margin-top: 4px;">Verified payments recorded</div>
                    </div>

                    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                        <div style="font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Outstanding Receivables</div>
                        <div style="font-size: 24px; font-weight: 800; color: #dc2626;"><?= peso($totalReceivables) ?></div>
                        <div style="font-size: 11.5px; color: #64748b; margin-top: 4px;">Unsettled tuition balances</div>
                    </div>

                    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                        <div style="font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Enrolled Students</div>
                        <div style="font-size: 24px; font-weight: 800; color: #2563eb;"><?= count($students) ?></div>
                        <div style="font-size: 11.5px; color: #64748b; margin-top: 4px;">Active student accounts</div>
                    </div>

                    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                        <div style="font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Official Receipts Issued</div>
                        <div style="font-size: 24px; font-weight: 800; color: #7c3aed;"><?= count($payments) ?></div>
                        <div style="font-size: 11.5px; color: #64748b; margin-top: 4px;">Payment receipts generated</div>
                    </div>
                </div>

                <!-- 2-Column Grid: Recent Verified Payments & Quick Actions -->
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 24px;">
                    <!-- Left: Recent Verified Transactions -->
                    <div class="panel" style="margin-bottom: 0;">
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid #e2e8f0;">
                            <div>
                                <h3 style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0;">Recent Payment Transactions</h3>
                                <p style="font-size: 12px; color: #64748b; margin: 2px 0 0;">Latest payments confirmed through the student portal</p>
                            </div>
                            <a href="<?= APP_URL ?>/public/admin/?view=transactions" style="font-size: 12px; font-weight: 600; color: #2563eb; text-decoration: none;">View All &rarr;</a>
                        </div>
                        <div class="table-responsive">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr>
                                        <th>Receipt (OR#)</th>
                                        <th>Student</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $recentPayments = array_slice($payments, 0, 5);
                                    if (empty($recentPayments)): 
                                    ?>
                                        <tr>
                                            <td colspan="5" style="text-align: center; padding: 32px; color: #64748b;">No payment transactions recorded yet.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($recentPayments as $rp): ?>
                                            <tr>
                                                <td><code><?= e($rp['or_number']) ?></code></td>
                                                <td><strong><?= e($rp['first_name'] . ' ' . $rp['last_name']) ?></strong></td>
                                                <td><strong style="color: #059669;"><?= peso((float)$rp['amount']) ?></strong></td>
                                                <td><span class="badge completed"><?= strtoupper(e($rp['payment_method'])) ?></span></td>
                                                <td><?= date('M d, Y', strtotime($rp['paid_at'])) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Right: Administrative Shortcuts -->
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                            <h4 style="font-size: 14px; font-weight: 700; color: #0f172a; margin: 0 0 12px;">Administrative Shortcuts</h4>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <a href="<?= APP_URL ?>/public/admin/?view=students" class="btn" style="justify-content: flex-start; gap: 8px; width: 100%;">
                                    <span>👥</span> View All Student Records
                                </a>
                                <a href="<?= APP_URL ?>/public/admin/?view=transactions" class="btn" style="justify-content: flex-start; gap: 8px; width: 100%;">
                                    <span>🔁</span> Official Transactions Log
                                </a>
                                <a href="<?= APP_URL ?>/public/admin/?view=fees" class="btn" style="justify-content: flex-start; gap: 8px; width: 100%;">
                                    <span>💳</span> Manage Tuition Assessments
                                </a>
                                <a href="<?= APP_URL ?>/public/admin/?view=categories" class="btn" style="justify-content: flex-start; gap: 8px; width: 100%;">
                                    <span>⚙️</span> Configure Fee Categories
                                </a>
                                <a href="<?= APP_URL ?>/public/admin/?view=logs" class="btn" style="justify-content: flex-start; gap: 8px; width: 100%;">
                                    <span>✉️</span> View Automated Email Logs
                                </a>
                            </div>
                        </div>

                        <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 12px; padding: 16px;">
                            <div style="display: flex; align-items: center; gap: 6px; font-weight: 700; font-size: 13px; color: #0b3d2e; margin-bottom: 6px;">
                                <span>📌</span> Billing Information
                            </div>
                            <p style="font-size: 12px; color: #64748b; line-height: 1.5; margin: 0;">
                                System automatically synchronizes section assignments with tuition semesters and emails login credentials to both student and parent upon account creation.
                            </p>
                        </div>
                    </div>
                </div>

            <!-- VIEW 1: STUDENTS & BALANCES -->
            <?php elseif ($currentView === 'students'): ?>
                <div class="page-head">
                    <div>
                        <h1>Students &amp; Balances</h1>
                        <p>Directory of student accounts, tuition statuses, and parent contacts</p>
                    </div>
                    <div>
                        <button class="btn dark" onclick="document.getElementById('createStudentModal').classList.add('active');">
                            <span>+</span> Create Student
                        </button>
                    </div>
                </div>

                <div class="panel">
                    <div class="table-responsive">
                        <table id="dataTable">
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>Student ID</th>
                                    <th>Class / Section</th>
                                    <th>Parent Contact</th>
                                    <th>Parent Email</th>
                                    <th>Remaining Balance</th>
                                    <th style="text-align: right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($students)): ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 36px; color: var(--text-sub);">
                                            No student accounts found. Click "+ Create Student" to add.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($students as $s): ?>
                                        <?php 
                                            $rem = max(0, (float)$s['total_fee'] - (float)$s['total_paid']);
                                        ?>
                                        <tr>
                                            <td><strong><?= e($s['first_name'] . ' ' . $s['last_name']) ?></strong></td>
                                            <td><code><?= e($s['student_id']) ?></code></td>
                                            <td><span class="badge" style="background: #e0f2fe; color: #0369a1; font-weight: 700;"><?= e($s['grade_level'] ?: 'BSCS 11A1') ?></span></td>
                                            <td><?= e($s['contact_number'] ?: '—') ?></td>
                                            <td style="color: var(--text-sub);"><?= e($s['parent_email'] ?: '—') ?></td>
                                            <td>
                                                <strong style="color: <?= $rem > 0 ? 'var(--red-text)' : 'var(--green-text)' ?>">
                                                    <?= peso($rem) ?>
                                                </strong>
                                            </td>
                                            <td style="text-align: right;">
                                                <button class="action-btn" onclick="viewStudentDetails(<?= htmlspecialchars(json_encode($s)) ?>, <?= $rem ?>)">
                                                    View
                                                </button>
                                                <form method="POST" action="<?= APP_URL ?>/public/admin/?view=students" style="display: inline;" id="delStudentForm-<?= $s['id'] ?>">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="delete_student">
                                                    <input type="hidden" name="student_id" value="<?= $s['id'] ?>">
                                                    <button type="button" class="action-btn danger"
                                                        onclick="confirmDeleteStudent('<?= e($s['first_name'] . ' ' . $s['last_name']) ?>', 'delStudentForm-<?= $s['id'] ?>')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <!-- VIEW 2: TRANSACTIONS -->
            <?php elseif ($currentView === 'transactions'): ?>
                <div class="page-head">
                    <div>
                        <h1>Payment Transactions</h1>
                        <p>Official payment receipts recorded through the portal</p>
                    </div>
                </div>

                <div class="panel">
                    <div class="table-responsive">
                        <table id="dataTable">
                            <thead>
                                <tr>
                                    <th>Receipt No (OR#)</th>
                                    <th>Student</th>
                                    <th>Tuition Description</th>
                                    <th>Amount Paid</th>
                                    <th>Method</th>
                                    <th>Date</th>
                                    <th style="text-align: right;">Receipt</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($payments)): ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 36px; color: var(--text-sub);">
                                            No payment transactions recorded yet.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($payments as $p): ?>
                                        <tr>
                                            <td><code><?= e($p['or_number']) ?></code></td>
                                            <td><strong><?= e($p['first_name'] . ' ' . $p['last_name']) ?></strong> (<?= e($p['student_num']) ?>)</td>
                                            <td><?= e($p['fee_desc']) ?></td>
                                            <td><strong style="color: var(--green-text);"><?= peso((float)$p['amount']) ?></strong></td>
                                            <td><span class="badge completed"><?= strtoupper(e($p['payment_method'])) ?></span></td>
                                            <td><?= date('M d, Y h:i A', strtotime($p['paid_at'])) ?></td>
                                            <td style="text-align: right;">
                                                <button class="action-btn" onclick="viewReceiptAdmin('<?= e($p['or_number']) ?>', '<?= e($p['first_name'] . ' ' . $p['last_name']) ?>', '<?= peso((float)$p['amount']) ?>', '<?= date('M d, Y h:i A', strtotime($p['paid_at'])) ?>', '<?= strtoupper(e($p['payment_method'])) ?>')">
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

            <!-- VIEW 3: TUITION FEES -->
            <?php elseif ($currentView === 'fees'): ?>
                <div class="page-head">
                    <div>
                        <h1>Tuition Fee Records &amp; Breakdown</h1>
                        <p>Assessments assigned to students with automated itemized aspect split</p>
                    </div>
                    <button class="btn dark" id="btnOpenAssignFeeModal">
                        <span>+</span> Assign New Fee
                    </button>
                </div>

                <div class="panel">
                    <div class="table-responsive">
                        <table id="dataTable">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Term / Description</th>
                                    <th>Total Assessment</th>
                                    <th>Amount Paid</th>
                                    <th>Remaining Balance</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th style="text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($fees)): ?>
                                    <tr>
                                        <td colspan="8" style="text-align: center; padding: 36px; color: var(--text-sub);">
                                            No tuition fee records found.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($fees as $fee): ?>
                                        <?php 
                                            $feeRem = max(0, (float)$fee['total_amount'] - (float)$fee['amount_paid']);
                                        ?>
                                        <tr>
                                            <td><strong><?= e($fee['first_name'] . ' ' . $fee['last_name']) ?></strong> (<?= e($fee['student_num']) ?>)</td>
                                            <td><?= e($fee['description']) ?></td>
                                            <td><strong><?= peso((float)$fee['total_amount']) ?></strong></td>
                                            <td style="color: var(--green-text);"><?= peso((float)$fee['amount_paid']) ?></td>
                                            <td>
                                                <strong style="color: <?= $feeRem > 0 ? 'var(--red-text)' : 'var(--green-text)' ?>">
                                                    <?= peso($feeRem) ?>
                                                </strong>
                                            </td>
                                            <td><?= !empty($fee['due_date']) ? date('M d, Y', strtotime($fee['due_date'])) : '—' ?></td>
                                            <td>
                                                <?php if ($fee['status'] === 'paid'): ?>
                                                    <span class="badge paid">Paid</span>
                                                <?php elseif ($fee['status'] === 'partial'): ?>
                                                    <span class="badge partial">Partial</span>
                                                <?php else: ?>
                                                    <span class="badge pending">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="text-align: right;">
                                                <button class="action-btn" onclick="viewFeeBreakdownModal(<?= htmlspecialchars(json_encode($fee)) ?>)">
                                                    Breakdown
                                                </button>
                                                <form method="POST" action="<?= APP_URL ?>/public/admin/?view=fees" style="display: inline;" id="delFeeForm-<?= $fee['id'] ?>">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="delete_fee">
                                                    <input type="hidden" name="fee_id" value="<?= $fee['id'] ?>">
                                                    <button type="button" class="action-btn danger"
                                                        onclick="confirmDeleteFee('<?= e($fee['description']) ?>', 'delFeeForm-<?= $fee['id'] ?>')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <!-- VIEW 4: FEE CATEGORIES -->
            <?php elseif ($currentView === 'categories'): ?>
                <div class="page-head">
                    <div>
                        <h1>Fee Aspects &amp; Categories</h1>
                        <p>Manage standard school fees (e.g. LMS, Miscellaneous) and variable subject fees</p>
                    </div>
                    <button class="btn dark" id="btnOpenCategoryModal">
                        <span>+</span> Add Fee Category
                    </button>
                </div>

                <div class="panel">
                    <div class="table-responsive">
                        <table id="dataTable">
                            <thead>
                                <tr>
                                    <th>Category Name</th>
                                    <th>Type</th>
                                    <th>Default Amount</th>
                                    <th>Sort Order</th>
                                    <th>Status</th>
                                    <th style="text-align: right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($feeCategories)): ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 36px; color: var(--text-sub);">
                                            No fee categories configured.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($feeCategories as $cat): ?>
                                        <tr>
                                            <td><strong><?= e($cat['name']) ?></strong></td>
                                            <td>
                                                <?php if (!empty($cat['is_variable'])): ?>
                                                    <span class="badge variable">Variable (Remainder)</span>
                                                <?php else: ?>
                                                    <span class="badge fixed">Fixed Fee</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($cat['is_variable'])): ?>
                                                    <em style="color: var(--text-muted);">Calculated automatically</em>
                                                <?php else: ?>
                                                    <strong><?= peso((float)$cat['default_amount']) ?></strong>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= e($cat['sort_order']) ?></td>
                                            <td>
                                                <span class="badge completed">Active</span>
                                            </td>
                                            <td style="text-align: right;">
                                                <button class="action-btn" onclick="editCategoryModal(<?= htmlspecialchars(json_encode($cat)) ?>)">Edit</button>
                                                <?php if (empty($cat['is_variable'])): ?>
                                                    <form method="POST" action="<?= APP_URL ?>/public/admin/?view=categories" style="display: inline;" id="delCatForm-<?= $cat['id'] ?>">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="action" value="delete_fee_category">
                                                        <input type="hidden" name="category_id" value="<?= $cat['id'] ?>">
                                                        <button type="button" class="action-btn danger" onclick="confirmDeleteCategory('<?= e($cat['name']) ?>', 'delCatForm-<?= $cat['id'] ?>')">Delete</button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <!-- VIEW 5: EMAIL LOGS -->
            <?php elseif ($currentView === 'logs'): ?>
                <div class="page-head">
                    <div>
                        <h1>Email Notification Logs</h1>
                        <p>Audit trail of credentials sent to students and receipts sent to parents</p>
                    </div>
                </div>

                <div class="panel">
                    <div class="table-responsive">
                        <table id="dataTable">
                            <thead>
                                <tr>
                                    <th>Recipient</th>
                                    <th>Subject</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Sent Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($emailLogs)): ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 36px; color: var(--text-sub);">
                                            No email logs recorded yet.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($emailLogs as $log): ?>
                                        <tr>
                                            <td><strong><?= e($log['recipient_email']) ?></strong></td>
                                            <td><?= e($log['subject']) ?></td>
                                            <td><code><?= e($log['type']) ?></code></td>
                                            <td>
                                                <?php if ($log['status'] === 'sent'): ?>
                                                    <span class="badge completed">Sent</span>
                                                <?php else: ?>
                                                    <span class="badge overdue">Failed</span>
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
            <?php endif; ?>

        </main>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 1: CREATE STUDENT ACCOUNT & TUITION  -->
<!-- ========================================== -->
<div class="modal-backdrop" id="createStudentModal">
    <div class="modal-window" style="max-width: 580px;">
        <button class="modal-close-x" id="btnCloseCreateStudentModal">&times;</button>
        <h2 class="modal-header-title">Create Student &amp; Assign Tuition</h2>

        <form method="POST" action="<?= APP_URL ?>/public/admin/?view=students" id="createStudentForm">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="create_student">

            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label" for="newStudentId">Student ID * (Format: 0000-00000)</label>
                    <input type="text" class="form-control" name="student_id" id="newStudentId" required placeholder="0000-00000" maxlength="10" autocomplete="off">
                    <small style="color: #64748b; font-size: 11px; display: block; margin-top: 3px;">Auto-formats: 4 numbers, automatic dash, then 5 numbers (e.g. <code>2024-12345</code>)</small>
                </div>
                <div class="form-group">
                    <label class="form-label" for="newCourse">Course / Program *</label>
                    <select class="form-control" name="course" id="newCourse" required onchange="updateGeneratedSection()">
                        <option value="BSCS" selected>BSCS — BS Computer Science</option>
                        <option value="BSIT">BSIT — BS Information Technology</option>
                        <option value="BSEE">BSEE — BS Electrical Engineering</option>
                        <option value="BSHM">BSHM — BS Hospitality Management</option>
                        <option value="BSIE">BSIE — BS Industrial Engineering</option>
                        <option value="BSCrim">BSCrim — BS Criminology</option>
                    </select>
                </div>
            </div>

            <!-- Section Dropdown -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 14px; margin-bottom: 14px;">
                <div style="font-size: 12px; font-weight: 700; color: #0f172a; margin-bottom: 8px; display: flex; align-items: center; justify-content: space-between;">
                    <span>🎓 Section Assignment</span>
                    <span id="sectionBadgePreview" style="background: #e0f2fe; color: #0369a1; padding: 3px 10px; border-radius: 4px; font-size: 11.5px; font-weight: 800;">BSCS 11A1</span>
                </div>
                <div class="form-group" style="margin-bottom: 6px;">
                    <label style="font-size: 11px; color: #475569; font-weight: 600; display: block; margin-bottom: 4px;">Section Code *</label>
                    <select class="form-control" id="sectionDropdown" onchange="updateGeneratedSection()" required>
                        <?php
                        $shifts = ['A', 'M', 'E'];
                        $shiftLabels = ['A' => 'Afternoon', 'M' => 'Morning', 'E' => 'Evening'];
                        foreach ([1,2,3,4] as $yr):
                            foreach ([1,2] as $sem):
                                foreach ($shifts as $sh):
                                    foreach ([1,2,3] as $sec):
                                        $code = "{$yr}{$sem}{$sh}{$sec}";
                                        $selected = ($code === '11A1') ? ' selected' : '';
                        ?>
                        <option value="<?= $code ?>"<?= $selected ?>><?= $code ?> — Year <?= $yr ?>, Sem <?= $sem ?>, <?= $shiftLabels[$sh] ?>, Section <?= $sec ?></option>
                        <?php endforeach; endforeach; endforeach; endforeach; ?>
                    </select>
                </div>
                <!-- Hidden inputs passed to server -->
                <input type="hidden" name="section_code" id="hiddenSectionCode" value="11A1">
                <input type="hidden" name="class_grade" id="hiddenClassGrade" value="BSCS 11A1">
                <div style="font-size: 11px; color: #64748b; margin-top: 6px;">
                    Pattern: <strong>[Year: 1-4][Sem: 1-2][Shift: M/A/E][Section: 1-3]</strong> &bull; Selecting a section auto-fills the semester below.
                </div>
            </div>

            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label" for="newFirstName">First Name *</label>
                    <input type="text" class="form-control" name="first_name" id="newFirstName" required placeholder="First name">
                </div>
                <div class="form-group">
                    <label class="form-label" for="newLastName">Last Name *</label>
                    <input type="text" class="form-control" name="last_name" id="newLastName" required placeholder="Last name">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="newStudentEmail">Student Email *</label>
                <input type="email" class="form-control" name="student_email" id="newStudentEmail" required placeholder="student@email.com">
            </div>

            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label" for="newParentName">Parent Name</label>
                    <input type="text" class="form-control" name="parent_name" id="newParentName" placeholder="Parent full name">
                </div>
                <div class="form-group">
                    <label class="form-label" for="newParentContact">Parent Contact</label>
                    <input type="text" class="form-control" name="parent_contact" id="newParentContact" placeholder="(0917) 123-4567">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="newParentEmail">Parent Email</label>
                <input type="email" class="form-control" name="parent_email" id="newParentEmail" placeholder="parent@email.com">
            </div>

            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 18px 0;">

            <div style="font-weight: 700; font-size: 13px; margin-bottom: 10px; color: #0f172a;">
                &#128179; Initial Tuition Fee Assessment
            </div>

            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label" for="createTuitionAmount">Total Tuition Amount (₱) *</label>
                    <input type="number" step="0.01" min="<?= $fixedFeeTotal ?>" class="form-control" name="total_tuition" id="createTuitionAmount" required value="21000.00">
                </div>
                <div class="form-group">
                    <label class="form-label" for="createSchoolYear">School Year *</label>
                    <input type="text" class="form-control" name="school_year" id="createSchoolYear" value="<?= date('Y') ?>-<?= date('Y') + 1 ?>" required>
                </div>
            </div>

            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label" for="createSemester">Semester *</label>
                    <select class="form-control" name="semester" id="createSemester" required>
                        <option value="1st Semester" selected>1st Semester</option>
                        <option value="2nd Semester">2nd Semester</option>
                    </select>
                    <small style="color: #64748b; font-size: 11px;">Auto-set from section code's 2nd digit.</small>
                </div>
                <div class="form-group">
                    <label class="form-label">Payment Due Date</label>
                    <input type="date" class="form-control" name="due_date" value="<?= date('Y-m-d', strtotime('+30 days')) ?>">
                </div>
            </div>

            <!-- Dynamic Aspect Breakdown Preview -->
            <div class="breakdown-preview-card">
                <div class="breakdown-preview-title">
                    <span>Automated Breakdown Preview</span>
                    <span style="color: #059669;">Auto-split Active</span>
                </div>
                <div id="breakdownRowsContainer">
                    <!-- Populated via JS -->
                </div>
            </div>

            <div style="margin-top: 14px;">
                <button type="submit" class="btn dark" style="width: 100%; justify-content: center; padding: 10px;">
                    Create Student &amp; Assessment
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 2: ASSIGN TUITION FEE MODAL          -->
<!-- ========================================== -->
<div class="modal-backdrop" id="assignFeeModal">
    <div class="modal-window" style="max-width: 520px;">
        <button class="modal-close-x" id="btnCloseAssignFeeModal">&times;</button>
        <h2 class="modal-header-title">Assign Tuition Assessment</h2>

        <form method="POST" action="<?= APP_URL ?>/public/admin/?view=fees" id="assignFeeForm">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="assign_fee">

            <div class="form-group">
                <label class="form-label">Select Student *</label>
                <select class="form-control" name="student_id" required>
                    <?php foreach ($students as $st): ?>
                        <option value="<?= $st['id'] ?>">
                            <?= e($st['first_name'] . ' ' . $st['last_name']) ?> (<?= e($st['student_id']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">School Year *</label>
                    <input type="text" class="form-control" name="school_year" value="2024-2025" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Semester *</label>
                    <select class="form-control" name="semester" required>
                        <option value="1st Semester">1st Semester</option>
                        <option value="2nd Semester">2nd Semester</option>
                        <option value="Summer">Summer</option>
                    </select>
                </div>
            </div>

            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">Total Tuition (₱) *</label>
                    <input type="number" step="0.01" min="<?= $fixedFeeTotal ?>" class="form-control" name="amount" id="assignFeeAmount" required value="21000.00">
                </div>
                <div class="form-group">
                    <label class="form-label">Due Date</label>
                    <input type="date" class="form-control" name="due_date" value="<?= date('Y-m-d', strtotime('+30 days')) ?>">
                </div>
            </div>

            <!-- Dynamic Aspect Breakdown Preview -->
            <div class="breakdown-preview-card">
                <div class="breakdown-preview-title">
                    <span>Automated Breakdown Preview</span>
                    <span style="color: #059669;">Auto-split Active</span>
                </div>
                <div id="assignBreakdownRows">
                    <!-- Populated via JS -->
                </div>
            </div>

            <div style="margin-top: 14px;">
                <button type="submit" class="btn dark" style="width: 100%; justify-content: center; padding: 10px;">
                    Assign Assessment with Breakdown
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 3: FEE CATEGORY MODAL                -->
<!-- ========================================== -->
<div class="modal-backdrop" id="categoryModal">
    <div class="modal-window" style="max-width: 440px;">
        <button class="modal-close-x" id="btnCloseCategoryModal">&times;</button>
        <h2 class="modal-header-title" id="catModalTitle">Add Fee Category</h2>

        <form method="POST" action="<?= APP_URL ?>/public/admin/?view=categories" id="catForm">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="save_fee_category">
            <input type="hidden" name="category_id" id="catId" value="0">

            <div class="form-group">
                <label class="form-label" for="catName">Category Name *</label>
                <input type="text" class="form-control" name="name" id="catName" required placeholder="e.g. Laboratory Fee">
            </div>

            <div class="form-group">
                <label class="form-label" for="catAmount">Default Amount (₱)</label>
                <input type="number" step="0.01" class="form-control" name="default_amount" id="catAmount" value="0.00">
                <small style="color: var(--text-muted);">Leave 0 if this category will receive variable remainder.</small>
            </div>

            <div class="form-group">
                <label class="form-label" for="catSort">Sort Order</label>
                <input type="number" class="form-control" name="sort_order" id="catSort" value="10">
            </div>

            <div style="margin-top: 14px;">
                <button type="submit" class="btn dark" style="width: 100%; justify-content: center; padding: 10px;">
                    Save Category
                </button>
            </div>
        </form>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // ── Active Fee Categories Data from Server ────────────────
    const ACTIVE_CATEGORIES = <?= json_encode($feeCategories ?? []) ?>;
    const FIXED_TOTAL = <?= (float)$fixedFeeTotal ?>;
    const VARIABLE_NAME = "<?= e($variableName) ?>";

    function computeBreakdown(totalAmount) {
        let fixedSum = 0;
        let rowsHtml = '';

        ACTIVE_CATEGORIES.forEach(cat => {
            if (!cat.is_variable && cat.is_active == 1) {
                const amt = parseFloat(cat.default_amount) || 0;
                fixedSum += amt;
                rowsHtml += `
                    <div class="breakdown-row">
                        <span>${cat.name} <span class="badge fixed" style="font-size: 10px;">Fixed</span></span>
                        <strong>₱${amt.toLocaleString('en-PH', {minimumFractionDigits: 2})}</strong>
                    </div>
                `;
            }
        });

        const remainder = Math.max(0, totalAmount - fixedSum);
        rowsHtml += `
            <div class="breakdown-row">
                <span>${VARIABLE_NAME} <span class="badge variable" style="font-size: 10px;">Remainder</span></span>
                <strong style="color: #059669;">₱${remainder.toLocaleString('en-PH', {minimumFractionDigits: 2})}</strong>
            </div>
            <div class="breakdown-row total-row">
                <span>Total Assessment</span>
                <span>₱${totalAmount.toLocaleString('en-PH', {minimumFractionDigits: 2})}</span>
            </div>
        `;
        return rowsHtml;
    }

    // Attach Live Preview on Create Student Modal
    const createTuitionInput = document.getElementById('createTuitionAmount');
    const breakdownContainer = document.getElementById('breakdownRowsContainer');

    function updateCreateBreakdown() {
        const val = parseFloat(createTuitionInput.value) || 0;
        if (breakdownContainer) {
            breakdownContainer.innerHTML = computeBreakdown(val);
        }
    }
    if (createTuitionInput) {
        createTuitionInput.addEventListener('input', updateCreateBreakdown);
        updateCreateBreakdown();
    }

    // Attach Live Preview on Assign Fee Modal
    const assignAmountInput = document.getElementById('assignFeeAmount');
    const assignBreakdownContainer = document.getElementById('assignBreakdownRows');

    function updateAssignBreakdown() {
        const val = parseFloat(assignAmountInput.value) || 0;
        if (assignBreakdownContainer) {
            assignBreakdownContainer.innerHTML = computeBreakdown(val);
        }
    }
    if (assignAmountInput) {
        assignAmountInput.addEventListener('input', updateAssignBreakdown);
        updateAssignBreakdown();
    }

    // ── SweetAlert Confirmations ──────────────────────────────
    const LOGOUT_URL = '<?= APP_URL ?>/public/?action=logout';

    document.getElementById('btnLogout').addEventListener('click', function () {
        Swal.fire({
            title: 'Log out?',
            text: 'You will be returned to the login page.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, log out',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#18181b',
            cancelButtonColor: '#e4e4e7',
            customClass: { cancelButton: 'swal-cancel-dark' }
        }).then(result => {
            if (result.isConfirmed) window.location.href = LOGOUT_URL;
        });
    });

    function confirmDeleteStudent(name, formId) {
        Swal.fire({
            title: 'Delete Student?',
            html: `This will permanently remove <strong>${name}</strong> and all their records. This cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '&#128465; Yes, delete',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#e4e4e7',
            focusCancel: true,
            customClass: { cancelButton: 'swal-cancel-dark' }
        }).then(result => {
            if (result.isConfirmed) document.getElementById(formId).submit();
        });
    }

    function confirmDeleteFee(desc, formId) {
        Swal.fire({
            title: 'Delete Fee Record?',
            html: `Fee: <strong>${desc}</strong> will be permanently removed.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '&#128465; Yes, delete',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#e4e4e7',
            focusCancel: true,
            customClass: { cancelButton: 'swal-cancel-dark' }
        }).then(result => {
            if (result.isConfirmed) document.getElementById(formId).submit();
        });
    }

    function confirmDeleteCategory(name, formId) {
        Swal.fire({
            title: 'Delete Fee Category?',
            html: `Category <strong>${name}</strong> will be deleted.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#e4e4e7'
        }).then(result => {
            if (result.isConfirmed) document.getElementById(formId).submit();
        });
    }

    // ── Detail & Receipt Modals with SweetAlert ───────────────
    function viewStudentDetails(student, remaining) {
        Swal.fire({
            title: student.first_name + ' ' + student.last_name,
            html: `
                <div style="text-align: left; font-size: 13px; line-height: 1.8; color: #3f3f46;">
                    <p><strong>Student ID:</strong> ${student.student_id}</p>
                    <p><strong>Email:</strong> ${student.email}</p>
                    <p><strong>Grade/Class:</strong> ${student.grade_level || 'N/A'}</p>
                    <p><strong>Parent Name:</strong> ${student.parent_name || 'N/A'}</p>
                    <p><strong>Parent Email:</strong> ${student.parent_email || 'N/A'}</p>
                    <p><strong>Parent Contact:</strong> ${student.contact_number || 'N/A'}</p>
                    <p><strong>Remaining Balance:</strong> <strong style="color: ${remaining > 0 ? 'var(--red-text)' : 'var(--green-text)'}">₱${parseFloat(remaining).toLocaleString('en-PH', {minimumFractionDigits: 2})}</strong></p>
                </div>
            `,
            confirmButtonColor: '#18181b'
        });
    }

    function viewFeeBreakdownModal(fee) {
        let itemsHtml = '';
        if (fee.items && fee.items.length > 0) {
            fee.items.forEach(item => {
                itemsHtml += `
                    <div style="display:flex; justify-content:space-between; padding: 6px 0; border-bottom: 1px dashed #e2e8f0;">
                        <span>${item.category_name}</span>
                        <strong>₱${parseFloat(item.amount).toLocaleString('en-PH', {minimumFractionDigits: 2})}</strong>
                    </div>
                `;
            });
        } else {
            itemsHtml = '<p style="color:#71717a;">No item breakdown available.</p>';
        }

        Swal.fire({
            title: 'Tuition Fee Assessment Breakdown',
            html: `
                <div style="text-align: left; font-size: 13px; line-height: 1.8; color: #3f3f46;">
                    <p style="margin-bottom: 4px;"><strong>Student:</strong> ${fee.first_name} ${fee.last_name} (${fee.student_num})</p>
                    <p style="margin-bottom: 12px;"><strong>Term:</strong> ${fee.description}</p>
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin-bottom: 12px;">
                        ${itemsHtml}
                        <div style="display:flex; justify-content:space-between; padding-top: 8px; margin-top: 6px; font-weight:700; border-top:1px solid #cbd5e1;">
                            <span>Total Assessment</span>
                            <span>₱${parseFloat(fee.total_amount).toLocaleString('en-PH', {minimumFractionDigits: 2})}</span>
                        </div>
                    </div>
                    <div style="display:flex; justify-content:space-between;">
                        <span>Total Paid: <strong style="color: #059669;">₱${parseFloat(fee.amount_paid).toLocaleString('en-PH', {minimumFractionDigits: 2})}</strong></span>
                        <span>Remaining: <strong style="color: #dc2626;">₱${Math.max(0, parseFloat(fee.total_amount) - parseFloat(fee.amount_paid)).toLocaleString('en-PH', {minimumFractionDigits: 2})}</strong></span>
                    </div>
                </div>
            `,
            confirmButtonColor: '#18181b'
        });
    }

    function viewReceiptAdmin(orNum, studentName, amount, date, method) {
        Swal.fire({
            icon: 'success',
            title: 'Official Payment Receipt',
            html: `
                <div style="text-align: left; font-size: 13px; line-height: 1.8; color: #3f3f46;">
                    <p><strong>Receipt Number:</strong> ${orNum}</p>
                    <p><strong>Student:</strong> ${studentName}</p>
                    <p><strong>Amount:</strong> ${amount}</p>
                    <p><strong>Payment Method:</strong> ${method}</p>
                    <p><strong>Date Recorded:</strong> ${date}</p>
                    <p><strong>Status:</strong> Completed &amp; Verified</p>
                </div>
            `,
            confirmButtonColor: '#18181b'
        });
    }

    // ── Mobile Drawer Elements ──────────────────────────────
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

    // ── Modals Handling ─────────────────────────────────────
    const createStudentModal = document.getElementById('createStudentModal');
    const btnOpenCreate = document.getElementById('btnOpenCreateStudentModal');
    const btnCloseCreate = document.getElementById('btnCloseCreateStudentModal');

    if (btnOpenCreate) {
        btnOpenCreate.addEventListener('click', () => {
            createStudentModal.classList.add('active');
            updateGeneratedSection(); // sync badge & semester on open
            const sid = document.getElementById('newStudentId');
            if (sid) {
                setTimeout(() => sid.focus(), 150);
            }
        });
    }
    if (btnCloseCreate) btnCloseCreate.addEventListener('click', () => createStudentModal.classList.remove('active'));

    // Single Section Dropdown → auto-fills preview badge + semester field
    window.updateGeneratedSection = function () {
        const course = document.getElementById('newCourse') ? document.getElementById('newCourse').value : 'BSCS';
        const sectionDropdown = document.getElementById('sectionDropdown');
        const sectionCode = sectionDropdown ? sectionDropdown.value : '11A1';

        const fullClass = `${course} ${sectionCode}`;

        // Update hidden fields
        if (document.getElementById('hiddenSectionCode')) document.getElementById('hiddenSectionCode').value = sectionCode;
        if (document.getElementById('hiddenClassGrade'))  document.getElementById('hiddenClassGrade').value  = fullClass;

        // Update badge preview
        if (document.getElementById('sectionBadgePreview')) {
            document.getElementById('sectionBadgePreview').textContent = fullClass;
        }

        // Auto-set semester from 2nd digit of section code (e.g. "11A1" → digit at index 1 = '1')
        const semDigit = sectionCode.charAt(1);
        const createSemesterEl = document.getElementById('createSemester');
        if (createSemesterEl) {
            createSemesterEl.value = semDigit === '2' ? '2nd Semester' : '1st Semester';
        }
    };

    // Initialize on page load
    updateGeneratedSection();

    // Automatic Student ID Input Mask: 4 digits, automatic dash '-', then 5 digits (0000-00000)
    const newStudentIdInput = document.getElementById('newStudentId');
    if (newStudentIdInput) {
        newStudentIdInput.addEventListener('input', function () {
            // Strip any non-digit character
            let digits = this.value.replace(/\D/g, '');
            // Limit to max 9 digits
            if (digits.length > 9) {
                digits = digits.substring(0, 9);
            }
            // Insert hyphen automatically after 4th digit
            if (digits.length > 4) {
                this.value = digits.substring(0, 4) + '-' + digits.substring(4);
            } else {
                this.value = digits;
            }
        });

        newStudentIdInput.addEventListener('keydown', function (e) {
            // Smooth backspace handling at the hyphen
            if (e.key === 'Backspace' && this.value.length === 5 && this.value.endsWith('-')) {
                this.value = this.value.substring(0, 4);
            }
        });
    }

    // ── Reusable red-field validation (replaces browser tooltip) ──────────────
    function applyRedFieldValidation(form) {
        if (!form) return;
        form.setAttribute('novalidate', '');
        form.addEventListener('submit', function (e) {
            let firstInvalid = null;
            form.querySelectorAll('[required]').forEach(function (field) {
                // Reset previous error state
                field.style.borderColor = '';
                field.style.background  = '';
                const existingMsg = field.parentElement.querySelector('.inline-field-error');
                if (existingMsg) existingMsg.remove();

                const isEmpty = field.tagName === 'SELECT'
                    ? field.value === '' || field.value === null
                    : !field.value.trim();

                if (isEmpty) {
                    e.preventDefault();
                    field.style.borderColor = '#ef4444';
                    field.style.background  = '#fef2f2';

                    const msg = document.createElement('span');
                    msg.className  = 'inline-field-error';
                    msg.textContent = 'This field is required.';
                    msg.style.cssText = 'color:#ef4444;font-size:11px;display:block;margin-top:4px;font-weight:600;';
                    field.parentElement.appendChild(msg);

                    if (!firstInvalid) firstInvalid = field;

                    field.addEventListener('input', function clear() {
                        field.style.borderColor = '';
                        field.style.background  = '';
                        const m = field.parentElement.querySelector('.inline-field-error');
                        if (m) m.remove();
                        field.removeEventListener('input', clear);
                    });
                    field.addEventListener('change', function clear() {
                        field.style.borderColor = '';
                        field.style.background  = '';
                        const m = field.parentElement.querySelector('.inline-field-error');
                        if (m) m.remove();
                        field.removeEventListener('change', clear);
                    });
                }
            });
            if (firstInvalid) firstInvalid.focus();
        });
    }

    // Client-side validation on student creation form
    const createStudentForm = document.getElementById('createStudentForm');
    if (createStudentForm) {
        applyRedFieldValidation(createStudentForm);
        // Additional format check for Student ID
        createStudentForm.addEventListener('submit', function (e) {
            if (e.defaultPrevented) return; // already caught by required check above
            const sid = newStudentIdInput ? newStudentIdInput.value.trim() : '';
            if (!/^\d{4}-\d{5}$/.test(sid)) {
                e.preventDefault();
                if (newStudentIdInput) {
                    newStudentIdInput.style.borderColor = '#ef4444';
                    newStudentIdInput.style.background  = '#fef2f2';
                    const existingMsg = newStudentIdInput.parentElement.querySelector('.inline-field-error');
                    if (!existingMsg) {
                        const msg = document.createElement('span');
                        msg.className = 'inline-field-error';
                        msg.textContent = 'Format must be: 4 digits - 5 digits (e.g. 2024-12345)';
                        msg.style.cssText = 'color:#ef4444;font-size:11px;display:block;margin-top:4px;font-weight:600;';
                        newStudentIdInput.parentElement.appendChild(msg);
                    }
                    newStudentIdInput.focus();
                }
                return false;
            }
        });
    }

    // Apply to other admin forms
    applyRedFieldValidation(document.getElementById('assignFeeForm'));
    applyRedFieldValidation(document.getElementById('categoryForm'));

    const assignFeeModal = document.getElementById('assignFeeModal');
    const btnOpenAssign = document.getElementById('btnOpenAssignFeeModal');
    const btnCloseAssign = document.getElementById('btnCloseAssignFeeModal');

    if (btnOpenAssign) btnOpenAssign.addEventListener('click', () => assignFeeModal.classList.add('active'));
    if (btnCloseAssign) btnCloseAssign.addEventListener('click', () => assignFeeModal.classList.remove('active'));

    const categoryModal = document.getElementById('categoryModal');
    const btnOpenCat = document.getElementById('btnOpenCategoryModal');
    const btnCloseCat = document.getElementById('btnCloseCategoryModal');

    if (btnOpenCat) {
        btnOpenCat.addEventListener('click', () => {
            document.getElementById('catModalTitle').textContent = 'Add Fee Category';
            document.getElementById('catId').value = 0;
            document.getElementById('catName').value = '';
            document.getElementById('catAmount').value = '0.00';
            document.getElementById('catSort').value = '10';
            categoryModal.classList.add('active');
        });
    }
    if (btnCloseCat) btnCloseCat.addEventListener('click', () => categoryModal.classList.remove('active'));

    function editCategoryModal(cat) {
        document.getElementById('catModalTitle').textContent = 'Edit Fee Category';
        document.getElementById('catId').value = cat.id;
        document.getElementById('catName').value = cat.name;
        document.getElementById('catAmount').value = cat.default_amount;
        document.getElementById('catSort').value = cat.sort_order;
        categoryModal.classList.add('active');
    }

    // ── Global Search & Live Autocomplete System ─────────────
    const ALL_STUDENTS = <?= json_encode(array_map(function($s) {
        return [
            'id' => $s['id'],
            'name' => $s['first_name'] . ' ' . $s['last_name'],
            'student_id' => $s['student_id'],
            'section' => $s['grade_level'] ?? '',
            'email' => $s['email'] ?? '',
            'balance' => max(0, (float)($s['total_fee'] ?? 0) - (float)($s['total_paid'] ?? 0))
        ];
    }, $students), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

    const ALL_PAYMENTS = <?= json_encode(array_map(function($p) {
        return [
            'or_number' => $p['or_number'],
            'name' => $p['first_name'] . ' ' . $p['last_name'],
            'student_num' => $p['student_num'],
            'amount' => (float)$p['amount'],
            'method' => $p['payment_method'],
            'date' => date('M d, Y', strtotime($p['paid_at']))
        ];
    }, $payments), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

    const searchInput = document.getElementById('adminSearchInput');
    const searchDropdown = document.getElementById('adminSearchDropdown');

    function filterCurrentViewTable(query) {
        const rows = document.querySelectorAll('table tbody tr');
        rows.forEach(r => {
            // Ignore empty state rows (like colspan)
            if (r.children.length === 1 && r.children[0].hasAttribute('colspan')) return;
            const text = r.textContent.toLowerCase();
            r.style.display = text.includes(query) ? '' : 'none';
        });
    }

    function renderSearchDropdown(query) {
        if (!searchDropdown) return;
        if (!query || query.trim().length === 0) {
            searchDropdown.style.display = 'none';
            searchDropdown.innerHTML = '';
            return;
        }

        const q = query.trim().toLowerCase();

        // 1. Match Students
        const matchedStudents = ALL_STUDENTS.filter(s => {
            return s.name.toLowerCase().includes(q) ||
                   s.student_id.toLowerCase().includes(q) ||
                   s.section.toLowerCase().includes(q) ||
                   s.email.toLowerCase().includes(q);
        }).slice(0, 5);

        // 2. Match Payments / Receipts
        const matchedPayments = ALL_PAYMENTS.filter(p => {
            return p.or_number.toLowerCase().includes(q) ||
                   p.name.toLowerCase().includes(q) ||
                   p.student_num.toLowerCase().includes(q) ||
                   p.method.toLowerCase().includes(q);
        }).slice(0, 4);

        if (matchedStudents.length === 0 && matchedPayments.length === 0) {
            searchDropdown.innerHTML = `
                <div class="search-dropdown-empty">
                    No matching records found for "<strong>${escapeHtml(query)}</strong>".<br>
                    <small style="color: #94a3b8; margin-top: 4px; display: inline-block;">Press Enter to search in Students Directory</small>
                </div>
            `;
            searchDropdown.style.display = 'block';
            return;
        }

        let html = '';

        if (matchedStudents.length > 0) {
            html += `<div class="search-dropdown-group-title">Students Found (${matchedStudents.length})</div>`;
            matchedStudents.forEach(s => {
                const balFormatted = '₱' + parseFloat(s.balance).toLocaleString('en-PH', {minimumFractionDigits: 2});
                html += `
                    <a href="<?= APP_URL ?>/public/admin/?view=students&q=${encodeURIComponent(s.name)}" class="search-dropdown-item">
                        <div>
                            <strong style="color: #0f172a;">${escapeHtml(s.name)}</strong>
                            <span class="item-meta"> &bull; ${escapeHtml(s.student_id)} &bull; ${escapeHtml(s.section)}</span>
                        </div>
                        <div style="font-weight: 700; font-size: 11.5px; color: ${s.balance > 0 ? '#dc2626' : '#059669'};">
                            Bal: ${balFormatted}
                        </div>
                    </a>
                `;
            });
        }

        if (matchedPayments.length > 0) {
            html += `<div class="search-dropdown-group-title">Official Receipts (${matchedPayments.length})</div>`;
            matchedPayments.forEach(p => {
                const amtFormatted = '₱' + parseFloat(p.amount).toLocaleString('en-PH', {minimumFractionDigits: 2});
                html += `
                    <a href="<?= APP_URL ?>/public/admin/?view=transactions&q=${encodeURIComponent(p.or_number)}" class="search-dropdown-item">
                        <div>
                            <strong style="font-family: monospace; color: #0b3d2e;">${escapeHtml(p.or_number)}</strong>
                            <span class="item-meta"> &bull; ${escapeHtml(p.name)} &bull; ${escapeHtml(p.date)}</span>
                        </div>
                        <div style="font-weight: 800; font-size: 12px; color: #059669;">
                            ${amtFormatted}
                        </div>
                    </a>
                `;
            });
        }

        html += `
            <div style="background: #f8fafc; padding: 7px 14px; text-align: right; border-top: 1px solid #f1f5f9;">
                <a href="<?= APP_URL ?>/public/admin/?view=students&q=${encodeURIComponent(query)}" style="font-size: 11px; font-weight: 700; color: #2563eb; text-decoration: none;">
                    View all results in Students Directory &rarr;
                </a>
            </div>
        `;

        searchDropdown.innerHTML = html;
        searchDropdown.style.display = 'block';
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>"']/g, function(m) {
            return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'}[m];
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value;
            filterCurrentViewTable(query.toLowerCase());
            renderSearchDropdown(query);
        });

        searchInput.addEventListener('focus', function() {
            if (this.value.trim().length > 0) {
                renderSearchDropdown(this.value);
            }
        });

        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const q = this.value.trim();
                if (q.length > 0) {
                    window.location.href = '<?= APP_URL ?>/public/admin/?view=students&q=' + encodeURIComponent(q);
                }
            } else if (e.key === 'Escape') {
                if (searchDropdown) searchDropdown.style.display = 'none';
            }
        });

        // Pre-fill filter on page load if ?q= is present
        const urlParams = new URLSearchParams(window.location.search);
        const initialQuery = urlParams.get('q');
        if (initialQuery && initialQuery.trim().length > 0) {
            searchInput.value = initialQuery;
            filterCurrentViewTable(initialQuery.toLowerCase());
        }
    }

    // Close search dropdown on click outside
    document.addEventListener('click', function(e) {
        if (searchDropdown && !searchDropdown.contains(e.target) && e.target !== searchInput) {
            searchDropdown.style.display = 'none';
        }
    });

    // ⌘K or Ctrl+K shortcut to focus search bar
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K')) {
            e.preventDefault();
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
    });

    // Enhanced SweetAlert notification when admin creates a student account
    <?php if (!empty($createdCreds)): ?>
        window.copyCredVal = function(text, btnId) {
            navigator.clipboard.writeText(text).then(() => {
                const btn = document.getElementById(btnId);
                if (btn) {
                    const oldHtml = btn.innerHTML;
                    btn.innerHTML = '<span>&#10003; Copied!</span>';
                    btn.style.background = '#059669';
                    btn.style.color = '#ffffff';
                    btn.style.borderColor = '#059669';
                    setTimeout(() => {
                        btn.innerHTML = oldHtml;
                        btn.style.background = '';
                        btn.style.color = '';
                        btn.style.borderColor = '';
                    }, 1800);
                }
            });
        };

        window.copyAllCreds = function(name, id, pw, url) {
            const fullText = `PayTrack — Student Portal Credentials\n` +
                `Student Name: ${name}\n` +
                `Student ID (Username): ${id}\n` +
                `Default Password: ${pw}\n` +
                `Sign In URL: ${url}\n` +
                `Note: Password is case-insensitive.`;

            navigator.clipboard.writeText(fullText).then(() => {
                const btn = document.getElementById('btnCopyAllDetails');
                const textSpan = document.getElementById('btnCopyAllText');
                if (btn && textSpan) {
                    btn.style.background = '#059669';
                    textSpan.textContent = '✓ All Credentials Copied!';
                    setTimeout(() => {
                        btn.style.background = '#1e3a8a';
                        textSpan.textContent = 'Copy All Credentials';
                    }, 2500);
                }
            });
        };

        Swal.fire({
            icon: 'success',
            title: '<span style="font-size: 21px; font-weight: 800; color: #0f172a;">Student Account Created!</span>',
            html: `
                <div style="text-align: left; color: #1e293b; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                    
                    <!-- Student Info Card -->
                    <div style="display: flex; align-items: center; gap: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 14px; margin: 10px 0 16px;">
                        <div style="width: 42px; height: 42px; border-radius: 50%; background: #1e3a8a; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 15px; flex-shrink: 0;">
                            <?= strtoupper(substr($createdCreds['student_name'] ?? 'S', 0, 1)) ?>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 15px; font-weight: 800; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?= e($createdCreds['student_name']) ?>
                            </div>
                            <div style="font-size: 12px; color: #64748b; display: flex; align-items: center; gap: 6px; margin-top: 2px;">
                                <span>Initial Tuition:</span>
                                <strong style="color: #059669; font-weight: 700;"><?= peso((float)$createdCreds['tuition']) ?></strong>
                            </div>
                        </div>
                    </div>

                    <!-- Credential Fields with One-Click Copy -->
                    <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 16px;">
                        <!-- Student ID Card -->
                        <div style="background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 10px 14px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                            <div>
                                <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; color: #64748b; margin-bottom: 2px;">Student ID (Username)</div>
                                <div style="font-family: monospace; font-size: 16px; font-weight: 800; color: #0f172a;"><?= e($createdCreds['student_id']) ?></div>
                            </div>
                            <button type="button" id="btnCopyId" onclick="copyCredVal('<?= e($createdCreds['student_id']) ?>', 'btnCopyId')" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; border-radius: 7px; padding: 6px 12px; font-size: 11.5px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 4px; transition: all 0.15s;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                <span>Copy</span>
                            </button>
                        </div>

                        <!-- Password Card -->
                        <div style="background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 10px 14px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                            <div>
                                <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; color: #64748b; margin-bottom: 2px;">Default Password (Last Name)</div>
                                <div style="font-family: monospace; font-size: 16px; font-weight: 800; color: #059669;"><?= e($createdCreds['default_password']) ?></div>
                            </div>
                            <button type="button" id="btnCopyPw" onclick="copyCredVal('<?= e($createdCreds['default_password']) ?>', 'btnCopyPw')" style="background: #ecfdf5; color: #059669; border: 1px solid #bbf7d0; border-radius: 7px; padding: 6px 12px; font-size: 11.5px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 4px; transition: all 0.15s;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                <span>Copy</span>
                            </button>
                        </div>
                    </div>

                    <!-- Notice Pill -->
                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 9px; padding: 9px 12px; margin-bottom: 16px; display: flex; align-items: flex-start; gap: 8px;">
                        <span style="font-size: 13px; line-height: 1.2;">💡</span>
                        <div style="font-size: 11px; color: #166534; line-height: 1.4;">
                            Default password is the student's <strong>Last Name</strong> (case-insensitive: <code><?= strtolower(e($createdCreds['default_password'])) ?></code> or <code><?= strtoupper(e($createdCreds['default_password'])) ?></code>).
                        </div>
                    </div>

                    <!-- Copy All Details Button -->
                    <button type="button" id="btnCopyAllDetails" onclick="copyAllCreds('<?= e(addslashes($createdCreds['student_name'])) ?>', '<?= e($createdCreds['student_id']) ?>', '<?= e($createdCreds['default_password']) ?>', '<?= APP_URL ?>/public/')" style="width: 100%; background: #1e3a8a; color: #ffffff; border: none; border-radius: 8px; padding: 11px 16px; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.15s; box-shadow: 0 2px 6px rgba(30,58,138,0.18);">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                        <span id="btnCopyAllText">Copy All Credentials</span>
                    </button>
                </div>
            `,
            showConfirmButton: true,
            confirmButtonText: 'Done / Close',
            confirmButtonColor: '#64748b',
            width: 480
        });
    <?php endif; ?>
</script>

</body>
</html>
