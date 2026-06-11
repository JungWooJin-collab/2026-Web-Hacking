<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='login_form.php';</script>";
    exit;
}

$post_id = $_GET['id'] ?? 0;

if (empty($post_id)) {
    die("<script>alert('잘못된 접근입니다.'); location.href='index.php';</script>");
}

$stmt = $pdo->prepare('SELECT p.*, u.username FROM posts p JOIN users u ON p.user_id = u.id WHERE p.id = ?');
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
    die("<script>alert('존재하지 않는 게시글입니다.'); location.href='index.php';</script>");
}

if ($_SESSION['user_id'] != $post['user_id'] && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) {
    die("<script>alert('수정 권한이 없습니다.'); history.back();</script>");
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>Edit Topic - NetAdmin Hub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="glass-container">
        <h2 class="title-gradient">> TRACEROUTE _ Edit Topic</h2>
        <form action="actions/update_post.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
            
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="content">Content</label>
                <textarea id="content" name="content" required><?= htmlspecialchars($post['content']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="attachment">New Attachment (Replaces existing)</label>
                <?php if ($post['file_name']): ?>
                    <div style="background: rgba(255,255,255,0.4); padding: 10px 15px; border-radius: 8px; margin-bottom: 10px; font-size: 0.9rem;">
                        Current File: <b><?= htmlspecialchars($post['file_name']) ?></b>
                        <input type="hidden" name="existing_file_name" value="<?= htmlspecialchars($post['file_name']) ?>">
                    </div>
                <?php endif; ?>
                <input type="file" id="attachment" name="attachment">
            </div>

            <div class="btn-group-right">
                <a href="view.php?id=<?= $post['id'] ?>" class="btn btn-cancel">Cancel</a>
                <button type="submit" class="btn">Update Route</button>
            </div>
        </form>
    </div>
</body>
</html>
