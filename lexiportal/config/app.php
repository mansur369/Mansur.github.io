<?php
declare(strict_types=1);

session_start();

const APP_NAME = 'LexPortal';
const APP_ROOT = __DIR__ . '/..';
const DB_PATH = APP_ROOT . '/data/lexiportal.sqlite';

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    if (!is_dir(dirname(DB_PATH))) {
        mkdir(dirname(DB_PATH), 0775, true);
    }

    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON');

    initialize_database($pdo);
    return $pdo;
}

function initialize_database(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            role TEXT NOT NULL CHECK (role IN ('client', 'practitioner')),
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS admins (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            level TEXT NOT NULL DEFAULT 'editor',
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS research_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            type TEXT NOT NULL,
            title TEXT NOT NULL,
            court TEXT,
            citation TEXT,
            year INTEGER,
            subject TEXT,
            summary TEXT NOT NULL,
            verified INTEGER NOT NULL DEFAULT 1
        );

        CREATE TABLE IF NOT EXISTS resources (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            type TEXT NOT NULL,
            title TEXT NOT NULL,
            category TEXT NOT NULL,
            meta TEXT NOT NULL,
            body TEXT
        );

        CREATE TABLE IF NOT EXISTS lawyers (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            specialty TEXT NOT NULL,
            location TEXT NOT NULL,
            email TEXT NOT NULL,
            verified INTEGER NOT NULL DEFAULT 1
        );

        CREATE TABLE IF NOT EXISTS client_cases (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            client_id INTEGER NOT NULL,
            advocate TEXT NOT NULL,
            title TEXT NOT NULL,
            next_step TEXT NOT NULL,
            status TEXT NOT NULL,
            FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS documents (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            case_id INTEGER,
            original_name TEXT NOT NULL,
            stored_name TEXT,
            size_bytes INTEGER NOT NULL DEFAULT 0,
            uploaded_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (case_id) REFERENCES client_cases(id) ON DELETE SET NULL
        );

        CREATE TABLE IF NOT EXISTS messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            sender TEXT NOT NULL,
            body TEXT NOT NULL,
            is_read INTEGER NOT NULL DEFAULT 0,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS contact_requests (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL,
            subject TEXT NOT NULL,
            message TEXT NOT NULL,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS ai_questions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            question TEXT NOT NULL,
            response TEXT NOT NULL,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        );
    ");

    $count = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    if ($count > 0) {
        return;
    }

    $userStmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)');
    $userStmt->execute(['Sarah Nakimuli', 'client@demo.lexportal.ug', password_hash('demo1234', PASSWORD_DEFAULT), 'client']);
    $clientId = (int) $pdo->lastInsertId();
    $userStmt->execute(['Adv. James Okello', 'lawyer@demo.lexportal.ug', password_hash('demo1234', PASSWORD_DEFAULT), 'practitioner']);

    $adminStmt = $pdo->prepare('INSERT INTO admins (name, email, password_hash, level) VALUES (?, ?, ?, ?)');
    $adminStmt->execute(['Site Administrator', 'admin@lexportal.ug', password_hash('AdminDemo#2026', PASSWORD_DEFAULT), 'super_admin']);

    $research = [
        ['case', 'Attorney General v. Salvatori Abuki', 'Constitutional Court', 'Const. Petition No. 2 of 1997', 1997, 'constitutional', 'A landmark ruling on customary punishment and constitutional rights, examining the limits of traditional sanctions against the Bill of Rights.'],
        ['case', 'Uganda v. Kato & Another', 'High Court - Criminal Division', 'HCT-CR-SC-0114-2024', 2024, 'criminal', 'Sentencing guidelines applied in an aggravated robbery case, with discussion of mitigating factors and statutory minimums.'],
        ['case', 'Mabirizi v. Attorney General', 'Supreme Court', 'Const. Appeal No. 2 of 2018', 2019, 'constitutional', 'An appeal concerning amendments to presidential age-limit provisions in the Constitution.'],
        ['legislation', 'Contracts Act, 2010', 'Parliament of Uganda', 'Act No. 7 of 2010', 2010, 'contract', 'Codifies core contract rules including offer, acceptance, consideration, capacity, consent, and lawful object.'],
        ['legislation', 'Land Act, Cap 227', 'Parliament of Uganda', 'Cap 227', 1998, 'property', 'Recognizes and regulates customary, freehold, mailo, and leasehold land tenure in Uganda.'],
        ['legislation', 'Penal Code Act, Cap 120', 'Parliament of Uganda', 'Cap 120', 1950, 'criminal', 'Principal criminal statute covering offences and penalties, including later amendments.'],
    ];
    $stmt = $pdo->prepare('INSERT INTO research_items (type, title, court, citation, year, subject, summary) VALUES (?, ?, ?, ?, ?, ?, ?)');
    foreach ($research as $item) {
        $stmt->execute($item);
    }

    $resources = [
        ['Article', 'Understanding Mailo Land Tenure in Uganda', 'property', '8 min read - Property Law', 'A plain-language guide to Mailo tenure, registered interests, and common transaction risks.'],
        ['Video lecture', 'Elements of a Valid Contract under the Contracts Act, 2010', 'contract', '22 min - Contract Law', 'A concise lesson covering formation, enforceability, and remedies.'],
        ['Template', 'Tenancy Agreement Template (Residential)', 'property', 'DOCX - Property Law', 'Starter tenancy terms for residential leases.'],
        ['Notes', 'Case Brief: Mabirizi v. Attorney General (2019)', 'constitutional', '5 min read - Constitutional Law', 'Issue, holding, and significance of the age-limit appeal.'],
        ['Guide', 'What to do after arrest in Uganda', 'criminal', '6 min read - Criminal Law', 'Rights, bail basics, and practical next steps.'],
    ];
    $stmt = $pdo->prepare('INSERT INTO resources (type, title, category, meta, body) VALUES (?, ?, ?, ?, ?)');
    foreach ($resources as $item) {
        $stmt->execute($item);
    }

    $lawyers = [
        ['Adv. James Okello', 'Land and commercial litigation', 'Kampala', 'lawyer@demo.lexportal.ug'],
        ['Adv. Grace Achan', 'Family law and mediation', 'Gulu', 'grace.achan@example.ug'],
        ['Adv. Mariam Nsubuga', 'Contracts and corporate advisory', 'Entebbe', 'mariam.nsubuga@example.ug'],
    ];
    $stmt = $pdo->prepare('INSERT INTO lawyers (name, specialty, location, email) VALUES (?, ?, ?, ?)');
    foreach ($lawyers as $item) {
        $stmt->execute($item);
    }

    $cases = [
        ['Adv. James Okello', 'Nakimuli v. Kampala City Developers Ltd', 'Hearing - 14 Jul 2026', 'Active'],
        ['Adv. James Okello', 'Estate of late S. Nakimuli - Probate', 'Awaiting Letters of Administration', 'Pending'],
        ['Adv. Grace Achan', 'Tenancy dispute - Bugolobi Apartments', 'Settled, 2 Feb 2026', 'Closed'],
    ];
    $stmt = $pdo->prepare('INSERT INTO client_cases (client_id, advocate, title, next_step, status) VALUES (?, ?, ?, ?, ?)');
    foreach ($cases as $case) {
        $stmt->execute(array_merge([$clientId], $case));
    }

    $docs = [
        ['Plaint_Nakimuli_v_KCDL.pdf', 421888],
        ['Letters_of_Administration_Draft.docx', 90112],
        ['Tenancy_Agreement_Bugolobi.pdf', 220160],
    ];
    $stmt = $pdo->prepare('INSERT INTO documents (user_id, original_name, size_bytes) VALUES (?, ?, ?)');
    foreach ($docs as $doc) {
        $stmt->execute(array_merge([$clientId], $doc));
    }

    $msg = $pdo->prepare('INSERT INTO messages (user_id, sender, body, is_read) VALUES (?, ?, ?, ?)');
    $msg->execute([$clientId, 'Adv. James Okello', 'Please review the draft witness statement before the hearing.', 0]);
    $msg->execute([$clientId, 'LexPortal', 'Your upload was received and linked to your matter.', 0]);
}

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function current_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    $stmt = db()->prepare('SELECT id, name, email, role FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch() ?: null;
}

function require_user(?string $role = null): array
{
    $user = current_user();
    if (!$user || ($role && $user['role'] !== $role)) {
        $_SESSION['flash'] = 'Please sign in to continue.';
        header('Location: index.php?login=1');
        exit;
    }
    return $user;
}

function current_admin(): ?array
{
    if (empty($_SESSION['admin_id'])) {
        return null;
    }
    $stmt = db()->prepare('SELECT id, name, email, level FROM admins WHERE id = ?');
    $stmt->execute([$_SESSION['admin_id']]);
    return $stmt->fetch() ?: null;
}

function require_admin(): array
{
    $admin = current_admin();
    if (!$admin) {
        flash('Please sign in with an admin account.');
        header('Location: admin-login.php');
        exit;
    }
    return $admin;
}

function flash(?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'] = $message;
        return null;
    }
    $existing = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $existing;
}

function redirect_to(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function active(string $page, string $current): string
{
    return $page === $current ? ' active' : '';
}
