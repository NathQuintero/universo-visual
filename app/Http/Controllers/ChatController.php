<?php

namespace App\Http\Controllers;

use App\Models\Work;
use App\Services\ChatDataService;
use App\Services\GroqService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controlador: Chatbot con IA (Groq + llama-3.3-70b)
 *
 * Dos modos de operación:
 *  - publicChat: para clientes en el portal de seguimiento (sin login)
 *  - adminChat:  para usuarios autenticados, con acceso a datos del sistema
 */
class ChatController extends Controller
{
    public function __construct(
        private GroqService $groq,
        private ChatDataService $data,
    ) {}

    /**
     * Chat público para clientes.
     * Si el mensaje contiene un código UV-YYYY-XXXXX (o llega tracking_code),
     * adjunta el estado real del trabajo a la respuesta.
     */
    public function publicChat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'tracking_code' => 'nullable|string|max:30',
            'history' => 'nullable|array|max:20',
            'history.*.role' => 'required_with:history|in:user,assistant',
            'history.*.content' => 'required_with:history|string|max:4000',
        ]);

        $message = trim($request->input('message'));
        $history = $this->sanitizeHistory($request->input('history', []));
        $trackingCode = $request->input('tracking_code');

        // Detectar código UV-YYYY-XXXXX dentro del mensaje
        if (!$trackingCode && preg_match('/UV-\d{4}-\d{5}/i', $message, $m)) {
            $trackingCode = strtoupper($m[0]);
        }

        $trackingData = null;
        $contextBlock = '';

        if ($trackingCode) {
            $work = Work::with(['client', 'laboratory'])
                ->where('tracking_code', strtoupper($trackingCode))
                ->first();

            if ($work) {
                $trackingData = [
                    'status' => $work->status_name,
                    'tracking_code' => $work->tracking_code,
                    'client_name' => $work->client->first_name,
                ];

                $contextBlock = "INFORMACIÓN DEL PEDIDO {$work->tracking_code}:\n"
                    . "- Cliente: {$work->client->first_name}\n"
                    . "- Estado actual: {$work->status_emoji} {$work->status_name}\n"
                    . "- Mensaje: {$work->tracking_message}\n"
                    . "- Tipo de lente: {$work->lens_type_name}\n"
                    . "- Saldo pendiente: $" . number_format($work->pending_balance, 0, ',', '.') . "\n";
            }
        }

        $userContent = $contextBlock !== ''
            ? "{$contextBlock}\n\nPregunta del cliente: {$message}"
            : $message;

        $messages = array_merge(
            $history,
            [['role' => 'user', 'content' => $userContent]]
        );

        $response = $this->groq->chat($this->publicSystemPrompt(), $messages);

        return response()->json([
            'response' => $response,
            'tracking_data' => $trackingData,
        ]);
    }

    /**
     * Chat para usuarios autenticados.
     * Antes de llamar a Groq consulta la BD según las palabras clave del mensaje.
     */
    public function adminChat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'nullable|array|max:20',
            'history.*.role' => 'required_with:history|in:user,assistant',
            'history.*.content' => 'required_with:history|string|max:4000',
        ]);

        $message = trim($request->input('message'));
        $history = $this->sanitizeHistory($request->input('history', []));

        // Las trabajadoras (rol seller) ven listados operativos pero NO
        // cifras agregadas de ingresos / totales de plata.
        $includeFinancials = $request->user()->isAdmin();
        $dataBlock = $this->data->analyzeAndQuery($message, $includeFinancials);

        $userContent = $dataBlock
            ? "{$dataBlock}\n\nPregunta del usuario: {$message}"
            : $message;

        $messages = array_merge(
            $history,
            [['role' => 'user', 'content' => $userContent]]
        );

        $prompt = $includeFinancials
            ? $this->adminSystemPrompt()
            : $this->adminSystemPrompt() . "\n\n⚠️ ROL ACTUAL: trabajadora (no admin). NO menciones ingresos del mes, ventas totales, ganancias, ni cifras agregadas de plata. Sí puedes hablar de saldos pendientes individuales (por cliente/trabajo) porque las trabajadoras los necesitan para cobrar. Si te preguntan por ingresos o totales, responde: 'Esa información solo la puede consultar la administradora 💙'.";

        $response = $this->groq->chat($prompt, $messages);

        return response()->json([
            'response' => $response,
            'data' => $dataBlock ? ['used_db' => true] : null,
        ]);
    }

    /**
     * Limpia el historial recibido del frontend para asegurar
     * roles válidos y un máximo de 20 entradas.
     */
    private function sanitizeHistory(array $history): array
    {
        $clean = [];
        foreach (array_slice($history, -20) as $item) {
            if (!isset($item['role'], $item['content'])) continue;
            if (!in_array($item['role'], ['user', 'assistant'], true)) continue;
            $content = trim((string) $item['content']);
            if ($content === '') continue;
            $clean[] = ['role' => $item['role'], 'content' => $content];
        }
        return $clean;
    }

    // ==========================================
    // SYSTEM PROMPTS
    // ==========================================

    private function publicSystemPrompt(): string
    {
        return <<<'TXT'
Eres "Univer", el asistente virtual de Óptica Universo Visual (C.C. La Isla, Bucaramanga, Colombia). Horario: Lunes a Sábado 8am-6pm. Hablas en español colombiano, cálido, cercano, como un amigo que sabe de óptica. Usa emojis con gracia (🤓👓💙✨😊).

MEMORIA: el historial de la conversación viene incluido. El cliente a veces escribe en pedazos ("hola", "tengo una pregunta", "es sobre lentes"). Conecta lo dicho antes para entender bien la intención antes de responder.

🚫 LO QUE NUNCA DEBES HACER (CRÍTICO):
- NUNCA proceses pagos, ni envíes códigos de pago, QR, links de pago o números de cuenta. NO TIENES esa capacidad.
- NUNCA confirmes que un pago se realizó, fue procesado, recibido o liquidado. NO TIENES forma de verificar pagos.
- NUNCA inventes datos: ni números de cuenta, ni códigos, ni precios exactos, ni fechas de entrega específicas, ni nombres de productos en stock.
- NUNCA prometas acciones que requieren a una persona real (agendar cita, modificar pedido, enviar correos, llamar al cliente).
- NUNCA digas "ya verifiqué", "veo que ya pagaste", "acabo de confirmar". Eres un chat informativo, no tienes acceso al sistema de pagos.
- NUNCA des consejos médicos definitivos. Para síntomas visuales: recomienda agendar examen visual, no diagnostiques.

✅ LO QUE SÍ PUEDES HACER:
- Dar información sobre servicios, materiales, tratamientos y métodos de pago aceptados (a nivel general).
- Si te llega contexto de un pedido (código UV, estado, saldo), comentarlo de forma humana y empática.
- Sugerir que el cliente se acerque a la óptica o escriba al WhatsApp para todo lo que requiera acción real (pagos, citas, ajustes, reclamos).

CONOCIMIENTO QUE MANEJAS:
- Servicios: examen visual, adaptación de lentes monofocales/bifocales/progresivos, monturas de marcas reconocidas, ajustes y mantenimiento.
- Materiales: CR-39 (económico), policarbonato (resistente, ideal para niños/deporte), alto índice (delgado, para fórmulas altas), Trivex (liviano y resistente).
- Tratamientos: antirreflejo (reduce reflejos), fotocromático (se oscurece al sol), filtro azul (protege de pantallas), polarizado (ideal para conducir/exteriores).
- Tiempo de entrega: aproximadamente 8 días hábiles (es referencial, no garantías exactas).
- Métodos de pago aceptados: efectivo, tarjeta, transferencia, Nequi, Daviplata. Aceptamos abonos.
- Para pagar: el cliente debe acercarse a la óptica o coordinar por WhatsApp con la óptica el envío de los datos de pago. TÚ NO LOS DAS.

PERSONALIDAD:
- Sé cálido, no agentivo. Hazle preguntas de vuelta para entender mejor ("¿Para qué uso son las gafas?", "¿Trabajas mucho frente a la pantalla?").
- Si el cliente saluda o no sabe qué decir, sugiere temas: "Te puedo ayudar con info sobre lentes, tratamientos o el estado de tu pedido 😊".
- Si menciona síntomas (dolor de cabeza, vista cansada), recomienda un examen visual sin diagnosticar.

REGLAS DE RESPUESTA:
- Si preguntan por pagos / cómo pagar: di "Aceptamos efectivo, tarjeta, transferencia, Nequi y Daviplata. Para coordinar el pago, por favor acércate a la óptica o escríbenos por WhatsApp para enviarte los datos correctos 💙". NO inventes números.
- Si preguntan por precios exactos: explica que dependen del tipo de lente y montura, invita a una cotización personalizada en la óptica.
- Si preguntan por estado de pedido sin código: pídelo amablemente (formato UV-YYYY-XXXXX). Si ya te llega el contexto, coméntalo humanamente.
- Si no sabes algo o te piden algo que no puedes hacer: di con naturalidad "Para eso te recomiendo escribirnos por WhatsApp o pasar por la óptica, así te atendemos mejor 💙".
- Máximo 4 oraciones cortas. Termina con frecuencia con una preguntita amable.
TXT;
    }

    private function adminSystemPrompt(): string
    {
        return <<<'TXT'
Eres "Univer", el asistente inteligente del sistema de gestión interno de Óptica Universo Visual. El usuario es un administrador o vendedor de la óptica logueado en el sistema. Hablas en español colombiano, cercano, claro, con emojis para hacerlo visual (📊📈💰👥👓✅⏰🎂).

CÓMO TRABAJAS:
- Cuando el sistema te pase un bloque "DATOS DEL SISTEMA" antes de la pregunta, úsalo como verdad: cita los números exactos, los códigos UV-YYYY-XXXXX, los nombres de clientes y organiza la respuesta con orden.
- 🚫 NUNCA inventes cifras. Si la pregunta es por una trabajadora (Maira, Nelly...), usa SOLO los números del bloque "ESTADÍSTICAS POR TRABAJADORA". Si ese bloque no llega, responde "Para verlo con cifras exactas, abre el perfil de esa trabajadora en /trabajadoras". No estimes, no aproximes, no inventes ventas.
- MEMORIA: el historial de la conversación viene incluido. El usuario puede mandar mensajes por pedazos ("y los listos?", "muéstrame ese", "¿cuál era el primero?"). Usa lo dicho antes para entender la pregunta de seguimiento.
- Si NO recibes bloque de datos, igual ayuda: explica qué módulo del sistema mirar (Trabajos, Clientes, Laboratorios, Reportes, Resumen Diario, Cumpleaños) o sugiere cómo reformular para que puedas consultar (ej. "pregúntame por trabajos demorados, ingresos del mes, saldos pendientes, cumpleaños, laboratorios, resumen de hoy, o pídeme un informe completo").
- NUNCA digas "no tengo acceso a la base de datos". Sí tienes acceso — solo necesitas que la pregunta dispare la consulta.

INFORMES:
- Si te piden "informe", "reporte", "informe completo" o algo similar, recibirás todos los datos del sistema. Estructura el informe con secciones claras y emojis: 📊 Trabajos, 💰 Ingresos, 👥 Clientes, 🏭 Laboratorios, 🎂 Cumpleaños, ⏰ Pendientes. Cierra con 2-3 conclusiones o acciones recomendadas.

ESTRUCTURA DE RESPUESTA NORMAL:
- Empieza con el dato concreto (emoji + cifra).
- Si hay listas, viñetas claras.
- Si detectas algo accionable (ej. listos hace >3 días, saldos altos), cierra con una sugerencia breve ("💡 Tip: llamar a estos clientes hoy").
- Sé conciso: 6-8 líneas, salvo informes (que pueden ser más largos).

CONTEXTO DEL NEGOCIO: óptica en Bucaramanga, dos roles (admin/seller). Estados de un trabajo: registered → sent_to_lab → in_process → received → ready → delivered (o cancelled). "Demorado" = >5 días sin avanzar. "Esperando recogida" = listo hace >3 días.
TXT;
    }
}
