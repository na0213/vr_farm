<x-top-layout>
    <x-slot name="title">牧場のこだわりと、おいしい理由|放牧・平飼いってなに?</x-slot>
    <x-slot name="metaDescription">放牧、平飼い、牧草で育てる。牧場ごとの育て方は、卵やミルク、お肉の味にどう表れるのでしょう?選び方のコツまで、5分でやさしく紹介します。</x-slot>

    <div class="container mx-auto px-4 py-12 max-w-3xl leading-relaxed text-stone-700">
        <div class="note-title reveal" data-animate>
            <p class="wavy-underline">おいしいには、理由がある</p>
        </div>

        <p class="mt-8 reveal" data-animate>
            同じ「卵」や「牛乳」でも、牧場によって味わいはぜんぜん違います。<br><br>
            その違いをつくっているのは、<strong>どんな場所で、何を食べて、どんなふうに育ったか</strong>。<br>
            牧場の人たちが日々続けている、ちいさなこだわりの積み重ねです。
        </p>

        <section class="reveal" data-animate>
        <h2 class="text-xl font-bold text-stone-800 mt-12 mb-4">知っておきたい、3つのキーワード</h2>
        <ol class="list-decimal list-inside mt-4 space-y-4 tint-box p-6">
            <li>
                <strong>放牧</strong><br>
                <span class="block mt-1 ml-5">牛や豚が屋外の草地で過ごす育て方。広い場所を歩き回り、季節の草を食べます。ミルクの風味や色あいに、季節ごとの違いが出ることもあります。</span>
            </li>
            <li>
                <strong>平飼い</strong><br>
                <span class="block mt-1 ml-5">ニワトリが鶏舎の床の上を、自由に歩き回れる育て方。砂浴びをしたり、止まり木にとまったり。のびのび育った鶏の卵として人気があります。</span>
            </li>
            <li>
                <strong>グラスフェッド(牧草で育つ)</strong><br>
                <span class="block mt-1 ml-5">エサの中心が、穀物ではなく牧草であること。牛乳やバター、チーズに、草の香りやコクが感じられると言われています。</span>
            </li>
        </ol>

        </section>

        <section class="reveal" data-animate>
        <h2 class="text-xl font-bold text-stone-800 mt-12 mb-4">育て方は、味にどう表れるの?</h2>
        <p>
            味を決めるのは、飼い方だけではありません。エサや品種、鮮度、つくり手の手間ひま。すべてが重なって、その牧場だけの味になります。<br><br>
            ただ、のびのびと育てている牧場は、エサの選び方や毎日の世話にも、しっかり目が行き届いていることが多いもの。<br>
            そのこだわりが、<strong>「ここでしか出せない味」</strong>になって、食卓に届きます。
        </p>

        </section>

        <section class="reveal" data-animate>
        <h2 class="text-xl font-bold text-stone-800 mt-12 mb-4">こだわりの見つけ方</h2>
        <ul class="list-disc list-inside mt-4 space-y-2">
            <li><strong>パッケージの表示を見る</strong> — 卵なら「平飼い」「放し飼い」、乳製品なら「放牧」「グラスフェッド」などが目印です</li>
            <li><strong>牧場のサイトや紹介を読む</strong> — どんな場所で、何を食べさせているか、書いてあることが多いです</li>
            <li><strong>つくり手に聞いてみる</strong> — これらの言葉には、はっきりした共通ルールがないものもあります。気になったら、牧場に直接たずねるのがいちばん確実です</li>
        </ul>

        </section>

        <section class="reveal" data-animate>
        <h2 class="text-xl font-bold text-stone-800 mt-12 mb-4">今日からできる、3つのこと</h2>
        <ul class="list-disc list-inside mt-4 space-y-2">
            <li><strong>牧場を知る</strong> — どんな人が、どんな風景の中で育てているのか、のぞいてみる</li>
            <li><strong>おいしく食べる</strong> — お取り寄せで、牧場の味を食べくらべてみる</li>
            <li><strong>足を運ぶ</strong> — 気に入った牧場へ、会いに行ってみる</li>
        </ul>

        </section>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-12 reveal grid-stretch" data-animate>
            <a href="{{ route('farm.index') }}" class="btn-butter block text-center">牧場を探してみる</a>
            <a href="{{ route('products.index') }}" class="btn-pasture block text-center">お取り寄せを見る</a>
        </div>
    </div>
</x-top-layout>
