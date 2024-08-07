<?php
function registerUser($username, $email, $referralCode = null) {
    global $conn;

    // Menghasilkan kode rujukan unik
    $generatedReferralCode = substr(md5(uniqid(rand(), true)), 0, 8);

    // Menyiapkan statement
    $stmt = $conn->prepare("INSERT INTO user (username, email, referral_code, referred_by) VALUES (?, ?, ?, (SELECT id FROM user WHERE referral_code = ?))");
    $stmt->bind_param("ssss", $username, $email, $generatedReferralCode, $referralCode);
    
    if ($stmt->execute()) {
        $newUserId = $stmt->insert_id;

        // Menambahkan poin untuk pengguna baru
        $stmt = $conn->prepare("UPDATE user SET points = points + 50 WHERE id = ?");
        $stmt->bind_param("i", $newUserId);
        $stmt->execute();

        // Menambahkan poin untuk perujuk
        if ($referralCode) {
            $stmt = $conn->prepare("UPDATE user SET points = points + 50 WHERE referral_code = ?");
            $stmt->bind_param("s", $referralCode);
            $stmt->execute();

            // Mencatat rujukan
            $referrerId = $stmt->insert_id; // Mengambil ID perujuk
            $stmt = $conn->prepare("INSERT INTO penguna_baru (referrer_id, new_user, referral_code) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $referrerId, $newUserId, $referralCode);
            $stmt->execute();
        }

        return ["success" => true, "referral_code" => $generatedReferralCode];
    } else {
        return ["success" => false, "error" => $stmt->error];
    }
}
?>
