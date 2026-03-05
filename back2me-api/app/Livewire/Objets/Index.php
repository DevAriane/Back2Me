<?php

namespace App\Livewire\Objets;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Objet;
use App\Models\Category;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $category_id = '';
    public $status = 'found';
    public $date_from = '';
    public $date_to = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'category_id' => ['except' => ''],
        'status' => ['except' => 'found'],
        'date_from' => ['except' => ''],
        'date_to' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Objet::with(['category', 'user']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->category_id) {
            $query->where('category_id', $this->category_id);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->date_from) {
            $query->whereDate('found_date', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->whereDate('found_date', '<=', $this->date_to);
        }

        $objets = $query->orderBy('created_at', 'desc')->paginate(10);
        $categories = Category::all();

        return view('livewire.objets.index', [
            'objets' => $objets,
            'categories' => $categories,
        ]);
    }
}
