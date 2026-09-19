/**
 * Paytrack — Auth Page Controller
 * Handles: role modal open/close, password toggle, form loading state,
 *          and SweetAlert2 error display.
 */

window.Paytrack = window.Paytrack || {};

Paytrack.Auth = (function () {

    // ── DOM refs ──
    let overlay, modal, closeBtn;
    let formRole, usernameLabel, modalIcon, modalTitle, modalSubtitle;
    let loginForm, submitBtn;
    let pwInput, togglePw, eyeIcon;

    // ── Role config ──
    const ROLES = {
        student: {
            label:    'Student / Parent Login',
            subtitle: 'Enter your Student ID and password',
            idLabel:  'Student ID',
            iconText: '🎓',
            iconClass: '',
        },
        admin: {
            label:    'Administrator Login',
            subtitle: 'Enter your admin username and password',
            idLabel:  'Username',
            iconText: '⚙️',
            iconClass: 'admin-mode',
        },
    };

    // ── Open modal ──
    function openModal(role) {
        const cfg = ROLES[role];
        if (!cfg) return;

        formRole.value          = role;
        modalTitle.textContent  = cfg.label;
        modalSubtitle.textContent = cfg.subtitle;
        usernameLabel.textContent = cfg.idLabel;
        modalIcon.textContent   = cfg.iconText;

        // Icon style
        modalIcon.classList.remove('admin-mode');
        if (cfg.iconClass) modalIcon.classList.add(cfg.iconClass);

        // Placeholder
        document.getElementById('username').placeholder =
            role === 'student' ? 'e.g. 2024-001' : 'Enter your username';

        overlay.setAttribute('aria-hidden', 'false');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';

        // Focus first input after animation
        setTimeout(() => document.getElementById('username').focus(), 220);
    }

    // ── Close modal ──
    function closeModal() {
        overlay.classList.remove('active');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        loginForm.reset();
    }

    // ── Password toggle ──
    function handlePwToggle() {
        const isPassword = pwInput.type === 'password';
        pwInput.type     = isPassword ? 'text' : 'password';
        eyeIcon.textContent = isPassword ? '🙈' : '👁';
    }

    // ── Form submit: show loading state ──
    function handleFormSubmit() {
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
    }

    // ── SweetAlert2 error ──
    function showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Login Failed',
            text: message,
            confirmButtonText: 'Try again',
            confirmButtonColor: '#18181b',
            borderRadius: '12px',
            customClass: {
                popup:   'pt-swal-popup',
                title:   'pt-swal-title',
                confirmButton: 'pt-swal-confirm',
            },
        });
    }

    // ── Init ──
    function init(error, openRole) {
        overlay       = document.getElementById('authOverlay');
        modal         = document.getElementById('authModal');
        closeBtn      = document.getElementById('btnClose');
        formRole      = document.getElementById('formRole');
        usernameLabel = document.getElementById('usernameLabel');
        modalIcon     = document.getElementById('modalIcon');
        modalTitle    = document.getElementById('modalTitle');
        modalSubtitle = document.getElementById('modalSubtitle');
        loginForm     = document.getElementById('loginForm');
        submitBtn     = document.getElementById('submitBtn');
        pwInput       = document.getElementById('password');
        togglePw      = document.getElementById('togglePw');
        eyeIcon       = document.getElementById('eyeIcon');

        // Role button clicks
        document.getElementById('btnStudent').addEventListener('click', () => openModal('student'));
        document.getElementById('btnAdmin').addEventListener('click',   () => openModal('admin'));

        // Close modal
        closeBtn.addEventListener('click', closeModal);
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) closeModal();
        });

        // Keyboard: Escape closes modal
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && overlay.classList.contains('active')) closeModal();
        });

        // Password visibility toggle
        togglePw.addEventListener('click', handlePwToggle);

        // Form submit spinner
        loginForm.addEventListener('submit', handleFormSubmit);

        // ── Re-open modal if server returned an error ──
        if (error && openRole) {
            openModal(openRole);
            // Small delay so modal animation completes first
            setTimeout(() => showError(error), 300);
        } else if (error) {
            showError(error);
        }
    }

    return { init };

})();
