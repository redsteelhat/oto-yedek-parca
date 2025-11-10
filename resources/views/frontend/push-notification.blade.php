<!-- Push Notification Subscription -->
<script>
    // Check if browser supports push notifications
    if ('Notification' in window && 'serviceWorker' in navigator) {
        // Request permission
        if (Notification.permission === 'default') {
            Notification.requestPermission().then(function(permission) {
                if (permission === 'granted') {
                    // Register service worker for push notifications
                    navigator.serviceWorker.register('/sw.js').then(function(registration) {
                        console.log('Service Worker registered:', registration);
                        
                        // Subscribe to push notifications
                        registration.pushManager.subscribe({
                            userVisibleOnly: true,
                            applicationServerKey: urlBase64ToUint8Array('{{ config("services.push.public_key") }}')
                        }).then(function(subscription) {
                            // Send subscription to server
                            fetch('{{ route("push.subscribe") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify(subscription)
                            });
                        });
                    });
                }
            });
        }
    }

    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/\-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }
</script>

