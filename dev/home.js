document.addEventListener('DOMContentLoaded', () => {
    const profileBtn = document.getElementById('profileBtn');
    const profileCard = document.getElementById('profileCard');
    const closeProfile = document.getElementById('closeProfile');

    profileBtn.addEventListener('click', () => {
        profileCard.classList.add('open');
    });

    closeProfile.addEventListener('click', () => {
        profileCard.classList.remove('open');
    });

    // Responsive navbar toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');

    menuToggle.addEventListener('click', () => {
        navLinks.classList.toggle('active');
    });
});
document.addEventListener("DOMContentLoaded", () => {
    const profileCircle = document.getElementById("profileCircle");
    const profileCard = document.getElementById("profileCard");

    // Toggle profile card
    profileCircle.addEventListener("click", () => {
        profileCard.classList.toggle("active");
    });
});

// Preview selected image before upload
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function(){
        document.getElementById("profilePreview").src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
}
