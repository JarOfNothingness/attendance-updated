<?php
// Connect to the database
include("../LoginRegisterAuthentication/connection.php");
include("../crud/header.php");

// Get the parameters from the URL or POST
$section = $_GET['section'] ?? '';
$subject_id = $_GET['subject_id'] ?? '';
$month = $_GET['month'] ?? date('Y-m');

// Check if the required parameters are available
if (empty($section) || empty($subject_id) || empty($month)) {
    echo "<p style='color: red;'>Invalid section, subject, or month.</p>";
    exit;
}

// Query to fetch attendance records for the selected section, subject, and month
$query = "SELECT s.learners_name, a.* 
          FROM attendance a
          JOIN students s ON a.student_id = s.id
          WHERE a.section = ? AND a.subject_id = ? AND a.month = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("sis", $section, $subject_id, $month);
$stmt->execute();
$result = $stmt->get_result();

// Check if attendance records exist
if ($result->num_rows == 0) {
    echo "<p style='color: red;'>No attendance records found for the selected section, subject, and month.</p>";
} else {
    // Display attendance records
    echo "<h2>Class Attendance for Section: " . htmlspecialchars($section) . " - Subject: " . htmlspecialchars($subject_id) . " - Month: " . htmlspecialchars($month) . "</h2>";
    echo "<table border='1' cellspacing='0' cellpadding='5' style='border-collapse: collapse; width: 100%;'>";
    echo "<thead><tr><th>Student Name</th>";

    // Display the days of the month (1-31)
    for ($i = 1; $i <= 31; $i++) {
        echo "<th>" . str_pad($i, 2, '0', STR_PAD_LEFT) . "</th>";
    }
    echo "<th>Total Present</th><th>Total Absent</th><th>Total Late</th></tr></thead><tbody>";

    // Loop through attendance records
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['learners_name']) . "</td>";

        // Display attendance for each day of the month
        for ($i = 1; $i <= 31; $i++) {
            $day = "day_" . str_pad($i, 2, '0', STR_PAD_LEFT);
            echo "<td>" . htmlspecialchars($row[$day] ?? '-') . "</td>";  // Display '-' if there's no data
        }

        // Display total columns
        echo "<td>" . htmlspecialchars($row['total_present']) . "</td>";
        echo "<td>" . htmlspecialchars($row['total_absent']) . "</td>";
        echo "<td>" . htmlspecialchars($row['total_late']) . "</td>";
        echo "</tr>";
    }

    echo "</tbody></table>";
}

// Back to attendance setup page
echo "<a href='Attendance.php'>Back to Attendance Setup</a>";

include("../crud/footer.php");
?>
