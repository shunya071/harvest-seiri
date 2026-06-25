<?php
/**
 * テスト環境用 send.php（実送信なし）
 *
 * /test/ ディレクトリに "send.php" としてアップする。
 * 本番の send.php（PHPMailer版）とは別ファイルなので、
 * SMTPパスワードを本番 send.php から分離したまま運用できる。
 *
 * 動作:
 *   - 必須チェックのみ行い、OK なら ?sent=1 へリダイレクト
 *   - メールは一切送信しない（SMTP 接続なし）
 */

mb_language('Japanese');
mb_internal_encoding('UTF-8');

// POST 以外からの直接アクセスを弾く
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contact.html');
    exit;
}

$name     = trim($_POST['name']     ?? '');
$furigana = trim($_POST['furigana'] ?? '');
$tel      = trim($_POST['tel']      ?? '');
$email    = trim($_POST['email']    ?? '');
$pref       = trim($_POST['pref']       ?? '');
$pref_other = trim($_POST['pref_other'] ?? '');
$message  = trim($_POST['message']  ?? '');

// 種別（複数選択 = 配列）
$types = $_POST['type'] ?? [];
if (!is_array($types)) { $types = [$types]; }
$types = array_filter(array_map('trim', $types));
$type  = implode('、', $types);

// 必須チェック（本番 send.php と同一ルール）
if (!$name || !$furigana || !$tel || !$email || !$pref || !$type || !$message || empty($_POST['privacy'])) {
    header('Location: contact.html?error=1');
    exit;
}

// メール形式チェック
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: contact.html?error=1');
    exit;
}

// 「その他」選択時は地域名を必須にする（本番 send.php と同一ルール）
if ($pref === 'その他' && $pref_other === '') {
    header('Location: contact.html?error=1');
    exit;
}

// テスト環境: 実送信せず完了画面へ
header('Location: contact.html?sent=1');
exit;
