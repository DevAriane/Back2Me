<div class="login-wrap" style="min-height:calc(100vh - 64px);">
    <div class="login-left">
        <div class="login-left-content">
            <div class="login-logo">🏠</div>
            <div class="login-hero-title">Créer un compte<br>Objets Trouvés</div>
            <div class="login-hero-sub">Inscrivez-vous pour consulter les objets trouvés et envoyer vos signalements.</div>
        </div>
    </div>
    <div class="login-right">
        <div class="login-form-wrap">
            <div class="login-form-title">Inscription</div>
            <div class="login-form-sub">Créez votre espace sécurisé</div>
            <form wire:submit.prevent="register">
                <div class="form-group">
                    <label class="form-label">Nom complet</label>
                    <input class="form-input" type="text" wire:model="name" placeholder="Ex: Safia N.">
                    @error('name')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input class="form-input" type="email" wire:model="email" placeholder="Ex: etudiant@iuget.edu">
                    @error('email')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Téléphone</label>
                    <input class="form-input" type="text" wire:model="phone" placeholder="Ex: +237 6XXXXXXXX">
                    @error('phone')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Mot de passe</label>
                    <input class="form-input" type="password" wire:model="password" placeholder="••••••••">
                    @error('password')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Confirmer le mot de passe</label>
                    <input class="form-input" type="password" wire:model="password_confirmation" placeholder="••••••••">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:13px;font-size:15px;border-radius:12px;">Créer mon compte →</button>
            </form>
            <div style="margin-top:10px;text-align:center;font-size:13px;color:var(--slate);">
                Déjà inscrit ? <a href="{{ route('login') }}" style="color:var(--teal);">Se connecter</a>
            </div>
        </div>
    </div>
</div>
