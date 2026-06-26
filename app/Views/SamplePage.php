<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sample Page - Bootstrap Components</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .theme-toggle {
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        .theme-toggle:hover {
            transform: scale(1.1);
        }
        .component-section {
            padding: 60px 0;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-bootstrap-fill me-2"></i>Sample Page
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#components">Components</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                </ul>
                <!-- Dark/Light Mode Toggle -->
                <button class="btn btn-outline-light theme-toggle" id="themeToggle" title="Toggle dark/light mode">
                    <i class="bi bi-moon-fill" id="themeIcon"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="component-section bg-gradient bg-primary text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Welcome to Sample Page</h1>
                    <p class="lead mb-4">Explore beautiful Bootstrap 5 components with dark/light mode support</p>
                    <div class="d-flex gap-2">
                        <button class="btn btn-light btn-lg">Get Started</button>
                        <button class="btn btn-outline-light btn-lg">Learn More</button>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="text-center">
                        <i class="bi bi-palette-fill" style="font-size: 200px; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="component-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Key Features</h2>
                <p class="lead text-muted">Discover what makes this template special</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 card-hover border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-lightning-charge-fill text-primary" style="font-size: 48px;"></i>
                            </div>
                            <h5 class="card-title">Fast & Lightweight</h5>
                            <p class="card-text text-muted">Optimized performance with minimal dependencies and quick load times.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 card-hover border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-phone-fill text-success" style="font-size: 48px;"></i>
                            </div>
                            <h5 class="card-title">Fully Responsive</h5>
                            <p class="card-text text-muted">Looks great on all devices from mobile phones to large desktop screens.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 card-hover border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-moon-stars-fill text-warning" style="font-size: 48px;"></i>
                            </div>
                            <h5 class="card-title">Dark Mode Support</h5>
                            <p class="card-text text-muted">Switch between light and dark themes for comfortable viewing.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Components Showcase -->
    <section id="components" class="component-section bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Bootstrap Components</h2>
                <p class="lead text-muted">A showcase of various Bootstrap components</p>
            </div>

            <!-- Buttons -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-hand-index-thumb me-2"></i>Buttons</h5>
                </div>
                <div class="card-body">
                    <h6 class="mb-3">Button Variants</h6>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <button type="button" class="btn btn-primary">Primary</button>
                        <button type="button" class="btn btn-secondary">Secondary</button>
                        <button type="button" class="btn btn-success">Success</button>
                        <button type="button" class="btn btn-danger">Danger</button>
                        <button type="button" class="btn btn-warning">Warning</button>
                        <button type="button" class="btn btn-info">Info</button>
                        <button type="button" class="btn btn-light">Light</button>
                        <button type="button" class="btn btn-dark">Dark</button>
                        <button type="button" class="btn btn-link">Link</button>
                    </div>
                    <h6 class="mb-3">Button Sizes</h6>
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <button type="button" class="btn btn-primary btn-lg">Large Button</button>
                        <button type="button" class="btn btn-primary">Default Button</button>
                        <button type="button" class="btn btn-primary btn-sm">Small Button</button>
                    </div>
                </div>
            </div>

            <!-- Alerts -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-bell me-2"></i>Alerts</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-primary alert-dismissible fade show" role="alert">
                        <strong>Primary Alert!</strong> This is a primary alert with <a href="#" class="alert-link">an example link</a>.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success Alert!</strong> Your changes have been saved successfully.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error Alert!</strong> Something went wrong. Please try again.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>Warning!</strong> Check your connection before proceeding.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            </div>

            <!-- Forms -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-input-cursor-text me-2"></i>Forms</h5>
                </div>
                <div class="card-body">
                    <form>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="firstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstName" placeholder="Enter first name">
                            </div>
                            <div class="col-md-6">
                                <label for="lastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastName" placeholder="Enter last name">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" placeholder="name@example.com">
                            <div class="form-text">We'll never share your email with anyone else.</div>
                        </div>
                        <div class="mb-3">
                            <label for="selectOption" class="form-label">Select Option</label>
                            <select class="form-select" id="selectOption">
                                <option selected>Choose an option...</option>
                                <option value="1">Option 1</option>
                                <option value="2">Option 2</option>
                                <option value="3">Option 3</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Radio Options</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1" checked>
                                    <label class="form-check-label" for="inlineRadio1">Option 1</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2">
                                    <label class="form-check-label" for="inlineRadio2">Option 2</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio3" value="option3">
                                    <label class="form-check-label" for="inlineRadio3">Option 3</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Checkboxes</label>
                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="check1" checked>
                                    <label class="form-check-label" for="check1">Check this option</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="check2">
                                    <label class="form-check-label" for="check2">Or this one</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" rows="3" placeholder="Enter your message"></textarea>
                        </div>
                        <div class="mb-3 form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="agreeSwitch" checked>
                            <label class="form-check-label" for="agreeSwitch">I agree to the terms and conditions</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Form</button>
                        <button type="reset" class="btn btn-secondary ms-2">Reset</button>
                    </form>
                </div>
            </div>

            <!-- Progress Bars -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-bar-chart-steps me-2"></i>Progress Bars</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Basic Progress</label>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar" role="progressbar" style="width: 75%;" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">75%</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Colored Progress Bars</label>
                        <div class="progress mb-2" style="height: 20px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
                        </div>
                        <div class="progress mb-2" style="height: 20px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">50%</div>
                        </div>
                        <div class="progress mb-2" style="height: 20px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 75%;" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">75%</div>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: 90%;" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100">90%</div>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Striped & Animated</label>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Loading...</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Badges & Pills -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-tag-fill me-2"></i>Badges & Pills</h5>
                </div>
                <div class="card-body">
                    <h6 class="mb-3">Contextual Badges</h6>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <span class="badge bg-primary">Primary</span>
                        <span class="badge bg-secondary">Secondary</span>
                        <span class="badge bg-success">Success</span>
                        <span class="badge bg-danger">Danger</span>
                        <span class="badge bg-warning text-dark">Warning</span>
                        <span class="badge bg-info text-dark">Info</span>
                        <span class="badge bg-light text-dark">Light</span>
                        <span class="badge bg-dark">Dark</span>
                    </div>
                    <h6 class="mb-3">Pill Badges</h6>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <span class="badge rounded-pill bg-primary">Primary Pill</span>
                        <span class="badge rounded-pill bg-success">Success Pill</span>
                        <span class="badge rounded-pill bg-danger">Danger Pill</span>
                        <span class="badge rounded-pill bg-warning text-dark">Warning Pill</span>
                    </div>
                    <h6 class="mb-3">Badges in Buttons</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-primary">
                            Notifications <span class="badge bg-danger">4</span>
                        </button>
                        <button type="button" class="btn btn-secondary">
                            Messages <span class="badge bg-light text-dark">12</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modals Trigger -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="bi bi-window-stack me-2"></i>Modal Example</h5>
                </div>
                <div class="card-body">
                    <p class="mb-3">Click the button below to launch a modal dialog:</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        <i class="bi bi-popup me-2"></i>Launch Demo Modal
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="component-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card border-0 shadow">
                        <div class="card-body p-5">
                            <h2 class="text-center mb-4">Contact Us</h2>
                            <form>
                                <div class="mb-3">
                                    <label for="contactName" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="contactName" required>
                                </div>
                                <div class="mb-3">
                                    <label for="contactEmail" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="contactEmail" required>
                                </div>
                                <div class="mb-3">
                                    <label for="contactSubject" class="form-label">Subject</label>
                                    <input type="text" class="form-control" id="contactSubject" required>
                                </div>
                                <div class="mb-3">
                                    <label for="contactMessage" class="form-label">Message</label>
                                    <textarea class="form-control" id="contactMessage" rows="5" required></textarea>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">Send Message</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Sample Page</h5>
                    <p class="text-muted mb-0">A Bootstrap 5 template with dark/light mode support.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="mb-2">
                        <a href="#" class="text-white me-3"><i class="bi bi-facebook fs-5"></i></a>
                        <a href="#" class="text-white me-3"><i class="bi bi-twitter fs-5"></i></a>
                        <a href="#" class="text-white me-3"><i class="bi bi-instagram fs-5"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-github fs-5"></i></a>
                    </div>
                    <p class="text-muted mb-0">&copy; 2025 Sample Page. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <i class="bi bi-info-circle-fill text-primary me-2"></i>Modal Title
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>This is a sample modal dialog. You can put any content here including forms, images, or other components.</p>
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-lightbulb me-2"></i>Pro tip: Modals are great for focused interactions!
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="bi bi-bell-fill text-primary me-2"></i>
                <strong class="me-auto">Notification</strong>
                <small>Just now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                Hello! This is a toast notification.
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Theme Toggle Script -->
    <script>
        // Dark/Light Mode Toggle
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const html = document.documentElement;

        // Check for saved theme preference or default to light
        const currentTheme = localStorage.getItem('theme') || 'light';
        html.setAttribute('data-bs-theme', currentTheme);
        updateIcon(currentTheme);

        themeToggle.addEventListener('click', () => {
            const currentTheme = html.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';

            html.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateIcon(newTheme);

            // Show toast notification
            const toast = new bootstrap.Toast(document.getElementById('liveToast'));
            toast.show();
        });

        function updateIcon(theme) {
            if (theme === 'dark') {
                themeIcon.classList.remove('bi-moon-fill');
                themeIcon.classList.add('bi-sun-fill');
            } else {
                themeIcon.classList.remove('bi-sun-fill');
                themeIcon.classList.add('bi-moon-fill');
            }
        }

        // Smooth scroll for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
