<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require __DIR__ . '/includes/db.php';

$formMessage = '';
$formStatus = '';
$formValues = [
    'name' => '',
    'email' => '',
    'phone' => '',
    'vehicle' => '',
    'message' => '',
];
$csrfToken = $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && ($_POST['form_action'] ?? '') === 'contact') {
    foreach (array_keys($formValues) as $field) {
        $submittedValue = $_POST[$field] ?? '';
        $formValues[$field] = is_string($submittedValue) ? trim($submittedValue) : '';
    }

    $submittedToken = $_POST['csrf_token'] ?? '';

    if (! is_string($submittedToken) || ! hash_equals($csrfToken, $submittedToken)) {
        $formStatus = 'error';
        $formMessage = 'Your form session expired. Please reload the page and try again.';
    } elseif (($_POST['website'] ?? '') !== '') {
        $formStatus = 'error';
        $formMessage = 'We could not process this request. Please try again.';
    } elseif (
        $formValues['name'] === ''
        || ! filter_var($formValues['email'], FILTER_VALIDATE_EMAIL)
        || $formValues['message'] === ''
        || mb_strlen($formValues['name'], 'UTF-8') > 120
        || mb_strlen($formValues['email'], 'UTF-8') > 150
        || mb_strlen($formValues['phone'], 'UTF-8') > 40
        || mb_strlen($formValues['vehicle'], 'UTF-8') > 120
        || mb_strlen($formValues['message'], 'UTF-8') > 1500
    ) {
        $formStatus = 'error';
        $formMessage = 'Check your details: name, a valid email, and project details are required. Please keep each field within its length limit.';
    } else {
        $saved = saveContactMessage($formValues);

        if ($saved) {
            $formStatus = 'success';
            $formMessage = 'Thank you. Your consultation request has been received and our team will contact you shortly.';
            $formValues = array_fill_keys(array_keys($formValues), '');
        } else {
            $formStatus = 'error';
            $formMessage = 'We could not save your request right now. Please email us directly at hello@anandkustoms.com.';
        }
    }
}

