<?php
// ------------------------------------------------------------
// review.php：レビュー詳細表示ページ
// ------------------------------------------------------------
session_start();

// DB接続
$db = new PDO('mysql:host=localhost;dbname=tourism_review;charset=utf8', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ID取得とレビュー検索
$review_id = $_GET['id'] ?? null;
if (!$review_id || !is_numeric($review_id)) {
    http_response_code(400);
    echo "不正なリクエストです。";
    exit;
}

$stmt = $db->prepare("SELECT * FROM reviews WHERE id = ?");
$stmt->execute([$review_id]);
$review = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$review) {
    http_response_code(404);
    echo "レビューが見つかりません。";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($review['spot_name']) ?>のレビュー詳細</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container mt-5">
    <div class="card">
      <div class="card-body">
        <h2 class="card-title"><?= htmlspecialchars($review['spot_name']) ?></h2>
        <p class="card-text"><?= htmlspecialchars($review['comment']) ?></p>

        <p class="card-text">
          評価:
          <?php for ($i = 0; $i < $review['rating']; $i++): ?>
            <span class="text-warning">★</span>
          <?php endfor; ?>
          <?php for ($i = 0; $i < 5 - $review['rating']; $i++): ?>
            <span class="text-muted">☆</span>
          <?php endfor; ?>
        </p>

        <?php if (!empty($review['created_at'])): ?>
            <p class="text-muted">
            投稿日: <?= date('Y年m月d日 H:i', strtotime($review['created_at'])) ?>
            </p>
        <?php else: ?>
            <p class="text-muted">投稿日: 不明</p>
        <?php endif; ?>
        <a href="index.php" class="btn btn-secondary mt-3">← 一覧に戻る</a>
      </div>
    </div>
  </div>
</body>
</html>