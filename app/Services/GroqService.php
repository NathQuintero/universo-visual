<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Servicio: Llamada a la API de Groq (modelo llama-3.3-70b-versatile)
 *
 * Encapsula la comunicación HTTP con el endpoint de Groq para que
 * los controladores no tengan que conocer el detalle del protocolo.
 */
class GroqService
{
    /**
     * Envía un prompt + historial al modelo y devuelve el texto de respuesta.
     *
     * @param  string  $systemPrompt  Instrucciones del sistema (rol del bot)
     * @param  array   $messages      Historial: [['role' => 'user'|'assistant', 'content' => '...']]
     * @return string                 Respuesta del modelo o mensaje de error amigable
     */
    public function chat(string $systemPrompt, array $messages): string
    {
        $apiKey = config('services.groq.key');

        if (empty($apiKey)) {
            return 'Lo siento, no puedo responder en este momento. Intenta de nuevo.';
        }

        $payload = [
            'model' => config('services.groq.model'),
            'messages' => array_merge(
                [['role' => 'system', 'content' => $systemPrompt]],
                $messages
            ),
            'temperature' => 0.7,
            'max_tokens' => 500,
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(15)->post(config('services.groq.endpoint'), $payload);

            if (!$response->successful()) {
                Log::warning('Groq API respondió con error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return 'Lo siento, no puedo responder en este momento. Intenta de nuevo.';
            }

            $content = $response->json('choices.0.message.content');

            return is_string($content) && trim($content) !== ''
                ? trim($content)
                : 'Lo siento, no puedo responder en este momento. Intenta de nuevo.';
        } catch (\Throwable $e) {
            Log::error('Error llamando a Groq', ['message' => $e->getMessage()]);
            return 'Lo siento, no puedo responder en este momento. Intenta de nuevo.';
        }
    }
}
