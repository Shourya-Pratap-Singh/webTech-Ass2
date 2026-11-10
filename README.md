# 🎓 Apex Institute of Technology - Online Registration Portal

A complete, production-grade Web Application for online candidate/student registration built using **HTML5**, **CSS3**, **JavaScript**, **jQuery**, and **PHP**. The form accepts user details, validates them on both client and server side, and upon submission, renders a formatted, styled official **Registration Slip** with an integrated browser print feature (`@media print`).

---

## 🚀 Key Features

- **Semantic HTML5 Architecture**: Organized into 4 structured sections (Personal Details, Academic Record, Contact Information, and Declaration) with step-by-step progress tracking.
- **Modern Responsive CSS3 Design**:
  - Gradient header, glassmorphism cards, custom radio pills, and checkbox tags.
  - Interactive validation states (green valid rings, red alert borders, smooth animations).
  - Dedicated **Print Stylesheet (`@media print`)**: Formats an official, high-resolution admission receipt on A4 paper and hides all web navigation, buttons, and footers.
- **Client-Side Validation & Interactivity (JavaScript & jQuery)**:
  - Real-time input masking (numeric constraint for phone & PIN code).
  - Character counter for address (`0/250`).
  - Regex-based format checks (Email, 10-digit Phone, 6-digit PIN code, Age eligibility between 15 and 60).
  - **jQuery Asynchronous AJAX Submission**: Submits form without full-page reloads, provides live button spinner feedback, and dynamically injects the formatted receipt.
- **Robust PHP Backend (`process.php`)**:
  - Strict input sanitization (`htmlspecialchars`, `stripslashes`, `trim`, `filter_var`).
  - Server-side validation with HTTP `422 Unprocessable Entity` structured JSON responses.
  - Automatic Application ID generation (`APEX-2026-XXXXXX`).
  - Dual-mode support: Supports both **jQuery AJAX JSON responses** and **direct browser POST submissions** (no-JS fallback).
- **Cloud Deployment Ready**: Preconfigured with `Dockerfile`, `render.yaml`, and `vercel.json` for one-click hosting on free cloud platforms.

---

## 📁 Project Structure

```
webTechAss-2/
├── index.html              # Main client-facing registration form
├── index.php               # PHP root entry point (routes to index.html)
├── process.php             # PHP backend processor, sanitization & receipt generator
├── css/
│   └── style.css           # Modern responsive styles & @media print rules
├── js/
│   └── script.js           # jQuery & JavaScript validation and AJAX logic
├── lib/
│   └── jquery-3.7.1.min.js # Local offline fallback copy of jQuery
├── Dockerfile              # Docker container definition (PHP 8.3 + Apache)
├── render.yaml             # Render Blueprint cloud deployment configuration
├── vercel.json             # Vercel serverless PHP runtime configuration
├── .dockerignore           # Files excluded from Docker builds
├── .gitignore              # Files excluded from Git tracking
└── README.md               # Project documentation and deployment guide
```

---

## 💻 Running Locally

### Option 1: Using PHP Built-In Web Server (Quickest)
1. Open PowerShell / Terminal in the project root:
   ```powershell
   cd "c:\Users\hp\OneDrive\Desktop\ppt.s\web  tech college\webTechAss-2"
   ```
2. Start the PHP server:
   ```powershell
   php -S 127.0.0.1:8000
   ```
3. Open your browser and navigate to:
   ```
   http://127.0.0.1:8000
   ```

### Option 2: Using XAMPP / WampServer / Laragon
1. Copy the `webTechAss-2` folder into your `htdocs` directory (e.g. `C:\xampp\htdocs\webTechAss-2`).
2. Start the **Apache** module from the XAMPP Control Panel.
3. Open your browser and visit:
   ```
   http://localhost/webTechAss-2/
   ```

---

## ☁️ Cloud Hosting Guide

You can host this application on any of the following free cloud platforms:

### 1. Render.com (Recommended - 100% Free with Docker)
Render is an industry-standard cloud platform that provides free web service hosting.

