<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('messages.running_schedule') }}
                </h2>
            </div>

            <a href="{{ route('running-schedules.create') }}"
               class="inline-flex items-center justify-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                + {{ __('messages.add_schedule') }}
            </a>
 
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        @if(session('success'))
            <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-5">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow overflow-hidden">

            {{-- Responsive Table --}}
            <div class="overflow-x-auto">

                <table class="w-full min-w-[700px]">

                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-left text-sm font-semibold text-gray-600">
                                No
                            </th>

                            <th class="px-4 sm:px-6 py-3 text-left text-sm font-semibold text-gray-600">
                                {{ __('messages.day') }}
                            </th>

                            <th class="px-4 sm:px-6 py-3 text-left text-sm font-semibold text-gray-600">
                                {{ __('messages.workout') }}
                            </th>

                            <th class="px-4 sm:px-6 py-3 text-left text-sm font-semibold text-gray-600">
                                {{ __('messages.distance') }}
                            </th>

                            <th class="px-4 sm:px-6 py-3 text-left text-sm font-semibold text-gray-600">
                                {{ __('messages.action') }}
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">

                        @forelse($runningSchedules as $schedule)

                            <tr class="hover:bg-gray-50">

                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                    {{ __('messages.' . \Illuminate\Support\Str::lower($schedule->day)) }}
                                </td>

                                <td class="px-4 sm:px-6 py-4">
                                    {{ $schedule->workout }}
                                </td>

                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                    {{ $schedule->distance }} km
                                    @if($schedule->notes)
                                        <p class="text-xs text-gray-500 mt-2">{{ $schedule->notes }}</p>
                                    @endif
                                </td>

                                <td class="px-4 sm:px-6 py-4">
                                    <div class="flex flex-wrap items-center gap-2">

                                        <a href="{{ route('running-schedules.edit', $schedule->slug) }}"
                                           class="bg-yellow-500 text-white px-3 py-2 rounded-lg hover:bg-yellow-600 transition">
                                            {{ __('messages.edit') }}
                                        </a>

                                        <form action="{{ route('running-schedules.destroy', $schedule->slug) }}"
                                              method="POST"
                                              class="inline-block"
                                              onsubmit="return confirm('Are you sure you want to delete this schedule?')">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="bg-red-600 text-white px-3 py-2 rounded-lg hover:bg-red-700 transition">
                                                {{ __('messages.delete') }}
                                            </button>

                                        </form>

                                    </div>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5"
                                    class="px-4 sm:px-6 py-10 text-center text-gray-500">
                                    {{ __('messages.No_running_found') }}
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</x-app-layout>
