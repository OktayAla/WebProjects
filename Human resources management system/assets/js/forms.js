// Form işlemleri için özel JS kodları
document.addEventListener('DOMContentLoaded', function() {
    // Dinamik form alanları
    const formHandlers = {
        // İzin formları için özel işlemler
        leaveForm: {
            init: function() {
                const leaveType = document.querySelector('select[name="leave_type"]');
                const dateInputs = document.querySelectorAll('input[type="date"]');
                
                if(leaveType) {
                    leaveType.addEventListener('change', this.handleLeaveTypeChange);
                }
                if(dateInputs.length) {
                    dateInputs.forEach(input => {
                        input.addEventListener('change', this.calculateDuration);
                    });
                }
            },
            handleLeaveTypeChange: function(e) {
                const maxDays = {
                    'Yıllık İzin': 14,
                    'Raporlu': 0,
                    'Ücretsiz İzin': 30,
                    'Mazeret İzni': 3
                };
                const selectedType = e.target.value;
                const durationInput = document.querySelector('input[name="duration"]');
                if(durationInput && maxDays[selectedType]) {
                    durationInput.max = maxDays[selectedType];
                }
            },
            calculateDuration: function() {
                const startDate = document.querySelector('input[name="start_date"]').value;
                const endDate = document.querySelector('input[name="end_date"]').value;
                if(startDate && endDate) {
                    const diff = Math.ceil((new Date(endDate) - new Date(startDate)) / (1000 * 60 * 60 * 24));
                    document.querySelector('input[name="duration"]').value = diff;
                }
            }
        },

        // Fazla mesai formları için özel işlemler
        overtimeForm: {
            init: function() {
                const timeInputs = document.querySelectorAll('input[type="time"]');
                if(timeInputs.length) {
                    timeInputs.forEach(input => {
                        input.addEventListener('change', this.calculateHours);
                    });
                }
            },
            calculateHours: function() {
                const startTime = document.querySelector('input[name="start_time"]').value;
                const endTime = document.querySelector('input[name="end_time"]').value;
                if(startTime && endTime) {
                    const diff = (new Date(`2000/01/01 ${endTime}`) - new Date(`2000/01/01 ${startTime}`)) / 3600000;
                    document.querySelector('input[name="total_hours"]').value = diff.toFixed(2);
                }
            }
        }
    };

    // Form validasyonları
    const validateForm = (form) => {
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        });

        return isValid;
    };

    // Form submit işlemleri
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
                showNotification('Lütfen tüm zorunlu alanları doldurun.', 'warning');
            }
        });
    });

    // Form handler'ları başlat
    Object.values(formHandlers).forEach(handler => {
        if (typeof handler.init === 'function') {
            handler.init();
        }
    });
});
