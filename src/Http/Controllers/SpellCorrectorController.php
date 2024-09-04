<?php

namespace Vigilant\GptSpellCorrector\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use OpenAI\Laravel\Facades\OpenAI;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Vigilant\GptSpellCorrector\Http\Requests\SpellCorrectRequest;

class SpellCorrectorController extends Controller
{
    public function correct(SpellCorrectRequest $request): StreamedResponse
    {
        $content = json_encode($request->bard['content']);

        $prompt = view('gpt-spell-corrector::prompt', [
            'content' => $content,
        ])->render();

        return response()->stream(
            callback: function () use ($prompt) {
                $stream = OpenAI::chat()->createStreamed([
                    'model' => config('gpt-spell-corrector.openai_model'),
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);

                foreach ($stream as $response) {
                    $text = $response->choices[0]->delta->content;

                    if (connection_aborted()) {
                        break;
                    }

                    echo $text;

                    ob_flush();
                    flush();
                }
            },
            headers: [
                'Cache-Control' => 'no-cache',
                'X-Accel-Buffering' => 'no',
                'Content-Type' => 'text/event-stream',
            ]
        );
    }
}
