<div>
    <div class="modal-overlay {{ $isOpen ? 'open' : '' }}" id="create-objet-modal" wire:click="closeModal">
        <div class="modal" wire:click.stop>
            <div class="modal-header">
                <div class="modal-title">➕ Enregistrer un objet trouvé</div>
                <button class="modal-close" wire:click="closeModal" type="button">✕</button>
            </div>

            <form wire:submit="save">
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Nom de l'objet *</label>
                            <input class="form-input" type="text" wire:model="name" placeholder="Ex: Smartphone, Sac…">
                            @error('name')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Catégorie *</label>
                            <select class="form-select" wire:model="category_id">
                                <option value="">Choisir…</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Lieu de découverte *</label>
                            <input class="form-input" type="text" wire:model="location" placeholder="Ex: Amphi B, Cafétéria…">
                            @error('location')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Date de découverte *</label>
                            <input class="form-input" type="date" wire:model="found_date">
                            @error('found_date')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea class="form-textarea" wire:model="description" placeholder="Couleur, taille, marque, détails particuliers…"></textarea>
                        @error('description')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Photo de l'objet</label>
                        <div class="upload-zone">
                            <div class="upload-icon">📷</div>
                            <div class="upload-text"><strong>Cliquez pour ajouter</strong> ou glissez une photo<br><span style="font-size:12px;">PNG, JPG jusqu'à 5 Mo</span></div>
                            <input type="file" wire:model="photo" accept="image/png,image/jpeg" style="margin-top:10px;">
                        </div>
                        @error('photo')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-outline" type="button" wire:click="closeModal">Annuler</button>
                    <button class="btn btn-primary" type="submit">✅ Enregistrer l'objet</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        window.openAddObjetModal = function () {
            Livewire.dispatch('open-create-objet-modal');
        };
    </script>
    @endpush
</div>
