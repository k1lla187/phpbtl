    </main>
    <footer class="text-center py-3" style="color: #64748b; font-size: 12px; border-top: 1px solid #e2e8f0; margin-top: 20px;">
        <p class="mb-0">Copyright &copy; <?= date('Y') ?> <strong style="color: #d4af37;">UNISCORE</strong> - Hệ Thống Quản Lý Điểm Sinh Viên</p>
    </footer>
</div>
<script>
(function() {
    var d = document.getElementById('userDropdown');
    var t = document.getElementById('userDropdownTrigger');
    if (d && t) {
        t.addEventListener('click', function(e) { e.stopPropagation(); d.classList.toggle('is-open'); });
        document.addEventListener('click', function() { d.classList.remove('is-open'); });
    }
})();
</script>
</body>
</html>
