document.addEventListener('DOMContentLoaded', function() {
    const notificationButton = document.getElementById('notificationButton');
    const notificationDropdown = document.getElementById('notificationDropdown');
    
    // Toggle notification dropdown
    notificationButton.addEventListener('click', function(e) {
        e.stopPropagation();
        notificationDropdown.classList.toggle('hidden');
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!notificationDropdown.contains(e.target) && e.target !== notificationButton) {
            notificationDropdown.classList.add('hidden');
        }
    });
    
    // Prevent dropdown from closing when clicking inside it
    notificationDropdown.addEventListener('click', function(e) {
        e.stopPropagation();
    });
}); 