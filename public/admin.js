document.addEventListener('DOMContentLoaded', function() {
    var icon = document.querySelector('.kw-page-admin-bar__toggle-icon');
    var content = document.querySelector('.kw-page-admin-bar__content');

    icon.addEventListener('click', function(event) {
        event.preventDefault();
        if (content.style.display === 'none' || content.style.display === '') {
            content.style.display = 'block';
        } else {
            content.style.display = 'none';
        }
    });

    // Optional: Initially hide the content
    content.style.display = 'none';
});