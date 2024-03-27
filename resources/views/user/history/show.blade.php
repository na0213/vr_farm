{{-- <x-farm-layout> --}}
<x-appshow-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-11/12 mx-auto mb-10 text-sm text-left text-gray-500 dark:text-gray-400">
                  <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                      <tr>
                          <th scope="col" class="px-6 py-2 whitespace-nowrap">
                            ID
                          </th>
                          <th scope="col" class="px-6 py-2 whitespace-nowrap">
                            牧場名</th>
                          <th scope="col" class="px-6 py-2 whitespace-nowrap">
                            SDGsポイント
                          </th>
                          <th scope="col" class="px-6 py-2 whitespace-nowrap">
                            詳細
                          </th>
                      </tr>
                  </thead>
                  <tbody>
                    @forelse ($histories as $history)
                      <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                          <td class="px-6 py-2 font-medium text-gray-900 dark:text-white">
                            {{ $history->id }}
                          </td>
                          <td class="px-6 py-2 font-medium text-gray-900 dark:text-white">
                            {{ $history->point->farm->farm_name }}
                          </td>
                          <td class="px-6 py-2 font-medium text-gray-900 dark:text-white">
                            {{ $history->point->point_info }}
                          </td>
                          <td class="px-6 py-2 font-medium text-gray-900 dark:text-white">
                            @foreach (json_decode($history->point->sdgs, true) as $sdg)
                                <img src="{{ asset('storage/sdgs/sdg_' . $sdg . '.png') }}" alt="SDG{{ $sdg }}" class="inline-block" width="90">
                            @endforeach
                          </td>
                      </tr>
                    @empty
                      <tr>
                          <td colspan="4" class="px-6 py-2 text-center">まだ登録されていません。</td>
                      </tr>
                    @endforelse
                  </tbody>                  
                </table>
            </div>
        </div>
    </div>

</x-appshow-layout>