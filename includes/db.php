<?php

declare(strict_types=1);

function getDatabaseConfig(): array
{
    return [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => getenv('DB_PORT') ?: '3306',
        'database' => getenv('DB_DATABASE') ?: 'anand_kustoms',
        'username' => getenv('DB_USERNAME') ?: 'root',
        'password' => getenv('DB_PASSWORD') ?: '',
        'charset' => 'utf8mb4',
    ];
}

function getDatabaseConnection(): ?PDO
{
    static $connection = null;
    static $connectionAttempted = false;

    if ($connectionAttempted) {
        return $connection;
    }

    $connectionAttempted = true;
    $config = getDatabaseConfig();
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        $config['host'],
        $config['port'],
        $config['database'],
        $config['charset']
    );

    try {
        $connection = new PDO(
            $dsn,
            $config['username'],
            $config['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );

        return $connection;
    } catch (PDOException $exception) {
        error_log('MySQL connection failed: ' . $exception->getMessage());

        return null;
    }
}

function getDemoServices(): array
{
    return [
        [
            'badge' => 'Most popular',
            'title' => 'Full body wraps',
            'description' => 'Design-driven wraps that protect paint while making your car stand out on the road.',
            'details' => ['Premium vinyl', 'Paint-safe prep', 'Finish protection'],
        ],
        [
            'badge' => 'Signature',
            'title' => 'Custom interiors',
            'description' => 'Luxury trim, stitched finishes, and tailored upgrades designed around your lifestyle.',
            'details' => ['Leather upgrades', 'Ambient lighting', 'Trim personalization'],
        ],
        [
            'badge' => 'Precision',
            'title' => 'Performance tuning',
            'description' => 'Drive better with carefully tuned setups focused on power, balance, and reliability.',
            'details' => ['ECU remaps', 'Exhaust tuning', 'Road testing'],
        ],
    ];
}

function getDemoProjects(): array
{
    return [
        [
            'title' => 'Urban Night Series',
            'category' => 'Wrap & paint',
            'description' => 'A satin-black build with smoked accents and complete cabin detailing.',
            'year' => '2025',
        ],
        [
            'title' => 'Trackline GT',
            'category' => 'Performance',
            'description' => 'A sharper suspension setup and signature tuning package for daily performance driving.',
            'year' => '2024',
        ],
        [
            'title' => 'Executive Luxe',
            'category' => 'Interior',
            'description' => 'Custom leather patterns, ambient lighting, and premium trims for refined comfort.',
            'year' => '2024',
        ],
    ];
}

function getProjectImageUrl(string $category): string
{
    if (stripos($category, 'performance') !== false) {
        return 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=900&q=80';
    }

    if (stripos($category, 'interior') !== false) {
        return 'https://images.unsplash.com/photo-1504215680853-026ed2a45def?auto=format&fit=crop&w=900&q=80';
    }

    return 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=900&q=80';
}

function getDemoTestimonials(): array
{
    return [
        [
            'name' => 'Rohan V.',
            'quote' => 'Their finish quality and attention to detail made my car feel completely new.',
        ],
        [
            'name' => 'Karan M.',
            'quote' => 'The wrap looked flawless and the whole process felt premium from start to finish.',
        ],
        [
            'name' => 'Sahil P.',
            'quote' => 'Professional, transparent, and obsessed with precision — exactly what you want.',
        ],
    ];
}

function getServices(): array
{
    $pdo = getDatabaseConnection();

    if ($pdo === null) {
        return getDemoServices();
    }

    try {
        $statement = $pdo->query('SELECT badge, title, description, details FROM services ORDER BY sort_order ASC, id ASC');
        $services = $statement->fetchAll();

        if ($services === []) {
            return getDemoServices();
        }

        return array_map(
            static function (array $service): array {
                $details = json_decode((string) $service['details'], true);

                return [
                    'badge' => $service['badge'],
                    'title' => $service['title'],
                    'description' => $service['description'],
                    'details' => is_array($details) ? $details : [],
                ];
            },
            $services
        );
    } catch (PDOException $exception) {
        error_log('Could not load services: ' . $exception->getMessage());

        return getDemoServices();
    }
}

function getProjects(): array
{
    $pdo = getDatabaseConnection();

    if ($pdo === null) {
        return getDemoProjects();
    }

    try {
        $statement = $pdo->query('SELECT title, category, description, year FROM projects ORDER BY year DESC, id ASC LIMIT 6');
        $projects = $statement->fetchAll();

        return $projects !== [] ? $projects : getDemoProjects();
    } catch (PDOException $exception) {
        error_log('Could not load projects: ' . $exception->getMessage());

        return getDemoProjects();
    }
}

function getTestimonials(): array
{
    return getDemoTestimonials();
}

function saveContactMessage(array $payload): bool
{
    $pdo = getDatabaseConnection();

    if ($pdo === null) {
        return false;
    }

    try {
        $statement = $pdo->prepare(
            'INSERT INTO contact_messages (name, email, phone, vehicle, message, created_at)
             VALUES (:name, :email, :phone, :vehicle, :message, NOW())'
        );

        return $statement->execute([
            ':name' => trim((string) ($payload['name'] ?? '')),
            ':email' => trim((string) ($payload['email'] ?? '')),
            ':phone' => trim((string) ($payload['phone'] ?? '')),
            ':vehicle' => trim((string) ($payload['vehicle'] ?? '')),
            ':message' => trim((string) ($payload['message'] ?? '')),
        ]);
    } catch (PDOException $exception) {
        error_log('Could not save contact message: ' . $exception->getMessage());

        return false;
    }
}
