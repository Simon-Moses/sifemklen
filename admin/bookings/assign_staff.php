<?php

require_once __DIR__ . '/../../includes/auth.php';

require_login('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin/bookings/index.php');
}

verify_csrf();

$bookingId = (int) ($_POST['booking_id'] ?? 0);
$staffId = (int) ($_POST['staff_id'] ?? 0);

$bookingStmt = $pdo->prepare(
    "SELECT id, booking_reference, status
     FROM bookings
     WHERE id = ?
       AND status <> 'Cancelled'
     LIMIT 1"
);
$bookingStmt->execute([$bookingId]);
$booking = $bookingStmt->fetch();

$staffStmt = $pdo->prepare(
    "SELECT id, full_name, status
     FROM staff
     WHERE id = ?
     LIMIT 1"
);
$staffStmt->execute([$staffId]);
$staff = $staffStmt->fetch();

if (!$booking || !$staff || $staff['status'] !== 'Active') {
    flash('error', 'Invalid booking or inactive staff member.');
    redirect('admin/bookings/index.php');
}

try {
    $pdo->beginTransaction();

    // Close any previous active assignment for this booking.
    $updateAssignment = $pdo->prepare(
        "UPDATE staff_assignments
         SET status = 'Cancelled'
         WHERE booking_id = ?
           AND status IN ('Assigned', 'Working')"
    );
    $updateAssignment->execute([$bookingId]);

    // Create the new assignment.
    $insertAssignment = $pdo->prepare(
        "INSERT INTO staff_assignments
            (booking_id, staff_id, assigned_by, status)
         VALUES (?, ?, ?, 'Assigned')"
    );
    $insertAssignment->execute([
        $bookingId,
        $staffId,
        $_SESSION['user_id']
    ]);

    // Move the booking into the Assigned stage.
    $updateBooking = $pdo->prepare(
        "UPDATE bookings
         SET status = 'Assigned', updated_at = NOW()
         WHERE id = ?"
    );
    $updateBooking->execute([$bookingId]);

    $pdo->commit();

    log_activity(
        'Staff assignment',
        'Assigned ' . $staff['full_name'] . ' to booking ' . $booking['booking_reference'] . '.'
    );

    flash('success', 'Staff assigned successfully.');
    redirect('admin/bookings/view.php?id=' . $bookingId);
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    flash('error', 'The staff assignment could not be completed.');
    redirect('admin/bookings/view.php?id=' . $bookingId);
}
