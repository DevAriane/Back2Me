<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\Objet;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClaimController extends Controller
{
    /**
     * Créer un signalement (réclamation) pour un objet
     */
    public function store(Request $request, Objet $objet)
    {
        // Vérifier que l'objet est toujours trouvé (pas rendu)
        if ($objet->status === 'returned') {
            return response()->json(['message' => 'Cet objet a déjà été rendu.'], 400);
        }

        // Vérifier si l'utilisateur a déjà signalé cet objet
        $existing = Claim::where('objet_id', $objet->id)
                         ->where('user_id', $request->user()->id)
                         ->first();
        if ($existing) {
            return response()->json(['message' => 'Vous avez déjà signalé cet objet.'], 400);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $claim = Claim::create([
            'objet_id' => $objet->id,
            'user_id' => $request->user()->id,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        // Notifier l'admin (optionnel)
        // On peut envoyer une notification à tous les admins via un topic 'admins'
        // ...

        return response()->json($claim->load('user', 'objet'), 201);
    }

    /**
     * Liste des signalements en attente (admin)
     */
    public function pending(Request $request)
    {
        $claims = Claim::with(['objet', 'user'])
                       ->where('status', 'pending')
                       ->latest()
                       ->paginate($request->get('per_page', 15));

        return response()->json($claims);
    }

    /**
     * Approuver un signalement (admin)
     */
    public function approve(Claim $claim)
    {
        if ($claim->status !== 'pending') {
            return response()->json(['message' => 'Ce signalement a déjà été traité.'], 400);
        }

        $claim->update(['status' => 'approved']);

        // Option : marquer l'objet comme en attente de restitution ou notifier le propriétaire
        // $claim->objet->update(['status' => 'claimed']); // si on veut un statut intermédiaire

        // Notifier l'utilisateur que son signalement est approuvé
        if ($claim->user->fcm_token) {
            try {
                $firebase = new FirebaseNotificationService();
                $firebase->sendToUser(
                    $claim->user->fcm_token,
                    'Signalement approuvé',
                    "Votre réclamation pour l'objet {$claim->objet->name} a été approuvée. Contactez le surveillant général pour la restitution.",
                    ['claim_id' => $claim->id, 'objet_id' => $claim->objet_id]
                );
            } catch (\Exception $e) {
                \Log::error('Erreur notification: ' . $e->getMessage());
            }
        }

        return response()->json(['message' => 'Signalement approuvé.']);
    }

    /**
     * Rejeter un signalement (admin)
     */
    public function reject(Request $request, Claim $claim)
    {
        if ($claim->status !== 'pending') {
            return response()->json(['message' => 'Ce signalement a déjà été traité.'], 400);
        }

        $validator = Validator::make($request->all(), [
            'reason' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        $claim->update([
            'status' => 'rejected',
            'rejection_reason' => $data['reason'] ?? null,
        ]);

        // Notifier l'utilisateur du rejet
        if ($claim->user->fcm_token) {
            try {
                $firebase = new FirebaseNotificationService();
                $firebase->sendToUser(
                    $claim->user->fcm_token,
                    'Signalement rejeté',
                    "Votre réclamation pour l'objet {$claim->objet->name} a été rejetée. Motif : " . (($data['reason'] ?? null) ?: 'Non specifie'),
                    ['claim_id' => $claim->id]
                );
            } catch (\Exception $e) {
                \Log::error('Erreur notification: ' . $e->getMessage());
            }
        }

        return response()->json(['message' => 'Signalement rejeté.']);
    }
}