1. **Create a GitHub Repository**:
   ```bash
   git add .
   git commit -m "Deploy online registration portal"
   git remote add origin https://github.com/<your-username>/online-registration-portal.git
   git push -u origin master
   ```
2. **Deploy on Render**:
   - Go to [https://render.com](https://render.com) and sign in with GitHub.
   - Click **"New +"** and choose **"Web Service"**.
   - Select your GitHub repository.
   - Choose **Docker** as the Runtime (Render will automatically detect the provided `Dockerfile`).
   - Select the **Free** instance plan.
   - Click **"Create Web Service"**.
   - Your application will be live in 1-2 minutes with a free HTTPS URL like:
     `https://apex-online-registration.onrender.com`

---

### 2. Railway (railway.app)
1. Sign in to [Railway](https://railway.app) using your GitHub account.
2. Click **"New Project"** -> **"Deploy from GitHub repo"**.
3. Select your repository. Railway automatically builds the `Dockerfile`.
4. Go to the project **Settings** -> **Domains** -> Click **"Generate Domain"**.
5. Your app is live with an `*.up.railway.app` URL.

---

### 3. InfinityFree / 000webhost (Traditional Free PHP Web Hosting)
1. Create a free account at [https://www.infinityfree.com](https://www.infinityfree.com).
2. Create a free hosting account and open the **File Manager** (or connect via FTP).
3. Navigate to `htdocs/`.
4. Upload all the files from this directory (`index.html`, `index.php`, `process.php`, `css/`, `js/`, `lib/`).
5. Your application is immediately accessible via your assigned free subdomain (e.g., `https://yourname.infinityfreeapp.com`).

---

### 4. Vercel (Using `vercel.json`)
1. Install Vercel CLI or import your GitHub repository on [https://vercel.com](https://vercel.com).
2. The included `vercel.json` automatically activates the `@vercel/php` runtime for serverless execution.
3. Deploy directly with:
   ```bash
   npx vercel --prod
   ```

---

## 🧪 Form Fields & Validation Rules

| Field Name | Type | Validation Rule |
| :--- | :--- | :--- |
| **Full Name** | Text | Required, 3–60 characters, alphabets & spaces only |
| **Date of Birth** | Date | Required, candidate age must be between 15 and 60 years |
| **Gender** | Radio | Required (Male / Female / Other) |
| **Program Applied**| Dropdown | Required, Engineering, Computing, or Management programs |
| **Highest Qualification** | Dropdown | Required, 12th, Diploma, Bachelor's, Master's |
| **Percentage / CGPA** | Number | Required, float between 35.00% and 100.00% |
| **Passing Year** | Dropdown | Required |
| **Technical Interests** | Checkbox | Optional multiple selection (Web, AI, Cyber, Mobile, DevOps) |
| **Email Address** | Email | Required, valid RFC email pattern |
| **Mobile Number** | Tel | Required, exactly 10 numeric digits |
| **Permanent Address** | Textarea | Required, 10–250 characters with live character counter |
| **City, State, PIN** | Text/Number | Required, PIN code must be exactly 6 digits |
| **Declaration** | Checkbox | Mandatory confirmation checkbox |

---

## 🖨️ Print & Browser Display Behavior

1. **Immediate In-Browser Display**:
   Upon clicking **"Submit Application"**, jQuery executes client-side validation, submits data via AJAX to `process.php`, and dynamically updates the page to display a stylized **Admission Registration Slip**.
2. **Official Registration Slip Includes**:
   - Unique System-Generated Application ID (e.g. `APEX-2026-B812FA`)
   - Exact submission timestamp with timezone
   - Academic program and candidate details in clear structured grids
   - Institutional verification seal and signature line
3. **One-Click Browser Print**:
   Clicking **"Print Registration Slip"** triggers `window.print()`. The dedicated `@media print` CSS cleanly strips navigation bars, action buttons, and page backgrounds, generating a crisp, professional A4 registration receipt ready to be saved as PDF or printed on paper.
