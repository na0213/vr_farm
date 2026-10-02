<x-top-layout>
    <x-slot name="title">運営者情報</x-slot>
    <x-slot name="metaDescription">FARM360を運営するNatomiについて。スーパーで見かけた平飼い卵をきっかけに、牧場を訪ね、飼い方やこだわりを伝えている個人の趣味のサイトです。</x-slot>
    <x-slot name="jsonLd">{!! \App\Services\StructuredData::json(\App\Services\StructuredData::about()) !!}</x-slot>

    <div class="container mx-auto px-4 py-12 max-w-3xl leading-relaxed text-stone-700">
        <div class="note-title reveal" data-animate>
            <p class="wavy-underline">運営者情報</p>
        </div>

        <p class="mt-8 reveal" data-animate>
            FARM360は、<strong>個人の趣味</strong>として運営している、牧場の紹介サイトです。<br>
            牧場を訪ね、飼い方や育て方、エサや環境へのこだわりを、見たまま、知ったままに伝えています。
        </p>

        <section class="reveal" data-animate>
            <h2 class="text-xl font-bold text-stone-800 mt-12 mb-4">はじめたきっかけ</h2>
            <p>
                きっかけは、スーパーで平飼い卵を見かけることが増えたことでした。<br>
                「平飼いではない卵は、どう違うのだろう?」と調べるうちに、動物の飼い方や環境を考える「アニマルウェルフェア」という言葉を知りました。<br><br>
                さらに調べていくと、すべてを平飼いや放牧にするのは、生産者さんにとって簡単なことではない、ということも分かってきました。値段や手に入りやすさとのバランスもあり、考え方は人それぞれです。<br><br>
                そこで、まずは自分の目で見て、知ったことを、訪問記として伝えていこうと思い、趣味として始めたのがこのサイトです。
            </p>
        </section>

        <x-walking-animals />

        <section class="reveal" data-animate>
            <h2 class="text-xl font-bold text-stone-800 mt-12 mb-4">牧場の選び方</h2>
            <p>スーパーで見つけたものや、実際に訪ねた牧場を紹介しています。</p>
        </section>

        <section class="reveal" data-animate>
            <h2 class="text-xl font-bold text-stone-800 mt-12 mb-4">運営者</h2>
            <dl class="tint-box p-6 space-y-3">
                <div class="flex">
                    <dt class="font-bold w-20 sm:w-28 shrink-0">名称</dt>
                    <dd class="flex-1">Natomi</dd>
                </div>
                <div class="flex">
                    <dt class="font-bold w-20 sm:w-28 shrink-0">運営</dt>
                    <dd class="flex-1">個人(趣味)</dd>
                </div>
                <div class="flex">
                    <dt class="font-bold w-20 sm:w-28 shrink-0">メール</dt>
                    <dd class="flex-1 break-all">natomi.work@gmail.com</dd>
                </div>
            </dl>
            <p class="mt-6">掲載内容の誤りや、変更のご連絡は、お問い合わせフォームからお願いします。</p>
        </section>

        <div class="mt-10 reveal grid-stretch" data-animate>
            <a href="{{ route('contact.form') }}" class="btn-butter block text-center sm:inline-block sm:px-10">お問い合わせへ</a>
        </div>
    </div>
</x-top-layout>
