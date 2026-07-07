<x-top-layout>
    <x-slot name="title">アニマルウェルフェアとは?5分でわかる「動物にやさしい」の意味</x-slot>
    <x-slot name="metaDescription">アニマルウェルフェアとは、動物がストレス少なく健康に暮らせるように配慮する考え方。5つの自由、おいしさとの関係、卵やお肉の選び方まで、5分でやさしく解説します。</x-slot>

    <div class="container mx-auto px-4 py-12 max-w-3xl leading-relaxed text-stone-700">
        <div class="note-title">
            <p class="wavy-underline">アニマルウェルフェアって、なに?</p>
        </div>

        <p class="mt-8">
            むずかしそうな言葉ですが、意味はシンプルです。<br><br>
            <strong>アニマルウェルフェア(動物福祉)= 家畜が生きている間、ストレスが少なく、健康に暮らせるように配慮すること。</strong><br><br>
            「かわいそうだから」という感情論ではなく、ニワトリや牛や豚が<strong>その動物らしく過ごせる環境</strong>を整えようという、世界共通の考え方です。
        </p>

        <h2 class="text-xl font-bold text-stone-800 mt-12 mb-4">世界基準の「5つの自由」</h2>
        <p>アニマルウェルフェアの土台になっているのが、1960年代のイギリスで生まれた「5つの自由」です。</p>
        <ol class="list-decimal list-inside mt-4 space-y-2 bg-orange-50 rounded-lg p-6">
            <li>飢えと渇きからの自由(十分なエサと水)</li>
            <li>不快からの自由(快適な環境)</li>
            <li>痛み・傷害・病気からの自由</li>
            <li>恐怖や抑圧からの自由</li>
            <li><strong>本来の行動がとれる自由</strong>(砂浴び、羽ばたき、放牧など)</li>
        </ol>
        <p class="mt-4">
            たとえばニワトリなら、砂浴びをしたり、止まり木にとまったり。牛なら、草の上を歩いて反芻したり。<br>
            「その動物が本来やりたいこと」を我慢させない飼い方が、アニマルウェルフェアです。
        </p>

        <h2 class="text-xl font-bold text-stone-800 mt-12 mb-4">おいしさと、どう関係あるの?</h2>
        <p>
            ストレスの少ない環境で育つ動物は、健康に育ちやすくなります。健康であれば、薬に頼る場面も減らしやすい。<br><br>
            そして、放牧地の牧草を食べて育った牛のミルク、太陽を浴びて駆け回るニワトリの卵には、<strong>その牧場でしか出せない味</strong>があります。<br><br>
            「動物にやさしい」は、まわりまわって「食べる私たちにもやさしい」。<br>
            このサイトが牧場を紹介しているのは、そんな<strong>おいしい理由のある食べもの</strong>に出会ってほしいからです。
        </p>

        <h2 class="text-xl font-bold text-stone-800 mt-12 mb-4">世界と日本の今</h2>
        <p>
            EUでは2012年から、従来型のケージでの採卵鶏の飼育が禁止されています。世界の食品企業でも「ケージフリー(ケージを使わない)宣言」が広がっています。<br><br>
            いっぽう日本では、採卵鶏の9割以上がケージ飼育。まだまだ「平飼い」「放牧」は少数派ですが、だからこそ、選んで買う人の一票が牧場の応援になります。
        </p>

        <h2 class="text-xl font-bold text-stone-800 mt-12 mb-4">今日からできる、3つのこと</h2>
        <ul class="list-disc list-inside mt-4 space-y-2">
            <li><strong>パッケージを見て選ぶ</strong> — 卵なら「平飼い」「放し飼い」の表示が目印です</li>
            <li><strong>牧場を知る</strong> — どんな人がどんな環境で育てているか、のぞいてみる</li>
            <li><strong>おいしく食べて応援する</strong> — お取り寄せは、いちばんおいしい応援です</li>
        </ul>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-12">
            <a href="{{ route('farm.index') }}" class="block text-center text-white bg-yellow-500 hover:bg-yellow-600 rounded-lg px-6 py-4 font-bold">牧場を探してみる</a>
            <a href="{{ route('products.index') }}" class="block text-center text-white bg-orange-400 hover:bg-orange-500 rounded-lg px-6 py-4 font-bold">お取り寄せを見る</a>
        </div>
    </div>
</x-top-layout>
