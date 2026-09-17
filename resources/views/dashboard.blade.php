<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        {{ __('messages.schedule') }}
                    </h3>

                    <p class="text-gray-500 mb-6">
                        {{ __('messages.' . \Illuminate\Support\Str::lower($today)) }}
                    </p>

                    @forelse($runningSchedules as $schedule)

                        <div class="border rounded-lg p-5 mb-3">
                            <div class="flex justify-between items-center">

                                <div>
                                    <p class="text-sm text-gray-500">
                                        {{ __('messages.workout') }}
                                    </p>

                                    <p class="text-xl font-semibold text-gray-800">
                                        {{ $schedule->workout }}
                                    </p>
                                </div>

                                <div class="text-right">
                                    <p class="text-sm text-gray-500">
                                        {{ __('messages.distance') }}
                                    </p>

                                    <p class="text-xl font-semibold text-blue-600">
                                        {{ $schedule->distance }} km
                                    </p>
                                    @if($schedule->notes)
                                        <p class="text-xs text-gray-500 mt-2">{{ $schedule->notes }}</p>
                                    @endif
                                </div>

                            </div>
                        </div>

                    @empty

                        <div class="border rounded-lg p-6 text-center">
                            <p class="text-gray-500">
                               {{ __('messages.no_running') }}
                            </p>
                        </div>

                    @endforelse

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
