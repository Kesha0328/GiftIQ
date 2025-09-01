// File: app.js
// ...existing code...
document.addEventListener("DOMContentLoaded", function() {
  const slides = document.querySelectorAll('.hero-slide');
  let current = 0;
  setInterval(() => {
    slides[current].classList.remove('active');
    current = (current + 1) % slides.length;
    slides[current].classList.add('active');
  }, 3500);
});
// ...existing code...


document.addEventListener("DOMContentLoaded", function () {
    const darkToggle = document.querySelector(".dark-toggle");
    const mobileMenuBtn = document.querySelector(".mobile-menu-toggle");
    const mobileMenu = document.querySelector(".mobile-menu");
  
    // Toggle dark mode
    if (darkToggle) {
      darkToggle.addEventListener("click", () => {
        document.body.classList.toggle("dark");
        localStorage.setItem("theme", document.body.classList.contains("dark") ? "dark" : "light");
      });
  
      // Apply saved theme on load
      if (localStorage.getItem("theme") === "dark") {
        document.body.classList.add("dark");
      }
    }
  
    // Toggle mobile nav menu
    if (mobileMenuBtn && mobileMenu) {
      mobileMenuBtn.addEventListener("click", () => {
        mobileMenu.classList.toggle("open");
      });
    }
  
    // Smooth scroll for anchor links
    const anchors = document.querySelectorAll('a[href^="#"]');
    anchors.forEach(anchor => {
      anchor.addEventListener("click", function (e) {
        const targetId = anchor.getAttribute("href").substring(1);
        const target = document.getElementById(targetId);
        if (target) {
          e.preventDefault();
          window.scrollTo({
            top: target.offsetTop,
            behavior: "smooth",
          });
        }
      });
    });
  });