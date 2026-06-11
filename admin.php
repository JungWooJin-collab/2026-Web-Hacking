<?php
session_start();
require_once 'config/db.php';

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("<script>alert('접근 권한이 없습니다.'); location.href='index.php';</script>");
}

// Fetch all users
$stmt = $pdo->query("SELECT id, username, role, status, created_at FROM users ORDER BY id DESC");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>User Control - NetAdmin Hub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="glass-container" style="max-width: 1000px;">
        <div class="header-bar">
            <h2 class="title-gradient" style="margin: 0; border: none; padding: 0;">Admin_Dashboard<span>_</span></h2>
            <div>
                <a href="index.php" class="btn" style="padding: 0.5rem 1rem; font-size: 0.85rem;">[ Return to Hub ]</a>
            </div>
        </div>

        <h3 style="margin-bottom: 1rem;">User Management</h3>
        <table>
            <thead>
                <tr>
                    <th width="5%">ID</th>
                    <th width="30%">Username</th>
                    <th width="15%">Role</th>
                    <th width="15%">Status</th>
                    <th width="20%">Registered Date</th>
                    <th width="15%">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($users) > 0): ?>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td style="text-align: center; color: var(--text-muted); font-weight: 500;"><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td style="text-align: center;">
                            <?php if ($user['role'] === 'admin'): ?>
                                <span class="role-badge">ADMIN</span>
                            <?php else: ?>
                                <span style="color: var(--text-muted);">USER</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <?php if ($user['status'] === 'suspended'): ?>
                                <span style="color: #ff4d4d; font-weight: bold;">[ SUSPENDED ]</span>
                            <?php else: ?>
                                <span style="color: #4cd137;">[ ACTIVE ]</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;"><span style="color: var(--text-muted); font-size: 0.9rem;"><?= substr($user['created_at'], 0, 10) ?></span></td>
                        <td style="text-align: center;">
                            <?php if ($user['role'] !== 'admin'): ?>
                                <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                    <?php if ($user['status'] === 'active'): ?>
                                        <button onclick="if(confirm('이 사용자를 정지하시겠습니까?')) location.href='actions/admin_user_action.php?action=suspend&id=<?= $user['id'] ?>'" class="btn" style="background:#ff9f43; color:#fff; padding: 0.3rem 0.5rem; font-size: 0.8rem;">정지</button>
                                    <?php else: ?>
                                        <button onclick="if(confirm('이 사용자의 정지를 해제하시겠습니까?')) location.href='actions/admin_user_action.php?action=activate&id=<?= $user['id'] ?>'" class="btn btn-success" style="padding: 0.3rem 0.5rem; font-size: 0.8rem;">활성</button>
                                    <?php endif; ?>
                                    
                                    <button onclick="if(confirm('정말로 이 사용자를 삭제하시겠습니까? 관련된 모든 데이터가 삭제됩니다.')) location.href='actions/admin_user_action.php?action=delete&id=<?= $user['id'] ?>'" class="btn btn-danger" style="padding: 0.3rem 0.5rem; font-size: 0.8rem;">삭제</button>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align: center; color: var(--text-muted); padding: 3rem;">No users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>
</body>
</html>
