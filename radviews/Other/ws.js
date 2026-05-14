self.addEventListener('install', function(event) {
    console.log('Service Worker installed');
    self.skipWaiting();
});

self.addEventListener('activate', function(event) {
    console.log('Service Worker activated');
    return self.clients.claim();
});

self.addEventListener('sync', function(event) {
    if (event.tag === 'apiSync') {
        event.waitUntil(callController());
    }
});

function callController() {
    // This would be your CodeIgniter controller function URL
    const controllerUrl = '<?php echo site_url('Other/save_publicIP'); ?>';
    // Assuming `publicIp` is available in your Service Worker scope

    return fetch('https://api.ipify.org?format=json')
        .then(response => response.json())
        .then(data => {
            const publicIp = data.ip;
            console.log("Calling CodeIgniter Controller with IP:", publicIp);
            return fetch(controllerUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ ip: publicIp })
            });
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Controller function called successfully:', data);
        })
        .catch(error => {
            console.error('Controller call failed:', error);
        });
}
