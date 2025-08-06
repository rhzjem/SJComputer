<?php
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validateCSRFToken($_POST['csrf_token'])) {
        die('Invalid CSRF token');
    }

    // Get form data
    $serviceName = sanitizeInput($_POST['serviceName']);
    $customerName = sanitizeInput($_POST['customerName']);
    $customerEmail = isset($_POST['customerEmail']) ? sanitizeInput($_POST['customerEmail']) : '';
    $contactNumber = sanitizeInput($_POST['contactNumber']);
    $preferredDate = sanitizeInput($_POST['preferredDate']);
    $problemDescription = sanitizeInput($_POST['problemDescription']);
    $userId = isLoggedIn() ? $_SESSION['user_id'] : null;
    $bookingDate = date('Y-m-d H:i:s');
    $status = 'Pending';

    try {
        $conn = getDBConnection();
        
        // Insert booking into database
        $stmt = $conn->prepare("INSERT INTO service_bookings 
                              (user_id, service_name, customer_name, customer_email, contact_number, preferred_date, problem_description, booking_date, status) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $success = $stmt->execute([
            $userId,
            $serviceName,
            $customerName,
            $customerEmail,
            $contactNumber,
            $preferredDate,
            $problemDescription,
            $bookingDate,
            $status
        ]);

        if ($success) {
            // Redirect to confirmation page
            header('Location: bookingConfirmation.php');
            exit();
        } else {
            throw new Exception("Failed to save booking");
        }
    } catch (Exception $e) {
        // Log error and redirect back with error message
        error_log("Booking error: " . $e->getMessage());
        $_SESSION['booking_error'] = "Failed to process booking. Please try again.";
        header('Location: services.php');
        exit();
    }
}

// If not a POST request or something went wrong, redirect back
header('Location: services.php');
exit();
?>