<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AIService
{
    public function generateScheduleReply(array $history, array $scheduleContext): array
    {
        $apiKey = config('services.gemini.api_key');

        $systemPrompt = implode("\n", [
            'Kamu adalah asisten jadwal lari.',
            'Tugasmu membantu membaca, membuat, mengubah, dan menghapus jadwal lari milik user.',
            'Balas selalu dalam JSON valid dengan format:',
            '{"reply":"string","actions":[',
            '  {"type":"read_schedule"},',
            '  {"type":"create_schedule","items":[{"day":"Monday","workout":"Easy Run","distance":5,"notes":"optional"}]},',
            '  {"type":"update_schedule","items":[{"id":1,"day":"Monday","workout":"Tempo Run","distance":8,"notes":"optional"}]},',
            '  {"type":"delete_schedule","ids":[1,2]}',
            ']}',
            'Untuk update_schedule, field "id" WAJIB ada dan harus id jadwal yang benar-benar ada di konteks jadwal user. Field lain (day, workout, distance, notes) hanya perlu diisi jika memang ingin diubah; jika tidak diubah boleh dihilangkan dari item.',
            'Untuk delete_schedule, "ids" wajib berupa array id jadwal yang valid dan benar-benar ada di konteks jadwal user.',
            'Jangan pernah mengarang id. Selalu ambil id dari konteks jadwal user yang diberikan.',
            'Jika pengguna hanya bertanya jadwal, gunakan actions kosong atau read_schedule.',
            'Jika ada data yang kurang atau ambigu (misalnya user tidak menyebutkan jadwal mana yang mau diubah/dihapus), tanyakan di reply dan jangan menambahkan action yang merusak.',
            'Gunakan nama hari dalam bahasa Inggris: Monday sampai Sunday.',
            'Reply harus singkat, jelas, dan sesuaikan dengan bahasa user.',
        ]);

        $contents = [
            [
                'role' => 'user',
                'parts' => [
                    ['text' => $systemPrompt],
                ],
            ],
            [
                'role' => 'user',
                'parts' => [
                    ['text' => 'Konteks jadwal user (gunakan field id untuk update/delete): ' . json_encode($scheduleContext, JSON_UNESCAPED_UNICODE)],
                ],
            ],
        ];

        foreach ($history as $message) {
            $contents[] = [
                'role' => $message['role'] === 'model' ? 'model' : 'user',
                'parts' => [
                    [
                        'text' => $message['text'],
                    ],
                ],
            ];
        }

        $response = Http::post(
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent?key=' . $apiKey,
            [
                'contents' => $contents,
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                ],
            ]
        );

        if ($response->failed()) {
            throw new \Exception('Gemini API error: ' . $response->body());
        }

        $raw = $response->json('candidates.0.content.parts.0.text', '{"reply":"Maaf, AI tidak memberikan respons.","actions":[]}');
        $raw = trim((string) $raw);
        $raw = preg_replace('/^```(?:json)?\s*/i', '', $raw);
        $raw = preg_replace('/\s*```$/', '', $raw);
        $decoded = json_decode($raw, true);

        if (! is_array($decoded)) {
            return [
                'reply' => trim((string) $raw),
                'actions' => [],
            ];
        }

        return [
            'reply' => (string) ($decoded['reply'] ?? 'Maaf, AI tidak memberikan respons.'),
            'actions' => array_values($decoded['actions'] ?? []),
        ];
    }
}