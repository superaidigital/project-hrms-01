<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>พิมพ์ข้อมูลแฟ้มประวัติ - ตู้ <?= htmlspecialchars($cabinet) ?> ชั้น <?= htmlspecialchars($shelf) ?></title>
    <!-- นำเข้า Bootstrap CSS สำหรับจัดหน้า -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* CSS เฉพาะสำหรับการพิมพ์ (Print Media Query) */
        @media print {
            .no-print { display: none !important; }
            body { background: white; -webkit-print-color-adjust: exact; }
            @page { size: A4; margin: 10mm; }
        }
        
        body { font-family: 'Sarabun', sans-serif; background: #f8f9fa; }
        .print-container { background: white; max-width: 210mm; margin: 20px auto; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.1); min-height: 297mm; }
        
        /* สไตล์สำหรับป้ายติดหน้าตู้ */
        .cabinet-label-box { border: 5px solid #000; padding: 40px; text-align: center; margin-top: 50px; border-radius: 15px; }
        .cabinet-title { font-size: 80px; font-weight: bold; margin: 0; }
        .shelf-title { font-size: 50px; margin: 0; color: #555; }
        
        /* สไตล์สำหรับสติ๊กเกอร์สันแฟ้ม */
        .spine-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; }
        .spine-label { border: 1px solid #000; padding: 15px; border-radius: 5px; height: 120px; display: flex; flex-direction: column; justify-content: center; }
        .spine-seq { font-size: 24px; font-weight: bold; position: absolute; top: 5px; right: 10px; }
    </style>
</head>
<body>

    <!-- ปุ่มควบคุม (ไม่แสดงตอนพิมพ์) -->
    <div class="text-center my-3 no-print">
        <button class="btn btn-success btn-lg" onclick="window.print()"><i class="fas fa-print"></i> สั่งพิมพ์เอกสารหน้านี้</button>
        <button class="btn btn-secondary btn-lg" onclick="window.close()">ปิดหน้าต่าง</button>
    </div>

    <div class="print-container">

        <?php if($type === 'label'): ?>
            <!-- ================= รูปแบบ 1: ป้ายติดหน้าตู้/ชั้น ================= -->
            <div class="cabinet-label-box">
                <p style="font-size: 30px; margin-bottom: -10px;">แฟ้มประวัติบุคลากร</p>
                <h1 class="cabinet-title">ตู้เอกสาร <?= htmlspecialchars($cabinet) ?></h1>
                <h2 class="shelf-title">ชั้นที่ <?= htmlspecialchars($shelf) ?></h2>
                <hr style="border-top: 3px solid #000; margin: 30px 0;">
                <p style="font-size: 24px;">จำนวนแฟ้มทั้งหมด: <?= count($employees) ?> เล่ม</p>
            </div>


        <?php elseif($type === 'list'): ?>
            <!-- ================= รูปแบบ 2: สารบัญรายชื่อ (ตาราง A4) ================= -->
            <div class="text-center mb-4">
                <h4>สารบัญแฟ้มประวัติบุคลากร</h4>
                <h5>ตู้เอกสาร: <strong><?= htmlspecialchars($cabinet) ?></strong> | ชั้นที่: <strong><?= htmlspecialchars($shelf) ?></strong></h5>
                <p>จำนวนทั้งหมด: <?= count($employees) ?> เล่ม (ข้อมูล ณ วันที่ <?= date('d/m/Y') ?>)</p>
            </div>
            <table class="table table-bordered table-sm" style="border-color: #000;">
                <thead class="table-light text-center" style="border-color: #000;">
                    <tr>
                        <th width="10%">ลำดับแฟ้ม</th>
                        <th width="30%">ชื่อ - สกุล</th>
                        <th width="30%">ตำแหน่ง</th>
                        <th width="20%">สังกัด</th>
                        <th width="10%">หมายเหตุ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($employees)): ?>
                        <tr><td colspan="5" class="text-center">ไม่มีข้อมูลแฟ้มในชั้นนี้</td></tr>
                    <?php else: ?>
                        <?php foreach($employees as $emp): ?>
                        <tr>
                            <td class="text-center fw-bold fs-5"><?= htmlspecialchars($emp['folder_seq'] ?: '-') ?></td>
                            <td><?= htmlspecialchars($emp['prefix'].$emp['first_name'].' '.$emp['last_name']) ?></td>
                            <td style="font-size: 0.9em;"><?= htmlspecialchars($emp['position_name']) ?></td>
                            <td style="font-size: 0.9em;"><?= htmlspecialchars($emp['department']) ?></td>
                            <td style="font-size: 0.9em;"><?= htmlspecialchars($emp['doc_remark']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>


        <?php elseif($type === 'spine'): ?>
            <!-- ================= รูปแบบ 3: แถบชื่อติดสันแฟ้ม (สติ๊กเกอร์) ================= -->
            <h5 class="no-print text-center mb-4 text-danger">รูปแบบนี้ออกแบบมาเพื่อปริ้นและตัดแปะสันแฟ้ม (หรือปกหน้า)</h5>
            <div class="spine-grid">
                <?php foreach($employees as $emp): ?>
                    <div class="spine-label" style="position: relative;">
                        <div class="spine-seq"><?= htmlspecialchars($emp['folder_seq']) ?></div>
                        <div style="font-size: 14px; color: #555;">ตู้ <?= htmlspecialchars($cabinet) ?> ชั้น <?= htmlspecialchars($shelf) ?></div>
                        <div style="font-size: 20px; font-weight: bold; margin-top: 5px;"><?= htmlspecialchars($emp['prefix'].$emp['first_name'].' '.$emp['last_name']) ?></div>
                        <div style="font-size: 14px; margin-top: 5px;"><strong>ตำแหน่ง:</strong> <?= htmlspecialchars($emp['position_name']) ?></div>
                        <div style="font-size: 14px;"><strong>เลข ปชช:</strong> <?= htmlspecialchars($emp['national_id']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php else: ?>
            <div class="alert alert-danger text-center">ไม่พบรูปแบบการพิมพ์ที่ระบุ</div>
        <?php endif; ?>

    </div>
</body>
</html>