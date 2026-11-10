<?php
/**
 * Apex Institute of Technology - Online Registration Backend
 * Technologies: HTML, CSS, JavaScript, jQuery, PHP
 * 
 * Handles form validation, data sanitization, application ID generation,
 * and outputs styled browser display and print-ready confirmation receipt.
 */

// Set default timezone
date_default_timezone_set('Asia/Kolkata');

// Helper function for safe HTTP headers
function safe_header($header) {
    if (!headers_sent()) {
        header($header);
    }
}

function safe_response_code($code) {
    if (!headers_sent()) {
        http_response_code($code);
    }
}

// Enable Cross-Origin Resource Sharing (CORS)
safe_header('Access-Control-Allow-Origin: *');
safe_header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
safe_header('Access-Control-Allow-Headers: Content-Type, X-Requested-With, Accept');

// Handle preflight OPTIONS request
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? ($_SERVER['HTTP_METHOD'] ?? (!empty($_POST) ? 'POST' : 'GET'));
if ($requestMethod === 'OPTIONS') {
    safe_response_code(200);
    exit(0);
}

// Helper function to sanitize input strings
function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    $data = trim((string)$data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Check if request is POST (safe check to prevent undefined array key warnings)
if ($requestMethod !== 'POST' && empty($_POST)) {
    safe_response_code(405);
    echo "<h1>405 Method Not Allowed</h1><p>Please submit the form via POST method.</p>";
    exit;
}

// Detect if this is an AJAX request from jQuery
$is_ajax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
            (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
            (!empty($_POST['is_ajax_flag']));

// Retrieve and sanitize fields
$fullName      = sanitize_input($_POST['fullName'] ?? '');
$dob           = sanitize_input($_POST['dob'] ?? '');
$gender        = sanitize_input($_POST['gender'] ?? '');
$category      = sanitize_input($_POST['category'] ?? 'General');
$course        = sanitize_input($_POST['course'] ?? '');
$qualification = sanitize_input($_POST['qualification'] ?? '');
$percentage    = filter_var($_POST['percentage'] ?? '', FILTER_VALIDATE_FLOAT);
$passingYear   = sanitize_input($_POST['passingYear'] ?? '');
$email         = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone         = sanitize_input($_POST['phone'] ?? '');
$address       = sanitize_input($_POST['address'] ?? '');
$city          = sanitize_input($_POST['city'] ?? '');
$state         = sanitize_input($_POST['state'] ?? '');
$pincode       = sanitize_input($_POST['pincode'] ?? '');
$declaration   = sanitize_input($_POST['declaration'] ?? '');

$rawInterests  = $_POST['interests'] ?? [];
$interests     = is_array($rawInterests) ? array_map('sanitize_input', $rawInterests) : [];

// Validation array
$errors = [];

if (empty($fullName) || strlen($fullName) < 3) {
    $errors['fullName'] = 'Full name must contain at least 3 characters.';
}

if (empty($dob)) {
    $errors['dob'] = 'Please enter your date of birth.';
} else {
    $dobDate = DateTime::createFromFormat('Y-m-d', $dob);
    if (!$dobDate) {
        $errors['dob'] = 'Invalid date format.';
    } else {
        $today = new DateTime();
        $age = $today->diff($dobDate)->y;
        if ($age < 15 || $age > 60) {
            $errors['dob'] = 'Applicant age must be between 15 and 60 years.';
        }
    }
}

if (empty($gender)) {
    $errors['gender'] = 'Please select your gender.';
}

if (empty($course)) {
    $errors['course'] = 'Please select a program.';
}

if (empty($qualification)) {
    $errors['qualification'] = 'Please select highest educational qualification.';
}

if ($percentage === false || $percentage < 35 || $percentage > 100) {
    $errors['percentage'] = 'Please enter a valid percentage/CGPA between 35 and 100.';
}

if (empty($passingYear)) {
    $errors['passingYear'] = 'Please select passing year.';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Please provide a valid email address.';
}

if (empty($phone) || !preg_match('/^[0-9]{10}$/', $phone)) {
    $errors['phone'] = 'Please provide a valid 10-digit phone number.';
}

if (empty($address) || strlen($address) < 10) {
    $errors['address'] = 'Address must be at least 10 characters.';
}

if (empty($city)) {
    $errors['city'] = 'City is required.';
}

if (empty($state)) {
    $errors['state'] = 'State is required.';
}

if (empty($pincode) || !preg_match('/^[0-9]{6}$/', $pincode)) {
    $errors['pincode'] = 'PIN Code must be a 6-digit number.';
}

if (empty($declaration) || $declaration !== 'Yes') {
    $errors['declaration'] = 'You must confirm the declaration statement.';
}

// If validation fails
if (!empty($errors)) {
    if ($is_ajax) {
        safe_response_code(422);
        safe_header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'error',
            'message' => 'Please fix the errors marked in the form.',
            'errors' => $errors
        ]);
        exit;
    } else {
        // Fallback for non-AJAX POST with errors
        safe_response_code(422);
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Validation Errors | Registration Portal</title>
            <link rel="stylesheet" href="css/style.css">
        </head>
        <body>
            <div class="page-wrapper">
                <div class="container">
                    <div class="form-card">
                        <div class="alert-box alert-danger">
                            <strong>Validation Failed:</strong> Please correct the errors below.
                        </div>
                        <ul>
                            <?php foreach ($errors as $field => $err): ?>
                                <li><strong><?= ucfirst($field) ?>:</strong> <?= htmlspecialchars($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <br>
                        <a href="index.html" class="btn btn-primary">Go Back & Fix Form</a>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// Generate application registration credentials
$regNumber = 'PIT-2026-' . strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 6));
$submissionTime = date('d F Y, h:i:s A');
$formattedDob = date('d M Y', strtotime($dob));
$interestsString = !empty($interests) ? implode(', ', $interests) : 'None Specified';

// Helper function to build the receipt HTML card
function render_receipt_html($regNumber, $submissionTime, $fullName, $formattedDob, $gender, $category, $course, $qualification, $percentage, $passingYear, $interests, $email, $phone, $address, $city, $state, $pincode) {
    ob_start();
    ?>
    <div class="receipt-wrapper" id="printableReceipt">
        <!-- Receipt Header -->
        <div class="receipt-banner">
            <div class="receipt-brand">
                <h2>Pravinya Institute of Technology</h2>
                <p>Office of the Registrar • Admissions & Merit Examination Cell</p>
                <p style="font-size: 0.72rem; color: #93c5fd; margin-top: 2px;">Accredited Autonomous Technical Institution (NAAC 'A++' Grade)</p>
            </div>
            <div class="receipt-status-pill">
                <i class="fa-solid fa-circle-check"></i> Registration Confirmed
            </div>
        </div>

        <!-- Meta Bar -->
        <div class="receipt-meta-bar">
            <div class="meta-item">
                <span class="meta-label">Application Reference ID</span>
                <span class="meta-value meta-reg-no"><?= $regNumber ?></span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Submitted On</span>
                <span class="meta-value"><?= $submissionTime ?></span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Academic Session</span>
                <span class="meta-value">2026 - 2027</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Verification Status</span>
                <span class="meta-value" style="color: #10b981;">PROVISIONAL ALLOTMENT</span>
            </div>
        </div>

        <!-- Receipt Body Details -->
        <div class="receipt-body">
            <!-- Section A: Candidate Info -->
            <div class="receipt-section-heading">
                <i class="fa-solid fa-user-graduate"></i> Candidate Personal Details
            </div>
            <div class="receipt-grid">
                <div class="receipt-field">
                    <span class="field-label">Applicant Full Name</span>
                    <span class="field-value"><?= $fullName ?></span>
                </div>
                <div class="receipt-field">
                    <span class="field-label">Date of Birth</span>
                    <span class="field-value"><?= $formattedDob ?></span>
                </div>
                <div class="receipt-field">
                    <span class="field-label">Gender</span>
                    <span class="field-value"><?= $gender ?></span>
                </div>
                <div class="receipt-field">
                    <span class="field-label">Category / Reservation</span>
                    <span class="field-value"><?= $category ?></span>
                </div>
            </div>

            <!-- Section B: Program & Qualification -->
            <div class="receipt-section-heading">
                <i class="fa-solid fa-graduation-cap"></i> Program & Academic Record
            </div>
            <div class="receipt-grid">
                <div class="receipt-field">
                    <span class="field-label">Program Applied For</span>
                    <span class="field-value" style="color: #4f46e5;"><?= $course ?></span>
                </div>
                <div class="receipt-field">
                    <span class="field-label">Qualifying Examination</span>
                    <span class="field-value"><?= $qualification ?></span>
                </div>
                <div class="receipt-field">
                    <span class="field-label">Aggregate Score / CGPA</span>
                    <span class="field-value"><?= number_format($percentage, 2) ?>%</span>
                </div>
                <div class="receipt-field">
                    <span class="field-label">Passing Year</span>
                    <span class="field-value"><?= $passingYear ?></span>
                </div>
                <div class="receipt-field" style="grid-column: 1 / -1;">
                    <span class="field-label">Areas of Technical Interest</span>
                    <div class="tag-list">
                        <?php if (!empty($interests)): ?>
                            <?php foreach ($interests as $item): ?>
                                <span class="badge-tag"><i class="fa-solid fa-check"></i> <?= $item ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="field-value">General / Not Specified</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Section C: Contact & Residence -->
            <div class="receipt-section-heading">
                <i class="fa-solid fa-address-book"></i> Communication & Address
            </div>
            <div class="receipt-grid">
                <div class="receipt-field">
                    <span class="field-label">Email Address</span>
                    <span class="field-value"><?= $email ?></span>
                </div>
                <div class="receipt-field">
                    <span class="field-label">Contact Mobile</span>
                    <span class="field-value">+91 <?= $phone ?></span>
                </div>
                <div class="receipt-field" style="grid-column: 1 / -1;">
                    <span class="field-label">Permanent Postal Address</span>
                    <span class="field-value"><?= nl2br($address) ?>, <?= $city ?>, <?= $state ?> - <?= $pincode ?></span>
                </div>
            </div>

            <!-- Verification & Digital Seal -->
            <div class="receipt-seal-box">
                <div class="verification-note">
                    <i class="fa-solid fa-shield-halved" style="color: #10b981; margin-right: 4px;"></i>
                    <strong>System Generated Digital Acknowledgement:</strong> This electronic registration slip is legally valid for admission verification and counseling rounds. Keep this document printed and signed for counseling.
                </div>
                <div class="official-signature">
                    <div class="sign-line"></div>
                    <span class="sign-title">Controller of Admissions</span>
                </div>
            </div>
        </div>

        <!-- Action Buttons (Hidden on Print) -->
        <div class="receipt-actions no-print">
            <button type="button" id="newRegBtn" class="btn btn-secondary">
                <i class="fa-solid fa-user-plus"></i> Register Another Applicant
            </button>
            <button type="button" id="printReceiptBtn" class="btn btn-print">
                <i class="fa-solid fa-print"></i> Print Registration Slip
            </button>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

$receiptHtml = render_receipt_html(
    $regNumber,
    $submissionTime,
    $fullName,
    $formattedDob,
    $gender,
    $category,
    $course,
    $qualification,
    $percentage,
    $passingYear,
    $interests,
    $email,
    $phone,
    $address,
    $city,
    $state,
    $pincode
);

// If jQuery AJAX request, return structured JSON containing the rendered HTML & details
if ($is_ajax) {
    safe_header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status'         => 'success',
        'message'        => 'Application registered successfully!',
        'registrationId' => $regNumber,
        'timestamp'      => $submissionTime,
        'html'           => $receiptHtml,
        'candidate'      => [
            'name'       => $fullName,
            'course'     => $course,
            'email'      => $email,
            'phone'      => $phone
        ]
    ]);
    exit;
}

// Fallback for Direct Browser POST (Non-AJAX)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Slip | <?= $regNumber ?></title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="top-nav-bar no-print">
        <div class="nav-container">
            <div class="brand">
                <i class="fa-solid fa-graduation-cap brand-icon"></i>
                <div class="brand-text">
                    <span class="brand-title">Pravinya Institute of Technology</span>
                    <span class="brand-sub">Admissions Portal 2026-27</span>
                </div>
            </div>
            <div class="portal-badge">
                <span class="pulse-dot"></span> Submission Successful
            </div>
        </div>
    </div>

    <main class="page-wrapper">
        <div class="container">
            <?= $receiptHtml ?>
        </div>
    </main>

    <footer class="site-footer no-print">
        <div class="footer-container">
            <p>© 2026 Pravinya Institute of Technology. All Rights Reserved.</p>
            <p class="footer-tech">Engineered with HTML5, CSS3, JavaScript, jQuery & PHP</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#printReceiptBtn').on('click', function() {
                window.print();
            });
            $('#newRegBtn').on('click', function() {
                window.location.href = 'index.html';
            });
        });
    </script>
</body>
</html>
