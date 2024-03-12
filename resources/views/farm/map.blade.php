<x-top-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="flex justify-around m-5">
        @foreach ($farms as $farm)
        <a href="{{ route('farm.show', ['id' => $farm->id]) }}">
            <div class="max-w-sm rounded overflow-hidden shadow-lg">
                <img class="w-full" src="../img/a1.JPG" alt="Sunset in the mountains">
                <div class="px-6 py-4">
                <div class="font-bold text-xl mb-2">{{ $farm->farm_name }}</div>
                <p class="text-gray-700 text-base">
                    {{ $farm->prefecture }}
                </p>
                </div>
                <div class="px-6 pt-4 pb-2">
                <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">#photography</span>
                <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">#travel</span>
                <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">#winter</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    <div class="map-container">
        <div class="overlay" style="background: url('{{ asset('../storage/comingsoon.png') }}') no-repeat center center; background-size: cover; opacity: 0.9;"></div>

        <img src="{{ asset('../storage/japanmap.jpg') }}" usemap="#ImageMap" alt="" />
        <map name="ImageMap">
            <div class="hokkaido">
                <area shape="rect" coords="1251,108,1503,339" href="#" alt="" />
            </div>
            <div class="aomori bg-gray">
                <area shape="rect" coords="1252,367,1440,437" alt="" class="bg-gray"/>
            </div>
            <div class="akita">
                <area shape="rect" coords="1250,444,1340,512" alt="" />
            </div>
            <div class="iwate">
                <area shape="rect" coords="1348,444,1443,513" alt="" />
            </div>
            <div class="yamagata">
                <area shape="rect" coords="1249,519,1341,588" alt="" />
            </div>
            <div class="miyagi">
                <area shape="rect" coords="1350,522,1442,589" alt="" />
            </div>
            <div class="fukushima">
                <area shape="rect" coords="1250,595,1441,665" alt="" />
            </div>
            <div class="gunma">
                <area shape="rect" coords="1247,671,1341,741" alt="" />
            </div>
            <div class="tochigi">
                <area shape="rect" coords="1349,671,1441,742" alt="" />
            </div>
            <div class="saitama">
                <area shape="rect" coords="1249,748,1341,816" alt="" />
            </div>
            <div class="ibaraki">
                <area shape="rect" coords="1349,747,1442,815" alt="" />
            </div>
            <div class="tokyo">
                <area shape="rect" coords="1248,823,1340,892" alt="" />
            </div>
            <div class="chiba">
                <area shape="rect" coords="1350,824,1440,969" alt="" />
            </div>
            <div class="kanagawa">
                <area shape="rect" coords="1249,899,1341,968" alt="" />
            </div>
            <div class="ishikawa">
                <area shape="rect" coords="948,594,1042,665" alt="" />
            </div>
            <div class="toyama">
                <area shape="rect" coords="1050,595,1143,666" alt="" />
            </div>
            <div class="nigata">
                <area shape="rect" coords="1149,596,1242,664" alt="" />
            </div>
            <div class="fukui">
                <area shape="rect" coords="950,670,1042,740" alt="" />
            </div>
            <div class="gifu">
                <area shape="rect" coords="1050,670,1143,816" alt="" />
            </div>
            <div class="nagano">
                <area shape="rect" coords="1149,671,1241,817" alt="" />
            </div>
            <div class="aichi">
                <area shape="rect" coords="1050,822,1142,892" alt="" />
            </div>
            <div class="yamanashi">
                <area shape="rect" coords="1150,822,1241,893" alt="" />
            </div>
            <div class="shizuoka">
                <area shape="rect" coords="1151,898,1240,970" alt="" />
            </div>
            <div class="shiga">
                <area shape="rect" coords="950,747,1041,817" alt="" />
            </div>
            <div class="mie">
                <area shape="rect" coords="951,822,1041,887" alt="" />
            </div>
            <div class="kyoto">
                <area shape="rect" coords="850,748,942,815" alt="" />
            </div>
            <div class="nara">
                <area shape="rect" coords="850,824,944,893" alt="" />
            </div>
            <div class="hyogo">
                <area shape="rect" coords="751,746,843,818" alt="" />
            </div>
            <div class="osaka">
                <area shape="rect" coords="751,823,842,893" alt="" />
            </div>
            <div class="wakayama">
                <area shape="rect" coords="750,899,943,969" alt="" />
            </div>
            <div class="totori">
                <area shape="rect" coords="650,746,743,818" alt="" />
            </div>
            <div class="okayama">
                <area shape="rect" coords="650,824,743,892" alt="" />
            </div>
            <div class="shimane">
                <area shape="rect" coords="550,746,645,818" alt="" />
            </div>
            <div class="hiroshima">
                <area shape="rect" coords="551,822,644,893" alt="" />
            </div>
            <div class="yamaguchi">
                <area shape="rect" coords="451,745,545,893" alt="" />
            </div>
            <div class="kagawa">
                <area shape="rect" coords="584,928,678,998" alt="" />
            </div>
            <div class="tokushima">
                <area shape="rect" coords="584,1003,678,1073" alt="" />
            </div>
            <div class="ehime">
                <area shape="rect" coords="487,928,579,997" alt="" />
            </div>
            <div class="kouchi">
                <area shape="rect" coords="487,1004,580,1074" alt="" />
            </div>
            <div class="oita">
                <area shape="rect" coords="324,746,417,818" alt="" />
            </div>
            <div class="miyazaki">
                <area shape="rect" coords="322,823,415,968" alt="" />
            </div>
            <div class="fukuoka">
                <area shape="rect" coords="221,749,316,817" alt="" />
            </div>
            <div class="kumamoto">
                <area shape="rect" coords="220,824,312,893" alt="" />
            </div>
            <div class="saga">
                <area shape="rect" coords="124,747,215,816" alt="" />
            </div>
            <div class="nagasaki">
                <area shape="rect" coords="123,823,216,893" alt="" />
            </div>
            <div class="kagoshima">
                <area shape="rect" coords="121,897,312,968" alt="" />
            </div>
            <div class="okinawa">
                <area shape="rect" coords="125,1027,217,1097" alt="" />
            </div>
        </map>
    </div>


</x-top-layout>