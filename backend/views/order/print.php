<?php
/**
 * Order Print - ใบเสร็จ/ใบกำกับภาษี
 * @var yii\web\View $this
 * @var common\models\Order $model
 * @var string $type (invoice, receipt, delivery)
 */

use yii\helpers\Html;

$this->title = $model->order_number;

$typeLabels = [
    'invoice' => 'ใบเสนอราคา',
    'receipt' => 'ใบเสร็จรับเงิน',
    'delivery' => 'ใบส่งของ',
    'tax_invoice' => 'ใบกำกับภาษี',
];

$docTitle = $typeLabels[$type] ?? 'ใบเสร็จ';

// Company info (would be from settings in real app)
$company = [
    'name' => 'ดูน ออโต้ พาร์ท',
    'name_en' => "Dune's Auto Parts",
    'address' => '123 ถนนสุขุมวิท แขวงคลองเตย เขตคลองเตย กรุงเทพฯ 10110',
    'phone' => '02-xxx-xxxx',
    'mobile' => '08x-xxx-xxxx',
    'email' => 'contact@dunesautoparts.com',
    'tax_id' => 'x-xxxx-xxxxx-xx-x',
    'line' => '@dunesautoparts',
];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Html::encode($docTitle . ' - ' . $model->order_number) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Sarabun', sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #333;
            background: #fff;
        }
        
        .print-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Header */
        .doc-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #000;
        }
        
        .company-info h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .company-info .subtitle {
            font-size: 16px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .company-info p {
            font-size: 12px;
            color: #666;
            margin-bottom: 2px;
        }
        
        .doc-type {
            text-align: right;
        }
        
        .doc-type h2 {
            font-size: 28px;
            font-weight: 700;
            color: #0056b3;
            margin-bottom: 10px;
        }
        
        .doc-type .doc-number {
            font-size: 16px;
            font-weight: 600;
        }
        
        .doc-type .doc-date {
            font-size: 14px;
            color: #666;
        }
        
        /* Customer & Shipping Info */
        .info-section {
            display: flex;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .info-box {
            flex: 1;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        
        .info-box h3 {
            font-size: 14px;
            font-weight: 600;
            color: #666;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        
        .info-box .name {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .info-box p {
            font-size: 13px;
            margin-bottom: 3px;
        }
        
        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .items-table th {
            background: #0056b3;
            color: #fff;
            padding: 12px 10px;
            text-align: left;
            font-weight: 600;
        }
        
        .items-table th.text-center {
            text-align: center;
        }
        
        .items-table th.text-end {
            text-align: right;
        }
        
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .items-table td.text-center {
            text-align: center;
        }
        
        .items-table td.text-end {
            text-align: right;
        }
        
        .items-table .item-name {
            font-weight: 600;
        }
        
        .items-table .item-sku {
            font-size: 12px;
            color: #666;
        }
        
        .items-table tfoot td {
            padding: 8px 10px;
            border: none;
        }
        
        .items-table tfoot .total-row td {
            font-size: 18px;
            font-weight: 700;
            padding-top: 15px;
            border-top: 2px solid #000;
        }
        
        /* Summary */
        .summary-section {
            display: flex;
            justify-content: space-between;
            gap: 30px;
        }
        
        .notes-box {
            flex: 1;
        }
        
        .notes-box h3 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .notes-box p {
            font-size: 13px;
            color: #666;
        }
        
        .payment-info {
            width: 300px;
        }
        
        .payment-info h3 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .bank-account {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            font-size: 13px;
        }
        
        .bank-account .bank-name {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        /* Footer */
        .doc-footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            width: 200px;
            text-align: center;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            margin: 40px 0 10px;
        }
        
        .signature-label {
            font-size: 12px;
            color: #666;
        }
        
        /* Print specific */
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            
            .no-print {
                display: none !important;
            }
            
            .print-container {
                padding: 0;
                max-width: none;
            }
        }
        
        /* Print button */
        .print-actions {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 100;
        }
        
        .print-actions button {
            padding: 10px 20px;
            font-size: 14px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            margin-left: 10px;
        }
        
        .btn-print {
            background: #0056b3;
            color: #fff;
        }
        
        .btn-back {
            background: #6c757d;
            color: #fff;
        }
    </style>
</head>
<body>
    <!-- Print Actions -->
    <div class="print-actions no-print">
        <button class="btn-back" onclick="window.history.back()">← กลับ</button>
        <button class="btn-print" onclick="window.print()">🖨️ พิมพ์</button>
    </div>

    <div class="print-container">
        <!-- Header -->
        <div class="doc-header">
            <div class="company-info">
                <h1><?= Html::encode($company['name']) ?></h1>
                <div class="subtitle"><?= Html::encode($company['name_en']) ?></div>
                <p><strong>ที่อยู่:</strong> <?= Html::encode($company['address']) ?></p>
                <p><strong>โทร:</strong> <?= Html::encode($company['phone']) ?> | <strong>มือถือ:</strong> <?= Html::encode($company['mobile']) ?></p>
                <p><strong>Email:</strong> <?= Html::encode($company['email']) ?> | <strong>Line:</strong> <?= Html::encode($company['line']) ?></p>
                <?php if ($type == 'tax_invoice'): ?>
                <p><strong>เลขประจำตัวผู้เสียภาษี:</strong> <?= Html::encode($company['tax_id']) ?></p>
                <?php endif; ?>
            </div>
            <div class="doc-type">
                <h2><?= Html::encode($docTitle) ?></h2>
                <div class="doc-number">เลขที่: <?= Html::encode($model->order_number) ?></div>
                <div class="doc-date">วันที่: <?= Yii::$app->formatter->asDate($model->created_at, 'php:d/m/Y') ?></div>
            </div>
        </div>

        <!-- Customer & Shipping Info -->
        <div class="info-section">
            <div class="info-box">
                <h3>ข้อมูลลูกค้า</h3>
                <?php if ($model->customer): ?>
                <div class="name"><?= Html::encode($model->customer->full_name) ?></div>
                <p><strong>รหัส:</strong> <?= Html::encode($model->customer->customer_code) ?></p>
                <p><strong>โทร:</strong> <?= Html::encode($model->customer->phone) ?></p>
                <?php if ($model->customer->email): ?>
                <p><strong>Email:</strong> <?= Html::encode($model->customer->email) ?></p>
                <?php endif; ?>
                <?php if ($model->customer->tax_id): ?>
                <p><strong>เลขผู้เสียภาษี:</strong> <?= Html::encode($model->customer->tax_id) ?></p>
                <?php endif; ?>
                <?php else: ?>
                <p>-</p>
                <?php endif; ?>
            </div>
            
            <div class="info-box">
                <h3>ข้อมูลจัดส่ง</h3>
                <div class="name"><?= Html::encode($model->shipping_name ?: '-') ?></div>
                <p><strong>โทร:</strong> <?= Html::encode($model->shipping_phone ?: '-') ?></p>
                <p><strong>ที่อยู่:</strong> <?= Html::encode($model->shipping_address ?: '-') ?></p>
                <p><strong>วิธีจัดส่ง:</strong> <?= Html::encode($model->getShippingMethodLabel()) ?></p>
                <?php if ($model->tracking_number): ?>
                <p><strong>เลขพัสดุ:</strong> <?= Html::encode($model->tracking_number) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th width="50" class="text-center">#</th>
                    <th>รายการสินค้า</th>
                    <th width="80" class="text-center">จำนวน</th>
                    <th width="120" class="text-end">ราคา/หน่วย</th>
                    <th width="100" class="text-end">ส่วนลด</th>
                    <th width="120" class="text-end">รวม</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($model->items as $index => $item): ?>
                <tr>
                    <td class="text-center"><?= $index + 1 ?></td>
                    <td>
                        <div class="item-name"><?= Html::encode($item->part_name) ?></div>
                        <?php if ($item->part): ?>
                        <div class="item-sku"><?= Html::encode($item->part->sku) ?></div>
                        <?php endif; ?>
                        <?php if ($item->warranty_until): ?>
                        <div class="item-sku">รับประกันถึง: <?= Yii::$app->formatter->asDate($item->warranty_until, 'php:d/m/Y') ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?= number_format($item->quantity) ?></td>
                    <td class="text-end"><?= Yii::$app->formatter->asCurrency($item->unit_price) ?></td>
                    <td class="text-end"><?= $item->discount > 0 ? Yii::$app->formatter->asCurrency($item->discount) : '-' ?></td>
                    <td class="text-end"><?= Yii::$app->formatter->asCurrency($item->subtotal) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4"></td>
                    <td class="text-end">ยอดสินค้า:</td>
                    <td class="text-end"><?= Yii::$app->formatter->asCurrency($model->subtotal) ?></td>
                </tr>
                <?php if ($model->discount_amount > 0): ?>
                <tr>
                    <td colspan="4"></td>
                    <td class="text-end">ส่วนลด:</td>
                    <td class="text-end">-<?= Yii::$app->formatter->asCurrency($model->discount_amount) ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td colspan="4"></td>
                    <td class="text-end">ค่าจัดส่ง:</td>
                    <td class="text-end"><?= Yii::$app->formatter->asCurrency($model->shipping_cost) ?></td>
                </tr>
                <?php if ($type == 'tax_invoice'): ?>
                <tr>
                    <td colspan="4"></td>
                    <td class="text-end">ภาษีมูลค่าเพิ่ม 7%:</td>
                    <td class="text-end"><?= Yii::$app->formatter->asCurrency($model->grand_total * 0.07 / 1.07) ?></td>
                </tr>
                <?php endif; ?>
                <tr class="total-row">
                    <td colspan="4"></td>
                    <td class="text-end">ยอดรวมทั้งสิ้น:</td>
                    <td class="text-end"><?= Yii::$app->formatter->asCurrency($model->grand_total) ?></td>
                </tr>
            </tfoot>
        </table>

        <!-- Summary Section -->
        <div class="summary-section">
            <div class="notes-box">
                <h3>หมายเหตุ</h3>
                <?php if ($model->notes): ?>
                <p><?= nl2br(Html::encode($model->notes)) ?></p>
                <?php else: ?>
                <p>-</p>
                <?php endif; ?>
                
                <?php if ($type == 'invoice' || $type == 'receipt'): ?>
                <div style="margin-top: 20px;">
                    <h3>เงื่อนไขการชำระเงิน</h3>
                    <p>• กรุณาชำระเงินภายใน 7 วัน</p>
                    <p>• โอนเงินเข้าบัญชีที่ระบุ แล้วแจ้งหลักฐานการโอน</p>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if ($type == 'invoice' || $type == 'receipt'): ?>
            <div class="payment-info">
                <h3>ข้อมูลการชำระเงิน</h3>
                <div class="bank-account">
                    <div class="bank-name">ธนาคารกสิกรไทย</div>
                    <p>ชื่อบัญชี: บริษัท ดูน ออโต้ พาร์ท จำกัด</p>
                    <p>เลขบัญชี: xxx-x-xxxxx-x</p>
                </div>
                <div class="bank-account" style="margin-top: 10px;">
                    <div class="bank-name">พร้อมเพย์</div>
                    <p>หมายเลข: xxx-xxx-xxxx</p>
                    <p>ชื่อ: ดูน ออโต้ พาร์ท</p>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Footer with Signatures -->
        <div class="doc-footer">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">ผู้รับสินค้า</div>
                <div class="signature-label">วันที่: ___/___/______</div>
            </div>
            
            <?php if ($type != 'delivery'): ?>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">ผู้รับเงิน</div>
                <div class="signature-label">วันที่: ___/___/______</div>
            </div>
            <?php endif; ?>
            
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">ผู้มีอำนาจลงนาม</div>
                <div class="signature-label">วันที่: <?= Yii::$app->formatter->asDate(time(), 'php:d/m/Y') ?></div>
            </div>
        </div>

        <!-- Thank you message -->
        <div style="text-align: center; margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 5px;">
            <p style="font-size: 16px; font-weight: 600; color: #0056b3;">ขอบคุณที่ใช้บริการ ดูน ออโต้ พาร์ท</p>
            <p style="font-size: 13px; color: #666;">หากมีข้อสงสัยประการใด กรุณาติดต่อ <?= Html::encode($company['phone']) ?> หรือ Line: <?= Html::encode($company['line']) ?></p>
        </div>
    </div>
</body>
</html>
