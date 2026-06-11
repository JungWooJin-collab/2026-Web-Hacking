<?php
session_start();
require_once 'config/db.php';

$page = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$stmt = $pdo->query('SELECT COUNT(*) FROM posts');
$total_posts = $stmt->fetchColumn();
$total_pages = ceil($total_posts / $limit);

$stmt = $pdo->prepare('SELECT p.*, u.username FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.id DESC LIMIT :limit OFFSET :offset');
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>NetAdmin Hub - Network Community</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="glass-container">
        <div class="header-bar">
            <h2 class="title-gradient" style="margin: 0; border: none; padding: 0;">NetAdmin Hub<span>_</span></h2>
            <div>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="user-info">
                        <span>Welcome, <b>ENG_<?= htmlspecialchars($_SESSION['username']) ?></b></span>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <a href="admin.php" class="btn btn-warning" style="padding: 0.2rem 0.5rem; font-size: 0.75rem; background: #ff9f43; color: white;">[ ADMIN MENU ]</a>
                            <span class="role-badge">ADMIN</span>
                        <?php endif; ?>
                        <a href="actions/logout.php" class="btn btn-danger" style="padding: 0.5rem 1rem; font-size: 0.85rem;">로그아웃</a>
                    </div>
                <?php else: ?>
                    <a href="login_form.php" class="btn" style="padding: 0.5rem 1rem; font-size: 0.85rem;">Authenticate // Login</a>
                <?php endif; ?>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="8%">ID</th>
                    <th width="50%">Topic</th>
                    <th width="15%">Engineer</th>
                    <th width="15%">Timestamp</th>
                    <th width="12%">Packet</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($posts) > 0): ?>
                    <?php foreach ($posts as $post): ?>
                    <tr onclick="location.href='view.php?id=<?= $post['id'] ?>'">
                        <td style="text-align: center; color: var(--text-muted); font-weight: 500;"><?= $post['id'] ?></td>
                        <td>
                            <a href="view.php?id=<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a>
                        </td>
                        <td><?= htmlspecialchars($post['username']) ?></td>
                        <td><span style="color: var(--text-muted); font-size: 0.9rem;"><?= substr($post['created_at'], 0, 10) ?></span></td>
                        <td style="text-align: center;"><?= $post['file_name'] ? '<span style="font-size: 1.2rem;">📎</span>' : '' ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align: center; color: var(--text-muted); padding: 3rem;">No active routes. Initialize the first topic!</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div style="text-align: right; margin-top: 1.5rem;">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="write.php" class="btn btn-success">> Initialize Topic</a>
            <?php endif; ?>
        </div>

        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
