{{-- Kayıtlı tasarım tercihlerini ilk boyamadan önce uygular (FOUC önler). --}}
<script>
    (function () {
        try {
            var raw = localStorage.getItem('waaiy-design');
            if (!raw) return;
            var d = JSON.parse(raw);
            var root = document.documentElement;
            if (d.header) root.dataset.header = d.header;
            if (d.blogLayout) root.dataset.blog = d.blogLayout;
            if (d.sector) root.dataset.sector = d.sector;
            if (d.vars) {
                for (var k in d.vars) {
                    if (Object.prototype.hasOwnProperty.call(d.vars, k)) {
                        root.style.setProperty(k, d.vars[k]);
                    }
                }
            }
        } catch (e) {}
    })();
</script>
