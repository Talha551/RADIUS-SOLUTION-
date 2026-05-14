<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PWA Background Sync with Start/Stop and CodeIgniter</title>
</head>
<body>
    <h1>PWA Background Sync Example</h1>
    <p>The controller function will be called after 5 minutes when you click "Start".</p>

    <button id="startButton">Start Call</button>
    <button id="stopButton" disabled>Stop Call</button>

    <script>
        let syncRegistered = false;
        let timeoutId;
        let publicIp = '';

        // Function to get public IP using a third-party API
        function getPublicIP() {
            fetch('https://api.ipify.org?format=json')
                .then(response => response.json())
                .then(data => {
                    publicIp = data.ip;
                    console.log("Public IP:", publicIp);
                })
                .catch(error => console.error('Error fetching public IP:', error));
        }

        // Call getPublicIP on page load
        window.onload = getPublicIP;

        // Register the service worker
        if ('serviceWorker' in navigator && 'SyncManager' in window) {
            navigator.serviceWorker.register('/sw.js').then(function(registration) {
                console.log('Service Worker registered', registration);
            }).catch(function(error) {
                console.error('Service Worker registration failed:', error);
            });
        } else {
            console.warn('Background Sync or Service Worker not supported');
        }

        // Function to start scheduling the sync
        function startApiCall() {
            if (publicIp === '') {
                alert('Fetching public IP, please wait...');
                return;
            }
            timeoutId = setTimeout(scheduleSync, 300000); // Schedule after 5 minutes
            document.getElementById('startButton').disabled = true;
            document.getElementById('stopButton').disabled = false;
            console.log('Call scheduled to start in 5 minutes.');
        }

        // Function to stop the scheduled API call
        function stopApiCall() {
            clearTimeout(timeoutId); // Cancel the scheduled sync
            document.getElementById('startButton').disabled = false;
            document.getElementById('stopButton').disabled = true;
            console.log('Call scheduling stopped.');
        }

        // Schedule the sync event
        function scheduleSync() {
            if (!syncRegistered) {
                navigator.serviceWorker.ready.then(function(registration) {
                    registration.sync.register('apiSync')
                        .then(() => {
                            syncRegistered = true;
                            console.log('Sync registered');
                        })
                        .catch(err => console.error('Sync registration failed:', err));
                });
            }
        }

        // Event listeners for Start and Stop buttons
        document.getElementById('startButton').addEventListener('click', startApiCall);
        document.getElementById('stopButton').addEventListener('click', stopApiCall);
    </script>
</body>
</html>