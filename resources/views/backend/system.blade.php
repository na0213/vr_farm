<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            サイトのしくみ
        </h2>
    </x-slot>

    {{--
        2026年10月から: 編集は Mac、公開は Cloudflare(静的サイト)。
        このリポジトリは公開なので、ここには秘密の値(鍵・パスワード・アカウントID)を書かない。
    --}}
    <style>
        .sys { color: var(--ink-900); }
        .sys-lead { color: var(--ink-700); }
        .sys-section { margin-top: 3rem; }
        .sys-h { display: flex; align-items: center; gap: .75rem; font-family: var(--font-heading); font-size: 1.35rem; font-weight: 700; }
        .sys-h::before { content: ""; width: 5px; height: 1.6em; border-radius: 3px; background: var(--butter-deep); }
        .sys-h small { font-size: .85rem; font-weight: 500; color: var(--ink-500); }
        .sys-card { background: var(--milk); border: 1px solid var(--line); border-radius: 18px; }
        .sys-num { font-family: var(--font-heading); font-size: 2.4rem; font-weight: 700; color: var(--butter-deep); line-height: 1.1; }
        .sys-diagram { overflow-x: auto; }
        .sys-diagram svg { min-width: 880px; width: 100%; height: auto; font-family: inherit; }
        .sys-diagram .t { fill: var(--ink-900); font-size: 17px; font-weight: 700; }
        .sys-diagram .s { fill: var(--ink-700); font-size: 13px; }
        .sys-diagram .n { fill: var(--ink-500); font-size: 12px; }
        .sys-diagram .lbl { fill: #8a6414; font-size: 12.5px; font-weight: 700; }
        .sys-diagram .arrow { stroke: #a07a2c; stroke-width: 2; fill: none; }
        .sys-table { width: 100%; border-collapse: collapse; font-size: .9rem; }
        .sys-table th, .sys-table td { padding: .75rem .9rem; border-bottom: 1px solid var(--line); text-align: left; vertical-align: top; }
        .sys-table th { font-weight: 700; color: var(--ink-700); background: #fbf0da; }
        /* top.css の .grid(300px の自動列)を避けるため、このページ専用の並べ方を使う */
        .sys-grid { display: grid; gap: 1rem; }
        .sys-grid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .sys-grid-steps, .sys-grid-2 { grid-template-columns: minmax(0, 1fr); }
        .sys-grid-2 { gap: 1.5rem; }
        @media (min-width: 768px) { .sys-grid-steps { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
        @media (min-width: 1024px) {
            .sys-grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
            .sys-grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        .sys-note { background: #fff6e2; border-left: 4px solid var(--butter-deep); border-radius: 10px; }
    </style>

    <div class="sys py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <p class="sys-lead text-sm">編集はこの Mac、公開は Cloudflare。サーバーを持たない「静的サイト」です(2026年10月から)。</p>

            {{-- 数字 --}}
            <div class="sys-grid sys-grid-4 mt-6">
                @foreach ([
                    ['n' => $stats['pages'], 'label' => '書き出すページ'],
                    ['n' => $stats['farms'], 'label' => '公開中の牧場'],
                    ['n' => $stats['articles'], 'label' => '公開中の記事'],
                    ['n' => $stats['images'], 'label' => '画像ファイル'],
                ] as $stat)
                    <div class="sys-card p-5 text-center">
                        <p class="sys-num">{{ $stat['n'] }}</p>
                        <p class="text-sm mt-1" style="color: var(--ink-700)">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
            <p class="text-sm mt-3" style="color: var(--ink-500)">
                最後の書き出し: {{ $lastExport ? $lastExport->timezone('Asia/Tokyo')->format('Y/m/d H:i') : 'まだありません' }}
                ・最後のバックアップ: {{ $lastBackup ? $lastBackup->timezone('Asia/Tokyo')->format('Y/m/d H:i') : 'まだありません' }}
            </p>

            {{-- 1. 全体の構成 --}}
            <section class="sys-section">
                <h3 class="sys-h">全体の構成 <small>だれが何を担当しているか</small></h3>
                <div class="sys-card sys-diagram mt-4 p-4">
                    <svg viewBox="0 0 1200 690" role="img" aria-labelledby="sys-diagram-title sys-diagram-desc">
                        <title id="sys-diagram-title">FARM360 の構成図</title>
                        <desc id="sys-diagram-desc">Mac の管理画面とデータベースから書き出したページを Cloudflare に送り、訪問者は Cloudflare から見る。問い合わせは Cloudflare から Gmail に届く。ドメインはお名前.com で契約し、DNS は Cloudflare が担当する。データは iCloud Drive にバックアップする。</desc>
                        <defs>
                            <marker id="sys-arrow" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse">
                                <path d="M0,0 L10,5 L0,10 z" fill="#a07a2c"/>
                            </marker>
                        </defs>

                        {{-- 訪問者 --}}
                        <rect x="20" y="20" width="360" height="100" rx="14" fill="#fff" stroke="#e6d9c6"/>
                        <rect x="20" y="20" width="6" height="100" rx="3" fill="#c0583a"/>
                        <text x="44" y="54" class="t">訪問者のブラウザ</text>
                        <text x="44" y="80" class="s">スマホ・パソコンで www.farm360.jp を開く</text>
                        <text x="44" y="102" class="s">問い合わせフォームから送信する</text>

                        {{-- Mac --}}
                        <rect x="20" y="160" width="360" height="370" rx="18" fill="#f3f8f0" stroke="#4f7a46" stroke-width="2"/>
                        <text x="40" y="194" class="t">この Mac</text>
                        <text x="130" y="194" class="n">データの正本はここだけ</text>
                        <rect x="40" y="210" width="320" height="66" rx="10" fill="#fff" stroke="#cfe0c8"/>
                        <text x="56" y="238" class="t" style="font-size:15px">管理画面(この画面)</text>
                        <text x="56" y="261" class="s">php artisan serve で起動・Mac の中だけで動く</text>
                        <rect x="40" y="288" width="320" height="86" rx="10" fill="#fff" stroke="#cfe0c8"/>
                        <text x="56" y="316" class="t" style="font-size:15px">データベース(MySQL「farm」)</text>
                        <text x="56" y="339" class="s">牧場・動物・商品・販売店・記事・</text>
                        <text x="56" y="359" class="s">キーワード・種類・管理画面のログイン</text>
                        <rect x="40" y="386" width="320" height="66" rx="10" fill="#fff" stroke="#cfe0c8"/>
                        <text x="56" y="414" class="t" style="font-size:15px">画像フォルダ(public/uploads)</text>
                        <text x="56" y="437" class="s">牧場の写真・VR パノラマ・商品の写真</text>
                        <rect x="40" y="464" width="320" height="50" rx="10" fill="none" stroke="#4f7a46" stroke-dasharray="5 4"/>
                        <text x="56" y="494" class="s" style="font-weight:700">公開: npm run site:publish(Claude に頼む)</text>

                        {{-- iCloud --}}
                        <rect x="20" y="580" width="360" height="92" rx="14" fill="#f2f6fb" stroke="#7d9cc0" stroke-width="1.5"/>
                        <text x="40" y="612" class="t">iCloud Drive「FARM360-backup」</text>
                        <text x="40" y="636" class="s">データベース(30回分)と画像の控え</text>
                        <text x="40" y="656" class="s">公開のたびに自動で保存される</text>
                        <path d="M200,530 L200,576" class="arrow" marker-end="url(#sys-arrow)"/>
                        <text x="212" y="560" class="lbl">④ バックアップ</text>

                        {{-- Cloudflare --}}
                        <rect x="470" y="20" width="430" height="560" rx="18" fill="#fff6e2" stroke="#e9b949" stroke-width="2"/>
                        <text x="490" y="54" class="t">Cloudflare(無料プラン)</text>
                        <text x="490" y="76" class="n">配信・DNS・メール・ロボット対策をまとめて担当</text>

                        <rect x="490" y="92" width="390" height="128" rx="10" fill="#fff" stroke="#f0dcae"/>
                        <text x="506" y="120" class="t" style="font-size:15px">DNS(farm360.jp の住所録)</text>
                        <text x="506" y="146" class="s">www.farm360.jp → Worker(オレンジの雲)</text>
                        <text x="506" y="168" class="s">farm360.jp(www なし)→ www へ転送</text>
                        <text x="506" y="190" class="s">メール(MX)→ Email Routing</text>
                        <text x="506" y="210" class="n">ほかに Search Console の確認用 TXT</text>

                        <rect x="490" y="240" width="390" height="150" rx="10" fill="#fff" stroke="#f0dcae"/>
                        <text x="506" y="268" class="t" style="font-size:15px">Workers「farm360」</text>
                        <text x="506" y="294" class="s">公開ページ: 書き出した HTML・CSS・画像</text>
                        <text x="506" y="316" class="s">/api/contact: 問い合わせの受け付け</text>
                        <text x="506" y="338" class="s">旧URL /animal-welfare → /kodawari(301)</text>
                        <text x="506" y="366" class="n">データベースは使わない。見る人が来ても処理はほぼゼロ</text>

                        <rect x="490" y="440" width="185" height="120" rx="10" fill="#fff" stroke="#f0dcae"/>
                        <text x="506" y="468" class="t" style="font-size:15px">Turnstile</text>
                        <text x="506" y="492" class="s">ロボット対策</text>
                        <text x="506" y="514" class="s">秘密キーは Worker に</text>
                        <text x="506" y="536" class="s">登録(画面に出さない)</text>

                        <rect x="695" y="440" width="185" height="120" rx="10" fill="#fff" stroke="#f0dcae"/>
                        <text x="711" y="468" class="t" style="font-size:15px">Email Routing</text>
                        <text x="711" y="492" class="s">form@farm360.jp から</text>
                        <text x="711" y="514" class="s">運営の Gmail へ送る</text>
                        <text x="711" y="536" class="s">info@ も Gmail へ転送</text>

                        <path d="M582,390 L582,436" class="arrow" marker-end="url(#sys-arrow)"/>
                        <text x="590" y="420" class="lbl">① 人か確認</text>
                        <path d="M787,390 L787,436" class="arrow" marker-end="url(#sys-arrow)"/>
                        <text x="795" y="420" class="lbl">② メール</text>

                        {{-- 訪問者 → Worker --}}
                        <path d="M380,70 L425,70 L425,290 L486,290" class="arrow" marker-end="url(#sys-arrow)"/>
                        <rect x="384" y="150" width="82" height="22" rx="11" fill="#fff"/>
                        <text x="425" y="166" class="lbl" text-anchor="middle">ページを見る</text>

                        {{-- Mac → Worker --}}
                        <path d="M380,489 L440,489 L440,350 L486,350" class="arrow" marker-end="url(#sys-arrow)"/>
                        <rect x="392" y="400" width="96" height="40" rx="11" fill="#fff"/>
                        <text x="440" y="416" class="lbl" text-anchor="middle">①〜③ 公開</text>
                        <text x="440" y="433" class="n" text-anchor="middle">書き出して送る</text>

                        {{-- お名前.com --}}
                        <rect x="960" y="20" width="220" height="168" rx="14" fill="#fff" stroke="#7d6d5f" stroke-width="1.5"/>
                        <text x="978" y="54" class="t">お名前.com</text>
                        <text x="978" y="80" class="s">ドメインの契約だけ</text>
                        <text x="978" y="102" class="s">farm360.jp を登録・年1回更新</text>
                        <text x="978" y="128" class="s">ネームサーバーに</text>
                        <text x="978" y="150" class="s">Cloudflare の2つを指定</text>
                        <text x="978" y="174" class="n">更新を忘れるとすべて止まる</text>
                        <path d="M960,140 L884,140" class="arrow" marker-end="url(#sys-arrow)"/>

                        {{-- Gmail --}}
                        <rect x="960" y="440" width="220" height="120" rx="14" fill="#fff" stroke="#7d6d5f" stroke-width="1.5"/>
                        <text x="978" y="474" class="t">Gmail</text>
                        <text x="978" y="498" class="s">farm360.info@gmail.com</text>
                        <text x="978" y="520" class="s">問い合わせが届く</text>
                        <text x="978" y="542" class="s">返信すると送った人に届く</text>
                        <path d="M880,500 L956,500" class="arrow" marker-end="url(#sys-arrow)"/>
                    </svg>
                </div>
            </section>

            {{-- 2. 保存している場所 --}}
            <section class="sys-section">
                <h3 class="sys-h">何をどこに保存しているか</h3>
                <div class="sys-card mt-4 overflow-x-auto">
                    <table class="sys-table">
                        <thead>
                            <tr><th style="width:26%">置き場所</th><th>中身</th><th style="width:30%">なくなったら</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>この Mac</strong><br>データベース「farm」</td>
                                <td>牧場・動物・商品・販売店・記事・キーワード・種類などの文字のデータ。<strong>これが正本</strong></td>
                                <td>iCloud Drive の控えから戻す</td>
                            </tr>
                            <tr>
                                <td><strong>この Mac</strong><br>public/uploads</td>
                                <td>管理画面で上げた写真・VR 画像の元ファイル</td>
                                <td>iCloud Drive の控えから戻す</td>
                            </tr>
                            <tr>
                                <td><strong>iCloud Drive</strong><br>FARM360-backup</td>
                                <td>上の2つの控え(データベースは新しい順に30回分)。公開のたびに自動で保存</td>
                                <td>Mac が無事なら、次の公開でまた作られる</td>
                            </tr>
                            <tr>
                                <td><strong>Cloudflare</strong><br>Workers「farm360」</td>
                                <td>公開用のコピー(書き出した HTML・CSS・画像)と、問い合わせを受け付ける小さなプログラム</td>
                                <td>Mac からもう一度公開すれば元どおり</td>
                            </tr>
                            <tr>
                                <td><strong>Cloudflare</strong><br>DNS・Email Routing・Turnstile</td>
                                <td>www とメールの行き先の設定、問い合わせのロボット対策の鍵</td>
                                <td>Cloudflare の画面で設定し直す</td>
                            </tr>
                            <tr>
                                <td><strong>お名前.com</strong></td>
                                <td>ドメイン farm360.jp の契約(名前を使う権利)</td>
                                <td>更新を忘れると、サイトもメールも止まり、取り戻せないこともある。<strong>期限前に必ず更新</strong></td>
                            </tr>
                            <tr>
                                <td><strong>GitHub</strong><br>na0213/vr_farm(公開)</td>
                                <td>プログラムだけ。データ・パスワード・鍵は入れない</td>
                                <td>この Mac にも同じものがある</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- 3. 公開の流れ --}}
            <section class="sys-section">
                <h3 class="sys-h">公開の流れ</h3>
                <div class="sys-note mt-4 p-4 text-sm">
                    <strong>管理画面で保存しただけでは、公開サイトは変わりません。</strong>
                    編集が終わったら、Claude に「公開して」と頼みます(下書きの記事・非公開の牧場は書き出されません)。
                </div>
                <ol class="sys-grid sys-grid-steps mt-4">
                    @foreach ([
                        ['① ビルド', '見た目(CSS・JS)をまとめる', 'vite build'],
                        ['② 書き出し', '公開ページを HTML ファイルにする(dist/)', 'php artisan site:export'],
                        ['③ 送る', 'Cloudflare にアップロード。数十秒で www.farm360.jp に反映', 'wrangler deploy'],
                        ['④ 控え', 'データベースと画像を iCloud Drive に保存', 'php artisan site:backup'],
                    ] as [$title, $text, $command])
                        <li class="sys-card sys-step p-4">
                            <p class="font-bold">{{ $title }}</p>
                            <p class="text-sm mt-1" style="color: var(--ink-700)">{{ $text }}</p>
                            <p class="text-xs mt-2 font-mono" style="color: var(--ink-500)">{{ $command }}</p>
                        </li>
                    @endforeach
                </ol>
            </section>

            {{-- 4. 問い合わせとドメイン --}}
            <div class="sys-grid sys-grid-2 sys-section">
                <section>
                    <h3 class="sys-h">問い合わせの流れ</h3>
                    <ol class="sys-card mt-4 p-5 text-sm space-y-2 list-decimal list-inside">
                        <li>入力 → 確認 → 完了は、同じページの中で切り替わる</li>
                        <li>送信の前に Turnstile が「人かどうか」を確かめる</li>
                        <li>Cloudflare の Worker が受け取り、Email Routing で Gmail に送る</li>
                        <li>Gmail で返信すると、送った人に届く(返信先が送った人になっている)</li>
                        <li>送った人への控えメールは送らない。完了画面に内容を表示する</li>
                    </ol>
                </section>
                <section>
                    <h3 class="sys-h">ドメインのしくみ</h3>
                    <div class="sys-card mt-4 p-5 text-sm space-y-2">
                        <p><strong>お名前.com</strong>: 「farm360.jp という名前を使う権利」の契約と更新だけをしている。</p>
                        <p><strong>Cloudflare</strong>: その名前の「住所録」(DNS)。www をどこに届けるか、メールをどこに届けるかを決めている。</p>
                        <p>お名前.com の「ネームサーバー」に Cloudflare の2つを登録して、つないでいる。</p>
                        <p style="color: var(--ink-500)">ネームサーバーを書き換えなければ、ドメインの契約先を変えても Cloudflare の設定はそのまま使える。</p>
                    </div>
                </section>
            </div>

            {{-- 5. 移行の記録 --}}
            <section class="sys-section">
                <h3 class="sys-h">以前の構成 <small>2026年10月2日に今の構成へ移行</small></h3>
                <div class="sys-card mt-4 p-5 text-sm" style="color: var(--ink-700)">
                    Heroku(PHP のサーバー)+ JawsDB(データベース)+ AWS S3(画像)+ お名前.com の DNS。
                    サーバーを毎月借りていたため費用がかかっていた。データと画像はこの Mac に移し、これらは停止・解約する。
                </div>
            </section>

            <p class="mt-10 text-sm">
                <a href="{{ route('admin.dashboard') }}" class="underline" style="color: var(--ink-700)">管理画面のトップへ戻る</a>
            </p>
        </div>
    </div>
</x-admin-layout>
