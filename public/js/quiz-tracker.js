/**
 * Quiz Activity Tracker
 * Include this script in the student quiz page to track activity.
 * 
 * Usage:
 * <script src="/js/quiz-tracker.js"></script>
 * <script>
 *   QuizTracker.init({
 *       studentId: 123,
 *       exerciseId: 456,
 *       apiUrl: '/api/quiz-activity/log',
 *       csrfToken: '{{ csrf_token() }}'
 *   });
 * </script>
 */

const QuizTracker = (function() {
    let config = {
        studentId: null,
        exerciseId: null,
        apiUrl: '/api/quiz-activity/log',
        csrfToken: null
    };

    let hasStarted = false;
    let isSubmitting = false;

    function sendEvent(eventType) {
        if (!config.studentId || !config.exerciseId) return;

        // Use fetch with keepalive or sendBeacon for reliability when unloading
        const payload = JSON.stringify({
            student_id: config.studentId,
            exercise_id: config.exerciseId,
            event_type: eventType,
            _token: config.csrfToken
        });

        // Use navigator.sendBeacon if available for exit events
        if (['QUIZ_EXIT', 'TAB_SWITCH'].includes(eventType) && navigator.sendBeacon) {
            const blob = new Blob([payload], { type: 'application/json' });
            navigator.sendBeacon(config.apiUrl, blob);
        } else {
            fetch(config.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': config.csrfToken
                },
                body: payload,
                keepalive: true
            }).catch(err => console.error('Error logging quiz activity:', err));
        }
    }

    function handleVisibilityChange() {
        if (document.hidden) {
            sendEvent('TAB_SWITCH');
        } else {
            sendEvent('QUIZ_REJOIN');
        }
    }

    function handleWindowBlur() {
        sendEvent('WINDOW_BLUR');
    }

    function handleWindowFocus() {
        sendEvent('WINDOW_FOCUS');
    }

    function handleBeforeUnload(e) {
        if (!isSubmitting) {
            sendEvent('QUIZ_EXIT');
        }
    }

    return {
        init: function(options) {
            config = { ...config, ...options };
            
            if (!hasStarted) {
                // Log the initial entry
                sendEvent('QUIZ_ENTER');
                hasStarted = true;

                // Listeners
                document.addEventListener('visibilitychange', handleVisibilityChange);
                window.addEventListener('blur', handleWindowBlur);
                window.addEventListener('focus', handleWindowFocus);
                window.addEventListener('beforeunload', handleBeforeUnload);
            }
        },
        
        submit: function() {
            isSubmitting = true;
            sendEvent('QUIZ_SUBMIT');
        }
    };
})();