$services = getServices();
$projects = getProjects();
$testimonials = getTestimonials();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anand Kustoms | Premium Auto Styling</title>
    <meta name="description" content="Anand Kustoms builds premium vehicle wraps, custom interiors, and performance upgrades for drivers who want standout style and precision workmanship.">
    <meta name="theme-color" content="#0d1117">
    <meta property="og:title" content="Anand Kustoms | Premium Auto Styling">
    <meta property="og:description" content="Premium wraps, custom interiors, and performance upgrades, crafted around your vision.">
    <meta property="og:type" content="website">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <a class="skip-link" href="#main-content">Skip to content</a>
    <header class="topbar">
        <div class="container navbar">
            <a class="brand" href="#home" aria-label="Anand Kustoms home">
                <span class="brand-mark">AK</span>
                <span>Anand Kustoms</span>
            </a>

            <nav class="nav-links" id="main-navigation" aria-label="Main navigation">
                <a href="#about">About</a>
                <a href="#services">Services</a>
                <a href="#projects">Projects</a>
                <a href="#reviews">Reviews</a>
                <a href="#contact" class="nav-cta">Book a consultation</a>
            </nav>

            <button class="menu-toggle" type="button" aria-label="Open navigation menu" aria-controls="main-navigation" aria-expanded="false">☰</button>
        </div>
    </header>

    <main id="main-content">
        <section class="hero" id="home">
            <div class="container hero-grid">
                <div class="reveal">
                    <span class="eyebrow">Luxury auto styling</span>
                    <h1>Turn your drive into a statement.</h1>
                    <p class="lead">
                        From premium wraps and interior upgrades to performance tuning, we shape every detail around your vision — with craftsmanship, finish quality, and a service experience that feels personal.
                    </p>

                    <div class="hero-actions">
                        <a class="primary-btn" href="#contact">Start your project</a>
                        <a class="secondary-btn" href="#services">Explore services</a>
                    </div>

                    <div class="hero-metrics">
                        <div class="metric">
                            <strong>8+</strong>
                            <span>Years experience</span>
                        </div>
                        <div class="metric">
                            <strong>350+</strong>
                            <span>Custom builds</span>
                        </div>
                        <div class="metric">
                            <strong>4.9/5</strong>
                            <span>Client rating</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card reveal">
                    <div class="car-visual" aria-label="Luxury vehicle showcase"></div>
                    <div class="feature-badge">Now booking 2026 projects</div>
                    <div class="card-panel">
                        <small>Featured build</small>
                        <strong>Urban Night Series</strong>
                        <p>Full vehicle wrap with gloss black finish and performance-focused detailing.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="about">
            <div class="container">
                <div class="section-head reveal">
                    <span class="kicker">Why clients choose us</span>
                    <h2>Performance, style, and finish that feels tailored.</h2>
                    <p class="section-lead">Every project is built around precision work, premium material choices, and clear guidance from the first idea to the final polish.</p>
                </div>
            </div>
        </section>

        <section id="services">
            <div class="container">
                <div class="section-head reveal">
                    <span class="kicker">What we do</span>
                    <h2>Signature services for standout vehicles.</h2>
                </div>

                <div class="services-grid">
                    <?php foreach ($services as $service): ?>
                        <article class="service-card reveal">
                            <span class="label"><?= htmlspecialchars((string) $service['badge'], ENT_QUOTES, 'UTF-8') ?></span>
                            <h3><?= htmlspecialchars((string) $service['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <p><?= htmlspecialchars((string) $service['description'], ENT_QUOTES, 'UTF-8') ?></p>
                            <ul class="service-list">
                                <?php foreach ($service['details'] as $detail): ?>
                                    <li><?= htmlspecialchars((string) $detail, ENT_QUOTES, 'UTF-8') ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section id="projects">
            <div class="container">
                <div class="section-head reveal">
                    <span class="kicker">Recent work</span>
                    <h2>Crafted builds with unmistakable presence.</h2>
                </div>

                <div class="projects-grid">
                    <?php foreach ($projects as $project): ?>
                        <article class="project-card reveal">
                            <img class="project-image" src="<?= htmlspecialchars(getProjectImageUrl((string) $project['category']), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars((string) $project['title'] . ' ' . $project['category'] . ' project', ENT_QUOTES, 'UTF-8') ?>" loading="lazy" decoding="async">
                            <span class="meta"><?= htmlspecialchars((string) $project['category'], ENT_QUOTES, 'UTF-8') ?></span>
                            <h3><?= htmlspecialchars((string) $project['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <p><?= htmlspecialchars((string) $project['description'], ENT_QUOTES, 'UTF-8') ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section id="reviews">
            <div class="container">
                <div class="section-head reveal">
                    <span class="kicker">Client feedback</span>
                    <h2>Trusted by enthusiasts who expect more.</h2>
                </div>

                <div class="testimonials-grid">
                    <?php foreach ($testimonials as $testimonial): ?>
                        <article class="testimonial-card reveal">
                            <div class="quote-mark">“</div>
                            <p><?= htmlspecialchars((string) $testimonial['quote'], ENT_QUOTES, 'UTF-8') ?></p>
                            <div class="person">
                                <div class="avatar"><?= htmlspecialchars(substr((string) $testimonial['name'], 0, 1), ENT_QUOTES, 'UTF-8') ?></div>
                                <strong><?= htmlspecialchars((string) $testimonial['name'], ENT_QUOTES, 'UTF-8') ?></strong>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section id="contact">
            <div class="container contact-grid">
                <aside class="info-panel reveal">
                    <span class="kicker">Let’s build yours</span>
                    <h2>Schedule your consultation.</h2>
                    <p class="section-lead">Tell us about your vehicle, goals, and timeline. We’ll recommend the right set of upgrades and a premium finish plan.</p>

                    <div class="contact-points">
                        <div class="contact-point">
                            <div class="contact-icon">✦</div>
                            <div>
                                <strong>Visit the studio</strong>
                                <div>42 Auto Lane, Andheri West, Mumbai</div>
                            </div>
                        </div>
                        <div class="contact-point">
                            <div class="contact-icon">☎</div>
                            <div>
                                <strong>Call us</strong>
                                <div>+91 98765 43210</div>
                            </div>
                        </div>
                        <div class="contact-point">
                            <div class="contact-icon">@</div>
                            <div>
                                <strong>Email</strong>
                                <div>hello@anandkustoms.com</div>
                            </div>
                        </div>
                    </div>
                </aside>

                <div class="contact-panel reveal">
                    <form method="POST" action="#contact">
                        <input type="hidden" name="form_action" value="contact">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                        <div class="form-trap" aria-hidden="true">
                            <label for="website">Leave this field empty</label>
                            <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
                        </div>
                        <div class="form-grid">
                            <div class="field">
                                <label for="name">Full name</label>
                                <input id="name" name="name" type="text" placeholder="Your name" maxlength="120" autocomplete="name" value="<?= htmlspecialchars($formValues['name'], ENT_QUOTES, 'UTF-8') ?>" required>
                            </div>
                            <div class="field">
                                <label for="email">Email address</label>
                                <input id="email" name="email" type="email" placeholder="you@example.com" maxlength="150" autocomplete="email" value="<?= htmlspecialchars($formValues['email'], ENT_QUOTES, 'UTF-8') ?>" required>
                            </div>
                            <div class="field">
                                <label for="phone">Phone</label>
                                <input id="phone" name="phone" type="tel" placeholder="+91 98..." maxlength="40" autocomplete="tel" value="<?= htmlspecialchars($formValues['phone'], ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                            <div class="field">
                                <label for="vehicle">Vehicle</label>
                                <input id="vehicle" name="vehicle" type="text" placeholder="Model / year" maxlength="120" value="<?= htmlspecialchars($formValues['vehicle'], ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                            <div class="field full">
                                <label for="message">Project details</label>
                                <textarea id="message" name="message" maxlength="1500" placeholder="Tell us about your build goals, styling preferences, and timeline." required><?= htmlspecialchars($formValues['message'], ENT_QUOTES, 'UTF-8') ?></textarea>
                            </div>
                        </div>

                        <div class="hero-actions" style="margin-top: 1.5rem;">
                            <button type="submit" name="contact_submit" class="primary-btn">Send enquiry</button>
                        </div>

                        <?php if ($formMessage !== ''): ?>
                            <div class="form-status <?= htmlspecialchars($formStatus, ENT_QUOTES, 'UTF-8') ?>" role="<?= $formStatus === 'error' ? 'alert' : 'status' ?>" aria-live="polite">
                                <?= htmlspecialchars($formMessage, ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="container footer-row">
            <div>© <?= date('Y') ?> Anand Kustoms. Premium auto styling and performance design.</div>
            <div>Built with HTML, CSS, JavaScript, PHP and MySQL.</div>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>
