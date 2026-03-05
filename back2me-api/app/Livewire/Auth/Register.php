<?php

namespace App\Livewire\Auth;

use App\Models\Filiere;
use App\Models\Niveau;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Register extends Component
{
    public $name = '';
    public $email = '';
    public $phone = '';
    public $filiere = '';
    public $niveau = '';
    public $password = '';
    public $password_confirmation = '';

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'required|string|max:30',
            'filiere' => ['required', 'string', Rule::exists('filieres', 'name')],
            'niveau' => ['required', 'string', Rule::exists('niveaux', 'name')],
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function register()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'filiere' => $this->filiere,
            'niveau' => $this->niveau,
            'password' => Hash::make($this->password),
            'role' => 'user',
        ]);

        Auth::login($user);
        session()->regenerate();

        return redirect()->intended('/dashboard');
    }

    public function render()
    {
        return view('livewire.auth.register', [
            'filiereOptions' => Filiere::query()->orderBy('id')->pluck('name')->all(),
            'niveauOptions' => Niveau::query()->orderBy('id')->pluck('name')->all(),
        ]);
    }
}
