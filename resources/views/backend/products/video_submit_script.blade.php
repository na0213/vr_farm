{{-- 動画を選んで送るときは変換に少し時間がかかるので、ボタンを押せなくして知らせる(撮影一覧の登録・編集で共通) --}}
<script>
    document.querySelectorAll('input[name="product_video"]').forEach(function (input) {
        input.form.addEventListener('submit', function () {
            if (!input.files.length) return;
            const button = input.form.querySelector('button[type="submit"]');
            button.disabled = true;
            button.textContent = '動画を変換しています…';
        });
    });
</script>
