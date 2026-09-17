<?php

namespace App\Http\Controllers;

use App\Models\RunningSchedule;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class ChatController extends Controller
{
    public function index()
    {
        $history = session('chat_history', []);
        $history = array_slice($history, -20);

        return view('chat.index', compact('history'));
    }

    public function send(Request $request, AIService $aiService)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $history = session('chat_history', []);
        $history = array_slice($history, -20);

        $history[] = [
            'role' => 'user',
            'text' => $validated['message'],
        ];

        try {
            $scheduleContext = $this->buildScheduleContext($request->user());
            $result = $aiService->generateScheduleReply($history, $scheduleContext);

            $reply = $result['reply'];
            $actions = $result['actions'] ?? [];
            $actionSummary = [];

            foreach ($actions as $action) {
                $actionSummary[] = $this->applyAction($request->user(), $action);
            }

            if (! empty($actionSummary)) {
                $reply .= "\n\n" . implode("\n", array_filter($actionSummary));
            }

            $history[] = [
                'role' => 'model',
                'text' => $reply,
            ];

            session()->put('chat_history', $history);

            return response()->json([
                'success' => true,
                'message' => $reply,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada AI.',
            ], 500);
        }
    }

    public function clear()
    {
        session()->forget('chat_history');

        return response()->json([
            'success' => true,
        ]);
    }

    private function buildScheduleContext($user): array
    {
        $schedules = $user->runningSchedules()
            ->latest()
            ->get()
            ->map(function (RunningSchedule $schedule) {
                return [
                    'id' => $schedule->id,
                    'day' => $schedule->day,
                    'workout' => $schedule->workout,
                    'distance' => (float) $schedule->distance,
                    'notes' => $schedule->notes,
                    'created_at' => optional($schedule->created_at)->toDateTimeString(),
                ];
            })
            ->values()
            ->all();

        return [
            'today' => Carbon::now()->format('l'),
            'schedule_count' => count($schedules),
            'schedules' => $schedules,
        ];
    }

    private function applyAction($user, array $action): string
    {
        $type = Arr::get($action, 'type');

        return match ($type) {
            'read_schedule' => $this->summarizeSchedules($user),
            'create_schedule' => $this->createSchedules($user, Arr::get($action, 'items', [])),
            'update_schedule' => $this->updateSchedules($user, Arr::get($action, 'items', [])),
            'delete_schedule' => $this->deleteSchedules($user, Arr::get($action, 'ids', [])),
            default => '',
        };
    }

    private function createSchedules($user, $items): string
    {
        if (! is_array($items) || empty($items)) {
            return 'AI meminta membuat jadwal, tetapi detailnya belum lengkap.';
        }

        $created = 0;

        foreach ($items as $item) {
            $day = Arr::get($item, 'day');
            $workout = Arr::get($item, 'workout');
            $distance = Arr::get($item, 'distance');
            $notes = Arr::get($item, 'notes');

            if (! $day || ! $workout || $distance === null) {
                continue;
            }

            $user->runningSchedules()->create([
                'day' => $day,
                'workout' => $workout,
                'distance' => $distance,
                'notes' => $notes,
            ]);

            $created++;
        }

        return $created > 0
            ? "Berhasil membuat {$created} jadwal baru."
            : 'Tidak ada jadwal yang berhasil dibuat karena detailnya belum lengkap.';
    }

    private function updateSchedules($user, $items): string
    {
        if (! is_array($items) || empty($items)) {
            return 'AI meminta mengubah jadwal, tetapi tidak ada detail yang diberikan.';
        }

        $updated = 0;
        $skipped = 0;

        foreach ($items as $item) {
            $id = Arr::get($item, 'id');

            if (! $id) {
                $skipped++;
                continue;
            }

            // Pastikan jadwal milik user yang sedang login (mencegah IDOR).
            $schedule = $user->runningSchedules()->find($id);

            if (! $schedule) {
                $skipped++;
                continue;
            }

            $payload = Arr::only($item, ['day', 'workout', 'distance', 'notes']);
            $payload = array_filter($payload, fn ($value) => $value !== null);

            if (empty($payload)) {
                $skipped++;
                continue;
            }

            $schedule->update($payload);
            $updated++;
        }

        if ($updated === 0) {
            return 'Tidak ada jadwal yang berhasil diubah (id tidak ditemukan atau data kosong).';
        }

        $summary = "Berhasil mengubah {$updated} jadwal.";
        if ($skipped > 0) {
            $summary .= " {$skipped} item dilewati karena id tidak valid atau data kosong.";
        }

        return $summary;
    }

    private function deleteSchedules($user, $ids): string
    {
        if (! is_array($ids) || empty($ids)) {
            return 'AI meminta menghapus jadwal, tetapi tidak ada id yang diberikan.';
        }

        $ids = array_filter($ids, fn ($id) => is_numeric($id));

        if (empty($ids)) {
            return 'Tidak ada id jadwal yang valid untuk dihapus.';
        }

        // Hanya menghapus jadwal milik user yang sedang login.
        $deleted = $user->runningSchedules()
            ->whereIn('id', $ids)
            ->delete();

        return $deleted > 0
            ? "Berhasil menghapus {$deleted} jadwal."
            : 'Tidak ada jadwal yang cocok untuk dihapus.';
    }

    private function summarizeSchedules($user): string
    {
        $schedules = $user->runningSchedules()
            ->orderBy('day')
            ->get();

        if ($schedules->isEmpty()) {
            return 'Jadwalmu masih kosong.';
        }

        $lines = ['Ringkasan jadwal kamu:'];

        foreach ($schedules as $schedule) {
            $extra = $schedule->notes ? ' | Catatan: ' . $schedule->notes : '';
            $lines[] = "- {$schedule->day} | {$schedule->workout} | {$schedule->distance} km{$extra}";
        }

        return implode("\n", $lines);
    }
}