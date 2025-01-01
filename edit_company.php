<?php
session_start();
include('db.php');

if (isset($_POST['edit_company'])) {
    $id = intval($_POST['company_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $telephone = mysqli_real_escape_string($conn, $_POST['telephone']);
    $fax = mysqli_real_escape_string($conn, $_POST['fax']);

    $sql = "UPDATE company SET Name = ?, Address = ?, Email = ?, Telephone = ?, Fax = ? WHERE Id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, 'sssssi', $name, $address, $email, $telephone, $fax, $id);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['msg'] = "Company updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating company. Please try again.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['error'] = "Error preparing statement: " . mysqli_error($conn);
    }

    // Redirect back to the main page
    header("Location: view_company.php");
    exit();
}
