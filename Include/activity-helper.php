<?php

function logActivity($pdo, $userID, $activity)
{
    try {

        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';

        $stmt = $pdo->prepare("
            INSERT INTO activity_logs
            (
                user_id,
                activity,
                ip_address,
                created_at
            )
            VALUES
            (?, ?, ?, NOW())
        ");

        $stmt->execute([
            $userID,
            $activity,
            $ipAddress
        ]);

    } catch (PDOException $e) {

        // Ignore logging errors

    }
}