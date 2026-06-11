<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    die("<script>alert('권한이 없습니다. 로그인 해주세요'); location.href='login_form.php';</script>");
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
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($post['title']) ?> - NetAdmin Hub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="glass-container">
        <div class="view-title"><?= htmlspecialchars($post['title']) ?></div>
        
        <div class="view-info">
            <span>Engineer: <strong><?= htmlspecialchars($post['username']) ?></strong></span>
            <span>Timestamp: <?= $post['created_at'] ?> <?= $post['updated_at'] !== $post['created_at'] ? '[UPDATED]' : '' ?></span>
        </div>
        
        <div class="view-content"><?= htmlspecialchars($post['content']) ?></div>

        <?php if ($post['file_name']): ?>
        <div class="attachment-box">
            <span>📎 Packet Data:</span>
            <a href="actions/download.php?id=<?= $post['id'] ?>"><?= htmlspecialchars($post['file_name']) ?></a>
        </div>
        <?php endif; ?>

        <div class="btn-group-right">
            <a href="index.php" class="btn btn-cancel">List Items</a>
            
            <?php 
                if (isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $post['user_id'] || (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'))): 
            ?>
                <a href="edit.php?id=<?= $post['id'] ?>" class="btn">Edit Post</a>
                <button type="button" class="btn btn-danger" onclick="deletePost(<?= $post['id'] ?>)">Delete</button>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function deletePost(id) {
            if (confirm("정말로 삭제하시겠습니까? 삭제된 데이터는 복구할 수 없습니다.")) {
                location.href = 'actions/delete_post.php?id=' + id;
            }
        }
    </script>
</body>
</html>
