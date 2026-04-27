<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ProductListing extends Component
{
    use WithPagination;

    #[Url]

    public $category = '';

    #[Url]

    public $search = '';

    #[Url]

    public $brand = '';

    #[Url]

    public $minPrice = '';

    #[Url]

    public $maxPrice = '';

    #[Url]

    public $sort = 'newest';

    #[Url]

    public $featured = '';

    public $priceRange = [0,10000];

    public function mount() {
        // set the price range based on availabe products
        $maxProductPrice = Product::active()->max('price') ?? 10000;
        $this->priceRange = [0, ceil($maxProductPrice)];

        if(empty($this->maxPrice)) {
            $this->maxPrice = $this->priceRange[1];
        }
    }


    public function render()
    {

        $query = Product::query()
        ->active()
        ->with(['category', 'brand', 'primaryImage']);

        //search
        if($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . this->search . '%')
                ->orWhere('description', 'like', '%' . this->search . '%')
                ->orWhere('sku', 'like', '%' . this->search . '%');
            });
        }

        //category
        if($this->category) {
            $categoryModel = Category::where('slug', $this->category)->first();
            if($categoryModel) {
                $query->where('category_id', $categoryModel->id);
            }
        }

        $products = $query->paginate(12);
        dd($products);
        return view('livewire.product-listing')->layout('layouts.front-end-layout');
    }
}
