<?php
/**
 * National College of Science and Technology (NCST)
 * Official Electronic Receipt Printable Voucher
 */
$studentName = $student ? ($student['first_name'] . ' ' . $student['last_name']) : 'Student';
$studentNum  = $student ? $student['student_id'] : '2023-53512';
$courseSec   = $student['grade_level'] ?? 'BSCS 11A1';

$p = $selectedPayment ?? (!empty($payments) ? $payments[0] : null);
$orNumber = $p['or_number'] ?? 'OR-2024-00001';
$amount   = (float) ($p['amount'] ?? 0);
$method   = strtoupper($p['payment_method'] ?? 'ONLINE');
$paidAt   = !empty($p['paid_at']) ? date('F d, Y h:i A', strtotime($p['paid_at'])) : date('F d, Y h:i A');
$feeDesc  = $p['fee_desc'] ?? 'Tuition Fee Assessment Installment';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Receipt - <?= e($orNumber) ?> | National College of Science and Technology</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif;
            background-color: #f1f5f9;
            color: #0f172a;
            padding: 30px 16px;
            display: flex;
            justify-content: center;
        }
        .receipt-container {
            width: 100%;
            max-width: 720px;
            background: #ffffff;
            border: 2px solid #0b3d2e;
            border-radius: 12px;
            padding: 36px 42px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .header-section {
            text-align: center;
            border-bottom: 2.5px solid #0b3d2e;
            padding-bottom: 18px;
            margin-bottom: 22px;
        }
        .header-country {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
            color: #0b3d2e;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .header-school {
            font-size: 22px;
            font-weight: 900;
            color: #0b3d2e;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .header-address {
            font-size: 12px;
            color: #475569;
            line-height: 1.4;
        }
        .or-badge {
            display: inline-block;
            margin-top: 12px;
            background: #f0fdf4;
            border: 1.5px solid #86efac;
            color: #166534;
            font-weight: 800;
            font-size: 13px;
            padding: 4px 18px;
            border-radius: 20px;
            letter-spacing: 1px;
        }
        .meta-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 18px;
            margin-bottom: 20px;
        }
        .meta-label {
            font-size: 11px;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
        }
        .particulars-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px 28px;
            margin-bottom: 22px;
        }
        .item-box {
            border-bottom: 1px dashed #e2e8f0;
            padding-bottom: 6px;
        }
        .item-label {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .item-val {
            font-size: 14.5px;
            font-weight: 800;
            color: #0f172a;
        }
        .table-box {
            background: #fafafa;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px 18px;
            margin-bottom: 22px;
        }
        .table-header {
            display: flex;
            justify-content: space-between;
            font-size: 12.5px;
            font-weight: 700;
            color: #334155;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 8px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        .table-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: #0f172a;
            padding: 4px 0;
        }
        .amount-highlight {
            background: #f0fdf4;
            border: 2px solid #86efac;
            border-radius: 10px;
            padding: 18px;
            text-align: center;
            margin-bottom: 24px;
        }
        .amount-title {
            font-size: 11.5px;
            font-weight: 800;
            color: #166534;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .amount-num {
            font-size: 32px;
            font-weight: 900;
            color: #15803d;
            line-height: 1.2;
            margin: 4px 0;
        }
        .amount-status {
            font-size: 12px;
            color: #166534;
            font-weight: 600;
        }
        .footer-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding-top: 16px;
            border-top: 1px solid #e2e8f0;
            font-size: 11.5px;
        }
        .footer-note {
            color: #64748b;
            line-height: 1.5;
            max-width: 340px;
        }
        .signature-box {
            text-align: center;
            width: 220px;
        }
        .action-bar {
            margin-top: 24px;
            display: flex;
            justify-content: center;
            gap: 12px;
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 24px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
        }
        .btn-print {
            background: #0b3d2e;
            color: #ffffff;
        }
        .btn-print:hover {
            background: #14532d;
        }
        .btn-back {
            background: #e2e8f0;
            color: #334155;
        }
        .btn-back:hover {
            background: #cbd5e1;
        }

        /* ── Strict Printable CSS ── */
        @media print {
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            html, body { width: 210mm; min-height: 297mm; }
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .action-bar, .no-print {
                display: none !important;
            }
            .receipt-container {
                box-shadow: none !important;
                border: 2px solid #0b3d2e !important;
                max-width: 190mm !important;
                width: 190mm !important;
                margin: 0 !important;
                padding: 24px 30px !important;
                border-radius: 0 !important;
            }
            @page {
                size: portrait;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>

<div class="receipt-container">
    <!-- Header -->
    <div class="header-section">
        <div class="header-country">Republic of the Philippines</div>
        <h1 class="header-school">National College of Science and Technology</h1>
        <p class="header-address">
            Emilio Aguinaldo Highway, Dasmariñas City, Cavite 4114, Philippines<br>
            Student Tuition &amp; Assessment Management System (PayTrack)
        </p>
        <div class="or-badge">OFFICIAL ELECTRONIC RECEIPT</div>
    </div>

    <!-- Meta Bar -->
    <div class="meta-bar">
        <div>
            <span class="meta-label">Receipt No (OR#):</span>
            <strong style="font-size: 16px; color: #0b3d2e; font-family: monospace; margin-left: 6px;"><?= e($orNumber) ?></strong>
        </div>
        <div>
            <span class="meta-label">Date Issued:</span>
            <strong style="font-size: 13px; color: #1e293b; margin-left: 6px;"><?= e($paidAt) ?></strong>
        </div>
    </div>

    <!-- Particulars Grid -->
    <div class="particulars-grid">
        <div class="item-box">
            <div class="item-label">Student Name</div>
            <div class="item-val"><?= e($studentName) ?></div>
        </div>
        <div class="item-box">
            <div class="item-label">Student ID Number</div>
            <div class="item-val" style="font-family: monospace;"><?= e($studentNum) ?></div>
        </div>
        <div class="item-box">
            <div class="item-label">Course &amp; Section</div>
            <div class="item-val"><?= e($courseSec) ?></div>
        </div>
        <div class="item-box">
            <div class="item-label">Payment Channel</div>
            <div class="item-val" style="color: #0b3d2e;"><?= e($method) ?></div>
        </div>
    </div>

    <!-- Payment Purpose / Particulars -->
    <div class="table-box">
        <div class="table-header">
            <span>Particulars / Payment Description</span>
            <span>Amount Paid</span>
        </div>
        <div class="table-row">
            <span><?= e($feeDesc) ?></span>
            <strong style="color: #0b3d2e;"><?= peso($amount) ?></strong>
        </div>
    </div>

    <!-- Highlighted Amount -->
    <div class="amount-highlight">
        <div class="amount-title">Total Amount Received</div>
        <div class="amount-num"><?= peso($amount) ?></div>
        <div class="amount-status">&#10003; Verified &amp; Credited to Student Tuition Ledger</div>
    </div>

    <!-- Footer -->
    <div class="footer-section">
        <div class="footer-note">
            <strong>National College of Science and Technology</strong><br>
            Official System Electronic Receipt<br>
            Valid for Examination Clearance &amp; Official Verification
        </div>
        <div class="signature-box">
            <div style="font-weight: 800; font-size: 12px; color: #0b3d2e; margin-bottom: 2px;">FINANCE &amp; CASHIERING</div>
            <div style="border-top: 1.5px solid #0f172a; padding-top: 4px; color: #475569; font-size: 10.5px;">
                Authorized Electronic Stamp / Signature
            </div>
        </div>
    </div>

    <!-- Action Bar (Hidden on print) -->
    <div class="action-bar no-print">
        <button type="button" class="btn-action btn-print" onclick="window.print()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Print / Save as PDF
        </button>
        <a href="<?= APP_URL ?>/public/student/?view=receipts" class="btn-action btn-back">
            &larr; Back to Portal
        </a>
    </div>
</div>

</body>
</html>
