<?php
// ------------------------------------------------------------
// アプリ名 : 観光レビュー投稿アプリ（PHP）
// 概要     : 観光スポットのレビュー（名称・コメント・評価）を投稿・閲覧
// 技術     : PHP / PDO / SQLite
// URL      : "/"（投稿＋一覧）, "/review.php?id=<id>"（詳細表示）
// 実行     : php -S localhost:8000（開発用）
// 注意     : セッション管理とCSRF対策は簡易化（本番では強化推奨）
// ------------------------------------------------------------

session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'db_connect.php';

// ------------------------------
// テーブル作成（初回のみ）
// ------------------------------
$db->exec("
    CREATE TABLE IF NOT EXISTS reviews (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        spot_name VARCHAR(255) NOT NULL,
        comment TEXT NOT NULL,
        rating INT UNSIGNED NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");
echo "<p>テーブル作成チェック完了</p>";


// ------------------------------
// POST処理：レビュー投稿
// ------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $spot_name = $_POST['spot_name'] ?? '';
    $comment = $_POST['comment'] ?? '';
    $rating = $_POST['rating'] ?? '';

    if ($spot_name && $comment && is_numeric($rating)) {
        $stmt = $db->prepare("INSERT INTO reviews (spot_name, comment, rating, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$spot_name, $comment, $rating]);
        header("Location: index.php"); // PRGパターン
        exit;
    }
}

// ------------------------------
// GET処理：レビュー一覧取得
// ------------------------------
$reviews = $db->query("SELECT * FROM reviews")->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- ------------------------------ -->
<!-- HTML表示（index.html相当） -->
<!-- ------------------------------ -->
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>観光レビュー投稿アプリ</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container mt-5">
    <h1>観光レビュー投稿</h1>

<form method="POST" action="index.php">
  <!-- 観光地名（必須） -->
  <div class="mb-3">
    <label class="form-label">観光地名</label>
    <input type="text" name="spot_name" class="form-control" required>
  </div>

  <!-- コメント（必須） -->
  <div class="mb-3">
    <label class="form-label">コメント</label>
    <textarea name="comment" class="form-control" required></textarea>
  </div>

  <!-- 評価（1〜5の整数） -->
  <div class="mb-3">
    <label class="form-label">評価（1〜5）</label>
    <input type="number" name="rating" class="form-control" min="1" max="5" required>
  </div>

  <!-- 投稿ボタン -->
  <button type="submit" class="btn btn-primary">投稿する</button>
</form>   
    <h2>レビュー一覧</h2>
    <ul>
        <?php foreach ($reviews as $review): ?>
            <li>
                <a href="review.php?id=<?= $review['id'] ?>">
                    <?= htmlspecialchars($review['spot_name']) ?>（評価: <?= $review['rating'] ?>）
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
  </div>
</body>
</html>