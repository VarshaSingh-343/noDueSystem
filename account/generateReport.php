<?php
session_start();
include '../connect.php';
require('../fpdf/fpdf.php');

// Fetch the filter values from POST request, if any
$selectedCourse = isset($_POST['course']) ? $_POST['course'] : '';
$selectedBatch = isset($_POST['batchSession']) ? $_POST['batchSession'] : '';
$selectedRefundStatus = isset($_POST['refundStatus']) ? $_POST['refundStatus'] : '';
$selectedStartDate = isset($_POST['startDate']) ? $_POST['startDate'] : '';
$selectedEndDate = isset($_POST['endDate']) ? $_POST['endDate'] : '';
$selectedDepartmentDues = isset($_POST['departmentDues']) ? $_POST['departmentDues'] : '';

// Initialize query components
$conditions = [];
$params = [];
$paramTypes = '';

// Build query conditions dynamically based on selected filters
if (!empty($selectedCourse)) {
    $conditions[] = "s.Course = ?";
    $params[] = $selectedCourse;
    $paramTypes .= 's'; // 's' for string
}

if (!empty($selectedBatch)) {
    $conditions[] = "s.batchSession = ?";
    $params[] = $selectedBatch;
    $paramTypes .= 's';
}

if (!empty($selectedRefundStatus)) {
    $conditions[] = "rr.refundStatus = ?";
    $params[] = $selectedRefundStatus;
    $paramTypes .= 's';
}

if (!empty($selectedStartDate)) {
    $conditions[] = "rr.requestDate >= ?";
    $params[] = $selectedStartDate;
    $paramTypes .= 's';
}

if (!empty($selectedEndDate)) {
    $conditions[] = "rr.requestDate <= ?";
    $params[] = $selectedEndDate;
    $paramTypes .= 's';
}

if (!empty($selectedDepartmentDues)) {
    if ($selectedDepartmentDues == 'Cleared') {
        $conditions[] = "(SELECT COUNT(*) FROM nodues n WHERE n.requestId = rr.requestId AND n.noDueApproval = 'Yes') = 
        (SELECT COUNT(*) FROM nodues n WHERE n.requestId = rr.requestId)";
    } elseif ($selectedDepartmentDues == 'Not Cleared') {
        $conditions[] = "(SELECT COUNT(*) FROM nodues n WHERE n.requestId = rr.requestId AND n.noDueApproval = 'Yes') < 
        (SELECT COUNT(*) FROM nodues n WHERE n.requestId = rr.requestId)";
    }
}

$query = "SELECT s.rollNo, s.name, s.course, s.batchSession, rr.requestId, s.securityAmount, rr.requestDate, rr.refundStatus, rr.refundDate, rr.refundDescription, rr.verifyDetails, rr.verifyReason,
          (SELECT COUNT(*) FROM nodues n WHERE n.requestId = rr.requestId AND n.noDueApproval = 'Yes') as countYes,
          (SELECT COUNT(*) FROM nodues n WHERE n.requestId = rr.requestId) as totalDepts,
          uc.filePath, uc.accHolderName, uc.bankName, uc.accountNo, uc.ifscCode
          FROM refundrequest rr
          JOIN student s ON rr.rollNo = s.rollNo
          LEFT JOIN uploadcheque uc ON s.rollNo = uc.rollNo";

if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " ORDER BY s.rollNo";

$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($paramTypes, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$pdf = new FPDF();
$pdf->SetTitle('Refund Report');
$pdf->AddPage(); // Set to landscape orientation

// Set font and column headers
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(15, 10, 'Roll No', 1, 0, 'C');
$pdf->Cell(30, 10, 'Name', 1, 0, 'C');
$pdf->Cell(25, 10, 'Course', 1, 0, 'C');
$pdf->Cell(30, 10, 'Account Holder', 1, 0, 'C'); 
$pdf->Cell(30, 10, 'Bank Name', 1, 0, 'C'); 
$pdf->Cell(30, 10, 'Account No', 1, 0, 'C');
$pdf->Cell(25, 10, 'IFSC Code', 1, 0, 'C');
$pdf->Cell(25, 10, 'Security Amt', 1, 1, 'C'); 

// Set font for table rows
$pdf->SetFont('Arial', '', 10);

while ($row = $result->fetch_assoc()) {
    $pdf->Cell(15, 10, $row['rollNo'], 1, 0, 'C'); 
    $pdf->Cell(30, 10, $row['name'], 1, 0, 'C');
    $pdf->Cell(25, 10, $row['course'], 1, 0, 'C');
    $pdf->Cell(30, 10, $row['accHolderName'], 1, 0, 'C'); 
    $pdf->Cell(30, 10, $row['bankName'], 1, 0, 'C'); 
    $pdf->Cell(30, 10, $row['accountNo'], 1, 0, 'C');
    $pdf->Cell(25, 10, $row['ifscCode'], 1, 0, 'C');
    $pdf->Cell(25, 10, $row['securityAmount'], 1, 1, 'C'); 
}


// Output PDF to browser
$pdf->Output('I', 'Refund_Report.pdf');
exit();
?>
