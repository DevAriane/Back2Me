<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Objet;
use App\Models\Category;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class ObjetController extends Controller
{
    /**
     * Liste paginée des objets avec filtres
     */
    public function index(Request $request)
    {
        $query = Objet::with(['category', 'user']);

        // Filtre par catégorie
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Filtre par statut
        if ($request->has('status') && in_array($request->status, ['found', 'returned', 'unclaimed'])) {
            $query->where('status', $request->status);
        }

        // Filtre par recherche (nom ou description)
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtre par date (ex: found_date >= ?)
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('found_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('found_date', '<=', $request->date_to);
        }

        // Tri
        $orderBy = $request->get('order_by', 'created_at');
        $orderDir = $request->get('order_dir', 'desc');
        $query->orderBy($orderBy, $orderDir);

        $objets = $query->paginate($request->get('per_page', 15));

        return response()->json($objets);
    }

    /**
     * Détail d'un objet
     */
    public function show(Objet $objet)
    {
        return response()->json($objet->load(['category', 'user', 'claims.user']));
    }

    /**
     * Créer un nouvel objet (trouvé)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'found_date' => 'required|date',
            'photo' => 'nullable|image|max:5120', // 5 Mo max
        ]);

        
if ($request->hasFile('photo')) {
    $path = $request->file('photo')->store('objets', 'public');
    $data['photo_url'] = Storage::url($path);
}
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['user_id'] = $request->user()->id; // déposant
        $data['status'] = 'found';

        // Upload photo si présente
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('objets', 'public');
            $data['photo_url'] = Storage::url($path);
        }

        $objet = Objet::create($data);

        // Notifier tous les utilisateurs (sauf le déposant ?)
        try {
            $firebase = new FirebaseNotificationService();
            $firebase->sendToAll(
                'Nouvel objet trouvé',
                "Un(e) {$objet->name} a été trouvé(e) à {$objet->location}.",
                ['objet_id' => $objet->id, 'type' => 'new_object']
            );
        } catch (\Exception $e) {
            // Log l'erreur mais ne bloque pas la création
            \Log::error('Erreur envoi notification Firebase: ' . $e->getMessage());
        }

        return response()->json($objet->load('category', 'user'), 201);
    }

    /**
     * Mettre à jour un objet (admin uniquement)
     */
    public function update(Request $request, Objet $objet)
    {
        $this->authorize('update', $objet); // Optionnel: politique d'accès

        $validator = Validator::make($request->all(), [
            'category_id' => 'sometimes|exists:categories,id',
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'location' => 'sometimes|string|max:255',
            'found_date' => 'sometimes|date',
            'status' => 'sometimes|in:found,returned,unclaimed',
            'photo' => 'nullable|image|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // Gérer nouvelle photo
        if ($request->hasFile('photo')) {
            // Supprimer ancienne photo si existante
            if ($objet->photo_url) {
                $oldPath = str_replace('/storage/', '', $objet->photo_url);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('photo')->store('objets', 'public');
            $data['photo_url'] = Storage::url($path);
        }

        $objet->update($data);

        return response()->json($objet->load('category', 'user'));
    }

    /**
     * Supprimer un objet (admin uniquement)
     */
    public function destroy(Objet $objet)
    {
        // Supprimer la photo associée
        if ($objet->photo_url) {
            $path = str_replace('/storage/', '', $objet->photo_url);
            Storage::disk('public')->delete($path);
        }

        $objet->delete();

        return response()->json(['message' => 'Objet supprimé.']);
    }

    /**
     * Marquer un objet comme rendu (admin)
     */
    public function markReturned(Objet $objet)
    {
        $objet->update(['status' => 'returned']);

        // Option : notifier le propriétaire si un claim a été approuvé
        // ...

        return response()->json(['message' => 'Objet marqué comme rendu.']);
    }
}