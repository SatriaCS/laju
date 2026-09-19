<?php

namespace App\Http\Controllers;

use App\Models\RunningSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RunningScheduleController extends Controller
{
    public function index()
    {
        $runningSchedules = auth()->user()
            ->runningSchedules()
            ->orderByRaw("FIELD(day, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')")
            ->get();

        return view('running-schedules.index', compact('runningSchedules'));
    }

    public function create()
    {
        return view('running-schedules.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'day' => 'required|string|max:20',
            'workout' => 'required|string|max:100',
            'distance' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['slug'] = $this->generateUniqueSlug($validated['workout']);

        $request->user()
            ->runningSchedules()
            ->create($validated);

        return redirect()
            ->route('running-schedules.index')
            ->with('success', 'Running schedule created successfully.');
    }

    public function edit($slug)
    {
        $runningSchedule = auth()->user()
            ->runningSchedules()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('running-schedules.edit', compact('runningSchedule'));
    }

    public function update(Request $request, $slug)
    {
        $validated = $request->validate([
            'day' => 'required|string|max:20',
            'workout' => 'required|string|max:100',
            'distance' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $runningSchedule = auth()->user()
            ->runningSchedules()
            ->where('slug', $slug)
            ->firstOrFail();

        // Regenerate slug hanya jika workout berubah
        if ($runningSchedule->workout !== $validated['workout']) {
            $validated['slug'] = $this->generateUniqueSlug($validated['workout'], $runningSchedule->id);
        }

        $runningSchedule->update($validated);

        return redirect()
            ->route('running-schedules.index')
            ->with('success', 'Running schedule updated successfully.');
    }

    public function destroy($slug)
    {
        $runningSchedule = auth()->user()
            ->runningSchedules()
            ->where('slug', $slug)
            ->firstOrFail();

        $runningSchedule->delete();

        return redirect()
            ->route('running-schedules.index')
            ->with('success', 'Running schedule deleted successfully.');
    }

    /**
     * Generate slug unik berdasarkan workout, hindari duplikat.
     */
    private function generateUniqueSlug(string $workout, ?int $ignoreId = null): string
    {
        $slug = Str::slug($workout);
        $original = $slug;
        $count = 1;

        $query = fn ($slug) => RunningSchedule::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists();

        while ($query($slug)) {
            $slug = "{$original}-{$count}";
            $count++;
        }

        return $slug;
    }
}