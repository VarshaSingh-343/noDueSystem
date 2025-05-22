<?php
include '../connect.php';

if (isset($_POST['requestId'])) {
    $requestId = $_POST['requestId'];

    // Query to fetch department-wise dues details for the given requestId
    $query = "SELECT s.rollNo, d.deptName, n.noDueApproval, n.noDueComment, n.approvalDate
              FROM nodues n 
              JOIN refundrequest rr ON rr.requestId = n.requestId 
              JOIN student s ON rr.rollNo = s.rollNo
              JOIN department d ON n.deptId = d.deptId
              WHERE n.requestId = ?";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Error preparing query: " . $conn->error);
    }

    $stmt->bind_param("s", $requestId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<h3 style = 'text-align:center;'>Roll No: " . htmlspecialchars($row['rollNo']) . "</h3>";
    
        echo "<table>
                <thead>
                    <tr>
                        <th>Department Name</th>
                        <th>No Due Approval</th>
                        <th>No Due Comment</th>
                        <th>Approval Date</th>
                    </tr>
                </thead>
                <tbody>";
    
        $result->data_seek(0);
    
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['deptName']) . "</td>
                    <td>" . ($row['noDueApproval'] === 'Yes' ? 'Cleared' : 'Not Cleared') . "</td>
                    <td>" . htmlspecialchars($row['noDueComment']) . "</td>
                    <td>" . htmlspecialchars($row['approvalDate']) . "</td>
                  </tr>";
        }
    
        echo "</tbody>
              </table>";
    } else {
        echo "<p>No dues details not found for this request.</p>";
    }
    

    $stmt->close();
    $conn->close();
}
?>
