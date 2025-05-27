document.addEventListener('DOMContentLoaded', function() {
    const notificationButton = document.getElementById('notificationButton');
    const notificationDropdown = document.getElementById('notificationDropdown');
    
    if (notificationButton && notificationDropdown) {
    // Toggle notification dropdown
    notificationButton.addEventListener('click', function(e) {
            e.preventDefault();
        e.stopPropagation();
        notificationDropdown.classList.toggle('hidden');
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
            if (!notificationButton.contains(e.target) && !notificationDropdown.contains(e.target)) {
            notificationDropdown.classList.add('hidden');
        }
    });
    
    // Prevent dropdown from closing when clicking inside it
    notificationDropdown.addEventListener('click', function(e) {
        e.stopPropagation();
    });
        
        // Handle notification clicks
        const notificationLinks = document.querySelectorAll('#notificationDropdown a[data-notification-id]');
        notificationLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                const notificationId = this.getAttribute('data-notification-id');
                if (notificationId) {
                    handleNotificationClick(notificationId, e);
                }
            });
        });
    }
});

// Function to handle notification clicks
function handleNotificationClick(notificationId, event) {
    const targetUrl = event.currentTarget.getAttribute('href');
    
    // Don't follow the link directly if it's a task link
    if (targetUrl && targetUrl !== '#') {
        event.preventDefault();
        
        // Make AJAX request to mark notification as read
        fetch('mark_notification_read.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'notification_id=' + notificationId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redirect to the task view page
                window.location.href = targetUrl;
            } else {
                // Just refresh the page if something went wrong
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
            // Still redirect even if marking as read failed
            window.location.href = targetUrl;
        });
    } else {
        // For non-task notifications, just mark as read and refresh
        fetch('mark_notification_read.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'notification_id=' + notificationId
        }).then(() => {
            location.reload();
        });
    }
} 