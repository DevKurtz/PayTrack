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
                    <span class="ic">&#8962;</span> Students
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

        <div style="margin-top: auto; padding-top: 16px;">
            <button type="button" class="btn" id="btnLogout" style="width: 100%; justify-content: center;">
                &#8592; Logout
            </button>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="main">
        <!-- Topbar -->
        <header class="topbar">
            <button class="mobile-menu-btn" id="btnOpenSidebar" aria-label="Toggle Navigation">&#9776;</button>

            <div class="search">
                <span class="search-ic">&#128269;</span>
                <input type="text" id="adminSearchInput" placeholder="Search records...">
                <span class="kbd">⌘K</span>
            </div>

            <div class="topbar-actions">
                <button class="btn dark" id="btnOpenCreateStudentModal">
                    <span>+</span> Create Student
                </button>
                <div class="avatar"></div>
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

            <!-- VIEW 1: STUDENTS -->
            <?php if ($currentView === 'home'): ?>
                <div class="page-head">
                    <div>
                        <h1>Students &amp; Balances</h1>
                        <p>Directory of student accounts, tuition statuses, and parent contacts</p>
                    </div>
                </div>

                <div class="panel">
                    <div class="table-responsive">
                        <table id="dataTable">
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>Student ID</th>
                                    <th>Class / Grade</th>
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
                                            No student accounts found. Click "+ Create Student" above to add.
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
                                            <td><?= e($s['grade_level'] ?: 'Class A') ?></td>
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
                                                <form method="POST" action="<?= APP_URL ?>/public/admin/" style="display: inline;" id="delStudentForm-<?= $s['id'] ?>">
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

        <form method="POST" action="<?= APP_URL ?>/public/admin/" id="createStudentForm">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="create_student">

            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label" for="newStudentId">Student ID * (Format: 0000-00000)</label>
                    <input type="text" class="form-control" name="student_id" id="newStudentId" required placeholder="0000-00000" maxlength="10" autocomplete="off">
                    <small style="color: #64748b; font-size: 11px; display: block; margin-top: 3px;">Auto-formats: 4 numbers, automatic dash, then 5 numbers (e.g. <code>2024-12345</code>)</small>
                </div>
                <div class="form-group">
                    <label class="form-label" for="newClassGrade">Class / Grade *</label>
                    <input type="text" class="form-control" name="class_grade" id="newClassGrade" required value="Grade 11 - STEM">
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
                    <input type="text" class="form-control" name="school_year" id="createSchoolYear" value="2024-2025" required>
                </div>
            </div>

            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">Semester *</label>
                    <select class="form-control" name="semester" required>
                        <option value="1st Semester">1st Semester</option>
                        <option value="2nd Semester">2nd Semester</option>
                        <option value="Summer">Summer</option>
                    </select>
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
            const sid = document.getElementById('newStudentId');
            if (sid) {
                setTimeout(() => sid.focus(), 150);
            }
        });
    }
    if (btnCloseCreate) btnCloseCreate.addEventListener('click', () => createStudentModal.classList.remove('active'));

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

    // Client-side validation on student creation form
    const createStudentForm = document.getElementById('createStudentForm');
    if (createStudentForm) {
        createStudentForm.addEventListener('submit', function (e) {
            const sid = newStudentIdInput ? newStudentIdInput.value.trim() : '';
            if (!/^\d{4}-\d{5}$/.test(sid)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Incomplete Student ID',
                    html: 'Please enter a complete <strong>9-digit Student ID</strong>.<br><br>Format: <strong>4 digits - 5 digits</strong> (e.g. <code>2024-12345</code>)',
                    confirmButtonColor: '#1e3a8a'
                });
                if (newStudentIdInput) newStudentIdInput.focus();
                return false;
            }
        });
    }

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

    // Search filter
    const searchInput = document.getElementById('adminSearchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll('#dataTable tbody tr');
            rows.forEach(r => {
                const text = r.textContent.toLowerCase();
                r.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }

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
