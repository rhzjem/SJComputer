<?php
// profileController.php
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user = getUserById($_SESSION['user_id']);
$error = '';
$success = '';

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = 'Please fill in all password fields';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'New passwords do not match';
    } elseif (strlen($newPassword) < 6) {
        $error = 'Password must be at least 6 characters long';
    } elseif (!password_verify($currentPassword, $user['password'])) {
        $error = 'Current password is incorrect';
    } else {
        $result = updateUserPassword($_SESSION['user_id'], $newPassword);
        if ($result['success']) {
            $success = 'Password changed successfully!';
        } else {
            $error = $result['message'];
        }
    }
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $fullName = sanitizeInput($_POST['full_name']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);
    $address = sanitizeInput($_POST['address']);
    
    if (empty($fullName) || empty($email)) {
        $error = 'Full name and email are required';
    } else {
        $result = updateUserProfile($_SESSION['user_id'], $fullName, $email, $phone, $address);
        if ($result['success']) {
            $success = 'Profile updated successfully!';
            $user = getUserById($_SESSION['user_id']); // Refresh user data
        } else {
            $error = $result['message'];
        }
    }
}

// Include the view file
require 'views/profileView.php';
?>