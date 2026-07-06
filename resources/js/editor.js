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
    // 既存記事のURL(S3画像など)を書き換えない
    convert_urls: false,
    branding: false,
    promotion: false,
});
