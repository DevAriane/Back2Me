<?php

namespace App\Livewire\Objets;

use App\Models\Category;
use App\Models\Objet;
use App\Services\ObjectFoundNotifier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateModal extends Component
{
    use WithFileUploads;

    public bool $isOpen = false;
    public string $name = '';
    public string $category_id = '';
    public string $location = '';
    public string $found_date = '';
    public string $description = '';
    public $photo;
    public string $redirectUrl = '';

    public function mount(): void
    {
        $this->found_date = now()->toDateString();
        $this->redirectUrl = url()->current();
    }

    #[On('open-create-objet-modal')]
    public function openModal(): void
    {
        $this->resetValidation();
        $this->isOpen = true;
    }

    public function closeModal(): void
    {
        $this->isOpen = false;
    }

    public function save(): mixed
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'location' => ['required', 'string', 'max:255'],
            'found_date' => ['required', 'date'],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ]);

        $payload = [
            'user_id' => Auth::id(),
            'category_id' => (int) $data['category_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?: null,
            'location' => $data['location'],
            'found_date' => $data['found_date'],
            'status' => 'found',
        ];

        if ($this->photo) {
            $path = $this->photo->store('objets', 'public');
            $payload['photo_url'] = Storage::url($path);
        }

        $objet = Objet::create($payload);
        app(ObjectFoundNotifier::class)->notifyAllUsers($objet);

        $this->reset(['name', 'category_id', 'location', 'description', 'photo']);
        $this->found_date = now()->toDateString();
        $this->isOpen = false;

        return redirect()->to($this->redirectUrl);
    }

    public function render()
    {
        return view('livewire.objets.create-modal', [
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }
}
