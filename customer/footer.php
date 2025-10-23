<!-- footer.php -->
<footer class="footer">
  <p>&copy; 2025 <strong>Mad Smile</strong> – Because every smile deserves a gift.</p>

  <div class="social-icons">
    <a href="mailto:madsmileee@gmail.com" target="_blank" title="Email"><i class="fas fa-envelope"></i></a>
    <a href="https://github.com/Kesha0328" target="_blank" title="GitHub"><i class="fab fa-github"></i></a>
    <a href="https://www.instagram.com/mad_smileee" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>
  </div>
</footer>

<!-- Font Awesome Icons -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<!-- Footer CSS -->
<style>
:root {
  --accent-pink: #efd8d6;
  --accent-gold: #ffe6b3;
  --shadow: 0 4px 24px rgba(239,216,214,0.25);
}

body {
  margin: 0;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  font-family: 'Poppins', sans-serif;
}

/* Footer */
.footer {
  margin-top: auto;
  text-align: center;
  padding: 2rem 0 1rem 0;
  background: #fff;
  color: #e2a6a4;
  font-size: 1.1rem;
  box-shadow: var(--shadow);
  border-radius: 0 0 20px 20px;
  animation: fadeIn 1.5s;
}

.footer p {
  margin: 0;
  font-weight: 500;
  letter-spacing: 0.5px;
}

.footer .social-icons {
  margin: 1.2rem 0 0 0;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1.3rem;
  flex-wrap: wrap;
}

.footer .social-icons a {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 42px;
  height: 42px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--accent-pink), var(--accent-gold));
  color: #fff;
  font-size: 1.4rem;
  transition: transform 0.25s, box-shadow 0.25s, background 0.25s;
  box-shadow: 0 2px 10px rgba(239,216,214,0.6);
  text-decoration: none;
}

.footer .social-icons a:hover {
  transform: scale(1.12) rotate(-4deg);
  background: linear-gradient(135deg, var(--accent-gold), var(--accent-pink));
  box-shadow: 0 4px 16px rgba(239,216,214,0.6);
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

/* Responsive */
@media (max-width: 600px) {
  .footer {
    padding: 1.3rem 1rem;
    font-size: 1rem;
    border-radius: 0;
  }
  .footer p {
    font-size: 0.95rem;
    line-height: 1.4;
  }
  .footer .social-icons {
    gap: 0.8rem;
  }
  .footer .social-icons a {
    width: 40px;
    height: 40px;
    font-size: 1.2rem;
  }
}
</style>
