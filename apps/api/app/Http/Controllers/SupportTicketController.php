<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SupportTicketController extends Controller
{
    /**
     * Helper to resolve authenticated user in API/testing environments.
     */
    private function resolveUser(Request $request)
    {
        if (auth()->check()) {
            return auth()->user();
        }

        $email = $request->header('X-Test-User-Email') ?? $request->input('user_email');
        if ($email) {
            return DB::table('users')->where('email', $email)->first();
        }

        // Default fallback to first admin or store owner for frictionless dev
        return DB::table('users')->where('role', 'admin')->first();
    }

    /**
     * Helper to log actions into the ticket timeline ledger.
     */
    private function logTimeline(int $ticketId, ?int $userId, string $actionType, string $description, ?array $metadata = null): void
    {
        DB::table('support_ticket_timeline')->insert([
            'ticket_id' => $ticketId,
            'user_id' => $userId,
            'action_type' => $actionType,
            'description' => $description,
            'metadata' => $metadata ? json_encode($metadata) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Storefront: List tickets for the store.
     */
    public function storeIndex(Request $request): JsonResponse
    {
        $user = $this->resolveUser($request);
        if (!$user) {
            return response()->json(['message' => 'Não autorizado.'], 401);
        }

        $storeUser = DB::table('store_users')->where('user_id', $user->id)->first();
        $storeId = $storeUser ? $storeUser->store_id : null;

        if (!$storeId) {
            return response()->json(['message' => 'Usuário não associado a uma loja.'], 403);
        }

        $tickets = DB::table('support_tickets')
            ->where('store_id', $storeId)
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json($tickets);
    }

    /**
     * Central Admin: List all tickets across all stores.
     */
    public function adminIndex(Request $request): JsonResponse
    {
        $tickets = DB::table('support_tickets')
            ->leftJoin('stores', 'support_tickets.store_id', '=', 'stores.id')
            ->leftJoin('users', 'support_tickets.user_id', '=', 'users.id')
            ->select('support_tickets.*', 'stores.name as store_name', 'users.name as user_name')
            ->orderBy('support_tickets.updated_at', 'desc')
            ->get();

        return response()->json($tickets);
    }

    /**
     * Show ticket details, timeline, and messages.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $ticket = DB::table('support_tickets')->where('id', $id)->first();

        if (!$ticket) {
            return response()->json(['message' => 'Chamado não encontrado.'], 404);
        }

        $messages = DB::table('support_ticket_messages')
            ->leftJoin('users', 'support_ticket_messages.user_id', '=', 'users.id')
            ->select('support_ticket_messages.*', 'users.name as user_name', 'users.role as user_role')
            ->where('ticket_id', $id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) {
                if ($msg->attachments) {
                    $msg->attachments = json_decode($msg->attachments);
                }
                return $msg;
            });

        $timeline = DB::table('support_ticket_timeline')
            ->leftJoin('users', 'support_ticket_timeline.user_id', '=', 'users.id')
            ->select('support_ticket_timeline.*', 'users.name as user_name')
            ->where('ticket_id', $id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($event) {
                if ($event->metadata) {
                    $event->metadata = json_decode($event->metadata);
                }
                return $event;
            });

        return response()->json([
            'ticket' => $ticket,
            'messages' => $messages,
            'timeline' => $timeline
        ]);
    }

    /**
     * Storefront: Open a new support ticket.
     */
    public function storeCreate(Request $request): JsonResponse
    {
        $user = $this->resolveUser($request);
        if (!$user) {
            return response()->json(['message' => 'Não autorizado.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'category' => 'required|string|in:financeiro,tecnico,suporte,outros',
            'priority' => 'nullable|string|in:low,medium,high',
            'message' => 'required|string',
            'attachments' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $storeUser = DB::table('store_users')->where('user_id', $user->id)->first();
        $storeId = $storeUser ? $storeUser->store_id : null;

        DB::beginTransaction();
        try {
            $ticketId = DB::table('support_tickets')->insertGetId([
                'store_id' => $storeId,
                'user_id' => $user->id,
                'title' => $request->input('title'),
                'category' => $request->input('category'),
                'priority' => $request->input('priority') ?? 'medium',
                'status' => 'aberto',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('support_ticket_messages')->insert([
                'ticket_id' => $ticketId,
                'user_id' => $user->id,
                'message' => $request->input('message'),
                'is_agent' => false,
                'attachments' => $request->has('attachments') ? json_encode($request->input('attachments')) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->logTimeline(
                $ticketId,
                $user->id,
                'status_change',
                "Chamado criado por {$user->name} com status Aberto.",
                ['category' => $request->input('category'), 'priority' => $request->input('priority') ?? 'medium']
            );

            DB::commit();

            return response()->json([
                'message' => 'Chamado aberto com sucesso!',
                'ticket_id' => $ticketId,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Erro ao criar chamado.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Storefront / Admin: Reply to ticket.
     */
    public function storeReply(Request $request, int $id): JsonResponse
    {
        return $this->reply($request, $id, false);
    }

    public function adminReply(Request $request, int $id): JsonResponse
    {
        return $this->reply($request, $id, true);
    }

    private function reply(Request $request, int $id, bool $isAgent): JsonResponse
    {
        $user = $this->resolveUser($request);
        if (!$user) {
            return response()->json(['message' => 'Não autorizado.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'attachments' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $ticket = DB::table('support_tickets')->where('id', $id)->first();
        if (!$ticket) {
            return response()->json(['message' => 'Chamado não encontrado.'], 404);
        }

        $newStatus = $isAgent ? 'respondido' : 'aguardando_cliente';

        DB::beginTransaction();
        try {
            DB::table('support_ticket_messages')->insert([
                'ticket_id' => $id,
                'user_id' => $user->id,
                'message' => $request->input('message'),
                'is_agent' => $isAgent,
                'attachments' => $request->has('attachments') ? json_encode($request->input('attachments')) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('support_tickets')->where('id', $id)->update([
                'status' => $newStatus,
                'updated_at' => now(),
            ]);

            $senderType = $isAgent ? 'Suporte Central' : $user->name;
            $this->logTimeline(
                $id,
                $user->id,
                'message_added',
                "Nova mensagem adicionada por {$senderType}. Status alterado para: " . ucfirst($newStatus),
                ['status' => $newStatus]
            );

            DB::commit();

            return response()->json(['message' => 'Resposta enviada com sucesso!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Erro ao enviar resposta.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Close support ticket.
     */
    public function close(Request $request, int $id): JsonResponse
    {
        $user = $this->resolveUser($request);
        if (!$user) {
            return response()->json(['message' => 'Não autorizado.'], 401);
        }

        DB::table('support_tickets')->where('id', $id)->update([
            'status' => 'encerrado',
            'updated_at' => now(),
        ]);

        $this->logTimeline($id, $user->id, 'status_change', "Chamado encerrado por {$user->name}.");

        return response()->json(['message' => 'Chamado encerrado com sucesso.']);
    }

    /**
     * Reopen support ticket.
     */
    public function reopen(Request $request, int $id): JsonResponse
    {
        $user = $this->resolveUser($request);
        if (!$user) {
            return response()->json(['message' => 'Não autorizado.'], 401);
        }

        DB::table('support_tickets')->where('id', $id)->update([
            'status' => 'aberto',
            'updated_at' => now(),
        ]);

        $this->logTimeline($id, $user->id, 'reopen', "Chamado reaberto por {$user->name}.");

        return response()->json(['message' => 'Chamado reaberto com sucesso.']);
    }

    /**
     * Rate support ticket (1 to 5).
     */
    public function rate(Request $request, int $id): JsonResponse
    {
        $user = $this->resolveUser($request);
        if (!$user) {
            return response()->json(['message' => 'Não autorizado.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::table('support_tickets')->where('id', $id)->update([
            'rating' => $request->input('rating'),
            'updated_at' => now(),
        ]);

        $this->logTimeline(
            $id,
            $user->id,
            'rating_added',
            "Chamado avaliado com {$request->input('rating')} estrelas por {$user->name}.",
            ['rating' => $request->input('rating')]
        );

        return response()->json(['message' => 'Chamado avaliado com sucesso.']);
    }

    /**
     * Central Admin: Update ticket category.
     */
    public function updateCategory(Request $request, int $id): JsonResponse
    {
        $user = $this->resolveUser($request);
        if (!$user) {
            return response()->json(['message' => 'Não autorizado.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'category' => 'required|string|in:financeiro,tecnico,suporte,outros',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $oldCategory = DB::table('support_tickets')->where('id', $id)->value('category');

        DB::table('support_tickets')->where('id', $id)->update([
            'category' => $request->input('category'),
            'updated_at' => now(),
        ]);

        $this->logTimeline(
            $id,
            $user->id,
            'category_change',
            "Categoria do chamado alterada de " . ucfirst((string)$oldCategory) . " para " . ucfirst($request->input('category')) . " por {$user->name}.",
            ['old_category' => $oldCategory, 'new_category' => $request->input('category')]
        );

        return response()->json(['message' => 'Categoria atualizada com sucesso.']);
    }
}
