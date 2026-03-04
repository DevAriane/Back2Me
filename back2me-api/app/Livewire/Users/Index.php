<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $role = '';

    protected $queryString = ['search', 'role'];

    public $showCreateModal = false;
    public $editingUser = null;
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $userRole = 'user';

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'userRole' => 'required|in:user,admin',
        ];

        if ($this->editingUser) {
            $rules['email'] = 'required|email|max:255|unique:users,email,' . $this->editingUser->id;
            $rules['password'] = 'nullable|string|min:8|confirmed';
        }

        return $rules;
    }

    public function create()
    {
        $this->reset(['editingUser', 'name', 'email', 'password', 'password_confirmation', 'userRole']);
        $this->showCreateModal = true;
    }

    public function edit(User $user)
    {
        $this->editingUser = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->userRole = $user->role;
        $this->password = $this->password_confirmation = '';
        $this->showCreateModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->userRole,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        } elseif (!$this->editingUser) {
            $data['password'] = Hash::make('defaultpassword'); // ou générer un mot de passe aléatoire
        }

        if ($this->editingUser) {
            $this->editingUser->update($data);
            session()->flash('success', 'Utilisateur mis à jour.');
        } else {
            User::create($data);
            session()->flash('success', 'Utilisateur créé.');
        }

        $this->showCreateModal = false;
    }

    public function delete(User $user)
    {
        if ($user->id === auth()->id()) {
            session()->flash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
            return;
        }
        $user->delete();
        session()->flash('success', 'Utilisateur supprimé.');
    }

    public function render()
    {
        $query = User::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->role) {
            $query->where('role', $this->role);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.users.index', compact('users'));
    }
}
