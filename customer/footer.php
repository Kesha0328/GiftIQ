
<footer class="footer">
  <p>&copy; 2025 Mad Smile – Because every smile deserves a gift.</p>

  <div class="social-icons">
    <a href="mailto:madsmileee@gmail.com" target="_blank" title="Email"><i class="fas fa-envelope"></i></a>
    <a href="https://github.com/Kesha0328" target="_blank" title="GitHub"><i class="fab fa-github"></i></a>
    <a href="https://www.instagram.com/mad_smileee" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>
  </div>
</footer>

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<style>
:root {
  --accent-pink: #efd8d6;
  --accent-gold: #ffe6b3;
  --shadow: 0 4px 24px rgba(239,216,214,0.25);
}

html, body {
  height: 100%;
  margin: 0;
  padding: 0;
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #fff8f6, #ffeecb);
  display: flex;
  flex-direction: column;
}
main {
  flex: 1;
}


.footer {
  margin-top: auto;
  text-align: center;
  padding: 2.5rem 0 1.5rem 0;
  background: #fff;
  color: #e2a6a4;
  font-size: 1.15rem;
  box-shadow: 0 4px 24px rgba(239, 216, 214, 0.25);
  border-radius: 0 0 25px 25px;
  animation: fadeIn 1.5s;
}

.footer p {
  margin: 0;
  font-weight: 500;
  letter-spacing: 0.5px;
}

.footer .social-icons {
  margin-top: 1.4rem;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1.5rem;
}

.footer .social-icons a {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: linear-gradient(135deg, #efd8d6, #ffe6b3);
  color: #fff;
  font-size: 1.5rem;
  box-shadow: 0 3px 12px rgba(239, 216, 214, 0.6);
  text-decoration: none;
  transition: all 0.3s ease;
}

.footer .social-icons a:hover {
  box-shadow: 0 6px 16px rgba(239, 216, 214, 0.8);
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

</style>
