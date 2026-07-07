// 記事編集用エディタ(TinyMCE セルフホスト・GPL版)
// CKEditor 4(サポート終了・脆弱性あり)の置き換え
import tinymce from 'tinymce/tinymce';

// テーマ・アイコン・スキンをバンドルに含める(CDN不要)
import 'tinymce/models/dom/model';
import 'tinymce/themes/silver';
import 'tinymce/icons/default';
import 'tinymce/skins/ui/oxide/skin.js';
import 'tinymce/skins/ui/oxide/content.js';
import 'tinymce/skins/content/default/content.js';

// 使用プラグイン
import 'tinymce/plugins/lists';
import 'tinymce/plugins/link';
import 'tinymce/plugins/image';
import 'tinymce/plugins/table';
import 'tinymce/plugins/charmap';
import 'tinymce/plugins/fullscreen';

// 日本語UI
import 'tinymce-i18n/langs8/ja.js';

// 本文中の画像をS3へアップロードする(noteのように文中に画像を挿入できる)
const uploadImage = (blobInfo, progress) => new Promise((resolve, reject) => {
    const formData = new FormData();
    formData.append('file', blobInfo.blob(), blobInfo.filename());

    fetch('/admin/article/upload-image', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: formData,
    })
        .then((res) => {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then((json) => resolve(json.location))
        .catch((err) => reject('画像のアップロードに失敗しました: ' + err.message));
});

tinymce.init({
    selector: 'textarea#editor1',
    license_key: 'gpl',
    language: 'ja',
    height: 500,
    menubar: false,
    plugins: 'lists link image table charmap fullscreen',
    toolbar: [
        'undo redo | blocks | bold italic underline strikethrough removeformat',
        'bullist numlist outdent indent | blockquote | link image table hr charmap | fullscreen',
    ].join(' | '),
    // 画像アップロード(ツールバーの画像ボタン・ドラッグ&ドロップ・ペーストに対応)
    images_upload_handler: uploadImage,
    automatic_uploads: true,
    paste_data_images: true,
    image_caption: true,
    // 本文中の画像をレスポンシブにする
    content_style: 'img { max-width: 100%; height: auto; }',
    // 既存記事のURL(S3画像など)を書き換えない
    convert_urls: false,
    branding: false,
    promotion: false,
});
