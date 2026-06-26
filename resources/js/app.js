// import './bootstrap';

import Alpine from 'alpinejs';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

window.Alpine = Alpine;
window.Swal = Swal;

window.AppAlerts = {
    success(message, title = 'Berhasil') {
        return Swal.fire({
            icon: 'success',
            title,
            text: message,
            timer: 2600,
            timerProgressBar: true,
            showConfirmButton: false,
            toast: true,
            position: 'top-end',
            background: '#0f172a',
            color: '#e2e8f0',
        });
    },

    warning(message, title = 'Perhatian') {
        return Swal.fire({
            icon: 'warning',
            title,
            text: message,
            confirmButtonText: 'Mengerti',
            confirmButtonColor: '#4f46e5',
            background: '#0f172a',
            color: '#e2e8f0',
        });
    },

    confirm({
        title = 'Konfirmasi tindakan',
        text = 'Tindakan ini tidak dapat dibatalkan.',
        confirmButtonText = 'Ya, lanjutkan',
        cancelButtonText = 'Batal',
        icon = 'warning',
    } = {}) {
        return Swal.fire({
            icon,
            title,
            text,
            showCancelButton: true,
            confirmButtonText,
            cancelButtonText,
            reverseButtons: true,
            focusCancel: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#475569',
            background: '#0f172a',
            color: '#e2e8f0',
        });
    },
};

document.addEventListener('DOMContentLoaded', () => {
    if (window.AppFlash?.message) {
        const type = window.AppFlash.type || 'success';
        const messageMap = {
            'profile-updated': 'Profil berhasil diperbarui.',
            'password-updated': 'Password berhasil diperbarui.',
            'verification-link-sent': 'Link verifikasi baru berhasil dikirim.',
        };

        const message = messageMap[window.AppFlash.message] || window.AppFlash.message;

        if (type === 'success') {
            window.AppAlerts.success(message);
        } else {
            window.AppAlerts.warning(message, 'Informasi');
        }
    }

    document.addEventListener('submit', async (event) => {
        const form = event.target.closest('form[data-confirm]');

        if (!form || form.dataset.confirmed === 'true') {
            return;
        }

        event.preventDefault();

        const result = await window.AppAlerts.confirm({
            title: form.dataset.confirmTitle,
            text: form.dataset.confirmText,
            confirmButtonText: form.dataset.confirmButton || 'Ya, lanjutkan',
            cancelButtonText: form.dataset.confirmCancel || 'Batal',
        });

        if (result.isConfirmed) {
            form.dataset.confirmed = 'true';
            form.submit();
        }
    }, true);
});

Alpine.start();
