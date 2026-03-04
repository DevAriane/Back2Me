<div class="login-wrap" style="min-height:calc(100vh - 64px);">
    <div class="login-left">
        <div class="login-left-content">
            <div class="login-logo">🏠</div>
            <div class="login-hero-title">Application Objets<br>Trouvés · IUGET</div>
            <div class="login-hero-sub">Un campus plus organisé grâce à la digitalisation du bureau des objets trouvés.</div>
            <div class="login-feat">
                <div class="login-feat-item"><div class="login-feat-icon">📦</div> Enregistrement centralisé des objets</div>
                <div class="login-feat-item"><div class="login-feat-icon">🔔</div> Notifications automatiques en temps réel</div>
                <div class="login-feat-item"><div class="login-feat-icon">🔍</div> Recherche rapide par catégorie ou date</div>
                <div class="login-feat-item"><div class="login-feat-icon">✅</div> Suivi complet des restitutions</div>
            </div>
        </div>
    </div>
    <div class="login-right">
        <div class="login-form-wrap">
            <div class="login-form-title">Connexion</div>
            <div class="login-form-sub">Accédez à votre espace sécurisé</div>
            <div class="login-role-select">
                <button type="button" class="role-btn active"><span>🛡</span>Administrateur</button>
                <button type="button" class="role-btn"><span>🎓</span>Étudiant</button>
                <button type="button" class="role-btn"><span>👨‍🏫</span>Personnel</button>
            </div>

            <form wire:submit.prevent="login">
                <div class="form-group">
                    <label class="form-label">Identifiant (email)</label>
                    <input class="form-input" type="email" wire:model="email" placeholder="Ex: surveillant@iuget.edu">
                    @error('email')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Mot de passe</label>
                    <input class="form-input" type="password" wire:model="password" placeholder="••••••••">
                    @error('password')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="forgot">Mot de passe oublié ?</div>
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:13px;font-size:15px;border-radius:12px;">Se connecter →</button>
            </form>

            <div style="margin-top:20px;text-align:center;font-size:13px;color:var(--slate);">
                🔒 Accès sécurisé · Données protégées · IUGET {{ now()->year }}
            </div>
            <div style="margin-top:10px;text-align:center;font-size:13px;color:var(--slate);">
                Pas encore de compte ? <a href="{{ route('register') }}" style="color:var(--teal);">S'inscrire</a>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.querySelectorAll('.role-btn').forEach((btn) => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.role-btn').forEach((b) => b.classList.remove('active'));
                btn.classList.add('active');
            });
        });
    </script>
    @endpush
</div>
