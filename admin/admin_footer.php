  <footer style="padding:18px 32px; color:var(--muted); font-size:13px;">
    <div style="text-align:center">© <?= date('Y') ?> Mad Smile — GiftIQ Admin</div>
  </footer>
</div> <!-- end wrapper -->

<!-- JS (toggle & Chart) -->
<script>
(function(){
  const sidebar = document.getElementById('sidebar');
  const toggle = document.getElementById('toggleSidebar');

  if (localStorage.getItem('sidebar_collapsed') === '1') {
    sidebar.classList.add('collapsed');
  }

  toggle.addEventListener('click', () => {
    sidebar.classList.toggle('collapsed');

    if (window.innerWidth <= 900) {
      sidebar.classList.toggle('show');
    }
    localStorage.setItem('sidebar_collapsed', sidebar.classList.contains('collapsed') ? '1' : '0');
  });

  document.addEventListener('click', (e)=>{
    if(window.innerWidth <= 900 && !sidebar.contains(e.target) && !toggle.contains(e.target)){
      sidebar.classList.remove('show');
    }
  });
})();
</script>
</body>
</html>
