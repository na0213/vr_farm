<x-app-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="max-w-sm w-full lg:max-w-full lg:flex">
                <div class="rounded border border-gray-400 lg:border-l-0 lg:border-t lg:border-gray-400 bg-white rounded-b lg:rounded-b-none lg:rounded-r p-4 flex flex-col justify-between leading-normal">
                    <div class="mb-8">
                        <p class="text-center text-sm text-gray-600">
                            {{ $point->farm->farm_name }}
                        </p>
                        <div class="text-center text-gray-900 font-bold text-xl mb-2">{{ $point->point_name }}</div>
                        <p class="text-center text-gray-700 text-base">{{ $point->point_info }}</p>
                        <div class="mt-10 flex justify-center">
                            @foreach (json_decode($point->sdgs, true) as $sdg)
                                <img src="{{ asset('storage/sdgs/sdg_' . $sdg . '.png') }}" alt="SDG{{ $sdg }}" class="ml-4 mb-2" width="90">
                            @endforeach
                        </div>
                    </div>
                    <div class="flex justify-center">
                        <form action="{{ route('user.links.store', ['point_id' => $point->id]) }}" method="POST">
                            @csrf
                            <div class="text-sm">
                                <button type="submit" class="text-white bg-yellow-500 border-0 py-2 px-8 focus:outline-none hover:bg-yellow-600 rounded text-lg">登録する</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>