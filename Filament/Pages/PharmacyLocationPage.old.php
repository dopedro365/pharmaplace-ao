<?php

namespace App\Livewire;

use App\Models\Pharmacy;
use Livewire\Component;
use Livewire\WithPagination;

class PharmacyLocationPage extends Component
{
    use WithPagination;

    // Search and filter properties
    public $search = '';
    public $selectedMunicipality = '';
    public $selectedProvince = '';
    public $sortBy = 'name';
    public $showOnlyActive = true;
    public $showOnlyVerified = false;
    public $showOnlyWithDelivery = false;
    public $maxDistance = null;
    
    // User location properties
    public $userLatitude = null;
    public $userLongitude = null;
    public $userAddress = '';
    public $showLocationDisplay = true;
    public $locationError = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedMunicipality' => ['except' => ''],
        'selectedProvince' => ['except' => ''],
        'sortBy' => ['except' => 'name'],
        'showOnlyActive' => ['except' => true],
        'showOnlyVerified' => ['except' => false],
        'showOnlyWithDelivery' => ['except' => false],
        'maxDistance' => ['except' => null],
    ];

    protected $listeners = [
        'location-updated' => 'updateUserLocation',
        'location-cleared' => 'clearUserLocation'
    ];

    public function mount()
    {
        // Tentar obter localização da sessão
        $this->userLatitude = session('user_latitude');
        $this->userLongitude = session('user_longitude');
        $this->userAddress = session('user_address', '');
        
        // Se não há localização, mostrar controles para obter
        $this->showLocationDisplay = true;
    }

    // ==================== UPDATERS ====================

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedMunicipality()
    {
        $this->resetPage();
    }

    public function updatedSelectedProvince()
    {
        $this->resetPage();
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function updatedShowOnlyActive()
    {
        $this->resetPage();
    }

    public function updatedShowOnlyVerified()
    {
        $this->resetPage();
    }

    public function updatedShowOnlyWithDelivery()
    {
        $this->resetPage();
    }

    public function updatedMaxDistance()
    {
        $this->resetPage();
    }

    // ==================== LOCATION METHODS ====================

    public function updateUserLocation($data)
    {
        $this->userLatitude = $data['latitude'];
        $this->userLongitude = $data['longitude'];
        $this->userAddress = $data['address'] ?? '';
        $this->locationError = '';
        
        // Salvar na sessão
        session([
            'user_latitude' => $this->userLatitude,
            'user_longitude' => $this->userLongitude,
            'user_address' => $this->userAddress
        ]);

        // Emitir evento para outros componentes
        $this->dispatch('location-updated', $data);
        
        $this->resetPage();
    }

    public function clearUserLocation()
    {
        $this->userLatitude = null;
        $this->userLongitude = null;
        $this->userAddress = '';
        $this->maxDistance = null;
        $this->locationError = '';
        
        // Limpar da sessão
        session()->forget(['user_latitude', 'user_longitude', 'user_address']);
        
        // Emitir evento para outros componentes
        $this->dispatch('location-cleared');
        
        $this->resetPage();
    }

    public function setLocationError($message)
    {
        $this->locationError = $message;
    }

    // ==================== FILTER METHODS ====================

    public function clearFilters()
    {
        $this->search = '';
        $this->selectedMunicipality = '';
        $this->selectedProvince = '';
        $this->sortBy = 'name';
        $this->showOnlyActive = true;
        $this->showOnlyVerified = false;
        $this->showOnlyWithDelivery = false;
        $this->maxDistance = null;
        $this->resetPage();
    }

    public function hasActiveFilters()
    {
        return $this->search || 
               $this->selectedMunicipality || 
               $this->selectedProvince || 
               !$this->showOnlyActive || 
               $this->showOnlyVerified || 
               $this->showOnlyWithDelivery || 
               $this->maxDistance;
    }

    // ==================== RENDER METHOD ====================

    public function render()
    {
        $query = Pharmacy::query();

        // Filtros básicos
        if ($this->showOnlyActive) {
            $query->active();
        }

        if ($this->showOnlyVerified) {
            $query->verified();
        }

        if ($this->showOnlyWithDelivery) {
            $query->withDelivery();
        }

        // Filtro por busca
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('address', 'like', '%' . $this->search . '%')
                  ->orWhere('municipality', 'like', '%' . $this->search . '%')
                  ->orWhere('province', 'like', '%' . $this->search . '%');
            });
        }

        // Filtro por município
        if ($this->selectedMunicipality) {
            $query->where('municipality', $this->selectedMunicipality);
        }

        // Filtro por província
        if ($this->selectedProvince) {
            $query->where('province', $this->selectedProvince);
        }

        // Filtro por distância
        if ($this->userLatitude && $this->userLongitude && $this->maxDistance) {
            $pharmacies = $query->get()->filter(function ($pharmacy) {
                if (!$pharmacy->hasLocation()) {
                    return false;
                }
                $distance = $pharmacy->distanceTo($this->userLatitude, $this->userLongitude);
                return $distance && $distance <= $this->maxDistance;
            });
            
            // Converter de volta para query builder para paginação
            $pharmacyIds = $pharmacies->pluck('id');
            if ($pharmacyIds->isNotEmpty()) {
                $query->whereIn('id', $pharmacyIds);
            } else {
                // Se não há farmácias na distância especificada, retornar query vazia
                $query->whereRaw('1 = 0');
            }
        }

        // Ordenação
        switch ($this->sortBy) {
            case 'distance':
                if ($this->userLatitude && $this->userLongitude) {
                    // Para ordenação por distância, precisamos buscar todos e ordenar manualmente
                    $pharmacies = $query->get()->map(function ($pharmacy) {
                        $pharmacy->calculated_distance = $pharmacy->hasLocation() 
                            ? $pharmacy->distanceTo($this->userLatitude, $this->userLongitude)
                            : 999999;
                        return $pharmacy;
                    })->sortBy('calculated_distance');
                    
                    $pharmacyIds = $pharmacies->pluck('id');
                    if ($pharmacyIds->isNotEmpty()) {
                        $query->whereIn('id', $pharmacyIds)->orderByRaw('FIELD(id, ' . $pharmacyIds->implode(',') . ')');
                    }
                } else {
                    $query->orderBy('name');
                }
                break;
            case 'newest':
                $query->latest();
                break;
            case 'rating':
                $query->orderByDesc('rating');
                break;
            default:
                $query->orderBy('name');
        }

        $pharmacies = $query->paginate(12);

        // Adicionar distância calculada para cada farmácia
        if ($this->userLatitude && $this->userLongitude) {
            $pharmacies->getCollection()->transform(function ($pharmacy) {
                $pharmacy->calculated_distance = $pharmacy->hasLocation() 
                    ? $pharmacy->distanceTo($this->userLatitude, $this->userLongitude)
                    : null;
                return $pharmacy;
            });
        }

        // Obter dados para filtros
        $municipalities = Pharmacy::distinct()
            ->whereNotNull('municipality')
            ->pluck('municipality')
            ->filter()
            ->sort()
            ->values();

        $provinces = Pharmacy::distinct()
            ->whereNotNull('province') 
            ->pluck('province')
            ->filter()
            ->sort()
            ->values();

        return view('livewire.pharmacy-location', [
            'pharmacies' => $pharmacies,
            'municipalities' => $municipalities,
            'provinces' => $provinces,
        ]);
    }
}
