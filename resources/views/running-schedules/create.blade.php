<x-app-layout>
    <x-slot name="header">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('messages.add_running_schedule') }}
                    </h2>
                </div>
            </div>
        

    </x-slot>
    <div class="max-w-7xl mt-2 mx-auto sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto py-10 px-4">

            <form action="{{ route('running-schedules.store') }}" method="POST">

                @csrf

                {{-- Day --}}
                <div class="mb-5">

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('messages.day') }}
                    </label>

                    <select name="day"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2">

                        <option value="">{{ __('messages.select_day') }}</option>

                        <option value="Monday" {{ old('day') == 'Monday' ? 'selected' : '' }}>
                            {{ __('messages.monday') }}
                        </option>

                        <option value="Tuesday" {{ old('day') == 'Tuesday' ? 'selected' : '' }}>
                            {{ __('messages.tuesday') }}
                        </option>

                        <option value="Wednesday" {{ old('day') == 'Wednesday' ? 'selected' : '' }}>
                            {{ __('messages.wednesday') }}
                        </option>

                        <option value="Thursday" {{ old('day') == 'Thursday' ? 'selected' : '' }}>
                            {{ __('messages.thursday') }}
                        </option>

                        <option value="Friday" {{ old('day') == 'Friday' ? 'selected' : '' }}>
                            {{ __('messages.friday') }}
                        </option>

                        <option value="Saturday" {{ old('day') == 'Saturday' ? 'selected' : '' }}>
                            {{ __('messages.saturday') }}
                        </option>

                        <option value="Sunday" {{ old('day') == 'Sunday' ? 'selected' : '' }}>
                            {{ __('messages.sunday') }}
                        </option>

                    </select>

                    @error('day')
                        <p class="text-red-500 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                {{-- Workout --}}
                <div class="mb-5">

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('messages.workout') }}
                    </label>

                    <input type="text"
                        name="workout"
                        value="{{ old('workout') }}"
                        placeholder="Example: Easy Run"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2">

                    @error('workout')
                        <p class="text-red-500 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Distance --}}
                <div class="mb-6">

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('messages.distance') }} (km)
                    </label>

                    <input type="number"
                        name="distance"
                        value="{{ old('distance') }}"
                        step="0.01"
                        min="0"
                        placeholder="Example: 5"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2">

                    @error('distance')
                        <p class="text-red-500 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                {{-- Notes --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('messages.notes') }}
                    </label>

                    <textarea name="notes" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2"
                        placeholder="Optional notes">{{ old('notes') }}</textarea>
                </div>


                <div class="flex gap-3">

                    <a href="{{ route('running-schedules.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100">
                        {{ __('messages.cancel') }}
                    </a>

                    <button type="submit"
                            class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700">
                        {{ __('messages.save_schedule') }}
                    </button>

                </div>

            </form>

        </div>
    </div>

</x-app-layout>
