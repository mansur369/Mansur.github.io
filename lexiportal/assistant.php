<?php
require __DIR__ . '/config/app.php';
$page = 'practice';
$title = 'AI Legal Assistant';
$user = current_user();

function ai_reply(string $question): array
{
    $patterns = [
        '/defil(e|ement)/i' => ['Under Ugandan law, defilement is governed primarily by the Penal Code Act, Cap 120, as amended. The offence concerns unlawful sexual intercourse with a person under 18 years of age.', ['Simple defilement concerns a sexual act with a person under 18.', 'Consent of the minor is not a defense.', 'Aggravated defilement involves factors such as the victim being under 14 or an offender in a position of authority.'], ['Penal Code Act, Cap 120', 'Penal Code (Amendment) Act, 2007']],
        '/contract|breach/i' => ['Contract law in Uganda is principally governed by the Contracts Act, 2010, which codifies common-law contract principles.', ['Offer and acceptance.', 'Consideration.', 'Capacity, free consent, lawful object, and certainty of terms.'], ['Contracts Act, 2010']],
        '/land|property|mailo/i' => ['Land matters in Uganda are governed by Article 237 of the Constitution and the Land Act, Cap 227.', ['Customary tenure.', 'Freehold tenure.', 'Mailo tenure.', 'Leasehold tenure.'], ['Constitution of Uganda, 1995, Article 237', 'Land Act, Cap 227']],
        '/divorce|marriage|matrimonial/i' => ['Marriage and divorce in Uganda depend on the type of marriage and the applicable statute.', ['Grounds can include adultery, cruelty, and desertion under the Divorce Act.', 'Customary and religious marriages have distinct registration and dissolution rules.', 'Matrimonial property questions depend heavily on contribution and evidence.'], ['Divorce Act, Cap 249', 'Marriage Act, Cap 251']],
    ];
    foreach ($patterns as $regex => $reply) {
        if (preg_match($regex, $question)) {
            return $reply;
        }
    }
    return ['I can help with that. Start by identifying the governing statute, checking amendments, then reading relevant case law and procedure rules.', ['Confirm the jurisdiction and court level.', 'Check limitation periods and filing requirements.', 'Verify against primary sources before acting.'], ['LexPortal Research Center', 'Judiciary of Uganda case records']];
}

$question = trim($_POST['question'] ?? $_GET['prompt'] ?? '');
$answer = null;
if ($question !== '') {
    $answer = ai_reply($question);
    $stmt = db()->prepare('INSERT INTO ai_questions (user_id, question, response) VALUES (?, ?, ?)');
    $stmt->execute([$user['id'] ?? null, $question, $answer[0]]);
}
$historyStmt = $user
    ? db()->prepare('SELECT question, response, created_at FROM ai_questions WHERE user_id = ? OR user_id IS NULL ORDER BY id DESC LIMIT 5')
    : db()->prepare('SELECT question, response, created_at FROM ai_questions WHERE user_id IS NULL ORDER BY id DESC LIMIT 5');
if ($user) {
    $historyStmt->execute([$user['id']]);
} else {
    $historyStmt->execute();
}
$history = $historyStmt->fetchAll();
include __DIR__ . '/templates/header.php';
?>
<section class="section section-alt">
  <div class="container">
    <div class="section-head left"><p class="eyebrow">AI Legal Assistant</p><h1>Ask LexAI, grounded in Ugandan law</h1><p>Deterministic demo answers are saved to SQLite so the prototype has real state.</p></div>
    <div class="panel">
      <div class="chat-window">
        <div class="chat-msg ai"><div class="chat-avatar ai">AI</div><div class="chat-bubble"><p>Ask about defilement, contract breach, land tenure, divorce, or another legal research starting point.</p></div></div>
        <?php if ($question && $answer): ?>
          <div class="chat-msg user"><div class="chat-bubble"><p><?= e($question) ?></p></div><div class="chat-avatar user-av">YOU</div></div>
          <div class="chat-msg ai"><div class="chat-avatar ai">AI</div><div class="chat-bubble"><p><?= e($answer[0]) ?></p><ul><?php foreach ($answer[1] as $point): ?><li><?= e($point) ?></li><?php endforeach; ?></ul><div class="source-pills"><?php foreach ($answer[2] as $source): ?><span class="source-pill"><?= e($source) ?></span><?php endforeach; ?></div><p class="form-hint">This is general legal information, not legal advice.</p></div></div>
        <?php endif; ?>
      </div>
      <form class="chat-form" method="post">
        <textarea name="question" placeholder="Type your legal research question..." required><?= e($question) ?></textarea>
        <button class="btn btn-primary">Send</button>
      </form>
      <div class="chip-row" style="margin-top:12px"><a class="chip" href="assistant.php?prompt=What are the ingredients of defilement under Ugandan law?">Defilement</a><a class="chip" href="assistant.php?prompt=Explain a breach of contract claim in Uganda">Contract breach</a><a class="chip" href="assistant.php?prompt=Summarize land tenure systems in Uganda">Land tenure</a></div>
    </div>
    <div class="section-head left" style="margin-top:24px"><h2>Recent questions</h2></div>
    <div class="grid-3"><?php foreach ($history as $row): ?><article class="dash-card"><h3><?= e($row['question']) ?></h3><p><?= e($row['response']) ?></p><div class="resource-meta"><?= e($row['created_at']) ?></div></article><?php endforeach; ?></div>
  </div>
</section>
<?php include __DIR__ . '/templates/footer.php'; ?>
